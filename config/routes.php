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
$router->get('/giohang', 'GioHangController@index');
$router->post('/giohang/add', 'GioHangController@add');
$router->post('/giohang/update', 'GioHangController@update');
$router->get('/giohang/remove/{masp}', 'GioHangController@remove');
$router->get('/giohang/clear', 'GioHangController@clear');

// ===================== ĐƠN HÀNG =====================
$router->get('/donhang', 'DonHangController@index');
$router->get('/donhang/detail/{madh}', 'DonHangController@show');
$router->get('/donhang/create', 'DonHangController@create');
$router->post('/donhang/store', 'DonHangController@store');
$router->get('/donhang/edit/{madh}', 'DonHangController@edit');
$router->post('/donhang/update', 'DonHangController@update');
$router->get('/donhang/delete/{madh}', 'DonHangController@delete');
$router->get('/donhang/cancel/{madh}', 'DonHangController@cancel');
$router->post('/donhang/update-status', 'DonHangController@updateStatus');

// API RESTful đơn hàng
$router->get('/api/donhang', 'DonHangController@apiIndex');
$router->post('/api/donhang', 'DonHangController@apiStore');
$router->get('/api/donhang/{id}', 'DonHangController@apiShow');
$router->put('/api/donhang/{id}', 'DonHangController@apiUpdate');
$router->delete('/api/donhang/{id}', 'DonHangController@apiDestroy');

// ===================== THANH TOÁN =====================
$router->get('/thanhtoan', 'ThanhToanController@index');
$router->get('/thanhtoan/detail/{matt}', 'ThanhToanController@show');
$router->get('/thanhtoan/add', 'ThanhToanController@create');
$router->post('/thanhtoan/store', 'ThanhToanController@store');
$router->get('/thanhtoan/edit/{matt}', 'ThanhToanController@edit');
$router->post('/thanhtoan/update', 'ThanhToanController@update');
$router->get('/thanhtoan/delete/{matt}', 'ThanhToanController@delete');
$router->get('/thanhtoan/quick-pay/{madh}', 'ThanhToanController@quickPay');
$router->post('/thanhtoan/process-quick-pay', 'ThanhToanController@processQuickPay');

// ===================== PROFILE =====================
$router->get('/profile', 'ProfileController@index');
$router->get('/profile/edit', 'ProfileController@edit');
$router->post('/profile/update', 'ProfileController@update');
$router->get('/profile/change-password', 'ProfileController@changePasswordForm');
$router->post('/profile/change-password', 'ProfileController@changePassword');

// ===================== THỐNG KÊ =====================
$router->get('/thongke', 'ThongKeController@index');
$router->get('/thongke/revenue', 'ThongKeController@revenue');
$router->get('/thongke/products', 'ThongKeController@products');
$router->get('/thongke/customers', 'ThongKeController@customers');
$router->get('/thongke/orders', 'ThongKeController@orders');
$router->get('/thongke/chart-data', 'ThongKeController@chartData');
$router->get('/thongke/export', 'ThongKeController@export');

// ===================== VẬN CHUYỂN =====================
$router->get('/vanchuyen', 'VanChuyenController@index');
$router->get('/vanchuyen/detail/{mavc}', 'VanChuyenController@show');
$router->get('/vanchuyen/add', 'VanChuyenController@create');
$router->post('/vanchuyen/store', 'VanChuyenController@store');
$router->get('/vanchuyen/edit/{mavc}', 'VanChuyenController@edit');
$router->post('/vanchuyen/update', 'VanChuyenController@update');
$router->get('/vanchuyen/delete/{mavc}', 'VanChuyenController@delete');
$router->get('/vanchuyen/confirm/{mavc}', 'VanChuyenController@confirm');

// ===================== NHÂN VIÊN =====================
$router->get('/nhanvien', 'NhanVienController@index');
$router->get('/nhanvien/detail/{manv}', 'NhanVienController@show');
$router->get('/nhanvien/add', 'NhanVienController@create');
$router->post('/nhanvien/store', 'NhanVienController@store');
$router->get('/nhanvien/edit/{manv}', 'NhanVienController@edit');
$router->post('/nhanvien/update', 'NhanVienController@update');
$router->get('/nhanvien/delete/{manv}', 'NhanVienController@delete');
$router->get('/nhanvien/search', 'NhanVienController@search');

// ===================== KHÁCH HÀNG =====================
$router->get('/khachhang', 'KhachHangController@index');
$router->get('/khachhang/detail/{makh}', 'KhachHangController@show');
$router->get('/khachhang/add', 'KhachHangController@create');
$router->post('/khachhang/store', 'KhachHangController@store');
$router->get('/khachhang/edit/{makh}', 'KhachHangController@edit');
$router->post('/khachhang/update', 'KhachHangController@update');
$router->get('/khachhang/delete/{makh}', 'KhachHangController@delete');
$router->get('/khachhang/search', 'KhachHangController@search');
