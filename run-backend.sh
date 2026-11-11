#!/usr/bin/env bash
cd "$(dirname "$0")/backend" || exit
php artisan serve --port=8000
