<?php
/**
 * DonHangController - Controller quản lý đơn hàng
 */

class DonHangController extends Controller {
    
    private $donHangModel;
    private $khachHangModel;
    private $nhanVienModel;
    private $sanPhamModel;
    private $gioHangModel;
    
    public function __construct() {
        parent::__construct();
        require_once BASE_PATH . '/model/DonHang.php';
        require_once BASE_PATH . '/model/KhachHang.php';
        require_once BASE_PATH . '/model/NhanVien.php';
        require_once BASE_PATH . '/model/SanPham.php';
        require_once BASE_PATH . '/model/GioHang.php';
        
        $this->donHangModel = new DonHang();
        $this->khachHangModel = new KhachHang();
        $this->nhanVienModel = new NhanVien();
        $this->sanPhamModel = new SanPham();
        $this->gioHangModel = new GioHang();
    }
    
    // ===================== RESTful API Methods =====================

    /**
     * GET /api/donhang
     * ?makh= để lấy đơn của một khách hàng
     */
    public function apiIndex() {
        header('Content-Type: application/json');
        $makh = $_GET['makh'] ?? null;
        if ($makh) {
            $orders = $this->donHangModel->getByCustomer($makh);
        } else {
            $orders = $this->donHangModel->getAllWithDetails();
        }
        echo json_encode(['status' => true, 'data' => $orders ?: []]);
    }

    /**
     * GET /api/donhang/{id}
     * Trả về header + chitiet của đơn hàng
     */
    public function apiShow($id) {
        header('Content-Type: application/json');
        $donhang = $this->donHangModel->getOneWithDetails($id);
        if (!$donhang) {
            http_response_code(404);
            echo json_encode(['status' => false, 'message' => 'Không tìm thấy đơn hàng']);
            return;
        }
        $chitiet = $this->donHangModel->getOrderDetails($id);
        $donhang['chitiet'] = $chitiet ?: [];
        echo json_encode(['status' => true, 'data' => $donhang]);
    }

    /**
     * POST /api/donhang
     * Body: makh, trigia, manv (optional)
     */
    public function apiStore() {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $makh  = $input['makh']  ?? null;
        $trigia = $input['trigia'] ?? 0;
        $manv  = $input['manv']  ?? null;
        if (!$makh) {
            http_response_code(400);
            echo json_encode(['status' => false, 'message' => 'Thiếu makh']);
            return;
        }
        $result = $this->donHangModel->createOrder($makh, $trigia, $manv);
        if ($result) {
            echo json_encode(['status' => true, 'message' => 'Tạo đơn hàng thành công']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => false, 'message' => 'Tạo đơn hàng thất bại']);
        }
    }

    /**
     * PUT /api/donhang/{id}
     * Body: trigia (optional), trangthai (optional)
     */
    public function apiUpdate($id) {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true) ?: [];
        $ok = false;
        if (isset($input['trigia'])) {
            $ok = $this->donHangModel->updateTrigia($id, $input['trigia']);
        }
        if (isset($input['trangthai'])) {
            $ok = $this->donHangModel->updateStatus($id, $input['trangthai']);
        }
        if ($ok) {
            echo json_encode(['status' => true, 'message' => 'Cập nhật thành công']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => false, 'message' => 'Cập nhật thất bại']);
        }
    }

    /**
     * DELETE /api/donhang/{id}
     */
    public function apiDestroy($id) {
        header('Content-Type: application/json');
        $result = $this->donHangModel->deleteOrder($id);
        if ($result) {
            echo json_encode(['status' => true, 'message' => 'Xóa đơn hàng thành công']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => false, 'message' => 'Xóa đơn hàng thất bại']);
        }
    }
}
