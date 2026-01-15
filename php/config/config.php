<?php
/**
 * CẤU HÌNH HỆ THỐNG - NHÀ THUỐC THANH HOÀN
 * Tự động detect XAMPP hoặc Docker
 */

// Detect môi trường
$isDocker = getenv('DOCKER_ENV') === 'true';

// URL cơ sở
if (!defined('BASE_URL')) {
    if ($isDocker) {
        define('BASE_URL', 'http://localhost:8080');
    } else {
        define('BASE_URL', 'http://localhost/Ql_NhaThuoc_API/php');
    }
}

// Database - Auto detect
if ($isDocker) {
    define('DB_HOST', getenv('DB_HOST') ?: 'db');
    define('DB_NAME', getenv('DB_NAME') ?: 'ql_nhathuoc_api');
    define('DB_USER', getenv('DB_USER') ?: 'root');
    define('DB_PASS', getenv('DB_PASS') ?: 'root123');
} else {
    // XAMPP
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'ql_nhathuoc_api');
    define('DB_USER', 'root');
    define('DB_PASS', '');
}

// Thông tin cửa hàng
define('STORE_NAME', 'Nhà Thuốc Tây Thanh Hoàn');
define('STORE_ADDRESS', 'Nguyễn Thiện Thành, Khóm 4, Phường 5, TP Trà Vinh');
define('STORE_PHONE', '0795930020');
define('STORE_EMAIL', 'thanhhoan@gmail.com');

// Thông tin ngân hàng (VietQR API)
define('BANK_ID', '970422');
define('BANK_ACCOUNT_NO', '0795930020');
define('BANK_ACCOUNT_NAME', 'NGUYEN KE TONG');
define('BANK_TEMPLATE', 'compact2');

// Google OAuth 2.0
define('GOOGLE_CLIENT_ID', 'YOUR_GOOGLE_CLIENT_ID');
define('GOOGLE_CLIENT_SECRET', 'YOUR_GOOGLE_CLIENT_SECRET');
define('GOOGLE_REDIRECT_URI', BASE_URL . '/user/googleCallback');

// eSMS API - Đăng ký tại https://esms.vn
define('ESMS_API_KEY', '1FF178AEFB29AD2C0B61FA1197E244');
define('ESMS_SECRET_KEY', 'C3A8D07128D23B50EBE8D116CC4859');
define('ESMS_BASE_URL', 'http://rest.esms.vn/MainService.svc/json');
define('ESMS_BRAND_NAME', '');

// OTP Mode: 'dev' = hiển thị OTP trên màn hình (test), 'real' = gửi SMS thật
define('OTP_MODE', 'dev');
