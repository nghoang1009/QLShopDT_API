<?php
/**
 * KhachHangController - Controller quản lý khách hàng
 */

class KhachHangController extends Controller {
    
    private $khachHangModel;
    private $taiKhoanModel;
    private $donHangModel;
    
    public function __construct() {
        parent::__construct();
        require_once BASE_PATH . '/model/KhachHang.php';
        require_once BASE_PATH . '/model/TaiKhoan.php';
        require_once BASE_PATH . '/model/DonHang.php';
        
        $this->khachHangModel = new KhachHang();
        $this->taiKhoanModel = new TaiKhoan();
        $this->donHangModel = new DonHang();
    }
    
    // ===================== RESTful API Methods =====================

    /**
     * GET /api/khachhang
     * ?keyword= để tìm kiếm
     */
    public function apiIndex() {
        header('Content-Type: application/json');
        $keyword = $_GET['keyword'] ?? '';
        if ($keyword !== '') {
            $customers = $this->khachHangModel->search($keyword);
        } else {
            $customers = $this->khachHangModel->getAllWithAccount();
        }
        echo json_encode(['status' => true, 'data' => $customers ?: [], 'total' => count($customers ?: [])], JSON_UNESCAPED_UNICODE);
    }

    /**
     * GET /api/khachhang/{id}
     */
    public function apiShow($id) {
        header('Content-Type: application/json');
        $customer = $this->khachHangModel->findWithAccount($id);
        if (!$customer) {
            http_response_code(404);
            echo json_encode(['status' => false, 'message' => 'Không tìm thấy khách hàng'], JSON_UNESCAPED_UNICODE);
            return;
        }
        echo json_encode(['status' => true, 'data' => $customer], JSON_UNESCAPED_UNICODE);
    }

    /**
     * POST /api/khachhang
     * Body: tenkh, diachi, sdt
     * Tự động tạo tài khoản với tentk=tenkh, password=123456, role=0
     */
    public function apiStore() {
        header('Content-Type: application/json');
        $input  = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $tenkh  = trim($input['tenkh']  ?? '');
        $diachi = trim($input['diachi'] ?? '');
        $sdt    = trim($input['sdt']    ?? '');

        if (empty($tenkh)) {
            http_response_code(400);
            echo json_encode(['status' => false, 'message' => 'Tên khách hàng không được để trống'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $tkResult = $this->taiKhoanModel->register($tenkh, '123456', 0);
        if (!$tkResult['success']) {
            http_response_code(400);
            echo json_encode(['status' => false, 'message' => $tkResult['message']], JSON_UNESCAPED_UNICODE);
            return;
        }

        $matk = $tkResult['id'];
        $ok = $this->khachHangModel->add(['makh' => $matk, 'tenkh' => $tenkh, 'diachi' => $diachi, 'sdt' => $sdt]);
        if ($ok === false) {
            $this->taiKhoanModel->deleteAccount($matk);
            http_response_code(500);
            echo json_encode(['status' => false, 'message' => 'Thêm khách hàng thất bại'], JSON_UNESCAPED_UNICODE);
            return;
        }

        echo json_encode(['status' => true, 'message' => 'Thêm khách hàng thành công', 'makh' => $matk], JSON_UNESCAPED_UNICODE);
    }

    /**
     * PUT /api/khachhang/{id}
     * Body: tenkh, diachi, sdt
     */
    public function apiUpdate($id) {
        header('Content-Type: application/json');
        $input  = json_decode(file_get_contents('php://input'), true) ?: [];
        $tenkh  = trim($input['tenkh']  ?? '');
        $diachi = trim($input['diachi'] ?? '');
        $sdt    = trim($input['sdt']    ?? '');

        if (empty($tenkh)) {
            http_response_code(400);
            echo json_encode(['status' => false, 'message' => 'Tên khách hàng không được để trống'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $customer = $this->khachHangModel->findById($id);
        if (!$customer) {
            http_response_code(404);
            echo json_encode(['status' => false, 'message' => 'Không tìm thấy khách hàng'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $ok = $this->khachHangModel->updateCustomer($id, ['tenkh' => $tenkh, 'diachi' => $diachi, 'sdt' => $sdt]);
        if ($ok !== false) {
            echo json_encode(['status' => true, 'message' => 'Cập nhật khách hàng thành công'], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode(['status' => false, 'message' => 'Cập nhật thất bại'], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * DELETE /api/khachhang/{id}
     * Xóa tài khoản → CASCADE xóa khách hàng
     */
    public function apiDestroy($id) {
        header('Content-Type: application/json');
        $customer = $this->khachHangModel->findById($id);
        if (!$customer) {
            http_response_code(404);
            echo json_encode(['status' => false, 'message' => 'Không tìm thấy khách hàng'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $orders = $this->donHangModel->getByCustomer($id);
        if (!empty($orders)) {
            http_response_code(409);
            echo json_encode(['status' => false, 'message' => 'Không thể xóa khách hàng đã có đơn hàng'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $ok = $this->taiKhoanModel->deleteAccount($id);
        if ($ok !== false) {
            echo json_encode(['status' => true, 'message' => 'Xóa khách hàng thành công'], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode(['status' => false, 'message' => 'Xóa khách hàng thất bại'], JSON_UNESCAPED_UNICODE);
        }
    }
}
