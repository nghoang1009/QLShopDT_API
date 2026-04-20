<?php
/**
 * SanPhamController
 */

class SanPhamController extends Controller {

    private $sanPhamModel;
    private $danhMucModel;

    public function __construct() {
        parent::__construct();
        // Mọi response của controller này đều là JSON
        header('Content-Type: application/json; charset=utf-8');
        require_once BASE_PATH . '/model/SanPham.php';
        require_once BASE_PATH . '/model/DanhMuc.php';
        $this->sanPhamModel = new SanPham();
        $this->danhMucModel = new DanhMuc();
    }

    /** Đọc và decode JSON payload từ request body (dùng cho POST / PUT). */
    private function jsonInput() {
        return json_decode(file_get_contents('php://input'), true) ?? [];
    }

    /** Xóa file ảnh khỏi filesystem (bỏ qua nếu không tồn tại). */
    private function deleteImage($filename) {
        if (empty($filename)) return;
        $path = BASE_PATH . '/includes/img/' . $filename;
        if (file_exists($path)) {
            @unlink($path);
        }
    }

    /** Kiểm tra sản phẩm đã có trong đơn hàng chưa. */
    private function hasOrders($masp) {
        $result = $this->db->select(
            'SELECT 1 FROM chitietdonhang WHERE masp = ? LIMIT 1', 'i', [$masp]
        );
        return !empty($result);
    }

    public function index() {
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        $madm    = isset($_GET['madm'])    ? (int)$_GET['madm']    : 0;
        $latest  = isset($_GET['latest'])  ? (int)$_GET['latest']  : 0;

        if ($latest > 0) {
            $products = $this->sanPhamModel->getLatest($latest);
        } elseif ($keyword !== '' || $madm > 0) {
            $products = $this->sanPhamModel->search($keyword, $madm ?: null);
        } else {
            $products = $this->sanPhamModel->getAllWithCategory();
        }

        $this->json([
            'status'  => true,
            'message' => 'Lấy danh sách sản phẩm thành công',
            'data'    => $products ?: [],
            'total'   => $products ? count($products) : 0,
        ]);
    }

    /**
     * GET /api/sanpham/{id}
     * Lấy chi tiết 1 sản phẩm kèm tên danh mục.
     * Quyền: đã đăng nhập
     */
    public function show($id) {
        $product = $this->sanPhamModel->findWithCategory((int)$id);

        if (!$product) {
            $this->json([
                'status'  => false,
                'message' => 'Không tìm thấy sản phẩm',
                'data'    => null,
            ], 404);
        }

        $this->json([
            'status'  => true,
            'message' => 'Lấy thông tin sản phẩm thành công',
            'data'    => $product,
        ]);
    }

    /**
     * POST /api/sanpham
     * Thêm sản phẩm mới.
     * Body JSON: { "tensp", "gia", "sl", "hang", "baohanh", "ghichu", "hinhanh", "madm" }
     * Upload ảnh được xử lý ở view trước khi gọi endpoint này — chỉ truyền tên file.
     * Quyền: role 1 hoặc 2
     */
    public function store() {
        $this->requireRole([1, 2]);

        $payload = $this->jsonInput();
        $tensp   = isset($payload['tensp']) ? trim($payload['tensp']) : '';

        if (empty($tensp)) {
            $this->json([
                'status'  => false,
                'message' => 'Tên sản phẩm không được để trống',
                'data'    => null,
            ], 422);
        }

        $data = [
            'tensp'   => $tensp,
            'gia'     => isset($payload['gia'])     ? (float)$payload['gia']    : 0,
            'sl'      => isset($payload['sl'])      ? (int)$payload['sl']       : 0,
            'hang'    => isset($payload['hang'])    ? trim($payload['hang'])    : '',
            'baohanh' => isset($payload['baohanh']) ? (int)$payload['baohanh']  : 0,
            'ghichu'  => isset($payload['ghichu'])  ? trim($payload['ghichu'])  : '',
            'hinhanh' => isset($payload['hinhanh']) ? trim($payload['hinhanh']) : '',
            'madm'    => isset($payload['madm'])    ? (int)$payload['madm']     : 0,
        ];

        $newId = $this->sanPhamModel->add($data);

        if (!$newId) {
            $this->json([
                'status'  => false,
                'message' => 'Lỗi khi thêm sản phẩm',
                'data'    => null,
            ], 500);
        }

        $this->json([
            'status'  => true,
            'message' => 'Thêm sản phẩm thành công',
            'data'    => ['masp' => $newId],
        ], 201);
    }

    /**
     * PUT /api/sanpham/{id}
     * Cập nhật sản phẩm.
     * Body JSON: { "tensp", "gia", "sl", "hang", "baohanh", "ghichu", "hinhanh", "madm" }
     * Nếu "hinhanh" không được gửi → giữ nguyên ảnh cũ trong DB.
     * Quyền: role 1 hoặc 2
     */
    public function update($id) {
        $this->requireRole([1, 2]);

        $id      = (int)$id;
        $payload = $this->jsonInput();
        $tensp   = isset($payload['tensp']) ? trim($payload['tensp']) : '';

        if (empty($tensp)) {
            $this->json([
                'status'  => false,
                'message' => 'Tên sản phẩm không được để trống',
                'data'    => null,
            ], 422);
        }

        $existing = $this->sanPhamModel->findWithCategory($id);
        if (!$existing) {
            $this->json([
                'status'  => false,
                'message' => 'Không tìm thấy sản phẩm',
                'data'    => null,
            ], 404);
        }

        $data = [
            'tensp'   => $tensp,
            'gia'     => isset($payload['gia'])     ? (float)$payload['gia']    : 0,
            'sl'      => isset($payload['sl'])      ? (int)$payload['sl']       : 0,
            'hang'    => isset($payload['hang'])    ? trim($payload['hang'])    : '',
            'baohanh' => isset($payload['baohanh']) ? (int)$payload['baohanh']  : 0,
            'ghichu'  => isset($payload['ghichu'])  ? trim($payload['ghichu'])  : '',
            // Nếu không truyền hinhanh mới → giữ nguyên ảnh cũ
            'hinhanh' => isset($payload['hinhanh']) ? trim($payload['hinhanh']) : $existing['hinhanh'],
            'madm'    => isset($payload['madm'])    ? (int)$payload['madm']     : 0,
        ];

        $result = $this->sanPhamModel->updateProduct($id, $data);

        if ($result === false) {
            $this->json([
                'status'  => false,
                'message' => 'Lỗi khi cập nhật sản phẩm',
                'data'    => null,
            ], 500);
        }

        $this->json([
            'status'  => true,
            'message' => 'Cập nhật sản phẩm thành công',
            'data'    => null,
        ]);
    }

    /**
     * DELETE /api/sanpham/{id}
     * Xóa sản phẩm, thông số kỹ thuật và file ảnh liên quan.
     * Từ chối nếu sản phẩm đã xuất hiện trong đơn hàng.
     * Quyền: role 1 hoặc 2
     */
    public function destroy($id) {
        $this->requireRole([1, 2]);

        $id      = (int)$id;
        $product = $this->sanPhamModel->findWithCategory($id);

        if (!$product) {
            $this->json([
                'status'  => false,
                'message' => 'Không tìm thấy sản phẩm',
                'data'    => null,
            ], 404);
        }

        if ($this->hasOrders($id)) {
            $this->json([
                'status'  => false,
                'message' => 'Không thể xóa sản phẩm đã có trong đơn hàng',
                'data'    => null,
            ], 409);
        }

        // Xóa thông số kỹ thuật liên quan trước
        require_once BASE_PATH . '/model/ThongSo.php';
        (new ThongSo())->deleteByProduct($id);

        $result = $this->sanPhamModel->delete($id);

        if ($result === false) {
            $this->json([
                'status'  => false,
                'message' => 'Lỗi khi xóa sản phẩm',
                'data'    => null,
            ], 500);
        }

        // Xóa file ảnh sau khi DB thành công
        $this->deleteImage($product['hinhanh'] ?? '');

        $this->json([
            'status'  => true,
            'message' => 'Xóa sản phẩm thành công',
            'data'    => null,
        ]);
    }
}