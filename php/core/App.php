<?php
/**
 * Core App - Router (MVC + RESTful API)
 * Hỗ trợ cả Website (HTML) và API (JSON)
 */
class App
{
    protected $controller = 'HomeController';
    protected $method = 'index';
    protected $params = [];
    protected $isApiRequest = false;

    public function __construct()
    {
        $url = $this->parseUrl();
        
        // Detect API request từ Accept header hoặc ?format=json
        $this->isApiRequest = $this->detectApiRequest();

        // Controller
        if (isset($url[0]) && !empty($url[0])) {
            // Chuyển đổi URL có dấu gạch ngang thành camelCase
            // gio-hang -> gioHang, nhom-thuoc -> nhomThuoc
            $controllerSlug = $this->convertToCamelCase($url[0]);
            $controllerName = ucfirst($controllerSlug) . 'Controller';
            $controllerFile = ROOT . '/app/controllers/' . $controllerName . '.php';
            if (file_exists($controllerFile)) {
                $this->controller = $controllerName;
            }
            unset($url[0]);
        }

        require_once ROOT . '/app/controllers/' . $this->controller . '.php';
        $this->controller = new $this->controller;

        // Method - Map HTTP method cho API request
        if (isset($url[1]) && !empty($url[1])) {
            $methodName = $this->convertToCamelCase($url[1]);
            if (method_exists($this->controller, $methodName)) {
                $this->method = $methodName;
            }
            unset($url[1]);
        } elseif ($this->isApiRequest) {
            // RESTful mapping khi không có method trong URL
            $this->method = $this->mapHttpMethod();
        }

        // Params
        $this->params = $url ? array_values($url) : [];

        call_user_func_array([$this->controller, $this->method], $this->params);
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

    /**
     * Map HTTP method to controller method for RESTful API
     * GET /thuoc -> index(), GET /thuoc/1 -> show(1)
     * POST /thuoc -> store(), PUT /thuoc/1 -> update(1)
     * DELETE /thuoc/1 -> destroy(1)
     */
    protected function mapHttpMethod()
    {
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $hasId = !empty($this->params);
        
        switch ($httpMethod) {
            case 'GET':
                return $hasId ? 'show' : 'index';
            case 'POST':
                return 'store';
            case 'PUT':
            case 'PATCH':
                return 'update';
            case 'DELETE':
                return 'destroy';
            default:
                return 'index';
        }
    }

    /**
     * Chuyển đổi slug có dấu gạch ngang thành camelCase
     * gio-hang -> gioHang
     * danh-sach -> danhSach
     */
    protected function convertToCamelCase($slug)
    {
        $parts = explode('-', $slug);
        $result = lcfirst($parts[0]);
        for ($i = 1; $i < count($parts); $i++) {
            $result .= ucfirst($parts[$i]);
        }
        return $result;
    }

    protected function parseUrl()
    {
        // Hỗ trợ cả 2 kiểu URL:
        // 1. Clean URL: /gioHang hoặc /gio-hang (qua .htaccess)
        // 2. Query string: index.php?url=gioHang
        
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        
        // Fallback: parse từ REQUEST_URI nếu không có $_GET['url']
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        $basePath = '/Ql_NhaThuoc_API/php/';
        
        if (strpos($uri, $basePath) !== false) {
            $path = substr($uri, strpos($uri, $basePath) + strlen($basePath));
            $path = strtok($path, '?'); // Bỏ query string
            if (!empty($path) && $path !== 'index.php') {
                return explode('/', filter_var(rtrim($path, '/'), FILTER_SANITIZE_URL));
            }
        }
        
        return [];
    }
}
