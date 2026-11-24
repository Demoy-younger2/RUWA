@echo off
title SiRW Digital - Setup & Migrasi Database MySQL
echo ======================================================================
echo           SETUP DATABASE MYSQL & MIGRASI SISTEM RUKUN WARGA
echo ======================================================================
echo.
echo Pastikan MySQL di XAMPP Control Panel sudah dalam kondisi START.
echo.
cd /d "%~dp0backend"
"C:\xampp\php\php.exe" setup-mysql.php
echo.
pause
