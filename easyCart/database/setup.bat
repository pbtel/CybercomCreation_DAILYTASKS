@echo off
REM Database Setup Script for EasyCart
REM This script creates the database and runs the schema

echo ========================================
echo EasyCart Database Setup
echo ========================================
echo.

REM Set PostgreSQL credentials
set PGPASSWORD=root
set PGUSER=postgres
set PGHOST=localhost
set PGPORT=5432

echo Step 1: Creating database 'easycart_db'...
psql -U %PGUSER% -h %PGHOST% -p %PGPORT% -c "DROP DATABASE IF EXISTS easycart_db;"
psql -U %PGUSER% -h %PGHOST% -p %PGPORT% -c "CREATE DATABASE easycart_db;"

if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Failed to create database
    pause
    exit /b 1
)

echo Database created successfully!
echo.

echo Step 2: Running schema.sql...
psql -U %PGUSER% -h %PGHOST% -p %PGPORT% -d easycart_db -f schema.sql

if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Failed to run schema
    pause
    exit /b 1
)

echo Schema created successfully!
echo.

echo ========================================
echo Database setup completed successfully!
echo ========================================
echo.
echo Next step: Run migrate.php to import existing data
echo.
pause
