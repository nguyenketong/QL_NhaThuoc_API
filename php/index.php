<?php
/**
 * NHÀ THUỐC THANH HOÀN - PHP MVC + RESTful API
 * Entry Point
 */

// Bật hiển thị lỗi (tắt khi deploy production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Định nghĩa đường dẫn
define('ROOT', dirname(__FILE__));

// Autoload
spl_autoload_register(function ($class) {
    $paths = [
        ROOT . '/core/' . $class . '.php',
        ROOT . '/app/controllers/' . $class . '.php',
        ROOT . '/app/models/' . $class . '.php',
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Load config & database
require_once ROOT . '/config/config.php';
require_once ROOT . '/config/database.php';

// Khởi chạy App
$app = new App();
