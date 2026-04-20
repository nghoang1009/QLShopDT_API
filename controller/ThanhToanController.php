<?php
/**
 * ThanhToanController - Controller quản lý thanh toán
 */

class ThanhToanController extends Controller {
    
    private $thanhToanModel;
    private $donHangModel;
    
    public function __construct() {
        parent::__construct();
        require_once BASE_PATH . '/model/ThanhToan.php';
        require_once BASE_PATH . '/model/DonHang.php';
        
        $this->thanhToanModel = new ThanhToan();
        $this->donHangModel = new DonHang();
    }

    /**
     * Trang quản lý thanh toán (web view)
     */
    public function index() {
        $this->requireLogin();

        $role = $_SESSION['role'] ?? 0;
        $userid = $_SESSION['userid'] ?? 0;

        if ((int)$role === 0) {
            $payments = $this->thanhToanModel->getByCustomer($userid);
        } else {
            $payments = $this->thanhToanModel->getAllWithDetails();
        }

        $canEdit = in_array((int)$role, [1, 2], true);

        $this->view('thanhtoan/index', [
            'page_title' => 'Quản lý Thanh toán',
            'active_nav' => 'thanhtoan',
            'payments' => $payments,
            'role' => (int)$role,
            'canEdit' => $canEdit,
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error'),
        ]);
    }

    /**
     * Chi tiết thanh toán (web view)
     */
    public function show($matt) {
        $this->requireLogin();

        $role = $_SESSION['role'] ?? 0;
        $userid = $_SESSION['userid'] ?? 0;

        $payment = $this->thanhToanModel->getOneWithDetails((int)$matt);
        if (!$payment) {
            $this->setFlash('error', 'Không tìm thấy thanh toán');
            $this->redirect('/thanhtoan');
            return;
        }

        if ((int)$role === 0) {
            $order = $this->donHangModel->getOneWithDetails((int)$payment['madh']);
            if (!$order || (int)$order['makh'] !== (int)$userid) {
                $this->error403('Bạn không có quyền truy cập thanh toán này');
                return;
            }
        }

        $order = $this->donHangModel->getOneWithDetails((int)$payment['madh']);
        $orderDetails = $this->donHangModel->getOrderDetails((int)$payment['madh']);

        $this->view('thanhtoan/detail', [
            'page_title' => 'Chi tiết Thanh toán #' . $matt,
            'active_nav' => 'thanhtoan',
            'payment' => $payment,
            'order' => $order,
            'orderDetails' => $orderDetails,
            'role' => (int)$role,
        ]);
    }

    /**
     * Form sửa thanh toán (web view)
     */
    public function edit($matt) {
        $this->requireRole([1, 2]);

        $payment = $this->thanhToanModel->getOneWithDetails((int)$matt);
        if (!$payment) {
            $this->setFlash('error', 'Không tìm thấy thanh toán');
            $this->redirect('/thanhtoan');
            return;
        }

        $paymentMethods = $this->thanhToanModel->getPaymentMethods();
        $paymentStatuses = $this->thanhToanModel->getPaymentStatuses();

        $this->view('thanhtoan/edit', [
            'page_title' => 'Sửa Thanh toán #' . $matt,
            'active_nav' => 'thanhtoan',
            'payment' => $payment,
            'paymentMethods' => $paymentMethods,
            'paymentStatuses' => $paymentStatuses,
        ]);
    }

    /**
     * Xử lý cập nhật thanh toán (web form)
     */
    public function update() {
        $this->requireRole([1, 2]);
        $this->verifyCsrf();

        $matt = (int)$this->input('matt');
        $payment = $this->thanhToanModel->getOneWithDetails($matt);

        if (!$payment) {
            $this->setFlash('error', 'Không tìm thấy thanh toán');
            $this->redirect('/thanhtoan');
            return;
        }

        $data = [
            'phuongthuc' => $this->input('phuongthuc'),
            'ngaythanhtoan' => $this->input('ngaythanhtoan'),
            'sotien' => $this->input('sotien'),
            'trangthai' => $this->input('trangthai'),
            'ghichu' => $this->input('ghichu'),
        ];

        $result = $this->thanhToanModel->updatePayment($matt, $data);

        if ($result !== false) {
            if ($data['trangthai'] === 'Đã thanh toán') {
                $this->donHangModel->updateStatus((int)$payment['madh'], 'Đã xác nhận');
            }
            $this->setFlash('success', 'Cập nhật thanh toán thành công');
        } else {
            $this->setFlash('error', 'Cập nhật thanh toán thất bại');
        }

        $this->redirect('/thanhtoan');
    }

    /**
     * Xóa thanh toán (web)
     */
    public function delete($matt) {
        $this->requireRole([1, 2]);

        $payment = $this->thanhToanModel->getOneWithDetails((int)$matt);
        if (!$payment) {
            $this->setFlash('error', 'Không tìm thấy thanh toán');
            $this->redirect('/thanhtoan');
            return;
        }

        $result = $this->thanhToanModel->deletePayment((int)$matt);

        if ($result) {
            $this->setFlash('success', 'Xóa thanh toán thành công');
        } else {
            $this->setFlash('error', 'Xóa thanh toán thất bại');
        }

        $this->redirect('/thanhtoan');
    }
    
    // ===================== RESTful API Methods =====================

    /**
     * GET /api/thanhtoan
     * Admin/NV: tất cả; KH: chỉ của mình. Hỗ trợ ?madh=
     */
    public function apiIndex() {
        header('Content-Type: application/json');
        $this->requireLogin();

        $role   = $_SESSION['role']   ?? 0;
        $userid = $_SESSION['userid'] ?? 0;
        $madh   = $_GET['madh'] ?? null;

        if ($madh) {
            $payment = $this->thanhToanModel->getByOrder((int)$madh);
            $data = $payment ? [$payment] : [];
        } elseif ($role == 0) {
            $data = $this->thanhToanModel->getByCustomer($userid) ?: [];
        } else {
            $data = $this->thanhToanModel->getAllWithDetails() ?: [];
        }

        echo json_encode(['status' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
    }

    /**
     * GET /api/thanhtoan/{id}
     * Chi tiết 1 thanh toán
     */
    public function apiShow($matt) {
        header('Content-Type: application/json');
        $this->requireLogin();

        $role   = $_SESSION['role']   ?? 0;
        $userid = $_SESSION['userid'] ?? 0;

        $payment = $this->thanhToanModel->getOneWithDetails((int)$matt);

        if (!$payment) {
            http_response_code(404);
            echo json_encode(['status' => false, 'message' => 'Không tìm thấy thanh toán'], JSON_UNESCAPED_UNICODE);
            return;
        }

        // Khách hàng chỉ xem thanh toán của mình
        if ($role == 0) {
            $order = $this->donHangModel->getOneWithDetails($payment['madh']);
            if (!$order || $order['makh'] != $userid) {
                http_response_code(403);
                echo json_encode(['status' => false, 'message' => 'Không có quyền truy cập'], JSON_UNESCAPED_UNICODE);
                return;
            }
        }

        echo json_encode(['status' => true, 'data' => $payment], JSON_UNESCAPED_UNICODE);
    }

    /**
     * POST /api/thanhtoan
     * Thêm mới thanh toán
     */
    public function apiStore() {
        header('Content-Type: application/json');
        $this->requireRole([1, 2]);

        $body = json_decode(file_get_contents('php://input'), true) ?? [];
        $madh      = $body['madh']      ?? null;
        $phuongthuc = $body['phuongthuc'] ?? null;
        $sotien    = $body['sotien']    ?? null;
        $trangthai = $body['trangthai'] ?? 'Chờ xác nhận';
        $ghichu    = $body['ghichu']    ?? '';

        if (!$madh || !$phuongthuc || !$sotien) {
            http_response_code(422);
            echo json_encode(['status' => false, 'message' => 'Thiếu thông tin bắt buộc (madh, phuongthuc, sotien)'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $order = $this->donHangModel->getOneWithDetails($madh);
        if (!$order) {
            http_response_code(404);
            echo json_encode(['status' => false, 'message' => 'Không tìm thấy đơn hàng'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $existing = $this->thanhToanModel->getByOrder($madh);
        if ($existing && $existing['trangthai'] == 'Đã thanh toán') {
            http_response_code(409);
            echo json_encode(['status' => false, 'message' => 'Đơn hàng này đã được thanh toán'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $matt = $this->thanhToanModel->add([
            'madh'      => $madh,
            'phuongthuc' => $phuongthuc,
            'sotien'    => $sotien,
            'trangthai' => $trangthai,
            'ghichu'    => $ghichu
        ]);

        if ($matt) {
            if ($trangthai == 'Đã thanh toán') {
                $this->donHangModel->updateStatus($madh, 'Đã xác nhận');
            }
            $payment = $this->thanhToanModel->getOneWithDetails($matt);
            http_response_code(201);
            echo json_encode(['status' => true, 'message' => 'Thêm thanh toán thành công', 'data' => $payment], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode(['status' => false, 'message' => 'Thêm thanh toán thất bại'], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * PUT /api/thanhtoan/{id}
     * Cập nhật thanh toán
     */
    public function apiUpdate($matt) {
        header('Content-Type: application/json');
        $this->requireRole([1, 2]);

        $payment = $this->thanhToanModel->getOneWithDetails((int)$matt);
        if (!$payment) {
            http_response_code(404);
            echo json_encode(['status' => false, 'message' => 'Không tìm thấy thanh toán'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $body = json_decode(file_get_contents('php://input'), true) ?? [];
        $data = [
            'phuongthuc'    => $body['phuongthuc']    ?? $payment['phuongthuc'],
            'ngaythanhtoan' => $body['ngaythanhtoan'] ?? $payment['ngaythanhtoan'],
            'sotien'        => $body['sotien']        ?? $payment['sotien'],
            'trangthai'     => $body['trangthai']     ?? $payment['trangthai'],
            'ghichu'        => $body['ghichu']        ?? $payment['ghichu'],
        ];

        $ok = $this->thanhToanModel->updatePayment((int)$matt, $data);

        if ($ok !== false) {
            if ($data['trangthai'] == 'Đã thanh toán') {
                $this->donHangModel->updateStatus($payment['madh'], 'Đã xác nhận');
            }
            $updated = $this->thanhToanModel->getOneWithDetails((int)$matt);
            echo json_encode(['status' => true, 'message' => 'Cập nhật thanh toán thành công', 'data' => $updated], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode(['status' => false, 'message' => 'Cập nhật thanh toán thất bại'], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * DELETE /api/thanhtoan/{id}
     * Xóa thanh toán
     */
    public function apiDestroy($matt) {
        header('Content-Type: application/json');
        $this->requireRole([1, 2]);

        $payment = $this->thanhToanModel->getOneWithDetails((int)$matt);
        if (!$payment) {
            http_response_code(404);
            echo json_encode(['status' => false, 'message' => 'Không tìm thấy thanh toán'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $ok = $this->thanhToanModel->deletePayment((int)$matt);

        if ($ok) {
            echo json_encode(['status' => true, 'message' => 'Xóa thanh toán thành công'], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode(['status' => false, 'message' => 'Xóa thanh toán thất bại'], JSON_UNESCAPED_UNICODE);
        }
    }
}
