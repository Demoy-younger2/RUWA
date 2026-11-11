#!/usr/bin/env bash
echo "Menjalankan Backend Laravel di port 8000..."
(cd backend && php artisan serve --port=8000) &

echo "Menjalankan Frontend Vite di port 5173..."
(cd frontend && npm run dev) &

wait
