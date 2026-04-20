<?php
/**
 * VanChuyenController - Controller quản lý vận chuyển
 */

class VanChuyenController extends Controller {
    
    private $vanChuyenModel;
    private $donHangModel;
    private $khachHangModel;
    
    public function __construct() {
        parent::__construct();
        require_once BASE_PATH . '/model/VanChuyen.php';
        require_once BASE_PATH . '/model/DonHang.php';
        require_once BASE_PATH . '/model/KhachHang.php';
        
        $this->vanChuyenModel = new VanChuyen();
        $this->donHangModel = new DonHang();
        $this->khachHangModel = new KhachHang();
    }

    /**
     * Trang quản lý vận chuyển (web view)
     */
    public function index() {
        $this->requireLogin();

        $role = $_SESSION['role'] ?? 0;
        if ((int)$role === 0) {
            $this->error403('Bạn không có quyền truy cập trang này');
            return;
        }

        $shippings = $this->vanChuyenModel->getAllWithDetails();

        $this->view('vanchuyen/index', [
            'page_title' => 'Quản lý Vận chuyển',
            'active_nav' => 'vanchuyen',
            'shippings' => $shippings,
            'role' => (int)$role,
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error'),
        ]);
    }
    
    // ===================== RESTful API Methods =====================

    /**
     * GET /api/vanchuyen
     * ?madh= để lấy vận chuyển của một đơn hàng
     */
    public function apiIndex() {
        header('Content-Type: application/json');
        $madh = $_GET['madh'] ?? null;
        if ($madh) {
            $shipping = $this->vanChuyenModel->getByOrder((int)$madh);
            $data = $shipping ? [$shipping] : [];
        } else {
            $data = $this->vanChuyenModel->getAllWithDetails() ?: [];
        }
        echo json_encode(['status' => true, 'data' => $data, 'total' => count($data)], JSON_UNESCAPED_UNICODE);
    }

    /**
     * GET /api/vanchuyen/{id}
     */
    public function apiShow($id) {
        header('Content-Type: application/json');
        $shipping = $this->vanChuyenModel->getOneWithDetails((int)$id);
        if (!$shipping) {
            http_response_code(404);
            echo json_encode(['status' => false, 'message' => 'Không tìm thấy vận chuyển'], JSON_UNESCAPED_UNICODE);
            return;
        }
        echo json_encode(['status' => true, 'data' => $shipping], JSON_UNESCAPED_UNICODE);
    }

    /**
     * POST /api/vanchuyen
     * Body: madh, makh, ngaygiao
     */
    public function apiStore() {
        header('Content-Type: application/json');
        $input    = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $madh     = (int)($input['madh']     ?? 0);
        $makh     = (int)($input['makh']     ?? 0);
        $ngaygiao = trim($input['ngaygiao']  ?? '');

        if (!$madh || !$makh || empty($ngaygiao)) {
            http_response_code(400);
            echo json_encode(['status' => false, 'message' => 'Thiếu madh, makh hoặc ngaygiao'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $id = $this->vanChuyenModel->add($madh, $makh, $ngaygiao);
        if ($id) {
            $this->donHangModel->updateStatus($madh, 'Đang giao');
            echo json_encode(['status' => true, 'message' => 'Thêm vận chuyển thành công', 'mavc' => $id], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode(['status' => false, 'message' => 'Thêm vận chuyển thất bại'], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * PUT /api/vanchuyen/{id}
     * Body: madh, makh, ngaygiao
     */
    public function apiUpdate($id) {
        header('Content-Type: application/json');
        $input    = json_decode(file_get_contents('php://input'), true) ?: [];
        $madh     = (int)($input['madh']     ?? 0);
        $makh     = (int)($input['makh']     ?? 0);
        $ngaygiao = trim($input['ngaygiao']  ?? '');

        $shipping = $this->vanChuyenModel->getOneWithDetails((int)$id);
        if (!$shipping) {
            http_response_code(404);
            echo json_encode(['status' => false, 'message' => 'Không tìm thấy vận chuyển'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $ok = $this->vanChuyenModel->updateShipping(
            (int)$id,
            $madh     ?: (int)$shipping['madh'],
            $makh     ?: (int)$shipping['makh'],
            $ngaygiao ?: $shipping['ngaygiao']
        );

        if ($ok !== false) {
            echo json_encode(['status' => true, 'message' => 'Cập nhật vận chuyển thành công'], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode(['status' => false, 'message' => 'Cập nhật vận chuyển thất bại'], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * DELETE /api/vanchuyen/{id}
     */
    public function apiDestroy($id) {
        header('Content-Type: application/json');
        $shipping = $this->vanChuyenModel->getOneWithDetails((int)$id);
        if (!$shipping) {
            http_response_code(404);
            echo json_encode(['status' => false, 'message' => 'Không tìm thấy vận chuyển'], JSON_UNESCAPED_UNICODE);
            return;
        }
        $ok = $this->vanChuyenModel->deleteShipping((int)$id);
        if ($ok !== false) {
            echo json_encode(['status' => true, 'message' => 'Xóa vận chuyển thành công'], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode(['status' => false, 'message' => 'Xóa vận chuyển thất bại'], JSON_UNESCAPED_UNICODE);
        }
    }
}
