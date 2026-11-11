@echo off
title SiRW Digital - Backend Server (:8000)
cd /d "%~dp0backend"
"C:\xampp\php\php.exe" artisan serve --port=8000
pause
