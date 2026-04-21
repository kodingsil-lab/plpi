#!/bin/bash

# PLPI Deployment Script
# Usage: ./deploy.sh [environment] [action]
# Example: ./deploy.sh production setup
#          ./deploy.sh production migrate

set -e  # Exit on error

ENVIRONMENT="${1:-production}"
ACTION="${2:-setup}"
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}=== PLPI Deployment Script ===${NC}"
echo "Environment: $ENVIRONMENT"
echo "Action: $ACTION"
echo ""

# Function to print success message
success() {
    echo -e "${GREEN}✓ $1${NC}"
}

# Function to print error message
error() {
    echo -e "${RED}✗ $1${NC}"
    exit 1
}

# Function to print warning message
warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

# Check if composer exists
check_composer() {
    if ! command -v composer &> /dev/null; then
        warning "Composer not found. Trying /usr/local/bin/composer"
        if [ ! -f "/usr/local/bin/composer" ]; then
            error "Composer is not installed. Please install Composer first."
        fi
        COMPOSER="/usr/local/bin/composer"
    else
        COMPOSER="composer"
    fi
    success "Composer found: $COMPOSER"
}

# Check if PHP exists
check_php() {
    if ! command -v php &> /dev/null; then
        error "PHP is not installed"
    fi
    
    PHP_VERSION=$(php -v | head -n 1 | awk '{print $2}' | cut -d. -f1,2)
    success "PHP version: $PHP_VERSION"
    
    if [ "$(echo "$PHP_VERSION < 8.0" | bc)" -eq 1 ]; then
        error "PHP 8.0 or higher is required"
    fi
}

# Function to create/update .env file
setup_env() {
    ENV_FILE="$PROJECT_ROOT/.env.$ENVIRONMENT"
    ENV_LOCAL="$PROJECT_ROOT/.env"
    
    if [ "$ENVIRONMENT" = "production" ]; then
        if [ ! -f "$ENV_FILE" ]; then
            warning ".env.production not found. Creating from template..."
            cp "$PROJECT_ROOT/.env.example" "$ENV_FILE" 2>/dev/null || {
                warning "Creating .env.production template"
                cat > "$ENV_FILE" << 'EOF'
#--------------------------------------------------------------------
# Environment
#--------------------------------------------------------------------
CI_ENVIRONMENT = production

#--------------------------------------------------------------------
# APP
#--------------------------------------------------------------------
app.baseURL = 'https://loa.ejurnal-unisap.ac.id/'
app.forceGlobalSecureRequests = true
app.CSPEnabled = true

#--------------------------------------------------------------------
# DATABASE
#--------------------------------------------------------------------
database.default.hostname = localhost
database.default.database = plpi_prod
database.default.username = plpi_user
database.default.password = CHANGE_ME
database.default.DBDriver = MySQLi
database.default.port = 3306

#--------------------------------------------------------------------
# SESSION
#--------------------------------------------------------------------
session.driver = 'CodeIgniter\Session\Handlers\FileHandler'

#--------------------------------------------------------------------
# LOGGER
#--------------------------------------------------------------------
logger.threshold = 3

#--------------------------------------------------------------------
# EMAIL CONFIGURATION
#--------------------------------------------------------------------
email.protocol = 'smtp'
email.host = mail.hosting.com
email.port = 587
email.username = noreply@loa.ejurnal-unisap.ac.id
email.password = CHANGE_ME
email.crypto = tls
email.timeout = 30

#--------------------------------------------------------------------
# SECURITY
#--------------------------------------------------------------------
encryption.key = 
encryption.driver = OpenSSL
EOF
            }
        fi
        
        cp "$ENV_FILE" "$ENV_LOCAL"
        success ".env.production configured"
        
        warning "IMPORTANT: Update the following in .env.production:"
        warning "  - database.default.password"
        warning "  - email.host, email.username, email.password"
        warning "  - encryption.key"
        
    else
        warning "For $ENVIRONMENT environment, please check .env file"
    fi
}

# Function to install dependencies
install_dependencies() {
    echo ""
    echo "Installing dependencies..."
    
    if [ -f "$PROJECT_ROOT/composer.lock" ]; then
        $COMPOSER install --no-dev --optimize-autoloader
        success "Dependencies installed (no-dev)"
    else
        $COMPOSER install --optimize-autoloader
        success "Dependencies installed"
    fi
}

# Function to run migrations
run_migrations() {
    echo ""
    echo "Running database migrations..."
    
    cd "$PROJECT_ROOT"
    php spark migrate || error "Migration failed"
    success "Database migrations completed"
}

# Function to clear cache
clear_cache() {
    echo ""
    echo "Clearing cache..."
    
    cd "$PROJECT_ROOT"
    php spark cache:clear || warning "Cache clear failed (non-critical)"
    success "Cache cleared"
}

# Function to set permissions
set_permissions() {
    echo ""
    echo "Setting permissions..."
    
    if [ -d "$PROJECT_ROOT/writable" ]; then
        chmod -R 755 "$PROJECT_ROOT/writable"
        chmod -R 644 "$PROJECT_ROOT/writable"/*
        success "Writable folder permissions set"
    fi
    
    if [ -d "$PROJECT_ROOT/public/uploads" ]; then
        chmod -R 755 "$PROJECT_ROOT/public/uploads"
        success "Uploads folder permissions set"
    fi
    
    if [ -f "$PROJECT_ROOT/.env" ]; then
        chmod 600 "$PROJECT_ROOT/.env"
        success ".env file permissions set"
    fi
}

# Function to generate encryption key
generate_encryption_key() {
    echo ""
    echo "Generating encryption key..."
    
    cd "$PROJECT_ROOT"
    KEY=$(php -r "echo bin2hex(random_bytes(32));")
    
    echo "Generated key: $KEY"
    warning "Update 'encryption.key = $KEY' in .env file"
}

# Main deployment function
deploy() {
    check_php
    check_composer
    
    case "$ACTION" in
        setup)
            setup_env
            install_dependencies
            generate_encryption_key
            set_permissions
            success "Setup completed! Next steps:"
            echo "  1. Update .env with production credentials"
            echo "  2. Run: ./deploy.sh $ENVIRONMENT migrate"
            ;;
        migrate)
            run_migrations
            clear_cache
            success "Migration completed!"
            ;;
        refresh)
            warning "This will reset the database!"
            read -p "Are you sure? (yes/no) " -n 3 -r
            echo
            if [[ $REPLY =~ ^[Yy][Ee][Ss]$ ]]; then
                cd "$PROJECT_ROOT"
                php spark migrate:refresh
                success "Database refreshed"
            else
                error "Migration cancelled"
            fi
            ;;
        cache-clear)
            clear_cache
            ;;
        *)
            error "Unknown action: $ACTION"
            ;;
    esac
}

# Run deployment
deploy
