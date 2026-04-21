#!/usr/bin/env bash

# PLPI deployment helper for Linux hosting / VPS
# Usage:
#   ./deploy.sh production doctor
#   ./deploy.sh production bootstrap
#   ./deploy.sh production deploy
#   ./deploy.sh production migrate
#   ./deploy.sh production cache-clear

set -euo pipefail

ENVIRONMENT="${1:-production}"
ACTION="${2:-deploy}"
BRANCH="${DEPLOY_BRANCH:-main}"
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PHP_BIN="${PHP_BIN:-php}"
COMPOSER_BIN="${COMPOSER_BIN:-composer}"
GIT_BIN="${GIT_BIN:-git}"

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

success() {
    echo -e "${GREEN}[OK]${NC} $1"
}

warning() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

error() {
    echo -e "${RED}[ERROR]${NC} $1"
    exit 1
}

run_in_project() {
    (
        cd "$PROJECT_ROOT"
        "$@"
    )
}

show_usage() {
    cat <<EOF
PLPI deployment helper

Usage:
  ./deploy.sh [environment] [action]

Arguments:
  environment   Default: production
  action        doctor | bootstrap | deploy | migrate | cache-clear

Environment variables:
  DEPLOY_BRANCH   Git branch to deploy (default: main)
  PHP_BIN         PHP executable (default: php)
  COMPOSER_BIN    Composer executable (default: composer)
  GIT_BIN         Git executable (default: git)
EOF
}

require_command() {
    local command_name="$1"
    local pretty_name="$2"

    if ! command -v "$command_name" >/dev/null 2>&1; then
        error "$pretty_name not found: $command_name"
    fi
}

check_php() {
    require_command "$PHP_BIN" "PHP"

    local php_version
    php_version="$("$PHP_BIN" -r 'echo PHP_MAJOR_VERSION . "." . PHP_MINOR_VERSION;')"
    success "PHP detected: $php_version"

    "$PHP_BIN" -r 'exit(version_compare(PHP_VERSION, "8.2.0", ">=") ? 0 : 1);' \
        || error "PHP 8.2 or newer is required."
}

check_composer() {
    require_command "$COMPOSER_BIN" "Composer"
    success "Composer detected"
}

check_git() {
    require_command "$GIT_BIN" "Git"
    success "Git detected"
}

ensure_env_file() {
    local source_env="$PROJECT_ROOT/.env.$ENVIRONMENT"
    local target_env="$PROJECT_ROOT/.env"

    if [[ -f "$target_env" ]]; then
        success ".env already exists"
        return
    fi

    if [[ -f "$source_env" ]]; then
        cp "$source_env" "$target_env"
        chmod 600 "$target_env" || true
        success ".env created from .env.$ENVIRONMENT"
        warning "Review .env and replace placeholder secrets before going live."
        return
    fi

    error "Missing .env and .env.$ENVIRONMENT template."
}

ensure_writable_structure() {
    local dirs=(
        "$PROJECT_ROOT/writable/cache"
        "$PROJECT_ROOT/writable/logs"
        "$PROJECT_ROOT/writable/session"
        "$PROJECT_ROOT/writable/uploads"
        "$PROJECT_ROOT/public/uploads"
    )

    for dir in "${dirs[@]}"; do
        mkdir -p "$dir"
    done

    if [[ -d "$PROJECT_ROOT/writable" ]]; then
        find "$PROJECT_ROOT/writable" -type d -exec chmod 775 {} \; 2>/dev/null || true
        find "$PROJECT_ROOT/writable" -type f -exec chmod 664 {} \; 2>/dev/null || true
    fi

    if [[ -d "$PROJECT_ROOT/public/uploads" ]]; then
        chmod -R 775 "$PROJECT_ROOT/public/uploads" 2>/dev/null || true
    fi

    success "Writable folders prepared"
}

install_dependencies() {
    info "Installing Composer dependencies"
    run_in_project "$COMPOSER_BIN" install --no-dev --prefer-dist --optimize-autoloader
    success "Composer dependencies installed"
}

run_migrations() {
    info "Running database migrations"
    run_in_project "$PHP_BIN" spark migrate --all
    success "Migrations finished"
}

clear_cache() {
    info "Clearing application cache"
    run_in_project "$PHP_BIN" spark cache:clear || warning "Cache clear returned non-zero status"
    success "Cache clear step finished"
}

check_git_status_clean() {
    local git_status
    git_status="$(run_in_project "$GIT_BIN" status --porcelain)"

    if [[ -n "$git_status" ]]; then
        error "Working tree is not clean. Commit or stash changes before deploy."
    fi
}

update_from_git() {
    check_git
    check_git_status_clean

    info "Fetching latest code from origin/$BRANCH"
    run_in_project "$GIT_BIN" fetch origin "$BRANCH"

    info "Checking out branch $BRANCH"
    run_in_project "$GIT_BIN" checkout "$BRANCH"

    info "Pulling latest commit"
    run_in_project "$GIT_BIN" pull --ff-only origin "$BRANCH"
    success "Repository updated from origin/$BRANCH"
}

doctor() {
    check_php
    check_composer
    check_git

    if [[ -f "$PROJECT_ROOT/.env" ]]; then
        success ".env found"
    else
        warning ".env not found yet"
    fi

    if [[ -f "$PROJECT_ROOT/composer.lock" ]]; then
        success "composer.lock found"
    else
        warning "composer.lock not found"
    fi

    ensure_writable_structure
    success "Server check complete"
}

bootstrap() {
    check_php
    check_composer
    ensure_env_file
    ensure_writable_structure
    install_dependencies
    clear_cache
    success "Bootstrap complete"
    echo "Next step: ./deploy.sh $ENVIRONMENT migrate"
}

deploy() {
    check_php
    check_composer
    update_from_git
    ensure_env_file
    ensure_writable_structure
    install_dependencies
    run_migrations
    clear_cache
    success "Deploy complete for branch $BRANCH"
}

case "$ACTION" in
    doctor)
        doctor
        ;;
    bootstrap)
        bootstrap
        ;;
    deploy)
        deploy
        ;;
    migrate)
        check_php
        ensure_env_file
        run_migrations
        clear_cache
        ;;
    cache-clear)
        check_php
        clear_cache
        ;;
    help|-h|--help)
        show_usage
        ;;
    *)
        show_usage
        error "Unknown action: $ACTION"
        ;;
esac
