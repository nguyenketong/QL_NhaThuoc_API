<?php
/**
 * Base Controller - MVC Framework + RESTful API
 */
class Controller
{
    protected $db;
    protected $data = [];
    protected $isApi = false;
    protected $currentUser = null;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        $this->data['title'] = STORE_NAME;
        
        // Detect API request
        $this->isApi = $this->detectApiRequest();
    }

    /**
     * Detect if request is API call
     */
    protected function detectApiRequest()
    {
        // Check Accept header
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        if (strpos($accept, 'application/json') !== false) {
            return true;
        }
        
        // Check Content-Type
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (strpos($contentType, 'application/json') !== false) {
            return true;
        }
        
        // Check query param
        if (isset($_GET['format']) && $_GET['format'] === 'json') {
            return true;
        }
        
        return false;
    }

    protected function model($model)
    {
        $modelFile = ROOT . '/app/models/' . $model . '.php';
        if (file_exists($modelFile)) {
            require_once $modelFile;
            return new $model($this->db);
        }
        return null;
    }

    protected function view($view, $data = [])
    {
        $data = array_merge($this->data, $data);
        extract($data);
        
        ob_start();
        $viewFile = ROOT . '/app/views/' . $view . '.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        }
        $content = ob_get_clean();
        
        require ROOT . '/app/views/layouts/main.php';
    }

    protected function redirect($url)
    {
        header('Location: ' . BASE_URL . '/' . $url);
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

    // ==================== AUTH METHODS ====================

    protected function getUserId()
    {
        return $_SESSION['user_id'] ?? ($_COOKIE['UserId'] ?? null);
    }

    protected function isLoggedIn()
    {
        return $this->getUserId() !== null;
    }

    protected function requireLogin()
    {
        if (!$this->isLoggedIn()) {
            if ($this->isApi) {
                $this->jsonError('Unauthorized - Vui lòng đăng nhập trước', 401);
            }
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            $this->redirect('user/phoneLogin');
        }
    }

    /**
     * Yêu cầu quyền Admin
     */
    protected function requireAdmin()
    {
        $this->requireLogin();
        
        $role = $_SESSION['user_role'] ?? 'User';
        if ($role !== 'Admin') {
            if ($this->isApi) {
                $this->jsonError('Forbidden - Chỉ Admin mới có quyền thực hiện', 403);
            }
            $this->setFlash('error', 'Bạn không có quyền truy cập!');
            $this->redirect('');
        }
    }

    protected function setFlash($type, $message)
    {
        $_SESSION['flash'][$type] = $message;
    }
}
