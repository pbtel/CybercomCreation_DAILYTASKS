@echo off
REM Enable PostgreSQL PHP Extension

echo ========================================
echo Enabling PostgreSQL PHP Extension
echo ========================================
echo.

set PHP_INI=C:\xampp\php\php.ini

echo PHP INI Location: %PHP_INI%
echo.

REM Create backup
echo Creating backup of php.ini...
copy "%PHP_INI%" "%PHP_INI%.backup" >nul
echo Backup created: %PHP_INI%.backup
echo.

REM Enable extensions using PowerShell
echo Enabling pdo_pgsql and pgsql extensions...
powershell -Command "(Get-Content '%PHP_INI%') -replace ';extension=pdo_pgsql', 'extension=pdo_pgsql' -replace ';extension=pgsql', 'extension=pgsql' | Set-Content '%PHP_INI%'"

if %ERRORLEVEL% EQU 0 (
    echo ✓ Extensions enabled successfully!
    echo.
    echo Please restart Apache/your web server for changes to take effect.
    echo.
    echo After restarting, run: php setup.php
) else (
    echo ❌ Failed to enable extensions
    echo Please manually edit %PHP_INI%
)

echo.
pause
