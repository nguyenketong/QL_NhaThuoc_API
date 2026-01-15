<?php
/**
 * CẤU HÌNH HỆ THỐNG - DOCKER
 */

// URL cơ sở
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost:8080');
}

// Database - Docker environment
define('DB_HOST', getenv('DB_HOST') ?: 'db');
define('DB_NAME', getenv('DB_NAME') ?: 'ql_nhathuoc_api');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: 'root123');

// Thông tin cửa hàng
define('STORE_NAME', 'Nhà Thuốc Tây Thanh Hoàn');
define('STORE_ADDRESS', 'Nguyễn Thiện Thành, Khóm 4, Phường 5, TP Trà Vinh');
define('STORE_PHONE', '0795930020');
define('STORE_EMAIL', 'thanhhoan@gmail.com');
