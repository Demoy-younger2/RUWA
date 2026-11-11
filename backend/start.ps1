Write-Host "========================================================" -ForegroundColor Cyan
Write-Host "  MENJALANKAN SERVER SIRW DIGITAL (RUKUN WARGA)" -ForegroundColor Yellow
Write-Host "========================================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Membuka browser ke http://localhost:8000 ..." -ForegroundColor Green
Start-Process "http://localhost:8000"
Write-Host "Server aktif di http://localhost:8000 (Tekan Ctrl + C untuk berhenti)" -ForegroundColor White
Write-Host ""
& "C:\xampp\php\php.exe" artisan serve --port=8000
