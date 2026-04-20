<?php
/**
 * AuthController - Controller xử lý đăng nhập, đăng xuất, đăng ký
 */

class AuthController extends Controller {
    
    private $taiKhoanModel;
    
    public function __construct() {
        parent::__construct();
        require_once BASE_PATH . '/model/TaiKhoan.php';
        $this->taiKhoanModel = new TaiKhoan();
    }
    
    /**
     * Hiển thị form đăng nhập
     */
    public function loginForm() {
        // Nếu đã đăng nhập thì redirect về trang chủ
        if (isLoggedIn()) {
            $this->redirect('/views/trangchu.php');
            return;
        }
        
        $this->view('auth/login', [
            'page_title' => 'Đăng nhập',
            'error' => $this->getFlash('error')
        ]);
    }
    
    /**
     * Xử lý đăng nhập
     */
    public function login() {
        if (isLoggedIn()) {
            $this->redirect('/views/trangchu.php');
            return;
        }
        
        $username = $this->input('username');
        $password = $this->input('password');
        
        if (empty($username) || empty($password)) {
            $this->setFlash('error', 'Vui lòng nhập đầy đủ thông tin');
            $this->redirect('/views/auth/login.php');
            return;
        }
        
        $user = $this->taiKhoanModel->authenticate($username, $password);
        
        if ($user) {
            // Đăng nhập thành công
            $_SESSION['username'] = $user['tentk'];
            $_SESSION['userid'] = $user['matk'];
            $_SESSION['role'] = $user['role'];
            
            // Regenerate CSRF token
            CSRF::regenerate();
            
            $this->redirect('/views/trangchu.php');
        } else {
            $this->setFlash('error', 'Tên đăng nhập hoặc mật khẩu không đúng');
            $this->redirect('/views/auth/login.php');
        }
    }
    
    /**
     * Hiển thị form đăng ký
     */
    public function registerForm() {
        if (isLoggedIn()) {
            $this->redirect('/views/trangchu.php');
            return;
        }
        
        $this->view('auth/register', [
            'page_title' => 'Đăng ký',
            'error' => $this->getFlash('error'),
            'success' => $this->getFlash('success')
        ]);
    }
    
    /**
     * Xử lý đăng ký
     */
    public function register() {
        if (isLoggedIn()) {
            $this->redirect('/views/trangchu.php');
            return;
        }
        
        $username = $this->input('username');
        $password = $this->input('password');
        $confirmPassword = $this->input('confirm_password');
        $fullname = $this->input('fullname');
        
        // Validate
        if (empty($username) || empty($password) || empty($confirmPassword)) {
            $this->setFlash('error', 'Vui lòng nhập đầy đủ thông tin');
            $this->redirect('/views/auth/register.php');
            return;
        }
        
        if ($password !== $confirmPassword) {
            $this->setFlash('error', 'Mật khẩu xác nhận không khớp');
            $this->redirect('/views/auth/register.php');
            return;
        }
        
        if (strlen($password) < 6) {
            $this->setFlash('error', 'Mật khẩu phải có ít nhất 6 ký tự');
            $this->redirect('/views/auth/register.php');
            return;
        }
        
        $result = $this->taiKhoanModel->register($username, $password, 0);
        
        if ($result['success']) {
            // Tạo thông tin khách hàng
            require_once BASE_PATH . '/model/KhachHang.php';
            $khachHangModel = new KhachHang();
            $khachHangModel->add([
                'makh' => $result['id'],
                'tenkh' => $fullname ?: ucfirst($username),
                'diachi' => '',
                'sdt' => ''
            ]);
            
            $this->setFlash('success', 'Đăng ký thành công! Bạn có thể đăng nhập ngay.');
            $this->redirect('/views/auth/login.php');
        } else {
            $this->setFlash('error', $result['message']);
            $this->redirect('/views/auth/register.php');
        }
    }
    
    /**
     * API Login - POST /api/auth/login
     * Trả về JSON với thông tin user để view tự set session
     */
    public function apiLogin() {
        header('Content-Type: application/json; charset=utf-8');

        $data     = json_decode(file_get_contents('php://input'), true) ?? [];
        $username = trim($data['username'] ?? '');
        $password = $data['password'] ?? '';

        if ($username === '' || $password === '') {
            http_response_code(422);
            echo json_encode(['status' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $user = $this->taiKhoanModel->authenticate($username, $password);

        if ($user) {
            $_SESSION['username'] = $user['tentk'];
            $_SESSION['userid']   = $user['matk'];
            $_SESSION['role']     = $user['role'];
            echo json_encode(['status' => true, 'message' => 'Đăng nhập thành công', 'user' => $user], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(401);
            echo json_encode(['status' => false, 'message' => 'Tên đăng nhập hoặc mật khẩu không đúng'], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * API Register - POST /api/auth/register
     * Tạo tài khoản + khách hàng + giỏ hàng, trả JSON
     */
    public function apiRegister() {
        header('Content-Type: application/json; charset=utf-8');

        $data     = json_decode(file_get_contents('php://input'), true) ?? [];
        $username = trim($data['username'] ?? '');
        $password = $data['password'] ?? '';
        $confirm  = $data['confirm_password'] ?? '';
        $name     = trim($data['name'] ?? '');
        $address  = trim($data['address'] ?? '');
        $phone    = trim($data['phone'] ?? '');

        if ($username === '' || $password === '' || $confirm === '' || $name === '') {
            http_response_code(422);
            echo json_encode(['status' => false, 'message' => 'Tên đăng nhập, mật khẩu và họ tên là bắt buộc'], JSON_UNESCAPED_UNICODE);
            return;
        }

        if (strlen($password) < 6) {
            http_response_code(422);
            echo json_encode(['status' => false, 'message' => 'Mật khẩu phải có ít nhất 6 ký tự'], JSON_UNESCAPED_UNICODE);
            return;
        }

        if ($password !== $confirm) {
            http_response_code(422);
            echo json_encode(['status' => false, 'message' => 'Mật khẩu xác nhận không khớp'], JSON_UNESCAPED_UNICODE);
            return;
        }

        if ($this->taiKhoanModel->usernameExists($username)) {
            http_response_code(409);
            echo json_encode(['status' => false, 'message' => 'Tên đăng nhập đã tồn tại'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $tkResult = $this->taiKhoanModel->register($username, $password, 0);

        if (!$tkResult['success']) {
            http_response_code(500);
            echo json_encode(['status' => false, 'message' => $tkResult['message']], JSON_UNESCAPED_UNICODE);
            return;
        }

        $matk = $tkResult['id'];

        require_once BASE_PATH . '/model/KhachHang.php';
        require_once BASE_PATH . '/model/GioHang.php';
        $khModel = new KhachHang();
        $ghModel = new GioHang();

        $khResult = $khModel->add([
            'makh'   => $matk,
            'tenkh'  => $name,
            'diachi' => $address,
            'sdt'    => $phone
        ]);

        if ($khResult === false) {
            $this->taiKhoanModel->deleteAccount($matk);
            http_response_code(500);
            echo json_encode(['status' => false, 'message' => 'Lỗi khi tạo thông tin khách hàng'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $ghModel->createCart($matk);

        echo json_encode(['status' => true, 'message' => 'Đăng ký thành công'], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Đăng xuất
     */
    public function logout() {
        // Xóa session
        session_unset();
        session_destroy();
        
        // Redirect về trang chủ
        header('Location: ' . BASE_URL . '/views/trangchu.php');
        exit();
    }
    
    /**
     * Đổi mật khẩu
     */
    public function changePassword() {
        $this->requireLogin();
        
        if (!$this->isPost()) {
            $this->view('auth/change_password', [
                'page_title' => 'Đổi mật khẩu'
            ]);
            return;
        }
        
        $this->verifyCsrf();
        
        $oldPassword = $this->input('old_password');
        $newPassword = $this->input('new_password');
        $confirmPassword = $this->input('confirm_password');
        
        if ($newPassword !== $confirmPassword) {
            $this->setFlash('error', 'Mật khẩu xác nhận không khớp');
            $this->redirect('/auth/change-password');
            return;
        }
        
        $userId = $_SESSION['userid'];
        $result = $this->taiKhoanModel->changePassword($userId, $oldPassword, $newPassword);
        
        if ($result['success']) {
            $this->setFlash('success', $result['message']);
        } else {
            $this->setFlash('error', $result['message']);
        }
        
        $this->redirect('/auth/change-password');
    }
}
