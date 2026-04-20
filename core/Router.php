<?php
/**
 * Router - Điều hướng URL đến Controller tương ứng
 */

class Router {
    private $routes = [];
    private $defaultController = 'HomeController';
    private $defaultAction = 'index';
    
    /**
     * Đăng ký route
     * @param string $method - GET, POST, PUT, DELETE
     * @param string $pattern - URL pattern (ví dụ: /sanpham/detail/{id})
     * @param string $handler - Controller@action
     */
    public function add($method, $pattern, $handler) {
        $this->routes[] = [
            'method' => strtoupper($method),
            'pattern' => $pattern,
            'handler' => $handler
        ];
    }
    
    public function get($pattern, $handler) {
        $this->add('GET', $pattern, $handler);
    }
    
    public function post($pattern, $handler) {
        $this->add('POST', $pattern, $handler);
    }

    public function put($pattern, $handler) {
        $this->add('PUT', $pattern, $handler);
    }

    public function delete($pattern, $handler) {
        $this->add('DELETE', $pattern, $handler);
    }
    
    /**
     * Phân tích URL và dispatch đến controller
     */
    public function dispatch($uri = null) {
        if ($uri === null) {
            $uri = $_SERVER['REQUEST_URI'];
        }
        
        // Loại bỏ query string
        $uri = parse_url($uri, PHP_URL_PATH);
        
        // Loại bỏ base path
        $basePath = BASE_URL;
        if (strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }
        
        // Chuẩn hóa URI
        $uri = '/' . trim($uri, '/');
        
        $method = $_SERVER['REQUEST_METHOD'];
        
        // Tìm route khớp
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method && $route['method'] !== 'ANY') {
                continue;
            }
            
            $params = $this->matchRoute($route['pattern'], $uri);
            if ($params !== false) {
                return $this->executeHandler($route['handler'], $params);
            }
        }
        
        // Không tìm thấy route - thử URL-based routing mặc định
        return $this->defaultRouting($uri);
    }
    
    /**
     * Kiểm tra URL có khớp với pattern không
     * @return array|false - Mảng params nếu khớp, false nếu không
     */
    private function matchRoute($pattern, $uri) {
        // Chuyển pattern thành regex
        $regex = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';
        
        if (preg_match($regex, $uri, $matches)) {
            // Lọc chỉ lấy named params
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            return $params;
        }
        
        return false;
    }
    
    /**
     * Thực thi handler
     */
    private function executeHandler($handler, $params = []) {
        if (is_callable($handler)) {
            return call_user_func_array($handler, $params);
        }
        
        // Phân tách Controller@action
        list($controller, $action) = explode('@', $handler);
        
        return $this->callController($controller, $action, $params);
    }
    
    /**
     * Routing mặc định dựa trên URL segments
     */
    private function defaultRouting($uri) {
        $segments = explode('/', trim($uri, '/'));
        
        // Lấy controller
        $controllerName = !empty($segments[0]) 
            ? ucfirst($segments[0]) . 'Controller' 
            : $this->defaultController;
            
        // Lấy action
        $action = !empty($segments[1]) ? $segments[1] : $this->defaultAction;
        
        // Lấy params
        $params = array_slice($segments, 2);
        
        return $this->callController($controllerName, $action, $params);
    }
    
    /**
     * Gọi controller và action
     */
    private function callController($controllerName, $action, $params = []) {
        $controllerFile = BASE_PATH . '/controller/' . $controllerName . '.php';
        
        if (!file_exists($controllerFile)) {
            $this->error404("Controller '$controllerName' không tồn tại");
            return false;
        }
        
        require_once $controllerFile;
        
        if (!class_exists($controllerName)) {
            $this->error404("Class '$controllerName' không tồn tại");
            return false;
        }
        
        $controller = new $controllerName();
        
        if (!method_exists($controller, $action)) {
            $this->error404("Action '$action' không tồn tại trong $controllerName");
            return false;
        }
        
        return call_user_func_array([$controller, $action], $params);
    }
    
    /**
     * Hiển thị lỗi 404
     */
    private function error404($message = 'Trang không tồn tại') {
        http_response_code(404);
        if (file_exists(BASE_PATH . '/views/errors/404.php')) {
            include BASE_PATH . '/views/errors/404.php';
        } else {
            echo "<h1>404 - Không tìm thấy</h1><p>$message</p>";
        }
        exit();
    }
}
