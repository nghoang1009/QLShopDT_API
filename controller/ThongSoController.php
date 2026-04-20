<?php
/**
 * ThongSoController - Controller quản lý thông số kỹ thuật sản phẩm
 */

class ThongSoController extends Controller {
    
    private $thongSoModel;
    private $sanPhamModel;
    
    public function __construct() {
        parent::__construct();
        require_once BASE_PATH . '/model/ThongSo.php';
        require_once BASE_PATH . '/model/SanPham.php';
        
        $this->thongSoModel = new ThongSo();
        $this->sanPhamModel = new SanPham();
    }
    
    /**
     * GET /api/thongso?masp=X  — Lấy danh sách thông số theo sản phẩm
     */
    public function apiIndex() {
        header('Content-Type: application/json; charset=utf-8');

        $masp  = $_GET['masp'] ?? '';
        $specs = $masp ? $this->thongSoModel->getByProduct($masp) : [];

        echo json_encode([
            'status' => true,
            'data'   => $specs ?: [],
            'total'  => count($specs ?: [])
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * GET /api/thongso/{id}  — Lấy 1 thông số
     */
    public function apiShow($id) {
        header('Content-Type: application/json; charset=utf-8');

        $spec = $this->thongSoModel->getOne((int)$id);

        if (!$spec) {
            http_response_code(404);
            echo json_encode(['status' => false, 'message' => 'Không tìm thấy thông số'], JSON_UNESCAPED_UNICODE);
            return;
        }

        echo json_encode(['status' => true, 'data' => $spec], JSON_UNESCAPED_UNICODE);
    }

    /**
     * POST /api/thongso  — Thêm thông số mới
     */
    public function apiStore() {
        header('Content-Type: application/json; charset=utf-8');
        $this->requireRole([1, 2]);

        $data   = json_decode(file_get_contents('php://input'), true) ?? [];
        $tents  = trim($data['tents'] ?? '');
        $masp   = trim($data['masp'] ?? '');
        $giatri = trim($data['giatri'] ?? '');

        if (empty($tents) || empty($masp)) {
            http_response_code(422);
            echo json_encode(['status' => false, 'message' => 'Tên thông số và mã sản phẩm không được để trống'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $id = $this->thongSoModel->add($tents, $masp, $giatri);

        if ($id) {
            http_response_code(201);
            echo json_encode(['status' => true, 'message' => 'Thêm thông số thành công', 'mats' => $id], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode(['status' => false, 'message' => 'Lỗi khi thêm thông số'], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * PUT /api/thongso/{id}  — Cập nhật thông số
     */
    public function apiUpdate($id) {
        header('Content-Type: application/json; charset=utf-8');
        $this->requireRole([1, 2]);

        $spec = $this->thongSoModel->getOne((int)$id);
        if (!$spec) {
            http_response_code(404);
            echo json_encode(['status' => false, 'message' => 'Không tìm thấy thông số'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $data   = json_decode(file_get_contents('php://input'), true) ?? [];
        $tents  = trim($data['tents'] ?? '');
        $masp   = trim($data['masp'] ?? $spec['masp']);
        $giatri = trim($data['giatri'] ?? '');

        if (empty($tents)) {
            http_response_code(422);
            echo json_encode(['status' => false, 'message' => 'Tên thông số không được để trống'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $result = $this->thongSoModel->updateSpec((int)$id, $tents, $masp, $giatri);

        if ($result !== false) {
            echo json_encode(['status' => true, 'message' => 'Cập nhật thông số thành công'], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode(['status' => false, 'message' => 'Lỗi khi cập nhật thông số'], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * DELETE /api/thongso/{id}  — Xóa thông số
     */
    public function apiDestroy($id) {
        header('Content-Type: application/json; charset=utf-8');
        $this->requireRole([1, 2]);

        $spec = $this->thongSoModel->getOne((int)$id);
        if (!$spec) {
            http_response_code(404);
            echo json_encode(['status' => false, 'message' => 'Không tìm thấy thông số'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $result = $this->thongSoModel->deleteSpec((int)$id);

        if ($result !== false) {
            echo json_encode(['status' => true, 'message' => 'Xóa thông số thành công'], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode(['status' => false, 'message' => 'Lỗi khi xóa thông số'], JSON_UNESCAPED_UNICODE);
        }
    }
}
