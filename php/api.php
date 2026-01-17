<?php
/**
 * API Router - RESTful API đầy đủ CRUD
 * 
 * AUTH:
 * POST   /api.php/auth/login         - Đăng nhập User
 * POST   /api.php/auth/register      - Đăng ký User
 * POST   /api.php/auth/admin-login   - Đăng nhập Admin
 * POST   /api.php/auth/logout        - Đăng xuất
 * GET    /api.php/auth/me            - Thông tin user hiện tại
 * 
 * CRUD:
 * GET    /api.php/thuoc         - Danh sách
 * GET    /api.php/thuoc/1       - Chi tiết
 * POST   /api.php/thuoc         - Tạo mới (Admin)
 * PUT    /api.php/thuoc/1       - Cập nhật (Admin)
 * DELETE /api.php/thuoc/1       - Xóa (Admin)
 */

session_start();
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

define('ROOT', __DIR__);
require_once ROOT . '/config/config.php';
require_once ROOT . '/config/database.php';

// Parse URL
$requestUri = $_SERVER['REQUEST_URI'];
$scriptName = $_SERVER['SCRIPT_NAME'];
$pathInfo = $_SERVER['PATH_INFO'] ?? '';

// Nếu có PATH_INFO (Apache), dùng trực tiếp
if (!empty($pathInfo)) {
    $path = $pathInfo;
} else {
    // Fallback: parse từ REQUEST_URI
    $path = str_replace(dirname($scriptName), '', $requestUri);
    $path = preg_replace('/^\/api\.php/', '', $path);
    $path = preg_replace('/^\/api/', '', $path);
}

$path = strtok($path, '?');
$segments = array_values(array_filter(explode('/', $path)));

$resource = $segments[0] ?? 'home';
$id = $segments[1] ?? null;
$method = $_SERVER['REQUEST_METHOD'];

$db = Database::getInstance()->getConnection();

// ==================== HELPERS ====================

function json($data, $message = 'Success', $code = 200) {
    http_response_code($code);
    echo json_encode(['success' => true, 'message' => $message, 'data' => $data], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

function error($message, $code = 400) {
    http_response_code($code);
    echo json_encode(['success' => false, 'message' => $message, 'error_code' => $code], JSON_UNESCAPED_UNICODE);
    exit;
}

function input() {
    return json_decode(file_get_contents('php://input'), true) ?? [];
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) || isset($_COOKIE['UserId']);
}

function isAdmin() {
    // Chỉ là Admin nếu user_role = Admin VÀ không phải đang đăng nhập bằng user thường
    $role = $_SESSION['user_role'] ?? '';
    return $role === 'Admin';
}

function requireLogin() {
    if (!isLoggedIn()) error('Unauthorized - Vui lòng đăng nhập', 401);
}

function requireAdmin() {
    if (!isAdmin()) error('Forbidden - Chỉ Admin mới có quyền', 403);
}

function paginate($data, $total, $page, $limit) {
    echo json_encode([
        'success' => true,
        'data' => $data,
        'pagination' => [
            'total' => (int)$total,
            'per_page' => (int)$limit,
            'current_page' => (int)$page,
            'total_pages' => (int)ceil($total / $limit)
        ]
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

function page() {
    $p = max(1, (int)($_GET['page'] ?? 1));
    $l = min(100, max(1, (int)($_GET['limit'] ?? 10)));
    return [$p, $l, ($p - 1) * $l];
}

// ==================== ROUTING ====================

switch ($resource) {

// ==================== AUTHENTICATION ====================
case 'auth':
    $action = $id; // login, register, admin-login, logout, me
    
    // POST /auth/login - Đăng nhập User
    if ($method === 'POST' && $action === 'login') {
        $data = input();
        $phone = $data['phone'] ?? $data['SoDienThoai'] ?? '';
        $password = $data['password'] ?? $data['MatKhau'] ?? '';
        
        if (empty($phone) || empty($password)) error('Vui lòng nhập số điện thoại và mật khẩu', 400);
        
        $stmt = $db->prepare("SELECT * FROM nguoi_dung WHERE SoDienThoai = ?");
        $stmt->execute([$phone]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) error('Số điện thoại không tồn tại', 401);
        
        // Verify password
        $isValid = false;
        if (!empty($user['MatKhau'])) {
            if (password_verify($password, $user['MatKhau'])) $isValid = true;
            elseif ($password === $user['MatKhau']) $isValid = true;
        }
        
        if (!$isValid) error('Mật khẩu không đúng', 401);
        
        // Clear session cũ trước khi set mới
        unset($_SESSION['admin_logged_in'], $_SESSION['admin_id'], $_SESSION['admin_name'], $_SESSION['admin_phone']);
        
        // Set session
        $_SESSION['user_id'] = $user['MaNguoiDung'];
        $_SESSION['user_name'] = $user['HoTen'];
        $_SESSION['user_role'] = $user['VaiTro'] ?? 'User';
        setcookie('UserId', $user['MaNguoiDung'], time() + 30 * 24 * 3600, '/');
        setcookie('AdminLoggedIn', '', time() - 3600, '/');
        
        unset($user['MatKhau']);
        json(['user' => $user, 'role' => $user['VaiTro'] ?? 'User'], 'Đăng nhập thành công');
    }
    
    // POST /auth/register - Đăng ký User
    elseif ($method === 'POST' && $action === 'register') {
        $data = input();
        $name = $data['name'] ?? $data['HoTen'] ?? '';
        $phone = $data['phone'] ?? $data['SoDienThoai'] ?? '';
        $password = $data['password'] ?? $data['MatKhau'] ?? '';
        
        if (empty($name) || empty($phone) || empty($password)) error('Vui lòng nhập đầy đủ thông tin', 400);
        
        $stmt = $db->prepare("SELECT MaNguoiDung FROM nguoi_dung WHERE SoDienThoai = ?");
        $stmt->execute([$phone]);
        if ($stmt->fetch()) error('Số điện thoại đã được đăng ký', 409);
        
        $stmt = $db->prepare("INSERT INTO nguoi_dung (HoTen, SoDienThoai, MatKhau, VaiTro, NgayTao) VALUES (?, ?, ?, 'User', NOW())");
        $stmt->execute([$name, $phone, password_hash($password, PASSWORD_DEFAULT)]);
        $newId = $db->lastInsertId();
        
        $_SESSION['user_id'] = $newId;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_role'] = 'User';
        setcookie('UserId', $newId, time() + 30 * 24 * 3600, '/');
        
        $stmt = $db->prepare("SELECT * FROM nguoi_dung WHERE MaNguoiDung = ?");
        $stmt->execute([$newId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        unset($user['MatKhau']);
        
        json(['user' => $user], 'Đăng ký thành công', 201);
    }
    
    // POST /auth/admin-login - Đăng nhập Admin
    elseif ($method === 'POST' && $action === 'admin-login') {
        $data = input();
        $phone = $data['phone'] ?? $data['soDienThoai'] ?? '';
        $password = $data['password'] ?? $data['matKhau'] ?? '';
        
        if (empty($phone) || empty($password)) error('Vui lòng nhập số điện thoại và mật khẩu', 400);
        
        $stmt = $db->prepare("SELECT * FROM nguoi_dung WHERE SoDienThoai = ? AND VaiTro = 'Admin'");
        $stmt->execute([$phone]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$admin) error('Tài khoản không có quyền quản trị', 401);
        
        // Verify password
        $isValid = false;
        $matKhauDB = $admin['MatKhau'] ?? null;
        if (empty($matKhauDB) || $matKhauDB === 'admin123') {
            if ($password === 'admin123') $isValid = true;
        } elseif (password_verify($password, $matKhauDB)) {
            $isValid = true;
        } elseif ($password === $matKhauDB) {
            $isValid = true;
        }
        
        if (!$isValid) error('Mật khẩu không đúng', 401);
        
        // Set session
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $admin['MaNguoiDung'];
        $_SESSION['admin_name'] = $admin['HoTen'];
        $_SESSION['admin_phone'] = $admin['SoDienThoai'];
        $_SESSION['user_id'] = $admin['MaNguoiDung'];
        $_SESSION['user_role'] = 'Admin';
        
        unset($admin['MatKhau']);
        json(['admin' => $admin], 'Đăng nhập Admin thành công');
    }
    
    // POST /auth/logout - Đăng xuất
    elseif ($method === 'POST' && $action === 'logout') {
        unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_role']);
        unset($_SESSION['admin_logged_in'], $_SESSION['admin_id'], $_SESSION['admin_name']);
        setcookie('UserId', '', time() - 3600, '/');
        setcookie('AdminLoggedIn', '', time() - 3600, '/');
        json(null, 'Đăng xuất thành công');
    }
    
    // POST /auth/send-otp - Gửi mã OTP
    elseif ($method === 'POST' && $action === 'send-otp') {
        $data = input();
        $phone = $data['phone'] ?? $data['SoDienThoai'] ?? '';
        
        if (empty($phone) || strlen($phone) < 10) error('Số điện thoại không hợp lệ', 400);
        
        // Tạo OTP 6 số
        $otp = rand(100000, 999999);
        
        // Lưu vào session
        $_SESSION['otp'] = $otp;
        $_SESSION['otp_phone'] = $phone;
        $_SESSION['otp_time'] = time();
        
        // Kiểm tra user tồn tại chưa
        $stmt = $db->prepare("SELECT MaNguoiDung FROM nguoi_dung WHERE SoDienThoai = ?");
        $stmt->execute([$phone]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Lưu OTP vào DB
            $db->prepare("UPDATE nguoi_dung SET OTP = ?, OTP_Expire = DATE_ADD(NOW(), INTERVAL 5 MINUTE) WHERE MaNguoiDung = ?")
               ->execute([$otp, $user['MaNguoiDung']]);
        }
        
        // Response (trong môi trường dev, trả về OTP để test)
        json([
            'phone' => $phone,
            'otp' => $otp,  // ⚠️ Chỉ hiển thị trong môi trường dev
            'expires_in' => 300,
            'message' => 'OTP đã được gửi (dev mode: hiển thị trực tiếp)'
        ], 'Gửi OTP thành công');
    }
    
    // POST /auth/verify-otp - Xác nhận OTP và đăng nhập
    elseif ($method === 'POST' && $action === 'verify-otp') {
        $data = input();
        $phone = $data['phone'] ?? $_SESSION['otp_phone'] ?? '';
        $otpInput = $data['otp'] ?? '';
        
        if (empty($otpInput)) error('Vui lòng nhập mã OTP', 400);
        
        $otpSaved = $_SESSION['otp'] ?? '';
        $otpTime = $_SESSION['otp_time'] ?? 0;
        
        // Kiểm tra hết hạn (5 phút)
        if (time() - $otpTime > 300) error('Mã OTP đã hết hạn', 401);
        
        // Kiểm tra OTP
        if ($otpInput != $otpSaved) error('Mã OTP không đúng', 401);
        
        // Tìm hoặc tạo user
        $stmt = $db->prepare("SELECT * FROM nguoi_dung WHERE SoDienThoai = ?");
        $stmt->execute([$phone]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            // Tạo user mới
            $db->prepare("INSERT INTO nguoi_dung (SoDienThoai, HoTen, VaiTro, NgayTao) VALUES (?, ?, 'User', NOW())")
               ->execute([$phone, 'Khách hàng ' . substr($phone, -4)]);
            $userId = $db->lastInsertId();
            
            $stmt = $db->prepare("SELECT * FROM nguoi_dung WHERE MaNguoiDung = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        // Đăng nhập
        $_SESSION['user_id'] = $user['MaNguoiDung'];
        $_SESSION['user_name'] = $user['HoTen'];
        $_SESSION['user_role'] = $user['VaiTro'] ?? 'User';
        setcookie('UserId', $user['MaNguoiDung'], time() + 30 * 24 * 3600, '/');
        
        // Xóa OTP session
        unset($_SESSION['otp'], $_SESSION['otp_phone'], $_SESSION['otp_time']);
        
        unset($user['MatKhau'], $user['OTP']);
        json(['user' => $user], 'Đăng nhập thành công');
    }
    
    // GET /auth/me - Thông tin user hiện tại
    elseif ($method === 'GET' && $action === 'me') {
        requireLogin();
        $userId = $_SESSION['user_id'] ?? $_COOKIE['UserId'] ?? null;
        
        $stmt = $db->prepare("SELECT * FROM nguoi_dung WHERE MaNguoiDung = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) error('User không tồn tại', 404);
        unset($user['MatKhau']);
        
        json([
            'user' => $user,
            'is_admin' => isAdmin()
        ], 'Thông tin người dùng');
    }
    
    else {
        error('Auth action không hợp lệ', 404);
    }
    break;

// ==================== NGƯỜI DÙNG (Admin) ====================
case 'nguoi-dung':
case 'users':
    requireAdmin();
    
    if ($method === 'GET' && !$id) {
        list($pg, $lm, $of) = page();
        $total = $db->query("SELECT COUNT(*) FROM nguoi_dung")->fetchColumn();
        $stmt = $db->prepare("SELECT MaNguoiDung, HoTen, SoDienThoai, Email, DiaChi, VaiTro, NgayTao FROM nguoi_dung ORDER BY MaNguoiDung DESC LIMIT $lm OFFSET $of");
        $stmt->execute();
        paginate($stmt->fetchAll(PDO::FETCH_ASSOC), $total, $pg, $lm);
    }
    elseif ($method === 'GET' && $id) {
        $stmt = $db->prepare("SELECT MaNguoiDung, HoTen, SoDienThoai, Email, DiaChi, VaiTro, NgayTao FROM nguoi_dung WHERE MaNguoiDung = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) error('Người dùng không tồn tại', 404);
        json($user);
    }
    elseif ($method === 'PUT' && $id) {
        $stmt = $db->prepare("SELECT * FROM nguoi_dung WHERE MaNguoiDung = ?");
        $stmt->execute([$id]);
        $old = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$old) error('Người dùng không tồn tại', 404);
        
        $data = input();
        $updateFields = [];
        $params = [];
        
        if (isset($data['HoTen'])) { $updateFields[] = "HoTen = ?"; $params[] = $data['HoTen']; }
        if (isset($data['Email'])) { $updateFields[] = "Email = ?"; $params[] = $data['Email']; }
        if (isset($data['DiaChi'])) { $updateFields[] = "DiaChi = ?"; $params[] = $data['DiaChi']; }
        if (isset($data['VaiTro'])) { $updateFields[] = "VaiTro = ?"; $params[] = $data['VaiTro']; }
        
        if (!empty($updateFields)) {
            $params[] = $id;
            $db->prepare("UPDATE nguoi_dung SET " . implode(', ', $updateFields) . " WHERE MaNguoiDung = ?")->execute($params);
        }
        
        $stmt = $db->prepare("SELECT MaNguoiDung, HoTen, SoDienThoai, Email, DiaChi, VaiTro FROM nguoi_dung WHERE MaNguoiDung = ?");
        $stmt->execute([$id]);
        json($stmt->fetch(PDO::FETCH_ASSOC), 'Cập nhật thành công');
    }
    elseif ($method === 'DELETE' && $id) {
        $stmt = $db->prepare("SELECT * FROM nguoi_dung WHERE MaNguoiDung = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) error('Người dùng không tồn tại', 404);
        
        $db->prepare("UPDATE nguoi_dung SET IsActive = 0 WHERE MaNguoiDung = ?")->execute([$id]);
        json(null, 'Xóa người dùng thành công');
    }
    break;

// ==================== ĐƠN HÀNG ====================
case 'don-hang':
case 'orders':
    requireLogin();
    
    if ($method === 'GET' && !$id) {
        list($pg, $lm, $of) = page();
        $userId = $_SESSION['user_id'];
        
        // Admin xem tất cả, User chỉ xem của mình
        if (isAdmin()) {
            $where = "1=1";
            $params = [];
        } else {
            $where = "dh.MaNguoiDung = ?";
            $params = [$userId];
        }
        
        $total = $db->prepare("SELECT COUNT(*) FROM don_hang dh WHERE $where");
        $total->execute($params);
        
        $sql = "SELECT dh.*, nd.HoTen, nd.SoDienThoai FROM don_hang dh 
                LEFT JOIN nguoi_dung nd ON dh.MaNguoiDung = nd.MaNguoiDung 
                WHERE $where ORDER BY dh.MaDonHang DESC LIMIT $lm OFFSET $of";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        paginate($stmt->fetchAll(PDO::FETCH_ASSOC), $total->fetchColumn(), $pg, $lm);
    }
    elseif ($method === 'GET' && $id) {
        $stmt = $db->prepare("SELECT dh.*, nd.HoTen, nd.SoDienThoai FROM don_hang dh 
                              LEFT JOIN nguoi_dung nd ON dh.MaNguoiDung = nd.MaNguoiDung 
                              WHERE dh.MaDonHang = ?");
        $stmt->execute([$id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$order) error('Đơn hàng không tồn tại', 404);
        
        // Check permission
        if (!isAdmin() && $order['MaNguoiDung'] != $_SESSION['user_id']) {
            error('Forbidden', 403);
        }
        
        // Get order items
        $stmt = $db->prepare("SELECT ct.*, t.TenThuoc, t.HinhAnh FROM chi_tiet_don_hang ct 
                              LEFT JOIN thuoc t ON ct.MaThuoc = t.MaThuoc 
                              WHERE ct.MaDonHang = ?");
        $stmt->execute([$id]);
        $order['chi_tiet'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        json($order);
    }
    elseif ($method === 'POST') {
        $data = input();
        if (empty($data['items']) || !is_array($data['items'])) error('Vui lòng thêm sản phẩm', 400);
        
        $userId = $_SESSION['user_id'];
        $tongTien = 0;
        
        // Calculate total
        foreach ($data['items'] as $item) {
            $stmt = $db->prepare("SELECT GiaBan FROM thuoc WHERE MaThuoc = ?");
            $stmt->execute([$item['MaThuoc']]);
            $thuoc = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($thuoc) {
                $tongTien += $thuoc['GiaBan'] * ($item['SoLuong'] ?? 1);
            }
        }
        
        // Create order
        $stmt = $db->prepare("INSERT INTO don_hang (MaNguoiDung, TongTien, TrangThai, DiaChiGiao, GhiChu, NgayDat) VALUES (?, ?, 'Chờ xác nhận', ?, ?, NOW())");
        $stmt->execute([$userId, $tongTien, $data['DiaChiGiao'] ?? '', $data['GhiChu'] ?? '']);
        $orderId = $db->lastInsertId();
        
        // Add items
        foreach ($data['items'] as $item) {
            $stmt = $db->prepare("SELECT GiaBan FROM thuoc WHERE MaThuoc = ?");
            $stmt->execute([$item['MaThuoc']]);
            $thuoc = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($thuoc) {
                $db->prepare("INSERT INTO chi_tiet_don_hang (MaDonHang, MaThuoc, SoLuong, DonGia) VALUES (?, ?, ?, ?)")
                   ->execute([$orderId, $item['MaThuoc'], $item['SoLuong'] ?? 1, $thuoc['GiaBan']]);
            }
        }
        
        $stmt = $db->prepare("SELECT * FROM don_hang WHERE MaDonHang = ?");
        $stmt->execute([$orderId]);
        json($stmt->fetch(PDO::FETCH_ASSOC), 'Đặt hàng thành công', 201);
    }
    elseif ($method === 'PUT' && $id) {
        requireAdmin();
        
        $stmt = $db->prepare("SELECT * FROM don_hang WHERE MaDonHang = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) error('Đơn hàng không tồn tại', 404);
        
        $data = input();
        if (!empty($data['TrangThai'])) {
            $db->prepare("UPDATE don_hang SET TrangThai = ? WHERE MaDonHang = ?")->execute([$data['TrangThai'], $id]);
        }
        
        $stmt = $db->prepare("SELECT * FROM don_hang WHERE MaDonHang = ?");
        $stmt->execute([$id]);
        json($stmt->fetch(PDO::FETCH_ASSOC), 'Cập nhật đơn hàng thành công');
    }
    break;

// ==================== THUỐC ====================
case 'thuoc':
    if ($method === 'GET' && !$id) {
        // GET /thuoc - Danh sách
        list($pg, $lm, $of) = page();
        $search = $_GET['search'] ?? '';
        $nhom = $_GET['nhom'] ?? '';
        
        $where = "WHERE t.IsActive = 1";
        $params = [];
        if ($search) { $where .= " AND t.TenThuoc LIKE ?"; $params[] = "%$search%"; }
        if ($nhom) { $where .= " AND t.MaNhomThuoc = ?"; $params[] = $nhom; }
        
        $total = $db->prepare("SELECT COUNT(*) FROM thuoc t $where");
        $total->execute($params);
        
        $sql = "SELECT t.*, nt.TenNhomThuoc, th.TenThuongHieu FROM thuoc t
                LEFT JOIN nhom_thuoc nt ON t.MaNhomThuoc = nt.MaNhomThuoc
                LEFT JOIN thuong_hieu th ON t.MaThuongHieu = th.MaThuongHieu
                $where ORDER BY t.MaThuoc DESC LIMIT $lm OFFSET $of";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        paginate($stmt->fetchAll(PDO::FETCH_ASSOC), $total->fetchColumn(), $pg, $lm);
    }
    elseif ($method === 'GET' && $id) {
        // GET /thuoc/1 - Chi tiết
        $stmt = $db->prepare("SELECT t.*, nt.TenNhomThuoc, th.TenThuongHieu FROM thuoc t
                LEFT JOIN nhom_thuoc nt ON t.MaNhomThuoc = nt.MaNhomThuoc
                LEFT JOIN thuong_hieu th ON t.MaThuongHieu = th.MaThuongHieu
                WHERE t.MaThuoc = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$item) error('Thuốc không tồn tại', 404);
        json($item);
    }
    elseif ($method === 'POST') {
        // POST /thuoc - Tạo mới (Admin only)
        requireAdmin();
        $data = input();
        if (empty($data['TenThuoc'])) error('TenThuoc là bắt buộc', 422);
        if (empty($data['GiaBan'])) error('GiaBan là bắt buộc', 422);
        
        $stmt = $db->prepare("INSERT INTO thuoc (TenThuoc, MoTa, DonViTinh, GiaBan, GiaGoc, PhanTramGiam, NgayBatDauKM, NgayKetThucKM, SoLuongTon, MaNhomThuoc, MaThuongHieu, MaNuocSX, HinhAnh, IsActive, IsNew, IsHot, NgayTao) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?, ?, NOW())");
        $stmt->execute([
            $data['TenThuoc'], $data['MoTa'] ?? '', $data['DonViTinh'] ?? 'Hộp',
            $data['GiaBan'], $data['GiaGoc'] ?? null, $data['PhanTramGiam'] ?? 0,
            !empty($data['NgayBatDauKM']) ? $data['NgayBatDauKM'] : null,
            !empty($data['NgayKetThucKM']) ? $data['NgayKetThucKM'] : null,
            $data['SoLuongTon'] ?? 0, $data['MaNhomThuoc'] ?? null, $data['MaThuongHieu'] ?? null,
            $data['MaNuocSX'] ?? null, $data['HinhAnh'] ?? null, $data['IsNew'] ?? 0, $data['IsHot'] ?? 0
        ]);
        $newId = $db->lastInsertId();
        
        $stmt = $db->prepare("SELECT * FROM thuoc WHERE MaThuoc = ?");
        $stmt->execute([$newId]);
        json($stmt->fetch(PDO::FETCH_ASSOC), 'Tạo thuốc thành công', 201);
    }
    elseif ($method === 'PUT' && $id) {
        // PUT /thuoc/1 - Cập nhật (Admin only)
        requireAdmin();
        $stmt = $db->prepare("SELECT * FROM thuoc WHERE MaThuoc = ?");
        $stmt->execute([$id]);
        $old = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$old) error('Thuốc không tồn tại', 404);
        
        $data = input();
        $stmt = $db->prepare("UPDATE thuoc SET 
            TenThuoc = ?, MoTa = ?, DonViTinh = ?, GiaBan = ?, GiaGoc = ?, PhanTramGiam = ?,
            NgayBatDauKM = ?, NgayKetThucKM = ?,
            SoLuongTon = ?, MaNhomThuoc = ?, MaThuongHieu = ?, MaNuocSX = ?, HinhAnh = ?,
            IsActive = ?, IsNew = ?, IsHot = ?
            WHERE MaThuoc = ?");
        $stmt->execute([
            $data['TenThuoc'] ?? $old['TenThuoc'], $data['MoTa'] ?? $old['MoTa'],
            $data['DonViTinh'] ?? $old['DonViTinh'], $data['GiaBan'] ?? $old['GiaBan'],
            $data['GiaGoc'] ?? $old['GiaGoc'], $data['PhanTramGiam'] ?? $old['PhanTramGiam'],
            array_key_exists('NgayBatDauKM', $data) ? ($data['NgayBatDauKM'] ?: null) : $old['NgayBatDauKM'],
            array_key_exists('NgayKetThucKM', $data) ? ($data['NgayKetThucKM'] ?: null) : $old['NgayKetThucKM'],
            $data['SoLuongTon'] ?? $old['SoLuongTon'], $data['MaNhomThuoc'] ?? $old['MaNhomThuoc'],
            $data['MaThuongHieu'] ?? $old['MaThuongHieu'], $data['MaNuocSX'] ?? $old['MaNuocSX'],
            $data['HinhAnh'] ?? $old['HinhAnh'], $data['IsActive'] ?? $old['IsActive'],
            $data['IsNew'] ?? $old['IsNew'], $data['IsHot'] ?? $old['IsHot'], $id
        ]);
        
        $stmt = $db->prepare("SELECT * FROM thuoc WHERE MaThuoc = ?");
        $stmt->execute([$id]);
        json($stmt->fetch(PDO::FETCH_ASSOC), 'Cập nhật thành công');
    }
    elseif ($method === 'DELETE' && $id) {
        // DELETE /thuoc/1 - Xóa (Admin only, soft delete)
        requireAdmin();
        $stmt = $db->prepare("SELECT * FROM thuoc WHERE MaThuoc = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) error('Thuốc không tồn tại', 404);
        
        $db->prepare("UPDATE thuoc SET IsActive = 0 WHERE MaThuoc = ?")->execute([$id]);
        json(null, 'Xóa thuốc thành công');
    }
    break;

// ==================== NHÓM THUỐC ====================
case 'nhom-thuoc':
    if ($method === 'GET' && !$id) {
        $stmt = $db->query("SELECT * FROM nhom_thuoc ORDER BY TenNhomThuoc");
        json($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
    elseif ($method === 'GET' && $id) {
        $stmt = $db->prepare("SELECT * FROM nhom_thuoc WHERE MaNhomThuoc = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$item) error('Nhóm thuốc không tồn tại', 404);
        json($item);
    }
    elseif ($method === 'POST') {
        $data = input();
        if (empty($data['TenNhomThuoc'])) error('TenNhomThuoc là bắt buộc', 422);
        
        $stmt = $db->prepare("INSERT INTO nhom_thuoc (TenNhomThuoc, MoTa, MaDanhMucCha) VALUES (?, ?, ?)");
        $stmt->execute([$data['TenNhomThuoc'], $data['MoTa'] ?? '', $data['MaDanhMucCha'] ?? null]);
        
        $stmt = $db->prepare("SELECT * FROM nhom_thuoc WHERE MaNhomThuoc = ?");
        $stmt->execute([$db->lastInsertId()]);
        json($stmt->fetch(PDO::FETCH_ASSOC), 'Tạo nhóm thuốc thành công', 201);
    }
    elseif ($method === 'PUT' && $id) {
        $stmt = $db->prepare("SELECT * FROM nhom_thuoc WHERE MaNhomThuoc = ?");
        $stmt->execute([$id]);
        $old = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$old) error('Nhóm thuốc không tồn tại', 404);
        
        $data = input();
        $db->prepare("UPDATE nhom_thuoc SET TenNhomThuoc = ?, MoTa = ?, MaDanhMucCha = ? WHERE MaNhomThuoc = ?")
           ->execute([$data['TenNhomThuoc'] ?? $old['TenNhomThuoc'], $data['MoTa'] ?? $old['MoTa'], $data['MaDanhMucCha'] ?? $old['MaDanhMucCha'], $id]);
        
        $stmt = $db->prepare("SELECT * FROM nhom_thuoc WHERE MaNhomThuoc = ?");
        $stmt->execute([$id]);
        json($stmt->fetch(PDO::FETCH_ASSOC), 'Cập nhật thành công');
    }
    elseif ($method === 'DELETE' && $id) {
        $stmt = $db->prepare("SELECT * FROM nhom_thuoc WHERE MaNhomThuoc = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) error('Nhóm thuốc không tồn tại', 404);
        
        // Kiểm tra có thuốc không
        $check = $db->prepare("SELECT COUNT(*) FROM thuoc WHERE MaNhomThuoc = ?");
        $check->execute([$id]);
        if ($check->fetchColumn() > 0) error('Không thể xóa! Nhóm có sản phẩm', 400);
        
        $db->prepare("DELETE FROM nhom_thuoc WHERE MaNhomThuoc = ?")->execute([$id]);
        json(null, 'Xóa nhóm thuốc thành công');
    }
    break;

// ==================== THƯƠNG HIỆU ====================
case 'thuong-hieu':
    if ($method === 'GET' && !$id) {
        json($db->query("SELECT * FROM thuong_hieu ORDER BY TenThuongHieu")->fetchAll(PDO::FETCH_ASSOC));
    }
    elseif ($method === 'GET' && $id) {
        $stmt = $db->prepare("SELECT * FROM thuong_hieu WHERE MaThuongHieu = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$item) error('Thương hiệu không tồn tại', 404);
        json($item);
    }
    elseif ($method === 'POST') {
        $data = input();
        if (empty($data['TenThuongHieu'])) error('TenThuongHieu là bắt buộc', 422);
        
        $db->prepare("INSERT INTO thuong_hieu (TenThuongHieu, QuocGia, DiaChi, HinhAnh) VALUES (?, ?, ?, ?)")
           ->execute([$data['TenThuongHieu'], $data['QuocGia'] ?? '', $data['DiaChi'] ?? '', $data['HinhAnh'] ?? null]);
        
        $stmt = $db->prepare("SELECT * FROM thuong_hieu WHERE MaThuongHieu = ?");
        $stmt->execute([$db->lastInsertId()]);
        json($stmt->fetch(PDO::FETCH_ASSOC), 'Tạo thương hiệu thành công', 201);
    }
    elseif ($method === 'PUT' && $id) {
        $stmt = $db->prepare("SELECT * FROM thuong_hieu WHERE MaThuongHieu = ?");
        $stmt->execute([$id]);
        $old = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$old) error('Thương hiệu không tồn tại', 404);
        
        $data = input();
        $db->prepare("UPDATE thuong_hieu SET TenThuongHieu = ?, QuocGia = ?, DiaChi = ?, HinhAnh = ? WHERE MaThuongHieu = ?")
           ->execute([$data['TenThuongHieu'] ?? $old['TenThuongHieu'], $data['QuocGia'] ?? $old['QuocGia'], $data['DiaChi'] ?? $old['DiaChi'], $data['HinhAnh'] ?? $old['HinhAnh'], $id]);
        
        $stmt = $db->prepare("SELECT * FROM thuong_hieu WHERE MaThuongHieu = ?");
        $stmt->execute([$id]);
        json($stmt->fetch(PDO::FETCH_ASSOC), 'Cập nhật thành công');
    }
    elseif ($method === 'DELETE' && $id) {
        $stmt = $db->prepare("SELECT * FROM thuong_hieu WHERE MaThuongHieu = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) error('Thương hiệu không tồn tại', 404);
        
        $check = $db->prepare("SELECT COUNT(*) FROM thuoc WHERE MaThuongHieu = ?");
        $check->execute([$id]);
        if ($check->fetchColumn() > 0) error('Không thể xóa! Thương hiệu có sản phẩm', 400);
        
        $db->prepare("DELETE FROM thuong_hieu WHERE MaThuongHieu = ?")->execute([$id]);
        json(null, 'Xóa thương hiệu thành công');
    }
    break;

// ==================== NƯỚC SẢN XUẤT ====================
case 'nuoc-san-xuat':
    if ($method === 'GET' && !$id) {
        json($db->query("SELECT * FROM nuoc_san_xuat ORDER BY TenNuocSX")->fetchAll(PDO::FETCH_ASSOC));
    }
    elseif ($method === 'GET' && $id) {
        $stmt = $db->prepare("SELECT * FROM nuoc_san_xuat WHERE MaNuocSX = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$item) error('Nước SX không tồn tại', 404);
        json($item);
    }
    elseif ($method === 'POST') {
        $data = input();
        if (empty($data['TenNuocSX'])) error('TenNuocSX là bắt buộc', 422);
        
        $db->prepare("INSERT INTO nuoc_san_xuat (TenNuocSX) VALUES (?)")->execute([$data['TenNuocSX']]);
        $stmt = $db->prepare("SELECT * FROM nuoc_san_xuat WHERE MaNuocSX = ?");
        $stmt->execute([$db->lastInsertId()]);
        json($stmt->fetch(PDO::FETCH_ASSOC), 'Tạo thành công', 201);
    }
    elseif ($method === 'PUT' && $id) {
        $stmt = $db->prepare("SELECT * FROM nuoc_san_xuat WHERE MaNuocSX = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) error('Nước SX không tồn tại', 404);
        
        $data = input();
        $db->prepare("UPDATE nuoc_san_xuat SET TenNuocSX = ? WHERE MaNuocSX = ?")->execute([$data['TenNuocSX'], $id]);
        
        $stmt = $db->prepare("SELECT * FROM nuoc_san_xuat WHERE MaNuocSX = ?");
        $stmt->execute([$id]);
        json($stmt->fetch(PDO::FETCH_ASSOC), 'Cập nhật thành công');
    }
    elseif ($method === 'DELETE' && $id) {
        $stmt = $db->prepare("SELECT * FROM nuoc_san_xuat WHERE MaNuocSX = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) error('Nước SX không tồn tại', 404);
        
        $db->prepare("DELETE FROM nuoc_san_xuat WHERE MaNuocSX = ?")->execute([$id]);
        json(null, 'Xóa thành công');
    }
    break;

// ==================== HOME ====================
case 'home':
case '':
    $thuocMoi = $db->query("SELECT * FROM thuoc WHERE IsActive = 1 AND IsNew = 1 ORDER BY NgayTao DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
    
    // Sản phẩm khuyến mãi: phải có PhanTramGiam > 0 VÀ ngày hiện tại nằm trong khoảng NgayBatDauKM - NgayKetThucKM
    $now = date('Y-m-d H:i:s');
    $stmtKM = $db->prepare("SELECT * FROM thuoc WHERE IsActive = 1 AND PhanTramGiam > 0 
        AND (NgayBatDauKM IS NULL OR NgayBatDauKM <= ?) 
        AND (NgayKetThucKM IS NULL OR NgayKetThucKM >= ?) 
        ORDER BY PhanTramGiam DESC LIMIT 10");
    $stmtKM->execute([$now, $now]);
    $thuocKM = $stmtKM->fetchAll(PDO::FETCH_ASSOC);
    
    $nhomThuoc = $db->query("SELECT * FROM nhom_thuoc ORDER BY TenNhomThuoc")->fetchAll(PDO::FETCH_ASSOC);
    json(['san_pham_moi' => $thuocMoi, 'san_pham_khuyen_mai' => $thuocKM, 'nhom_thuoc' => $nhomThuoc], 'Trang chủ API');
    break;

// ==================== THÀNH PHẦN ====================
case 'thanh-phan':
    if ($method === 'GET' && !$id) {
        json($db->query("SELECT * FROM thanh_phan ORDER BY TenThanhPhan")->fetchAll(PDO::FETCH_ASSOC));
    }
    elseif ($method === 'GET' && $id) {
        $stmt = $db->prepare("SELECT * FROM thanh_phan WHERE MaThanhPhan = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$item) error('Thành phần không tồn tại', 404);
        json($item);
    }
    elseif ($method === 'POST') {
        requireAdmin();
        $data = input();
        if (empty($data['TenThanhPhan'])) error('TenThanhPhan là bắt buộc', 422);
        
        $db->prepare("INSERT INTO thanh_phan (TenThanhPhan, MoTa) VALUES (?, ?)")
           ->execute([$data['TenThanhPhan'], $data['MoTa'] ?? '']);
        $stmt = $db->prepare("SELECT * FROM thanh_phan WHERE MaThanhPhan = ?");
        $stmt->execute([$db->lastInsertId()]);
        json($stmt->fetch(PDO::FETCH_ASSOC), 'Tạo thành công', 201);
    }
    elseif ($method === 'PUT' && $id) {
        requireAdmin();
        $stmt = $db->prepare("SELECT * FROM thanh_phan WHERE MaThanhPhan = ?");
        $stmt->execute([$id]);
        $old = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$old) error('Thành phần không tồn tại', 404);
        
        $data = input();
        $db->prepare("UPDATE thanh_phan SET TenThanhPhan = ?, MoTa = ? WHERE MaThanhPhan = ?")
           ->execute([$data['TenThanhPhan'] ?? $old['TenThanhPhan'], $data['MoTa'] ?? $old['MoTa'], $id]);
        
        $stmt = $db->prepare("SELECT * FROM thanh_phan WHERE MaThanhPhan = ?");
        $stmt->execute([$id]);
        json($stmt->fetch(PDO::FETCH_ASSOC), 'Cập nhật thành công');
    }
    elseif ($method === 'DELETE' && $id) {
        requireAdmin();
        $stmt = $db->prepare("SELECT * FROM thanh_phan WHERE MaThanhPhan = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) error('Thành phần không tồn tại', 404);
        
        $db->prepare("DELETE FROM thanh_phan WHERE MaThanhPhan = ?")->execute([$id]);
        json(null, 'Xóa thành công');
    }
    break;

// ==================== TÁC DỤNG PHỤ ====================
case 'tac-dung-phu':
    if ($method === 'GET' && !$id) {
        json($db->query("SELECT * FROM tac_dung_phu ORDER BY TenTacDungPhu")->fetchAll(PDO::FETCH_ASSOC));
    }
    elseif ($method === 'GET' && $id) {
        $stmt = $db->prepare("SELECT * FROM tac_dung_phu WHERE MaTacDungPhu = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$item) error('Tác dụng phụ không tồn tại', 404);
        json($item);
    }
    elseif ($method === 'POST') {
        requireAdmin();
        $data = input();
        if (empty($data['TenTacDungPhu'])) error('TenTacDungPhu là bắt buộc', 422);
        
        $db->prepare("INSERT INTO tac_dung_phu (TenTacDungPhu, MoTa) VALUES (?, ?)")
           ->execute([$data['TenTacDungPhu'], $data['MoTa'] ?? '']);
        $stmt = $db->prepare("SELECT * FROM tac_dung_phu WHERE MaTacDungPhu = ?");
        $stmt->execute([$db->lastInsertId()]);
        json($stmt->fetch(PDO::FETCH_ASSOC), 'Tạo thành công', 201);
    }
    elseif ($method === 'PUT' && $id) {
        requireAdmin();
        $stmt = $db->prepare("SELECT * FROM tac_dung_phu WHERE MaTacDungPhu = ?");
        $stmt->execute([$id]);
        $old = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$old) error('Tác dụng phụ không tồn tại', 404);
        
        $data = input();
        $db->prepare("UPDATE tac_dung_phu SET TenTacDungPhu = ?, MoTa = ? WHERE MaTacDungPhu = ?")
           ->execute([$data['TenTacDungPhu'] ?? $old['TenTacDungPhu'], $data['MoTa'] ?? $old['MoTa'], $id]);
        
        $stmt = $db->prepare("SELECT * FROM tac_dung_phu WHERE MaTacDungPhu = ?");
        $stmt->execute([$id]);
        json($stmt->fetch(PDO::FETCH_ASSOC), 'Cập nhật thành công');
    }
    elseif ($method === 'DELETE' && $id) {
        requireAdmin();
        $stmt = $db->prepare("SELECT * FROM tac_dung_phu WHERE MaTacDungPhu = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) error('Tác dụng phụ không tồn tại', 404);
        
        $db->prepare("DELETE FROM tac_dung_phu WHERE MaTacDungPhu = ?")->execute([$id]);
        json(null, 'Xóa thành công');
    }
    break;

// ==================== ĐỐI TƯỢNG SỬ DỤNG ====================
case 'doi-tuong':
    if ($method === 'GET' && !$id) {
        json($db->query("SELECT * FROM doi_tuong_su_dung ORDER BY TenDoiTuong")->fetchAll(PDO::FETCH_ASSOC));
    }
    elseif ($method === 'GET' && $id) {
        $stmt = $db->prepare("SELECT * FROM doi_tuong_su_dung WHERE MaDoiTuong = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$item) error('Đối tượng không tồn tại', 404);
        json($item);
    }
    elseif ($method === 'POST') {
        requireAdmin();
        $data = input();
        if (empty($data['TenDoiTuong'])) error('TenDoiTuong là bắt buộc', 422);
        
        $db->prepare("INSERT INTO doi_tuong_su_dung (TenDoiTuong, MoTa) VALUES (?, ?)")
           ->execute([$data['TenDoiTuong'], $data['MoTa'] ?? '']);
        $stmt = $db->prepare("SELECT * FROM doi_tuong_su_dung WHERE MaDoiTuong = ?");
        $stmt->execute([$db->lastInsertId()]);
        json($stmt->fetch(PDO::FETCH_ASSOC), 'Tạo thành công', 201);
    }
    elseif ($method === 'PUT' && $id) {
        requireAdmin();
        $stmt = $db->prepare("SELECT * FROM doi_tuong_su_dung WHERE MaDoiTuong = ?");
        $stmt->execute([$id]);
        $old = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$old) error('Đối tượng không tồn tại', 404);
        
        $data = input();
        $db->prepare("UPDATE doi_tuong_su_dung SET TenDoiTuong = ?, MoTa = ? WHERE MaDoiTuong = ?")
           ->execute([$data['TenDoiTuong'] ?? $old['TenDoiTuong'], $data['MoTa'] ?? $old['MoTa'], $id]);
        
        $stmt = $db->prepare("SELECT * FROM doi_tuong_su_dung WHERE MaDoiTuong = ?");
        $stmt->execute([$id]);
        json($stmt->fetch(PDO::FETCH_ASSOC), 'Cập nhật thành công');
    }
    elseif ($method === 'DELETE' && $id) {
        requireAdmin();
        $stmt = $db->prepare("SELECT * FROM doi_tuong_su_dung WHERE MaDoiTuong = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) error('Đối tượng không tồn tại', 404);
        
        $db->prepare("DELETE FROM doi_tuong_su_dung WHERE MaDoiTuong = ?")->execute([$id]);
        json(null, 'Xóa thành công');
    }
    break;

// ==================== BÀI VIẾT ====================
case 'bai-viet':
    if ($method === 'GET' && !$id) {
        list($pg, $lm, $of) = page();
        $total = $db->query("SELECT COUNT(*) FROM baiviet")->fetchColumn();
        $stmt = $db->prepare("SELECT * FROM baiviet ORDER BY NgayTao DESC LIMIT $lm OFFSET $of");
        $stmt->execute();
        paginate($stmt->fetchAll(PDO::FETCH_ASSOC), $total, $pg, $lm);
    }
    elseif ($method === 'GET' && $id) {
        $stmt = $db->prepare("SELECT * FROM baiviet WHERE MaBaiViet = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$item) error('Bài viết không tồn tại', 404);
        json($item);
    }
    elseif ($method === 'POST') {
        requireAdmin();
        $data = input();
        if (empty($data['TieuDe'])) error('TieuDe là bắt buộc', 422);
        
        $db->prepare("INSERT INTO baiviet (TieuDe, NoiDung, HinhAnh, TacGia, NgayTao) VALUES (?, ?, ?, ?, NOW())")
           ->execute([$data['TieuDe'], $data['NoiDung'] ?? '', $data['HinhAnh'] ?? null, $data['TacGia'] ?? '']);
        $stmt = $db->prepare("SELECT * FROM baiviet WHERE MaBaiViet = ?");
        $stmt->execute([$db->lastInsertId()]);
        json($stmt->fetch(PDO::FETCH_ASSOC), 'Tạo bài viết thành công', 201);
    }
    elseif ($method === 'PUT' && $id) {
        requireAdmin();
        $stmt = $db->prepare("SELECT * FROM baiviet WHERE MaBaiViet = ?");
        $stmt->execute([$id]);
        $old = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$old) error('Bài viết không tồn tại', 404);
        
        $data = input();
        $db->prepare("UPDATE baiviet SET TieuDe = ?, NoiDung = ?, HinhAnh = ?, TacGia = ? WHERE MaBaiViet = ?")
           ->execute([
               $data['TieuDe'] ?? $old['TieuDe'], 
               $data['NoiDung'] ?? $old['NoiDung'], 
               $data['HinhAnh'] ?? $old['HinhAnh'], 
               $data['TacGia'] ?? $old['TacGia'], 
               $id
           ]);
        
        $stmt = $db->prepare("SELECT * FROM baiviet WHERE MaBaiViet = ?");
        $stmt->execute([$id]);
        json($stmt->fetch(PDO::FETCH_ASSOC), 'Cập nhật thành công');
    }
    elseif ($method === 'DELETE' && $id) {
        requireAdmin();
        $stmt = $db->prepare("SELECT * FROM baiviet WHERE MaBaiViet = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) error('Bài viết không tồn tại', 404);
        
        $db->prepare("DELETE FROM baiviet WHERE MaBaiViet = ?")->execute([$id]);
        json(null, 'Xóa bài viết thành công');
    }
    break;

default:
    error('Endpoint không tồn tại', 404);
}

error('Method không được hỗ trợ', 405);
