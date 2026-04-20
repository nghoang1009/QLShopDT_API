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
    
    /**
     * Danh sách khách hàng
     */
    public function index() {
        $this->requireRole([1, 2]); // Admin và nhân viên
        
        $customers = $this->khachHangModel->getAllWithAccount();
        
        $this->view('khachhang/index', [
            'page_title' => 'Quản lý Khách hàng',
            'active_nav' => 'khachhang',
            'customers' => $customers,
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error')
        ]);
    }
    
    /**
     * Chi tiết khách hàng
     */
    public function show($makh) {
        $this->requireRole([1, 2]);
        
        $customer = $this->khachHangModel->findWithAccount($makh);
        
        if (!$customer) {
            $this->setFlash('error', 'Không tìm thấy khách hàng');
            $this->redirect('/khachhang');
            return;
        }
        
        // Lấy lịch sử đơn hàng
        $orders = $this->donHangModel->getByCustomer($makh);
        
        $this->view('khachhang/detail', [
            'page_title' => 'Chi tiết Khách hàng',
            'active_nav' => 'khachhang',
            'customer' => $customer,
            'orders' => $orders
        ]);
    }
    
    /**
     * Form thêm khách hàng
     */
    public function create() {
        $this->requireRole([1, 2]);
        
        $this->view('khachhang/create', [
            'page_title' => 'Thêm Khách hàng',
            'active_nav' => 'khachhang'
        ]);
    }
    
    /**
     * Xử lý thêm khách hàng
     */
    public function store() {
        $this->requireRole([1, 2]);
        $this->verifyCsrf();
        
        $tenkh = $this->input('tenkh');
        $diachi = $this->input('diachi');
        $sdt = $this->input('sdt');
        $username = $this->input('username');
        $password = $this->input('password');
        
        // Validate
        if (empty($tenkh)) {
            $this->setFlash('error', 'Vui lòng nhập tên khách hàng');
            $this->redirect('/khachhang/add');
            return;
        }
        
        // Tạo tài khoản nếu có username và password
        $makh = null;
        if (!empty($username) && !empty($password)) {
            // Kiểm tra username đã tồn tại
            if ($this->taiKhoanModel->usernameExists($username)) {
                $this->setFlash('error', 'Tên đăng nhập đã tồn tại');
                $this->redirect('/khachhang/add');
                return;
            }
            
            // Tạo tài khoản khách hàng (role = 0)
            $result = $this->taiKhoanModel->register($username, $password, 0);
            if ($result['success']) {
                $makh = $result['id'];
            } else {
                $this->setFlash('error', $result['message']);
                $this->redirect('/khachhang/add');
                return;
            }
        }
        
        // Thêm khách hàng
        $data = [
            'makh' => $makh,
            'tenkh' => $tenkh,
            'diachi' => $diachi,
            'sdt' => $sdt
        ];
        
        $result = $this->khachHangModel->add($data);
        
        if ($result) {
            $this->setFlash('success', 'Thêm khách hàng thành công');
        } else {
            $this->setFlash('error', 'Thêm khách hàng thất bại');
        }
        
        $this->redirect('/khachhang');
    }
    
    /**
     * Form chỉnh sửa khách hàng
     */
    public function edit($makh) {
        $this->requireRole([1, 2]);
        
        $customer = $this->khachHangModel->findWithAccount($makh);
        
        if (!$customer) {
            $this->setFlash('error', 'Không tìm thấy khách hàng');
            $this->redirect('/khachhang');
            return;
        }
        
        $this->view('khachhang/edit', [
            'page_title' => 'Sửa Khách hàng',
            'active_nav' => 'khachhang',
            'customer' => $customer
        ]);
    }
    
    /**
     * Xử lý cập nhật khách hàng
     */
    public function update() {
        $this->requireRole([1, 2]);
        $this->verifyCsrf();
        
        $makh = $this->input('makh');
        $tenkh = $this->input('tenkh');
        $diachi = $this->input('diachi');
        $sdt = $this->input('sdt');
        
        $customer = $this->khachHangModel->findById($makh);
        
        if (!$customer) {
            $this->setFlash('error', 'Không tìm thấy khách hàng');
            $this->redirect('/khachhang');
            return;
        }
        
        if (empty($tenkh)) {
            $this->setFlash('error', 'Vui lòng nhập tên khách hàng');
            $this->redirect('/khachhang/edit/' . $makh);
            return;
        }
        
        $data = [
            'tenkh' => $tenkh,
            'diachi' => $diachi,
            'sdt' => $sdt
        ];
        
        $result = $this->khachHangModel->updateCustomer($makh, $data);
        
        if ($result) {
            $this->setFlash('success', 'Cập nhật khách hàng thành công');
        } else {
            $this->setFlash('error', 'Cập nhật khách hàng thất bại');
        }
        
        $this->redirect('/khachhang');
    }
    
    /**
     * Xóa khách hàng
     */
    public function delete($makh) {
        $this->requireRole([1]); // Chỉ admin mới được xóa
        
        $customer = $this->khachHangModel->findById($makh);
        
        if (!$customer) {
            $this->setFlash('error', 'Không tìm thấy khách hàng');
            $this->redirect('/khachhang');
            return;
        }
        
        // Kiểm tra có đơn hàng không
        $orders = $this->donHangModel->getByCustomer($makh);
        if (!empty($orders)) {
            $this->setFlash('error', 'Không thể xóa khách hàng đã có đơn hàng');
            $this->redirect('/khachhang');
            return;
        }
        
        $result = $this->khachHangModel->deleteCustomer($makh);
        
        if ($result) {
            $this->setFlash('success', 'Xóa khách hàng thành công');
        } else {
            $this->setFlash('error', 'Xóa khách hàng thất bại');
        }
        
        $this->redirect('/khachhang');
    }
    
    /**
     * Tìm kiếm khách hàng
     */
    public function search() {
        $this->requireRole([1, 2]);
        
        $keyword = $this->input('q', '');
        
        if (empty($keyword)) {
            $this->redirect('/khachhang');
            return;
        }
        
        $customers = $this->khachHangModel->search($keyword);
        
        $this->view('khachhang/index', [
            'page_title' => 'Tìm kiếm Khách hàng',
            'active_nav' => 'khachhang',
            'customers' => $customers,
            'keyword' => $keyword
        ]);
    }
}
