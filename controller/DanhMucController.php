<?php
/**
 * DanhMucController
 */

class DanhMucController extends Controller {

    private $danhMucModel;

    public function __construct() {
        parent::__construct();
        // Khai báo mọi response của controller này là JSON
        header('Content-Type: application/json; charset=utf-8');
        require_once BASE_PATH . '/model/DanhMuc.php';
        $this->danhMucModel = new DanhMuc();
    }

    /**
     * Đọc và decode JSON payload từ request body (dùng cho POST / PUT).
     */
    private function jsonInput() {
        return json_decode(file_get_contents('php://input'), true) ?? [];
    }

    /**
     * GET /danhmuc
     * Trả về toàn bộ danh mục. Hỗ trợ lọc qua query param: ?keyword=...
     * Quyền: đã đăng nhập
     */
    public function index() {
        $keyword    = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        $categories = $keyword !== ''
            ? $this->danhMucModel->search($keyword)
            : $this->danhMucModel->getAllCategories();

        $this->json([
            'status'  => true,
            'message' => 'Lấy danh sách danh mục thành công',
            'data'    => $categories ?: [],
            'total'   => $categories ? count($categories) : 0,
        ]);
    }

    /**
     * GET /danhmuc/{id}
     * Trả về chi tiết 1 danh mục theo ID.
     * Quyền: đã đăng nhập
     */
    public function show($id) {
        $category = $this->danhMucModel->findById((int)$id);

        if (!$category) {
            $this->json([
                'status'  => false,
                'message' => 'Không tìm thấy danh mục',
                'data'    => null,
            ], 404);
        }

        $this->json([
            'status'  => true,
            'message' => 'Lấy thông tin danh mục thành công',
            'data'    => $category,
        ]);
    }

    /**
     * POST /danhmuc
     * Thêm danh mục mới.
     * Body JSON: { "tendm": "Tên danh mục" }
     * Quyền: role 1 hoặc 2
     */
    public function store() {
        $this->requireRole([1, 2]);

        $payload = $this->jsonInput();
        $tendm   = isset($payload['tendm']) ? trim($payload['tendm']) : '';

        if (empty($tendm)) {
            $this->json([
                'status'  => false,
                'message' => 'Tên danh mục không được để trống',
                'data'    => null,
            ], 422);
        }

        $newId = $this->danhMucModel->add($tendm);

        if (!$newId) {
            $this->json([
                'status'  => false,
                'message' => 'Lỗi khi thêm danh mục',
                'data'    => null,
            ], 500);
        }

        $this->json([
            'status'  => true,
            'message' => 'Thêm danh mục thành công',
            'data'    => ['madm' => $newId],
        ], 201);
    }

    /**
     * PUT /danhmuc/{id}
     * Cập nhật tên danh mục.
     * Body JSON: { "tendm": "Tên mới" }
     * Quyền: role 1 hoặc 2
     */
    public function update($id) {
        $this->requireRole([1, 2]);

        $id      = (int)$id;
        $payload = $this->jsonInput();
        $tendm   = isset($payload['tendm']) ? trim($payload['tendm']) : '';

        if (empty($tendm)) {
            $this->json([
                'status'  => false,
                'message' => 'Tên danh mục không được để trống',
                'data'    => null,
            ], 422);
        }

        if (!$this->danhMucModel->findById($id)) {
            $this->json([
                'status'  => false,
                'message' => 'Không tìm thấy danh mục',
                'data'    => null,
            ], 404);
        }

        $result = $this->danhMucModel->updateCategory($id, $tendm);

        if ($result === false) {
            $this->json([
                'status'  => false,
                'message' => 'Lỗi khi cập nhật danh mục',
                'data'    => null,
            ], 500);
        }

        $this->json([
            'status'  => true,
            'message' => 'Cập nhật danh mục thành công',
            'data'    => null,
        ]);
    }

    /**
     * DELETE /danhmuc/{id}
     * Xóa danh mục. Từ chối nếu danh mục còn chứa sản phẩm.
     * Quyền: role 1 hoặc 2
     */
    public function destroy($id) {
        $this->requireRole([1, 2]);

        $id = (int)$id;

        if (!$this->danhMucModel->findById($id)) {
            $this->json([
                'status'  => false,
                'message' => 'Không tìm thấy danh mục',
                'data'    => null,
            ], 404);
        }

        if ($this->danhMucModel->hasProducts($id)) {
            $this->json([
                'status'  => false,
                'message' => 'Không thể xóa danh mục đang có sản phẩm',
                'data'    => null,
            ], 409);
        }

        $result = $this->danhMucModel->deleteCategory($id);

        if ($result === false) {
            $this->json([
                'status'  => false,
                'message' => 'Lỗi khi xóa danh mục',
                'data'    => null,
            ], 500);
        }

        $this->json([
            'status'  => true,
            'message' => 'Xóa danh mục thành công',
            'data'    => null,
        ]);
    }
}
