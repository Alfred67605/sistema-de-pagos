@echo off
chcp 65001 > nul
echo ========================================================
echo   TORMAN MINERÍA - SISTEMA ERP DE GESTIÓN Y PAGOS
echo   Iniciando servidor local seguro en 127.0.0.1:8000...
echo ========================================================

:: Agregar PHP al Path de sesión si está instalado vía WinGet
set "WINGET_PHP=C:\Users\%USERNAME%\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe"
if exist "%WINGET_PHP%\php.exe" (
    set "PATH=%WINGET_PHP%;%PATH%"
)

where php >nul 2>nul
if %errorlevel% neq 0 (
    echo [ERROR] No se encontró el ejecutable de PHP en el sistema.
    echo Por favor instala PHP o verifica la ruta configurada.
    pause
    exit /b 1
)

cd /d "%~dp0"

echo [1/2] Verificando base de datos SQLite...
if not exist "database\database.sqlite" (
    echo Creando archivo database.sqlite...
    type nul > database\database.sqlite
)

echo [2/2] Iniciando servidor web Artisan en http://127.0.0.1:8000...
start "" http://127.0.0.1:8000

php artisan serve --host=127.0.0.1 --port=8000
