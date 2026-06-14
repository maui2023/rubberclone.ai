<?php
// public/index.php

// Pengepala CORS untuk memudahkan sambungan aplikasi mudah alih Android
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Tamatkan awal jika jenis request ialah OPTIONS (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Mengaktifkan paparan ralat (boleh ditutup semasa production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Muat konfigurasi
require_once __DIR__ . '/../app/config/database.php';

// Autoloader untuk memuatkan kelas secara dinamik
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/../app/core/',
        __DIR__ . '/../app/controllers/',
        __DIR__ . '/../app/models/'
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

if (!function_exists('route')) {
    function route($path) {
        $cleanPath = ltrim($path, '/');
        $useCleanUrls = defined('ENABLE_CLEAN_URLS') && ENABLE_CLEAN_URLS;
        if ($useCleanUrls) {
            return '/' . $cleanPath;
        }
        return '/index.php?url=' . $cleanPath;
    }
}

// Mulakan sistem
$app = new App();
