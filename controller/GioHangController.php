<?php
/**
 * GioHangController - Controller quản lý giỏ hàng
 */

class GioHangController extends Controller {
    
    private $gioHangModel;
    
    public function __construct() {
        parent::__construct();
        require_once BASE_PATH . '/model/GioHang.php';
        require_once BASE_PATH . '/model/SanPham.php';
        $this->gioHangModel = new GioHang();
    }
    
    /**
     * Hiển thị giỏ hàng
     */
    public function index() {
        $this->requireLogin();
        
        $role = $_SESSION['role'] ?? 0;
        $username = $_SESSION['username'];
        
        if ($role == 0) {
            // Khách hàng - hiển thị giỏ hàng của họ
            $makh = $this->gioHangModel->findCustomerByUsername($username);
            
            if (!$makh) {
                $this->view('giohang/index', [
                    'error' => 'Không tìm thấy thông tin khách hàng',
                    'page_title' => 'Giỏ hàng',
                    'items' => [],
                    'total' => 0
                ]);
                return;
            }
            
            $items = $this->gioHangModel->getByCustomer($makh);
            $total = $this->gioHangModel->getCartTotal($makh);
        } else {
            // Admin/Nhân viên - hiển thị tất cả giỏ hàng
            $items = $this->gioHangModel->getAllItems();
            $total = 0;
            foreach ($items as $item) {
                $total += $item['thanhtien'];
            }
        }
        
        $this->view('giohang/index', [
            'items' => $items,
            'total' => $total,
            'role' => $role,
            'page_title' => 'Giỏ hàng',
            'active_nav' => 'giohang'
        ]);
    }
    
    /**
     * Thêm sản phẩm vào giỏ
     */
    public function add() {
        $this->requireLogin();
        
        // Chỉ khách hàng mới được thêm vào giỏ
        if ($_SESSION['role'] != 0) {
            $this->json(['success' => false, 'message' => 'Chỉ khách hàng mới có thể thêm vào giỏ'], 403);
            return;
        }
        
        $masp = $this->input('masp');
        $quantity = (int)($this->input('soluong') ?: 1);
        
        if (!$masp) {
            $this->setFlash('error', 'Thiếu thông tin sản phẩm');
            $this->redirect('/views/giohang.php');
            return;
        }
        
        $username = $_SESSION['username'];
        $makh = $this->gioHangModel->findCustomerByUsername($username);
        
        if (!$makh) {
            $this->setFlash('error', 'Không tìm thấy thông tin khách hàng');
            $this->redirect('/views/giohang.php');
            return;
        }
        
        $result = $this->gioHangModel->addToCart($makh, $masp, $quantity);
        
        if ($result['success']) {
            $this->setFlash('success', $result['message']);
        } else {
            $this->setFlash('error', $result['message']);
        }
        
        // Redirect về trang trước đó hoặc giỏ hàng
        $referer = $_SERVER['HTTP_REFERER'] ?? BASE_URL . '/views/giohang.php';
        header('Location: ' . $referer);
        exit();
    }
    
    /**
     * Cập nhật số lượng sản phẩm
     */
    public function update() {
        $this->requireLogin();
        
        if ($_SESSION['role'] != 0) {
            $this->json(['success' => false, 'message' => 'Không có quyền'], 403);
            return;
        }
        
        $masp = $this->input('masp');
        $newQuantity = (int)$this->input('soluong');
        
        $username = $_SESSION['username'];
        $makh = $this->gioHangModel->findCustomerByUsername($username);
        $magio = $this->gioHangModel->getCartId($makh);
        
        if (!$magio) {
            $this->setFlash('error', 'Không tìm thấy giỏ hàng');
            $this->redirect('/views/giohang.php');
            return;
        }
        
        // Kiểm tra tồn kho
        $sanPhamModel = new SanPham();
        $stock = $sanPhamModel->getStock($masp);
        
        if ($newQuantity > $stock) {
            $this->setFlash('error', "Số lượng vượt quá tồn kho (còn $stock sản phẩm)");
            $this->redirect('/views/giohang.php');
            return;
        }
        
        if ($newQuantity <= 0) {
            // Xóa sản phẩm khỏi giỏ
            $this->gioHangModel->removeItem($magio, $masp);
            $this->setFlash('success', 'Đã xóa sản phẩm khỏi giỏ hàng');
        } else {
            $this->gioHangModel->updateItemQuantity($magio, $masp, $newQuantity);
            $this->setFlash('success', 'Cập nhật số lượng thành công');
        }
        
        $this->redirect('/views/giohang.php');
    }
    
    /**
     * Xóa sản phẩm khỏi giỏ
     */
    public function remove() {
        $this->requireLogin();
        
        if ($_SESSION['role'] != 0) {
            $this->json(['success' => false, 'message' => 'Không có quyền'], 403);
            return;
        }
        
        $masp = $this->input('masp');
        
        $username = $_SESSION['username'];
        $makh = $this->gioHangModel->findCustomerByUsername($username);
        $magio = $this->gioHangModel->getCartId($makh);
        
        if ($magio) {
            $this->gioHangModel->removeItem($magio, $masp);
            $this->setFlash('success', 'Đã xóa sản phẩm khỏi giỏ hàng');
        }
        
        $this->redirect('/views/giohang.php');
    }
    
    /**
     * Xóa toàn bộ giỏ hàng
     */
    public function clear() {
        $this->requireLogin();
        
        if ($_SESSION['role'] != 0) {
            $this->json(['success' => false, 'message' => 'Không có quyền'], 403);
            return;
        }
        
        $username = $_SESSION['username'];
        $makh = $this->gioHangModel->findCustomerByUsername($username);
        $magio = $this->gioHangModel->getCartId($makh);
        
        if ($magio) {
            $this->gioHangModel->clearCart($magio);
            $this->setFlash('success', 'Đã xóa toàn bộ giỏ hàng');
        }
        
        $this->redirect('/views/giohang.php');
    }
}
