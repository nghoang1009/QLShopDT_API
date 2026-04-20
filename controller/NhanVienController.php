<?php
/**
 * NhanVienController - Controller quản lý nhân viên
 */

class NhanVienController extends Controller {
    
    private $nhanVienModel;
    private $taiKhoanModel;
    
    public function __construct() {
        parent::__construct();
        require_once BASE_PATH . '/model/NhanVien.php';
        require_once BASE_PATH . '/model/TaiKhoan.php';
        
        $this->nhanVienModel = new NhanVien();
        $this->taiKhoanModel = new TaiKhoan();
    }
    
    // ===================== RESTful API Methods =====================

    /**
     * GET /api/nhanvien
     * ?keyword= để tìm kiếm
     */
    public function apiIndex() {
        header('Content-Type: application/json');
        $keyword = $_GET['keyword'] ?? '';
        if ($keyword !== '') {
            $employees = $this->nhanVienModel->search($keyword);
        } else {
            $employees = $this->nhanVienModel->getAll();
        }
        echo json_encode(['status' => true, 'data' => $employees ?: [], 'total' => count($employees ?: [])], JSON_UNESCAPED_UNICODE);
    }

    /**
     * GET /api/nhanvien/{id}
     */
    public function apiShow($id) {
        header('Content-Type: application/json');
        $employee = $this->nhanVienModel->findById($id);
        if (!$employee) {
            http_response_code(404);
            echo json_encode(['status' => false, 'message' => 'Không tìm thấy nhân viên'], JSON_UNESCAPED_UNICODE);
            return;
        }
        echo json_encode(['status' => true, 'data' => $employee], JSON_UNESCAPED_UNICODE);
    }

    /**
     * POST /api/nhanvien
     * Body: tennv, sdt, ns
     */
    public function apiStore() {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $tennv    = trim($input['tennv']    ?? '');
        $sdt      = trim($input['sdt']      ?? '');
        $ns       = trim($input['ns']       ?? '');
        $diachi   = trim($input['diachi']   ?? '');
        $username = trim($input['username'] ?? '');
        $password = trim($input['password'] ?? '');

        if (empty($tennv)) {
            http_response_code(400);
            echo json_encode(['status' => false, 'message' => 'Tên nhân viên không được để trống'], JSON_UNESCAPED_UNICODE);
            return;
        }
        if (empty($username) || empty($password)) {
            http_response_code(400);
            echo json_encode(['status' => false, 'message' => 'Tên đăng nhập và mật khẩu không được để trống'], JSON_UNESCAPED_UNICODE);
            return;
        }

        // Tạo tài khoản nhân viên (role = 2) trước để lấy matk làm manv
        $regResult = $this->taiKhoanModel->register($username, $password, 2);
        if (!$regResult['success']) {
            http_response_code(400);
            echo json_encode(['status' => false, 'message' => $regResult['message']], JSON_UNESCAPED_UNICODE);
            return;
        }

        $manv = $regResult['id'];
        $ok = $this->nhanVienModel->add([
            'manv'   => $manv,
            'tennv'  => $tennv,
            'diachi' => $diachi,
            'sdt'    => $sdt,
            'ns'     => $ns ?: null,
        ]);

        if ($ok !== false) {
            echo json_encode(['status' => true, 'message' => 'Thêm nhân viên thành công', 'manv' => $manv], JSON_UNESCAPED_UNICODE);
        } else {
            // Rollback: xóa tài khoản vừa tạo nếu insert nhanvien thất bại
            $this->taiKhoanModel->delete($manv);
            http_response_code(500);
            echo json_encode(['status' => false, 'message' => 'Thêm nhân viên thất bại'], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * PUT /api/nhanvien/{id}
     * Body: tennv, sdt, ns
     */
    public function apiUpdate($id) {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true) ?: [];
        $tennv = trim($input['tennv'] ?? '');
        $sdt   = trim($input['sdt']   ?? '');
        $ns    = trim($input['ns']    ?? '');

        if (empty($tennv)) {
            http_response_code(400);
            echo json_encode(['status' => false, 'message' => 'Tên nhân viên không được để trống'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $employee = $this->nhanVienModel->findById($id);
        if (!$employee) {
            http_response_code(404);
            echo json_encode(['status' => false, 'message' => 'Không tìm thấy nhân viên'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $ok = $this->nhanVienModel->updateStaff($id, ['tennv' => $tennv, 'sdt' => $sdt, 'ns' => $ns ?: null]);
        if ($ok !== false) {
            echo json_encode(['status' => true, 'message' => 'Cập nhật nhân viên thành công'], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode(['status' => false, 'message' => 'Cập nhật thất bại'], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * DELETE /api/nhanvien/{id}
     */
    public function apiDestroy($id) {
        header('Content-Type: application/json');
        $employee = $this->nhanVienModel->findById($id);
        if (!$employee) {
            http_response_code(404);
            echo json_encode(['status' => false, 'message' => 'Không tìm thấy nhân viên'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $ok = $this->nhanVienModel->deleteStaff($id);
        if ($ok !== false) {
            echo json_encode(['status' => true, 'message' => 'Xóa nhân viên thành công'], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode(['status' => false, 'message' => 'Xóa nhân viên thất bại'], JSON_UNESCAPED_UNICODE);
        }
    }
}
