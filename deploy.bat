@echo off
REM PLPI Deployment Script untuk Windows
REM Usage: deploy.bat [environment] [action]
REM Example: deploy.bat production setup

setlocal enabledelayedexpansion

set ENVIRONMENT=%1
set ACTION=%2

if "%ENVIRONMENT%"=="" (
    set ENVIRONMENT=production
)

if "%ACTION%"=="" (
    set ACTION=setup
)

echo.
echo === PLPI Deployment Script (Windows) ===
echo Environment: %ENVIRONMENT%
echo Action: %ACTION%
echo.

REM Check if Composer exists
where composer >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] Composer not found. Please install Composer first.
    echo Download from: https://getcomposer.org/download/
    exit /b 1
)

echo [OK] Composer found

REM Check if PHP exists
where php >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] PHP not found. Please install PHP first.
    exit /b 1
)

echo [OK] PHP found

REM Get current directory
cd /d "%~dp0"
set PROJECT_ROOT=%cd%

REM Run action
if "%ACTION%"=="setup" (
    echo.
    echo [*] Setting up environment...
    
    if exist ".env.%ENVIRONMENT%" (
        echo [OK] .env.%ENVIRONMENT% found
        copy ".env.%ENVIRONMENT%" ".env" >nul
        echo [OK] .env created
    ) else (
        echo [WARN] .env.%ENVIRONMENT% not found
        if exist ".env.example" (
            copy ".env.example" ".env" >nul
            echo [OK] .env created from example
        )
    )
    
    echo.
    echo [*] Installing dependencies...
    call composer install --no-dev --optimize-autoloader
    if %ERRORLEVEL% NEQ 0 (
        echo [ERROR] Failed to install dependencies
        exit /b 1
    )
    echo [OK] Dependencies installed
    
    echo.
    echo [*] Setting permissions...
    icacls writable /grant:r "%USERNAME%":F /t >nul
    echo [OK] Permissions set
    
    echo.
    echo [SUCCESS] Setup completed!
    echo.
    echo Next steps:
    echo 1. Edit .env file with production credentials
    echo 2. Run: deploy.bat %ENVIRONMENT% migrate
    
) else if "%ACTION%"=="migrate" (
    echo.
    echo [*] Running database migrations...
    call php spark migrate
    if %ERRORLEVEL% NEQ 0 (
        echo [ERROR] Migration failed
        exit /b 1
    )
    echo [OK] Migration completed
    
    echo.
    echo [*] Clearing cache...
    call php spark cache:clear
    
    echo.
    echo [SUCCESS] Migration completed!
    
) else if "%ACTION%"=="cache-clear" (
    echo.
    echo [*] Clearing cache...
    call php spark cache:clear
    echo [OK] Cache cleared
    
) else (
    echo [ERROR] Unknown action: %ACTION%
    echo Usage: deploy.bat [environment] [action]
    echo Actions: setup, migrate, cache-clear
    exit /b 1
)

echo.
pause
