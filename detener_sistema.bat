@echo off
chcp 65001 > nul
echo ========================================================
echo   TORMAN MINERÍA - DETENER SERVIDOR LOCAL
echo ========================================================

echo Buscando procesos de Artisan en el puerto 8000...
for /f "tokens=5" %%a in ('netstat -aon ^| findstr ":8000" ^| findstr "LISTENING"') do (
    echo Finalizando proceso con PID %%a...
    taskkill /F /PID %%a >nul 2>nul
)

echo Servidor local detenido correctamente.
timeout /t 2 >nul
exit /b 0
