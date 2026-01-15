<?php
/**
 * ADMIN PANEL - Entry Point (MVC + RESTful API)
 * Nhà Thuốc Thanh Hoàn
 */

session_start();

// Handle CORS for API
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Định nghĩa ROOT cho admin
define('ADMIN_ROOT', __DIR__);
define('ROOT', dirname(__DIR__));

// Load config
require_once ROOT . '/config/config.php';
require_once ROOT . '/config/database.php';

// Load core admin
require_once ADMIN_ROOT . '/core/AdminController.php';

// Routing đơn giản
$controller = $_GET['controller'] ?? 'dashboard';
$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

// Detect API request
$isApi = false;
$accept = $_SERVER['HTTP_ACCEPT'] ?? '';
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (strpos($accept, 'application/json') !== false || 
    strpos($contentType, 'application/json') !== false ||
    (isset($_GET['format']) && $_GET['format'] === 'json')) {
    $isApi = true;
}

// Kiểm tra đăng nhập (trừ trang login)
if ($controller !== 'auth') {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        if ($isApi) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized', 'error_code' => 401]);
            exit;
        }
        header('Location: ' . BASE_URL . '/admin/?controller=auth&action=login');
        exit;
    }
}

// Map HTTP method to action for RESTful API
if ($isApi && $action === 'index') {
    $httpMethod = $_SERVER['REQUEST_METHOD'];
    switch ($httpMethod) {
        case 'GET':
            $action = $id ? 'show' : 'index';
            break;
        case 'POST':
            $action = 'store';
            break;
        case 'PUT':
        case 'PATCH':
            $action = 'update';
            break;
        case 'DELETE':
            $action = 'destroy';
            break;
    }
}

// Map controller
$controllerMap = [
    'auth' => 'AuthController',
    'dashboard' => 'DashboardController',
    'thuoc' => 'ThuocController',
    'don-hang' => 'DonHangController',
    'nhom-thuoc' => 'NhomThuocController',
    'thuong-hieu' => 'ThuongHieuController',
    'nguoi-dung' => 'NguoiDungController',
    'bai-viet' => 'BaiVietController',
    'nuoc-san-xuat' => 'NuocSanXuatController',
    'thanh-phan' => 'ThanhPhanController',
    'tac-dung-phu' => 'TacDungPhuController',
    'doi-tuong' => 'DoiTuongController'
];

$controllerClass = $controllerMap[$controller] ?? 'DashboardController';
$controllerFile = ADMIN_ROOT . '/controllers/' . $controllerClass . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $ctrl = new $controllerClass();
    
    if (method_exists($ctrl, $action)) {
        $ctrl->$action($id);
    } else {
        $ctrl->index();
    }
} else {
    if ($isApi) {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Controller not found', 'error_code' => 404]);
        exit;
    }
    echo "Controller không tồn tại: " . $controllerClass;
}
