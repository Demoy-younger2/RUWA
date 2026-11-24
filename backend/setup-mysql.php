<?php

// Setup & Migration Helper untuk MySQL SiRW Digital

$envFile = __DIR__ . '/.env';
if (!file_exists($envFile)) {
    die("File .env tidak ditemukan.\n");
}

$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$env = [];
foreach ($lines as $line) {
    if (str_starts_with(trim($line), '#')) continue;
    if (str_contains($line, '=')) {
        list($key, $val) = explode('=', $line, 2);
        $env[trim($key)] = trim($val);
    }
}

$host = $env['DB_HOST'] ?? '127.0.0.1';
$port = $env['DB_PORT'] ?? '3306';
$database = $env['DB_DATABASE'] ?? 'rukun_warga';
$username = $env['DB_USERNAME'] ?? 'root';
$password = $env['DB_PASSWORD'] ?? '';

echo "====================================================\n";
echo "   SETUP DATABASE MYSQL - SISTEM INFORMASI RW       \n";
echo "====================================================\n";
echo "Host     : {$host}:{$port}\n";
echo "Database : {$database}\n";
echo "Username : {$username}\n\n";

try {
    echo "1. Menghubungkan ke MySQL Server...\n";
    $pdo = new PDO("mysql:host={$host};port={$port}", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    echo "   [OK] Terhubung ke MySQL Server.\n\n";

    echo "2. Membuat database '{$database}' jika belum ada...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    echo "   [OK] Database '{$database}' siap.\n\n";

} catch (PDOException $e) {
    echo "   [GAGAL] Tidak dapat terhubung ke MySQL: " . $e->getMessage() . "\n";
    echo "   Pastikan Apache & MySQL di XAMPP Control Panel sudah AKTIF (Start).\n";
    exit(1);
}

echo "3. Menjalankan migrasi tabel Laravel...\n";
passthru("\"" . PHP_BINARY . "\" artisan migrate --force", $returnVar);

if ($returnVar === 0) {
    echo "\n4. Menjalankan Database Seeder...\n";
    passthru("\"" . PHP_BINARY . "\" artisan db:seed --force", $seedVar);
    echo "\n====================================================\n";
    echo "   [SELESAI] Database MySQL SiRW Berhasil Disiapkan! \n";
    echo "====================================================\n";
} else {
    echo "\n[ERROR] Migrasi gagal dengan kode status {$returnVar}.\n";
}
