@echo off
setlocal enabledelayedexpansion

REM PLPI deployment helper for Windows
REM Usage:
REM   deploy.bat production doctor
REM   deploy.bat production bootstrap
REM   deploy.bat production deploy

set "ENVIRONMENT=%~1"
set "ACTION=%~2"

if "%ENVIRONMENT%"=="" set "ENVIRONMENT=production"
if "%ACTION%"=="" set "ACTION=deploy"

set "PROJECT_ROOT=%~dp0"
set "PROJECT_ROOT=%PROJECT_ROOT:~0,-1%"
set "DEPLOY_BRANCH=%DEPLOY_BRANCH%"
if "%DEPLOY_BRANCH%"=="" set "DEPLOY_BRANCH=main"

call :info PLPI deployment helper
echo Environment: %ENVIRONMENT%
echo Action: %ACTION%
echo Branch: %DEPLOY_BRANCH%
echo.

if /I "%ACTION%"=="doctor" goto doctor
if /I "%ACTION%"=="bootstrap" goto bootstrap
if /I "%ACTION%"=="deploy" goto deploy
if /I "%ACTION%"=="migrate" goto migrate
if /I "%ACTION%"=="cache-clear" goto cacheclear
if /I "%ACTION%"=="help" goto usage
if /I "%ACTION%"=="-h" goto usage
if /I "%ACTION%"=="--help" goto usage

call :usage
call :fail Unknown action: %ACTION%
goto :eof

:doctor
call :check_php
call :check_composer
call :check_git
call :ensure_writable

if exist "%PROJECT_ROOT%\.env" (
    call :ok .env found
) else (
    call :warn .env not found yet
)

call :ok Server check complete
goto :eof

:bootstrap
call :check_php
call :check_composer
call :ensure_env
call :ensure_writable
call :install_dependencies
call :clear_cache
call :ok Bootstrap complete
echo Next step: deploy.bat %ENVIRONMENT% migrate
goto :eof

:deploy
call :check_php
call :check_composer
call :check_git
call :git_update
call :ensure_env
call :ensure_writable
call :install_dependencies
call :run_migrations
call :clear_cache
call :ok Deploy complete for branch %DEPLOY_BRANCH%
goto :eof

:migrate
call :check_php
call :ensure_env
call :run_migrations
call :clear_cache
goto :eof

:cacheclear
call :check_php
call :clear_cache
goto :eof

:check_php
where php >nul 2>nul || call :fail PHP not found in PATH
for /f "tokens=2 delims= " %%v in ('php -v ^| findstr /b /c:"PHP"') do set "PHP_VERSION=%%v"
call :ok PHP detected: %PHP_VERSION%
goto :eof

:check_composer
where composer >nul 2>nul || call :fail Composer not found in PATH
call :ok Composer detected
goto :eof

:check_git
where git >nul 2>nul || call :fail Git not found in PATH
call :ok Git detected
goto :eof

:ensure_env
if exist "%PROJECT_ROOT%\.env" (
    call :ok .env already exists
    goto :eof
)

if exist "%PROJECT_ROOT%\.env.%ENVIRONMENT%" (
    copy /Y "%PROJECT_ROOT%\.env.%ENVIRONMENT%" "%PROJECT_ROOT%\.env" >nul || call :fail Failed to create .env
    call :ok .env created from .env.%ENVIRONMENT%
    goto :eof
)

call :fail Missing .env and .env.%ENVIRONMENT% template.
goto :eof

:ensure_writable
if not exist "%PROJECT_ROOT%\writable\cache" mkdir "%PROJECT_ROOT%\writable\cache"
if not exist "%PROJECT_ROOT%\writable\logs" mkdir "%PROJECT_ROOT%\writable\logs"
if not exist "%PROJECT_ROOT%\writable\session" mkdir "%PROJECT_ROOT%\writable\session"
if not exist "%PROJECT_ROOT%\writable\uploads" mkdir "%PROJECT_ROOT%\writable\uploads"
if not exist "%PROJECT_ROOT%\public\uploads" mkdir "%PROJECT_ROOT%\public\uploads"
call :ok Writable folders prepared
goto :eof

:install_dependencies
call :info Installing Composer dependencies
pushd "%PROJECT_ROOT%"
call composer install --no-dev --prefer-dist --optimize-autoloader || (
    popd
    call :fail Composer install failed
)
popd
call :ok Composer dependencies installed
goto :eof

:run_migrations
call :info Running database migrations
pushd "%PROJECT_ROOT%"
call php spark migrate --all || (
    popd
    call :fail Migration failed
)
popd
call :ok Migrations finished
goto :eof

:clear_cache
call :info Clearing application cache
pushd "%PROJECT_ROOT%"
call php spark cache:clear
popd
call :ok Cache clear step finished
goto :eof

:git_update
pushd "%PROJECT_ROOT%"
for /f %%i in ('git status --porcelain') do (
    popd
    call :fail Working tree is not clean. Commit or stash changes before deploy.
)
git fetch origin %DEPLOY_BRANCH% || (
    popd
    call :fail git fetch failed
)
git checkout %DEPLOY_BRANCH% || (
    popd
    call :fail git checkout failed
)
git pull --ff-only origin %DEPLOY_BRANCH% || (
    popd
    call :fail git pull failed
)
popd
call :ok Repository updated from origin/%DEPLOY_BRANCH%
goto :eof

:usage
echo Usage: deploy.bat [environment] [action]
echo.
echo Actions:
echo   doctor
echo   bootstrap
echo   deploy
echo   migrate
echo   cache-clear
echo.
echo Optional env var:
echo   DEPLOY_BRANCH=main
goto :eof

:info
echo [INFO] %*
goto :eof

:ok
echo [OK] %*
goto :eof

:warn
echo [WARN] %*
goto :eof

:fail
echo [ERROR] %*
exit /b 1
