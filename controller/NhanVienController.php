<?php
/**
 * NhanVienController - Controller quản lý nhân viên
 */

class NhanVienController extends Controller {
    
    private $nhanVienModel;
    private $taiKhoanModel;
    
    public function __construct() {
        parent::__construct();
        require_once BASE_PATH . '/model/NhanVien.php';
        require_once BASE_PATH . '/model/TaiKhoan.php';
        
        $this->nhanVienModel = new NhanVien();
        $this->taiKhoanModel = new TaiKhoan();
    }
    
    /**
     * Danh sách nhân viên
     */
    public function index() {
        $this->requireRole([1]); // Chỉ admin
        
        $employees = $this->nhanVienModel->getAll();
        
        $this->view('nhanvien/index', [
            'page_title' => 'Quản lý Nhân viên',
            'active_nav' => 'nhanvien',
            'employees' => $employees,
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error')
        ]);
    }
    
    /**
     * Chi tiết nhân viên
     */
    public function show($manv) {
        $this->requireRole([1]);
        
        $employee = $this->nhanVienModel->findById($manv);
        
        if (!$employee) {
            $this->setFlash('error', 'Không tìm thấy nhân viên');
            $this->redirect('/nhanvien');
            return;
        }
        
        $this->view('nhanvien/detail', [
            'page_title' => 'Chi tiết Nhân viên',
            'active_nav' => 'nhanvien',
            'employee' => $employee
        ]);
    }
    
    /**
     * Form thêm nhân viên
     */
    public function create() {
        $this->requireRole([1]);
        
        $this->view('nhanvien/create', [
            'page_title' => 'Thêm Nhân viên',
            'active_nav' => 'nhanvien'
        ]);
    }
    
    /**
     * Xử lý thêm nhân viên
     */
    public function store() {
        $this->requireRole([1]);
        $this->verifyCsrf();
        
        $tennv = $this->input('tennv');
        $sdt = $this->input('sdt');
        $ns = $this->input('ns');
        $username = $this->input('username');
        $password = $this->input('password');
        
        // Validate
        if (empty($tennv)) {
            $this->setFlash('error', 'Vui lòng nhập tên nhân viên');
            $this->redirect('/nhanvien/add');
            return;
        }
        
        // Tạo tài khoản nếu có username và password
        $manv = null;
        if (!empty($username) && !empty($password)) {
            // Kiểm tra username đã tồn tại
            if ($this->taiKhoanModel->usernameExists($username)) {
                $this->setFlash('error', 'Tên đăng nhập đã tồn tại');
                $this->redirect('/nhanvien/add');
                return;
            }
            
            // Tạo tài khoản nhân viên (role = 2)
            $result = $this->taiKhoanModel->register($username, $password, 2);
            if ($result['success']) {
                $manv = $result['id'];
            } else {
                $this->setFlash('error', $result['message']);
                $this->redirect('/nhanvien/add');
                return;
            }
        }
        
        // Thêm nhân viên
        $data = [
            'manv' => $manv,
            'tennv' => $tennv,
            'sdt' => $sdt,
            'ns' => $ns ?: null
        ];
        
        $result = $this->nhanVienModel->add($data);
        
        if ($result) {
            $this->setFlash('success', 'Thêm nhân viên thành công');
        } else {
            $this->setFlash('error', 'Thêm nhân viên thất bại');
        }
        
        $this->redirect('/nhanvien');
    }
    
    /**
     * Form chỉnh sửa nhân viên
     */
    public function edit($manv) {
        $this->requireRole([1]);
        
        $employee = $this->nhanVienModel->findById($manv);
        
        if (!$employee) {
            $this->setFlash('error', 'Không tìm thấy nhân viên');
            $this->redirect('/nhanvien');
            return;
        }
        
        $this->view('nhanvien/edit', [
            'page_title' => 'Sửa Nhân viên',
            'active_nav' => 'nhanvien',
            'employee' => $employee
        ]);
    }
    
    /**
     * Xử lý cập nhật nhân viên
     */
    public function update() {
        $this->requireRole([1]);
        $this->verifyCsrf();
        
        $manv = $this->input('manv');
        $tennv = $this->input('tennv');
        $sdt = $this->input('sdt');
        $ns = $this->input('ns');
        
        $employee = $this->nhanVienModel->findById($manv);
        
        if (!$employee) {
            $this->setFlash('error', 'Không tìm thấy nhân viên');
            $this->redirect('/nhanvien');
            return;
        }
        
        if (empty($tennv)) {
            $this->setFlash('error', 'Vui lòng nhập tên nhân viên');
            $this->redirect('/nhanvien/edit/' . $manv);
            return;
        }
        
        $data = [
            'tennv' => $tennv,
            'sdt' => $sdt,
            'ns' => $ns ?: null
        ];
        
        $result = $this->nhanVienModel->updateStaff($manv, $data);
        
        if ($result) {
            $this->setFlash('success', 'Cập nhật nhân viên thành công');
        } else {
            $this->setFlash('error', 'Cập nhật nhân viên thất bại');
        }
        
        $this->redirect('/nhanvien');
    }
    
    /**
     * Xóa nhân viên
     */
    public function delete($manv) {
        $this->requireRole([1]);
        
        $employee = $this->nhanVienModel->findById($manv);
        
        if (!$employee) {
            $this->setFlash('error', 'Không tìm thấy nhân viên');
            $this->redirect('/nhanvien');
            return;
        }
        
        $result = $this->nhanVienModel->deleteStaff($manv);
        
        if ($result) {
            $this->setFlash('success', 'Xóa nhân viên thành công');
        } else {
            $this->setFlash('error', 'Xóa nhân viên thất bại');
        }
        
        $this->redirect('/nhanvien');
    }
    
    /**
     * Tìm kiếm nhân viên
     */
    public function search() {
        $this->requireRole([1]);
        
        $keyword = $this->input('q', '');
        
        if (empty($keyword)) {
            $this->redirect('/nhanvien');
            return;
        }
        
        $employees = $this->nhanVienModel->search($keyword);
        
        $this->view('nhanvien/index', [
            'page_title' => 'Tìm kiếm Nhân viên',
            'active_nav' => 'nhanvien',
            'employees' => $employees,
            'keyword' => $keyword
        ]);
    }
}
