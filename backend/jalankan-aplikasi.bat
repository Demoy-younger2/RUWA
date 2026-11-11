@echo off
title SiRW Digital - Server Rukun Warga
echo ========================================================
echo   MENJALANKAN SERVER SIRW DIGITAL (RUKUN WARGA)
echo ========================================================
echo.
echo Membuka browser ke http://localhost:8000 ...
start http://localhost:8000
echo.
echo Server berjalan di http://localhost:8000
echo Tekan Ctrl + C untuk menghentikan server.
echo.
"C:\xampp\php\php.exe" artisan serve --port=8000
pause
