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
    
    // ===================== RESTful API Methods =====================

    /**
     * GET /api/profile
     * Lấy thông tin cá nhân của người dùng đang đăng nhập
     */
    public function apiGet() {
        header('Content-Type: application/json');
        $this->requireLogin();

        $role   = $_SESSION['role']   ?? 0;
        $userid = $_SESSION['userid'] ?? 0;

        if ($role == 0) {
            $profile = $this->khachHangModel->findWithAccount($userid);
        } else {
            $profile = $this->nhanVienModel->findById($userid);
            if ($profile) {
                $profile['tentk'] = $_SESSION['username'] ?? '';
            }
        }

        if (!$profile) {
            http_response_code(404);
            echo json_encode(['status' => false, 'message' => 'Không tìm thấy thông tin cá nhân'], JSON_UNESCAPED_UNICODE);
            return;
        }

        echo json_encode(['status' => true, 'data' => $profile], JSON_UNESCAPED_UNICODE);
    }

    /**
     * PUT /api/profile
     * Cập nhật thông tin cá nhân của người dùng đang đăng nhập
     */
    public function apiUpdate() {
        header('Content-Type: application/json');
        $this->requireLogin();

        $role   = $_SESSION['role']   ?? 0;
        $userid = $_SESSION['userid'] ?? 0;

        $body = json_decode(file_get_contents('php://input'), true) ?? [];

        if ($role == 0) {
            $name = trim($body['tenkh'] ?? '');
            if (empty($name)) {
                http_response_code(422);
                echo json_encode(['status' => false, 'message' => 'Tên không được để trống'], JSON_UNESCAPED_UNICODE);
                return;
            }
            $ok = $this->khachHangModel->updateCustomer($userid, [
                'tenkh'  => $name,
                'diachi' => $body['diachi'] ?? '',
                'sdt'    => $body['sdt']    ?? '',
            ]);
        } else {
            $name = trim($body['tennv'] ?? '');
            if (empty($name)) {
                http_response_code(422);
                echo json_encode(['status' => false, 'message' => 'Tên không được để trống'], JSON_UNESCAPED_UNICODE);
                return;
            }
            $data = ['tennv' => $name, 'sdt' => $body['sdt'] ?? ''];
            if (!empty($body['ns'])) {
                $data['ns'] = $body['ns'];
            }
            $ok = $this->nhanVienModel->updateStaff($userid, $data);
        }

        if ($ok !== false) {
            echo json_encode(['status' => true, 'message' => 'Cập nhật thông tin cá nhân thành công'], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode(['status' => false, 'message' => 'Cập nhật thông tin thất bại'], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * POST /api/profile/change-password
     * Đổi mật khẩu
     */
    public function apiChangePassword() {
        header('Content-Type: application/json');
        $this->requireLogin();

        $userid = $_SESSION['userid'] ?? 0;
        $body   = json_decode(file_get_contents('php://input'), true) ?? [];

        $currentPassword = $body['current_password'] ?? '';
        $newPassword     = $body['new_password']     ?? '';
        $confirmPassword = $body['confirm_password'] ?? '';

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            http_response_code(422);
            echo json_encode(['status' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin'], JSON_UNESCAPED_UNICODE);
            return;
        }

        if ($newPassword !== $confirmPassword) {
            http_response_code(422);
            echo json_encode(['status' => false, 'message' => 'Mật khẩu mới không khớp'], JSON_UNESCAPED_UNICODE);
            return;
        }

        if (strlen($newPassword) < 6) {
            http_response_code(422);
            echo json_encode(['status' => false, 'message' => 'Mật khẩu mới phải có ít nhất 6 ký tự'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $result = $this->taiKhoanModel->changePassword($userid, $currentPassword, $newPassword);

        if ($result['success']) {
            echo json_encode(['status' => true, 'message' => $result['message']], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(400);
            echo json_encode(['status' => false, 'message' => $result['message']], JSON_UNESCAPED_UNICODE);
        }
    }
}
