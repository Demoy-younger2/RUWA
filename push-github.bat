@echo off
title Push ke GitHub - RUWA
echo ========================================================
echo   SiRW Digital: Push ke Repository GitHub
echo   Target: https://github.com/Demoy-younger2/RUWA.git
echo ========================================================
echo.
echo Sedang melakukan push ke branch main...
echo (Jika jendela browser terbuka, silakan klik Authorize)
echo.
git push -u origin main
if %errorlevel% neq 0 (
    echo.
    echo --------------------------------------------------------
    echo CATATAN AUTENTIKASI:
    echo GitHub membutuhkan izin akun pemilik (Demoy-younger2).
    echo Anda dapat:
    echo 1. Login langsung lewat jendela browser yang muncul, ATAU
    echo 2. Gunakan Personal Access Token (PAT) dari:
    echo    https://github.com/settings/tokens
    echo --------------------------------------------------------
) else (
    echo.
    echo ========================================================
    echo   BERHASIL! Seluruh kode telah berhasil di-push ke GitHub.
    echo ========================================================
)
echo.
pause
