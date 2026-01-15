<?php
/**
 * Base Admin Controller - MVC + RESTful API
 */
class AdminController
{
    protected $db;
    protected $data = [];
    protected $isApi = false;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        $this->data['title'] = 'Admin Panel';
        $this->data['adminName'] = $_SESSION['admin_name'] ?? 'Admin';
        $this->data['adminPhone'] = $_SESSION['admin_phone'] ?? '';
        
        // Detect API request
        $this->isApi = $this->detectApiRequest();
        
        // Đếm đơn hàng chờ xử lý
        $this->data['soDonChoXuLy'] = $this->countPendingOrders();
        
        // Kiểm tra yêu cầu đổi mật khẩu (trừ trang đổi mật khẩu và logout)
        if (!$this->isApi) {
            $this->checkPasswordChangeRequired();
        }
    }

    /**
     * Detect if request is API call
     */
    protected function detectApiRequest()
    {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        if (strpos($accept, 'application/json') !== false) {
            return true;
        }
        
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (strpos($contentType, 'application/json') !== false) {
            return true;
        }
        
        if (isset($_GET['format']) && $_GET['format'] === 'json') {
            return true;
        }
        
        return false;
    }

    protected function checkPasswordChangeRequired()
    {
        $controller = $_GET['controller'] ?? '';
        $action = $_GET['action'] ?? '';
        
        if ($controller === 'auth') {
            return;
        }
        
        if (isset($_SESSION['require_password_change']) && $_SESSION['require_password_change'] === true) {
            header('Location: ' . BASE_URL . '/admin/?controller=auth&action=changePassword');
            exit;
        }
    }

    protected function countPendingOrders()
    {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) FROM don_hang WHERE TrangThai = 'Cho xu ly'");
            return $stmt->fetchColumn();
        } catch (Exception $e) {
            return 0;
        }
    }

    protected function view($view, $data = [])
    {
        $data = array_merge($this->data, $data);
        extract($data);
        
        ob_start();
        $viewFile = ADMIN_ROOT . '/views/' . $view . '.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        }
        $content = ob_get_clean();
        
        require ADMIN_ROOT . '/views/layouts/admin-layout.php';
    }

    protected function viewWithoutLayout($view, $data = [])
    {
        $data = array_merge($this->data, $data);
        extract($data);
        require ADMIN_ROOT . '/views/' . $view . '.php';
    }

    protected function redirect($url)
    {
        header('Location: ' . BASE_URL . '/admin/' . $url);
        exit;
    }

    protected function redirectFull($url)
    {
        header('Location: ' . $url);
        exit;
    }

    // ==================== API METHODS ====================

    /**
     * Trả về JSON response thành công
     */
    protected function json($data, $message = 'Success', $code = 200)
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        echo json_encode([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('c')
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Trả về JSON response lỗi
     */
    protected function jsonError($message = 'Error', $code = 400, $errors = null)
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        $response = [
            'success' => false,
            'message' => $message,
            'error_code' => $code,
            'timestamp' => date('c')
        ];
        if ($errors) {
            $response['errors'] = $errors;
        }
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Trả về JSON với pagination
     */
    protected function jsonPaginate($data, $total, $page, $limit)
    {
        $totalPages = ceil($total / $limit);
        http_response_code(200);
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        echo json_encode([
            'success' => true,
            'data' => $data,
            'pagination' => [
                'total' => (int)$total,
                'per_page' => (int)$limit,
                'current_page' => (int)$page,
                'total_pages' => (int)$totalPages,
                'has_next' => $page < $totalPages,
                'has_prev' => $page > 1
            ],
            'timestamp' => date('c')
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Lấy JSON input từ request body
     */
    protected function getJsonInput()
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        
        if (strpos($contentType, 'application/json') !== false) {
            $input = json_decode(file_get_contents('php://input'), true);
            return $input ?: [];
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'PUT' || $_SERVER['REQUEST_METHOD'] === 'DELETE') {
            parse_str(file_get_contents('php://input'), $input);
            return $input;
        }
        
        return $_POST;
    }

    /**
     * Lấy pagination params
     */
    protected function getPagination()
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = min(100, max(1, (int)($_GET['limit'] ?? 10)));
        $offset = ($page - 1) * $limit;
        return [$page, $limit, $offset];
    }

    /**
     * Validate input
     */
    protected function validate($data, $rules)
    {
        $errors = [];
        foreach ($rules as $field => $rule) {
            $ruleList = explode('|', $rule);
            foreach ($ruleList as $r) {
                if ($r === 'required' && empty($data[$field])) {
                    $errors[$field][] = "$field is required";
                }
                if (strpos($r, 'min:') === 0 && isset($data[$field])) {
                    $min = (int)substr($r, 4);
                    if (strlen($data[$field]) < $min) {
                        $errors[$field][] = "$field must be at least $min characters";
                    }
                }
                if (strpos($r, 'max:') === 0 && isset($data[$field])) {
                    $max = (int)substr($r, 4);
                    if (strlen($data[$field]) > $max) {
                        $errors[$field][] = "$field must not exceed $max characters";
                    }
                }
                if ($r === 'numeric' && isset($data[$field]) && !is_numeric($data[$field])) {
                    $errors[$field][] = "$field must be numeric";
                }
            }
        }
        return $errors;
    }

    // ==================== HELPER METHODS ====================

    protected function setFlash($type, $message)
    {
        $_SESSION['flash'][$type] = $message;
    }

    protected function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function isPut()
    {
        return $_SERVER['REQUEST_METHOD'] === 'PUT';
    }

    protected function isDelete()
    {
        return $_SERVER['REQUEST_METHOD'] === 'DELETE';
    }

    /**
     * Upload hình ảnh từ file
     */
    protected function uploadImage($file, $folder = 'images')
    {
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            return null;
        }

        $uploadDir = ROOT . '/assets/' . $folder . '/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' . $ext;
        $filePath = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            return BASE_URL . '/assets/' . $folder . '/' . $fileName;
        }
        return null;
    }

    /**
     * Xử lý hình ảnh - hỗ trợ cả upload file và paste URL
     */
    protected function processImage($file, $imageUrl = null, $currentImage = null, $folder = 'images')
    {
        if (isset($file['tmp_name']) && !empty($file['tmp_name'])) {
            return $this->uploadImage($file, $folder);
        }
        
        if (!empty($imageUrl) && filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            return $imageUrl;
        }
        
        return $currentImage;
    }
}
