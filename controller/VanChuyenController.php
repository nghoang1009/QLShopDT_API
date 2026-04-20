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

    /**
     * Chi tiết vận chuyển (web view)
     */
    public function show($mavc) {
        $this->requireLogin();

        $shipping = $this->vanChuyenModel->getOneWithDetails((int)$mavc);
        if (!$shipping) {
            $this->setFlash('error', 'Không tìm thấy vận chuyển');
            $this->redirect('/vanchuyen');
            return;
        }

        $orderDetails = $this->donHangModel->getOrderDetails((int)$shipping['madh']);

        $this->view('vanchuyen/detail', [
            'page_title'   => 'Chi tiết Vận chuyển #' . $mavc,
            'active_nav'   => 'vanchuyen',
            'shipping'     => $shipping,
            'orderDetails' => $orderDetails,
            'role'         => (int)($_SESSION['role'] ?? 0),
        ]);
    }

    /**
     * Form sửa vận chuyển (web view)
     */
    public function edit($mavc) {
        $this->requireRole([1, 2]);

        $shipping = $this->vanChuyenModel->getOneWithDetails((int)$mavc);
        if (!$shipping) {
            $this->setFlash('error', 'Không tìm thấy vận chuyển');
            $this->redirect('/vanchuyen');
            return;
        }

        $customers = $this->khachHangModel->getAll();

        $this->view('vanchuyen/edit', [
            'page_title' => 'Sửa Vận chuyển #' . $mavc,
            'active_nav' => 'vanchuyen',
            'shipping'   => $shipping,
            'customers'  => $customers,
        ]);
    }

    /**
     * Xử lý cập nhật vận chuyển (web form)
     */
    public function update() {
        $this->requireRole([1, 2]);
        $this->verifyCsrf();

        $mavc     = (int)$this->input('mavc');
        $madh     = (int)$this->input('madh');
        $makh     = (int)$this->input('makh');
        $ngaygiao = trim($this->input('ngaygiao', ''));

        $shipping = $this->vanChuyenModel->getOneWithDetails($mavc);
        if (!$shipping) {
            $this->setFlash('error', 'Không tìm thấy vận chuyển');
            $this->redirect('/vanchuyen');
            return;
        }

        $ok = $this->vanChuyenModel->updateShipping($mavc, $madh, $makh, $ngaygiao);

        if ($ok !== false) {
            $this->setFlash('success', 'Cập nhật vận chuyển thành công');
        } else {
            $this->setFlash('error', 'Cập nhật vận chuyển thất bại');
        }

        $this->redirect('/vanchuyen');
    }

    /**
     * Xác nhận đã giao (web)
     */
    public function confirm($mavc) {
        $this->requireRole([1, 2]);

        $shipping = $this->vanChuyenModel->getOneWithDetails((int)$mavc);
        if (!$shipping) {
            $this->setFlash('error', 'Không tìm thấy vận chuyển');
            $this->redirect('/vanchuyen');
            return;
        }

        $today = date('Y-m-d');
        $ok = $this->vanChuyenModel->updateShipping(
            (int)$mavc,
            (int)$shipping['madh'],
            (int)$shipping['makh'],
            $today
        );

        if ($ok !== false) {
            $this->donHangModel->updateStatus((int)$shipping['madh'], 'Đã giao');
            $this->setFlash('success', 'Xác nhận giao hàng thành công');
        } else {
            $this->setFlash('error', 'Xác nhận giao hàng thất bại');
        }

        $this->redirect('/vanchuyen');
    }

    /**
     * Xóa vận chuyển (web)
     */
    public function delete($mavc) {
        $this->requireRole([1, 2]);

        $shipping = $this->vanChuyenModel->getOneWithDetails((int)$mavc);
        if (!$shipping) {
            $this->setFlash('error', 'Không tìm thấy vận chuyển');
            $this->redirect('/vanchuyen');
            return;
        }

        $ok = $this->vanChuyenModel->deleteShipping((int)$mavc);

        if ($ok) {
            $this->setFlash('success', 'Xóa vận chuyển thành công');
        } else {
            $this->setFlash('error', 'Xóa vận chuyển thất bại');
        }

        $this->redirect('/vanchuyen');
    }

    /**
     * GET /vanchuyen/add — Form thêm vận chuyển
     */
    public function create() {
        $this->requireRole([1, 2]);

        $orders    = $this->donHangModel->getAllWithDetails();
        $customers = $this->khachHangModel->getAll();

        $this->view('vanchuyen/create', [
            'page_title' => 'Thêm Vận chuyển',
            'active_nav' => 'vanchuyen',
            'orders'     => $orders    ?: [],
            'customers'  => $customers ?: [],
        ]);
    }

    /**
     * POST /vanchuyen/store — Xử lý thêm vận chuyển
     */
    public function store() {
        $this->requireRole([1, 2]);

        $madh     = (int)($_POST['madh']     ?? 0);
        $makh     = (int)($_POST['makh']     ?? 0);
        $ngaygiao = trim($_POST['ngaygiao']  ?? '');

        if ($madh <= 0 || $makh <= 0 || empty($ngaygiao)) {
            $this->setFlash('error', 'Vui lòng điền đầy đủ thông tin bắt buộc');
            $this->redirect('/vanchuyen/add');
            return;
        }

        $result = $this->vanChuyenModel->add($madh, $makh, $ngaygiao);

        if ($result) {
            $this->setFlash('success', 'Thêm vận chuyển thành công');
        } else {
            $this->setFlash('error', 'Thêm vận chuyển thất bại');
        }

        $this->redirect('/vanchuyen');
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
