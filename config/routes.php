<?php
/**
 * Routes - Định nghĩa tất cả routes cho ứng dụng
 * 
 * Cú pháp:
 *   $router->get('/path', 'Controller@action');
 *   $router->post('/path', 'Controller@action');
 *   $router->get('/path/{id}', 'Controller@action');  // Route với param
 */

// ===================== HOME =====================
$router->get('/', 'HomeController@index');
$router->get('/home', 'HomeController@index');

// ===================== AUTH =====================
$router->get('/logout',             'AuthController@logout');
$router->post('/api/auth/login',    'AuthController@apiLogin');    // JSON login
$router->post('/api/auth/register', 'AuthController@apiRegister'); // JSON register

// ===================== WEB PAGES =====================
$router->get('/sanpham',            'HomeController@sanpham');
$router->get('/giohang',            'GioHangController@index');
$router->get('/thanhtoan',                  'ThanhToanController@index');
$router->get('/thanhtoan/detail/{matt}',    'ThanhToanController@show');
$router->get('/thanhtoan/edit/{matt}',      'ThanhToanController@edit');
$router->post('/thanhtoan/update',          'ThanhToanController@update');
$router->get('/thanhtoan/delete/{matt}',    'ThanhToanController@delete');
$router->get('/thongke',            'ThongKeController@index');
$router->get('/vanchuyen',          'VanChuyenController@index');

// WEB: ĐƠN HÀNG
$router->get('/donhang',            'DonHangController@index');     // Danh sách đơn hàng
$router->get('/donhang/create',     'DonHangController@create');    // Form tạo đơn
$router->post('/donhang',           'DonHangController@store');     // Xử lý tạo đơn
$router->get('/donhang/{id}',       'DonHangController@show');      // Chi tiết đơn
$router->get('/donhang/{id}/edit',  'DonHangController@edit');      // Form sửa đơn
$router->post('/donhang/{id}',      'DonHangController@update');    // Xử lý sửa đơn
$router->get('/donhang/{id}/delete','DonHangController@delete');    // Xóa đơn

// ===================== SẢN PHẨM =====================
$router->get('/api/sanpham',           'SanPhamController@index');   // ?keyword=, ?madm=, ?latest=N
$router->get('/api/sanpham/{id}',      'SanPhamController@show');    // Chi tiết 1 sản phẩm
$router->post('/api/sanpham',          'SanPhamController@store');   // Thêm mới
$router->put('/api/sanpham/{id}',      'SanPhamController@update');  // Cập nhật
$router->delete('/api/sanpham/{id}',   'SanPhamController@destroy'); // Xóa

// ===================== DANH MỤC =====================
$router->get('/api/danhmuc',           'DanhMucController@index');   // Lấy danh sách (hỗ trợ ?keyword=)
$router->get('/api/danhmuc/{id}',      'DanhMucController@show');    // Lấy chi tiết 1 danh mục
$router->post('/api/danhmuc',          'DanhMucController@store');   // Thêm mới
$router->put('/api/danhmuc/{id}',      'DanhMucController@update');  // Cập nhật
$router->delete('/api/danhmuc/{id}',   'DanhMucController@destroy'); // Xóa

// ===================== THÔNG SỐ =====================
$router->get('/api/thongso',           'ThongSoController@apiIndex');   // ?masp=X
$router->get('/api/thongso/{id}',      'ThongSoController@apiShow');    // Chi tiết 1 thông số
$router->post('/api/thongso',          'ThongSoController@apiStore');   // Thêm mới
$router->put('/api/thongso/{id}',      'ThongSoController@apiUpdate');  // Cập nhật
$router->delete('/api/thongso/{id}',   'ThongSoController@apiDestroy'); // Xóa

// ===================== GIỎ HÀNG =====================
$router->get('/api/giohang',              'GioHangController@apiGet');    // Lấy giỏ hàng
$router->post('/api/giohang',             'GioHangController@apiAdd');    // Thêm sản phẩm
$router->put('/api/giohang/{masp}',       'GioHangController@apiUpdate'); // Cập nhật SL
$router->delete('/api/giohang/{masp}',    'GioHangController@apiRemove'); // Xóa 1 sản phẩm
$router->delete('/api/giohang',           'GioHangController@apiClear');  // Xóa toàn bộ

// ===================== ĐƠN HÀNG =====================
$router->get('/api/donhang',           'DonHangController@apiIndex');   // ?keyword=, ?trangthai=
$router->get('/api/donhang/{id}',      'DonHangController@apiShow');    // Chi tiết 1 đơn hàng
$router->post('/api/donhang',          'DonHangController@apiStore');   // Thêm mới
$router->put('/api/donhang/{id}',      'DonHangController@apiUpdate');  // Cập nhật
$router->delete('/api/donhang/{id}',   'DonHangController@apiDestroy'); // Xóa

// ===================== KHÁCH HÀNG =====================
$router->get('/api/khachhang',         'KhachHangController@apiIndex');   // ?keyword=
$router->get('/api/khachhang/{id}',    'KhachHangController@apiShow');    // Chi tiết 1 KH
$router->post('/api/khachhang',        'KhachHangController@apiStore');   // Thêm mới
$router->put('/api/khachhang/{id}',    'KhachHangController@apiUpdate');  // Cập nhật
$router->delete('/api/khachhang/{id}', 'KhachHangController@apiDestroy'); // Xóa

// ===================== NHÂN VIÊN =====================
$router->get('/api/nhanvien',         'NhanVienController@apiIndex');   // ?keyword=
$router->get('/api/nhanvien/{id}',    'NhanVienController@apiShow');    // Chi tiết 1 NV
$router->post('/api/nhanvien',        'NhanVienController@apiStore');   // Thêm mới
$router->put('/api/nhanvien/{id}',    'NhanVienController@apiUpdate');  // Cập nhật
$router->delete('/api/nhanvien/{id}', 'NhanVienController@apiDestroy'); // Xóa

// ===================== THANH TOÁN =====================
$router->get('/api/thanhtoan',           'ThanhToanController@apiIndex');   // ?madh=
$router->get('/api/thanhtoan/{id}',      'ThanhToanController@apiShow');    // Chi tiết 1 thanh toán
$router->post('/api/thanhtoan',          'ThanhToanController@apiStore');   // Thêm mới
$router->put('/api/thanhtoan/{id}',      'ThanhToanController@apiUpdate');  // Cập nhật
$router->delete('/api/thanhtoan/{id}',   'ThanhToanController@apiDestroy'); // Xóa

// ===================== PROFILE =====================
$router->get('/api/profile',                    'ProfileController@apiGet');            // Lấy thông tin cá nhân
$router->put('/api/profile',                    'ProfileController@apiUpdate');         // Cập nhật thông tin
$router->post('/api/profile/change-password',   'ProfileController@apiChangePassword'); // Đổi mật khẩu

// ===================== THỐNG KÊ =====================
$router->get('/api/thongke',           'ThongKeController@apiIndex');     // Tổng quan
$router->get('/api/thongke/revenue',   'ThongKeController@apiRevenue');   // ?year= / ?start_date=&end_date=
$router->get('/api/thongke/products',  'ThongKeController@apiProducts');  // ?limit=
$router->get('/api/thongke/customers', 'ThongKeController@apiCustomers'); // ?limit=
$router->get('/api/thongke/orders',    'ThongKeController@apiOrders');    // Theo trạng thái
$router->get('/api/thongke/category',  'ThongKeController@apiCategory');  // Theo danh mục

// ===================== VẬN CHUYỂN =====================
$router->get('/api/vanchuyen',         'VanChuyenController@apiIndex');   // ?madh=
$router->post('/api/vanchuyen',        'VanChuyenController@apiStore');   // Thêm mới
$router->get('/api/vanchuyen/{id}',    'VanChuyenController@apiShow');    // Chi tiết
$router->put('/api/vanchuyen/{id}',    'VanChuyenController@apiUpdate');  // Cập nhật
$router->delete('/api/vanchuyen/{id}', 'VanChuyenController@apiDestroy'); // Xóa


