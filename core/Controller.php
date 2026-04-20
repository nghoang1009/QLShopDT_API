<?php
/**
 * Controller - Base Controller class
 * Tất cả controller đều kế thừa từ đây
 */

require_once BASE_PATH . '/core/CSRF.php';

abstract class Controller {
    
    protected $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Load view
     * @param string $view - Đường dẫn view (ví dụ: sanpham/list)
     * @param array $data - Dữ liệu truyền vào view
     */
    protected function view($view, $data = []) {
        // Extract data để dùng trong view
        extract($data);
        
        $viewFile = BASE_PATH . '/views/' . $view . '.php';
        
        if (!file_exists($viewFile)) {
            die("View '$view' không tồn tại");
        }
        
        include $viewFile;
    }
    
    /**
     * Load model
     * @param string $model - Tên model
     * @return object
     */
    protected function model($model) {
        $modelFile = BASE_PATH . '/model/' . $model . '.php';
        
        if (!file_exists($modelFile)) {
            die("Model '$model' không tồn tại");
        }
        
        require_once $modelFile;
        
        return new $model();
    }
    
    /**
     * Redirect đến URL khác
     */
    protected function redirect($url) {
        if (strpos($url, '/') === 0) {
            $url = BASE_URL . $url;
        }
        header("Location: " . $url);
        exit();
    }
    
    /**
     * Trả về JSON response
     */
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit();
    }
    
    /**
     * Kiểm tra user đã đăng nhập chưa
     */
    protected function requireLogin() {
        if (!isset($_SESSION['username'])) {
            if ($this->isApiRequest()) {
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(401);
                echo json_encode(['status' => false, 'message' => 'Chưa đăng nhập'], JSON_UNESCAPED_UNICODE);
                exit();
            }
            $this->redirect('/views/auth/login.php');
        }
    }
    
    /**
     * Kiểm tra quyền
     * @param array|int $allowedRoles - Role được phép truy cập
     */
    protected function requireRole($allowedRoles) {
        $this->requireLogin();
        
        if (!is_array($allowedRoles)) {
            $allowedRoles = [$allowedRoles];
        }
        
        $currentRole = isset($_SESSION['role']) ? (int)$_SESSION['role'] : -1;
        
        if (!in_array($currentRole, $allowedRoles)) {
            if ($this->isApiRequest()) {
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(403);
                echo json_encode(['status' => false, 'message' => 'Không có quyền thực hiện thao tác này'], JSON_UNESCAPED_UNICODE);
                exit();
            }
            $this->error403();
        }
    }
    
    /**
     * Kiểm tra có phải API request không (URL bắt đầu bằng /api/)
     */
    protected function isApiRequest() {
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        return strpos($uri, '/api/') !== false;
    }
    
    /**
     * Kiểm tra request method
     */
    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    protected function isGet() {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }
    
    /**
     * Lấy input từ POST/GET với sanitize
     */
    protected function input($key, $default = null) {
        if (isset($_POST[$key])) {
            return is_string($_POST[$key]) ? trim($_POST[$key]) : $_POST[$key];
        }
        if (isset($_GET[$key])) {
            return is_string($_GET[$key]) ? trim($_GET[$key]) : $_GET[$key];
        }
        return $default;
    }
    
    /**
     * Lấy tất cả input POST
     */
    protected function postData() {
        return array_map(function($value) {
            return is_string($value) ? trim($value) : $value;
        }, $_POST);
    }
    
    /**
     * Verify CSRF token
     */
    protected function verifyCsrf() {
        if (!CSRF::verify()) {
            $this->error403('CSRF token không hợp lệ');
        }
    }
    
    /**
     * Hiển thị lỗi 403
     */
    protected function error403($message = 'Bạn không có quyền truy cập') {
        http_response_code(403);
        if (file_exists(BASE_PATH . '/views/errors/403.php')) {
            include BASE_PATH . '/views/errors/403.php';
        } else {
            echo "<h1>403 - Không có quyền</h1><p>$message</p>";
        }
        exit();
    }
    
    /**
     * Hiển thị lỗi 404
     */
    protected function error404($message = 'Không tìm thấy') {
        http_response_code(404);
        if (file_exists(BASE_PATH . '/views/errors/404.php')) {
            include BASE_PATH . '/views/errors/404.php';
        } else {
            echo "<h1>404 - Không tìm thấy</h1><p>$message</p>";
        }
        exit();
    }
    
    /**
     * Set flash message
     */
    protected function setFlash($key, $message) {
        $_SESSION['flash'][$key] = $message;
    }
    
    /**
     * Get flash message
     */
    protected function getFlash($key) {
        if (isset($_SESSION['flash'][$key])) {
            $message = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $message;
        }
        return null;
    }
}
