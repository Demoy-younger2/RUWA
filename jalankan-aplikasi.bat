@echo off
title SiRW Digital - Launcher
echo ======================================================================
echo       MENJALANKAN SISTEM INFORMASI RUKUN WARGA (SiRW Digital)
echo               Arsitektur Terpisah: Backend & Frontend
echo ======================================================================
echo.
echo 1. Menjalankan Backend Laravel di port 8000...
start "Backend Laravel (:8000)" cmd /k "cd /d "%~dp0backend" && "C:\xampp\php\php.exe" artisan serve --port=8000"

echo 2. Menjalankan Frontend Vite SPA di port 5173...
start "Frontend Vite (:5173)" cmd /k "cd /d "%~dp0frontend" && npm run dev"

echo.
echo Menunggu server siap...
timeout /t 3 /nobreak >nul

echo 3. Membuka browser ke http://localhost:5173 ...
start http://localhost:5173

echo.
echo ======================================================================
echo   Frontend : http://localhost:5173
echo   Backend  : http://localhost:8000
echo   API Docs : http://localhost:8000/api/stats
echo ======================================================================
echo Tutup jendela cmd backend/frontend jika ingin menghentikan server.
pause
