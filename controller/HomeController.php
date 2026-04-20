<?php
/**
 * HomeController - Controller cho trang chủ và các trang chung
 */

class HomeController extends Controller {
    
    private $sanPhamModel;
    private $danhMucModel;
    
    public function __construct() {
        parent::__construct();
        require_once BASE_PATH . '/model/SanPham.php';
        require_once BASE_PATH . '/model/DanhMuc.php';
        $this->sanPhamModel = new SanPham();
        $this->danhMucModel = new DanhMuc();
    }
    
    /**
     * Trang chủ
     */
    public function index() {
        // Lấy danh mục
        $danhmucs = $this->danhMucModel->getAllCategories();
        
        // Lấy tham số tìm kiếm
        $search = $this->input('search', '');
        $madm = $this->input('madm', '0');
        
        // Xác định tiêu đề và lấy sản phẩm
        $sectionTitle = 'SẢN PHẨM NỔI BẬT';
        
        if ($search !== '' || ($madm !== '' && $madm != '0')) {
            // Có điều kiện tìm kiếm/lọc
            $products = $this->sanPhamModel->search($search, $madm);
            
            if ($search !== '' && $madm != '0') {
                $sectionTitle = 'KẾT QUẢ: "' . htmlspecialchars($search) . '" trong danh mục đã chọn';
            } elseif ($search !== '') {
                $sectionTitle = 'KẾT QUẢ TÌM KIẾM: "' . htmlspecialchars($search) . '"';
            } else {
                $category = $this->danhMucModel->findById($madm);
                $sectionTitle = 'DANH MỤC: ' . htmlspecialchars($category['tendm'] ?? '');
            }
        } else {
            // Lấy 12 sản phẩm mới nhất
            $products = $this->sanPhamModel->getLatest(12);
        }
        
        // Render view
        $this->view('home/index', [
            'danhmucs' => $danhmucs,
            'products' => $products,
            'search' => $search,
            'madm' => $madm,
            'sectionTitle' => $sectionTitle,
            'page_title' => 'Trang Chủ',
            'active_nav' => 'trangchu',
            'showHero' => ($search === '' && ($madm === '' || $madm == '0')),
            'formAction' => BASE_URL . '/app.php/',
        ]);
    }

    /**
     * Trang tất cả sản phẩm
     */
    public function sanpham() {
        $danhmucs = $this->danhMucModel->getAllCategories();

        $search = $this->input('search', '');
        $madm   = $this->input('madm', '0');

        if ($search !== '' || ($madm !== '' && $madm != '0')) {
            $products = $this->sanPhamModel->search($search, $madm);

            if ($search !== '' && $madm != '0') {
                $sectionTitle = 'KẾT QUẢ: "' . htmlspecialchars($search) . '" trong danh mục đã chọn';
            } elseif ($search !== '') {
                $sectionTitle = 'KẾT QUẢ TÌM KIẾM: "' . htmlspecialchars($search) . '"';
            } else {
                $category = $this->danhMucModel->findById($madm);
                $sectionTitle = 'DANH MỤC: ' . htmlspecialchars($category['tendm'] ?? '');
            }
        } else {
            $products = $this->sanPhamModel->getAllWithCategory();
            $sectionTitle = 'TẤT CẢ SẢN PHẨM';
        }

        $this->view('home/index', [
            'danhmucs'    => $danhmucs,
            'products'    => $products,
            'search'      => $search,
            'madm'        => $madm,
            'sectionTitle' => $sectionTitle,
            'page_title'  => 'Tất cả Sản phẩm',
            'active_nav'  => 'trangchu',
            'showHero'    => false,
            'formAction'  => BASE_URL . '/app.php/sanpham',
        ]);
    }

    /**
     * Trang giới thiệu
     */
    public function about() {
        $this->view('home/about', [
            'page_title' => 'Giới thiệu',
            'active_nav' => ''
        ]);
    }
    
    /**
     * Trang liên hệ
     */
    public function contact() {
        $this->view('home/contact', [
            'page_title' => 'Liên hệ',
            'active_nav' => ''
        ]);
    }
}
