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
     * Danh sách thông số theo sản phẩm
     */
    public function index() {
        $this->requireLogin();
        
        $masp = $this->input('masp');
        
        if (!$masp) {
            $this->setFlash('error', 'Vui lòng chọn sản phẩm');
            $this->redirect('/sanpham');
            return;
        }
        
        $product = $this->sanPhamModel->findById($masp);
        
        if (!$product) {
            $this->setFlash('error', 'Không tìm thấy sản phẩm');
            $this->redirect('/sanpham');
            return;
        }
        
        $specs = $this->thongSoModel->getByProduct($masp);
        
        $role = $_SESSION['role'] ?? 0;
        $canEdit = in_array($role, [1, 2]);
        
        $this->view('thongso/index', [
            'page_title' => 'Thông số kỹ thuật - ' . $product['tensp'],
            'active_nav' => 'sanpham',
            'product' => $product,
            'specs' => $specs,
            'canEdit' => $canEdit,
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error')
        ]);
    }
    
    /**
     * Form thêm thông số
     */
    public function create() {
        $this->requireRole([1, 2]);
        
        $masp = $this->input('masp');
        
        if (!$masp) {
            $this->setFlash('error', 'Vui lòng chọn sản phẩm');
            $this->redirect('/sanpham');
            return;
        }
        
        $product = $this->sanPhamModel->findById($masp);
        
        if (!$product) {
            $this->setFlash('error', 'Không tìm thấy sản phẩm');
            $this->redirect('/sanpham');
            return;
        }
        
        // Lấy tất cả sản phẩm để chọn
        $products = $this->sanPhamModel->getAll();
        
        $this->view('thongso/create', [
            'page_title' => 'Thêm Thông số kỹ thuật',
            'active_nav' => 'sanpham',
            'product' => $product,
            'products' => $products
        ]);
    }
    
    /**
     * Xử lý thêm thông số
     */
    public function store() {
        $this->requireRole([1, 2]);
        $this->verifyCsrf();
        
        $tents = $this->input('tents');
        $masp = $this->input('masp');
        $giatri = $this->input('giatri');
        
        // Validate
        if (empty($tents) || empty($masp) || empty($giatri)) {
            $this->setFlash('error', 'Vui lòng nhập đầy đủ thông tin');
            $this->redirect('/thongso/add?masp=' . $masp);
            return;
        }
        
        // Kiểm tra sản phẩm tồn tại
        $product = $this->sanPhamModel->findById($masp);
        if (!$product) {
            $this->setFlash('error', 'Không tìm thấy sản phẩm');
            $this->redirect('/sanpham');
            return;
        }
        
        $result = $this->thongSoModel->add($tents, $masp, $giatri);
        
        if ($result) {
            $this->setFlash('success', 'Thêm thông số thành công');
        } else {
            $this->setFlash('error', 'Thêm thông số thất bại');
        }
        
        $this->redirect('/thongso?masp=' . $masp);
    }
    
    /**
     * Form chỉnh sửa thông số
     */
    public function edit($mats) {
        $this->requireRole([1, 2]);
        
        $spec = $this->thongSoModel->getOne($mats);
        
        if (!$spec) {
            $this->setFlash('error', 'Không tìm thấy thông số');
            $this->redirect('/sanpham');
            return;
        }
        
        $products = $this->sanPhamModel->getAll();
        
        $this->view('thongso/edit', [
            'page_title' => 'Sửa Thông số kỹ thuật',
            'active_nav' => 'sanpham',
            'spec' => $spec,
            'products' => $products
        ]);
    }
    
    /**
     * Xử lý cập nhật thông số
     */
    public function update() {
        $this->requireRole([1, 2]);
        $this->verifyCsrf();
        
        $mats = $this->input('mats');
        $tents = $this->input('tents');
        $masp = $this->input('masp');
        $giatri = $this->input('giatri');
        
        $spec = $this->thongSoModel->getOne($mats);
        
        if (!$spec) {
            $this->setFlash('error', 'Không tìm thấy thông số');
            $this->redirect('/sanpham');
            return;
        }
        
        // Validate
        if (empty($tents) || empty($giatri)) {
            $this->setFlash('error', 'Vui lòng nhập đầy đủ thông tin');
            $this->redirect('/thongso/edit/' . $mats);
            return;
        }
        
        $result = $this->thongSoModel->updateSpec($mats, $tents, $masp, $giatri);
        
        if ($result) {
            $this->setFlash('success', 'Cập nhật thông số thành công');
        } else {
            $this->setFlash('error', 'Cập nhật thông số thất bại');
        }
        
        $this->redirect('/thongso?masp=' . $masp);
    }
    
    /**
     * Xóa thông số
     */
    public function delete($mats) {
        $this->requireRole([1, 2]);
        
        $spec = $this->thongSoModel->getOne($mats);
        
        if (!$spec) {
            $this->setFlash('error', 'Không tìm thấy thông số');
            $this->redirect('/sanpham');
            return;
        }
        
        $masp = $spec['masp'];
        
        $result = $this->thongSoModel->deleteSpec($mats);
        
        if ($result) {
            $this->setFlash('success', 'Xóa thông số thành công');
        } else {
            $this->setFlash('error', 'Xóa thông số thất bại');
        }
        
        $this->redirect('/thongso?masp=' . $masp);
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

    /**
     * Thêm nhiều thông số cùng lúc (AJAX)
     */
    public function bulkAdd() {
        $this->requireRole([1, 2]);
        
        $masp = $this->input('masp');
        $specs = $this->input('specs'); // Array of [tents, giatri]
        
        if (!$masp || empty($specs)) {
            $this->json(['success' => false, 'message' => 'Thiếu thông tin'], 400);
            return;
        }
        
        $product = $this->sanPhamModel->findById($masp);
        if (!$product) {
            $this->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm'], 404);
            return;
        }
        
        $added = 0;
        foreach ($specs as $spec) {
            if (!empty($spec['tents']) && !empty($spec['giatri'])) {
                $result = $this->thongSoModel->add($spec['tents'], $masp, $spec['giatri']);
                if ($result) $added++;
            }
        }
        
        $this->json([
            'success' => true,
            'message' => "Đã thêm $added thông số",
            'added' => $added
        ]);
    }
    
    /**
     * Xóa tất cả thông số của sản phẩm
     */
    public function deleteAll() {
        $this->requireRole([1, 2]);
        
        $masp = $this->input('masp');
        
        if (!$masp) {
            $this->setFlash('error', 'Thiếu mã sản phẩm');
            $this->redirect('/sanpham');
            return;
        }
        
        $result = $this->thongSoModel->deleteByProduct($masp);
        
        if ($result !== false) {
            $this->setFlash('success', 'Xóa tất cả thông số thành công');
        } else {
            $this->setFlash('error', 'Xóa thông số thất bại');
        }
        
        $this->redirect('/thongso?masp=' . $masp);
    }
}
