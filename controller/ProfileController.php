<?php
/**
 * ProfileController - Controller quản lý hồ sơ người dùng
 */

class ProfileController extends Controller {
    
    private $khachHangModel;
    private $nhanVienModel;
    private $taiKhoanModel;
    
    public function __construct() {
        parent::__construct();
        require_once BASE_PATH . '/model/KhachHang.php';
        require_once BASE_PATH . '/model/NhanVien.php';
        require_once BASE_PATH . '/model/TaiKhoan.php';
        $this->khachHangModel = new KhachHang();
        $this->nhanVienModel = new NhanVien();
        $this->taiKhoanModel = new TaiKhoan();
    }
    
    /**
     * Hiển thị trang profile
     */
    public function index() {
        $this->requireLogin();
        
        $username = $_SESSION['username'];
        $role = $_SESSION['role'] ?? 0;
        $userid = $_SESSION['userid'] ?? 0;
        
        $profile_data = [];
        
        // Lấy thông tin chi tiết dựa trên role
        if ($role == 0) {
            // Khách hàng
            $profile_data = $this->khachHangModel->findWithAccount($userid);
        } else {
            // Nhân viên / Admin
            $profile_data = $this->nhanVienModel->findById($userid);
            if ($profile_data) {
                $profile_data['tentk'] = $username;
            }
        }
        
        $this->view('profile/index', [
            'page_title' => 'Hồ sơ cá nhân',
            'active_nav' => 'profile',
            'profile' => $profile_data,
            'role' => $role,
            'username' => $username,
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error')
        ]);
    }
    
    /**
     * Form chỉnh sửa profile
     */
    public function edit() {
        $this->requireLogin();
        
        $username = $_SESSION['username'];
        $role = $_SESSION['role'] ?? 0;
        $userid = $_SESSION['userid'] ?? 0;
        
        $profile_data = [];
        
        if ($role == 0) {
            $profile_data = $this->khachHangModel->findWithAccount($userid);
        } else {
            $profile_data = $this->nhanVienModel->findById($userid);
            if ($profile_data) {
                $profile_data['tentk'] = $username;
            }
        }
        
        $this->view('profile/edit', [
            'page_title' => 'Chỉnh sửa hồ sơ',
            'active_nav' => 'profile',
            'profile' => $profile_data,
            'role' => $role,
            'username' => $username
        ]);
    }
    
    /**
     * Xử lý cập nhật profile
     */
    public function update() {
        $this->requireLogin();
        $this->verifyCsrf();
        
        $role = $_SESSION['role'] ?? 0;
        $userid = $_SESSION['userid'] ?? 0;
        
        if ($role == 0) {
            // Cập nhật khách hàng
            $data = [
                'tenkh' => $this->input('tenkh'),
                'diachi' => $this->input('diachi'),
                'sdt' => $this->input('sdt')
            ];
            
            $result = $this->khachHangModel->updateCustomer($userid, $data);
        } else {
            // Cập nhật nhân viên
            $data = [
                'tennv' => $this->input('tennv'),
                'sdt' => $this->input('sdt'),
                'ns' => $this->input('ns')
            ];
            
            $result = $this->nhanVienModel->updateStaff($userid, $data);
        }
        
        if ($result) {
            $this->setFlash('success', 'Cập nhật thông tin thành công');
        } else {
            $this->setFlash('error', 'Cập nhật thông tin thất bại');
        }
        
        $this->redirect('/profile');
    }
    
    /**
     * Form đổi mật khẩu
     */
    public function changePasswordForm() {
        $this->requireLogin();
        
        $this->view('profile/change_password', [
            'page_title' => 'Đổi mật khẩu',
            'active_nav' => 'profile',
            'error' => $this->getFlash('error'),
            'success' => $this->getFlash('success')
        ]);
    }
    
    /**
     * Xử lý đổi mật khẩu
     */
    public function changePassword() {
        $this->requireLogin();
        $this->verifyCsrf();
        
        $userid = $_SESSION['userid'] ?? 0;
        $currentPassword = $this->input('current_password');
        $newPassword = $this->input('new_password');
        $confirmPassword = $this->input('confirm_password');
        
        // Validate
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $this->setFlash('error', 'Vui lòng nhập đầy đủ thông tin');
            $this->redirect('/profile/change-password');
            return;
        }
        
        if ($newPassword !== $confirmPassword) {
            $this->setFlash('error', 'Mật khẩu mới không khớp');
            $this->redirect('/profile/change-password');
            return;
        }
        
        if (strlen($newPassword) < 6) {
            $this->setFlash('error', 'Mật khẩu mới phải có ít nhất 6 ký tự');
            $this->redirect('/profile/change-password');
            return;
        }
        
        // Kiểm tra mật khẩu hiện tại
        $user = $this->taiKhoanModel->findById($userid);
        if (!$user) {
            $this->setFlash('error', 'Không tìm thấy tài khoản');
            $this->redirect('/profile/change-password');
            return;
        }
        
        // Đổi mật khẩu (method sẽ kiểm tra password cũ)
        $result = $this->taiKhoanModel->changePassword($userid, $currentPassword, $newPassword);
        
        if ($result['success']) {
            $this->setFlash('success', $result['message']);
            $this->redirect('/profile');
        } else {
            $this->setFlash('error', $result['message']);
            $this->redirect('/profile/change-password');
        }
    }
}
