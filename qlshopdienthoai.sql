-- Database: qlshopdienthoai
DROP DATABASE IF EXISTS qlshopdienthoai;
CREATE DATABASE qlshopdienthoai CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci;
USE qlshopdienthoai;

-- Bảng danh mục
CREATE TABLE danhmuc (
  madm INT AUTO_INCREMENT PRIMARY KEY,
  tendm VARCHAR(50) NOT NULL
);

-- Bảng tài khoản
CREATE TABLE taikhoan (
  matk INT AUTO_INCREMENT PRIMARY KEY,
  tentk VARCHAR(50) NOT NULL,
  mk VARCHAR(255) NOT NULL,
  role INT NOT NULL COMMENT '0: Khách hàng, 1: Admin, 2: Nhân viên'
);

-- Bảng khách hàng
CREATE TABLE khachhang (
  makh INT PRIMARY KEY,
  tenkh VARCHAR(50) NOT NULL,
  diachi VARCHAR(50),
  sdt VARCHAR(30),
  FOREIGN KEY (makh) REFERENCES taikhoan(matk) ON DELETE CASCADE
);

-- Bảng nhân viên
CREATE TABLE nhanvien (
  manv INT PRIMARY KEY,
  tennv VARCHAR(50) NOT NULL,
  diachi VARCHAR(50) NOT NULL,
  sdt VARCHAR(30) NOT NULL,
  ns DATE NOT NULL,
  FOREIGN KEY (manv) REFERENCES taikhoan(matk) ON DELETE CASCADE
);

-- Bảng sản phẩm
CREATE TABLE sanpham (
  masp INT AUTO_INCREMENT PRIMARY KEY,
  tensp VARCHAR(50) NOT NULL,
  gia DOUBLE NOT NULL,
  sl INT NOT NULL,
  hang VARCHAR(30) NOT NULL,
  baohanh INT NOT NULL,
  ghichu VARCHAR(255) NOT NULL,
  hinhanh VARCHAR(255) NOT NULL,
  madm INT NOT NULL,
  FOREIGN KEY (madm) REFERENCES danhmuc(madm) ON DELETE CASCADE
);

-- Bảng thông số
CREATE TABLE thongso (
  mats INT AUTO_INCREMENT PRIMARY KEY,
  tents VARCHAR(50) NOT NULL,
  masp INT NOT NULL,
  giatri VARCHAR(255) NOT NULL,
  FOREIGN KEY (masp) REFERENCES sanpham(masp) ON DELETE CASCADE
);

-- Bảng giỏ hàng
CREATE TABLE giohang (
  magio INT AUTO_INCREMENT PRIMARY KEY,
  makh INT NOT NULL,
  FOREIGN KEY (makh) REFERENCES khachhang(makh) ON DELETE CASCADE
);

-- Bảng giỏ hàng item
CREATE TABLE giohang_item (
  maitem INT AUTO_INCREMENT PRIMARY KEY,
  magio INT NOT NULL,
  masp INT NOT NULL,
  sl INT NOT NULL,
  FOREIGN KEY (magio) REFERENCES giohang(magio) ON DELETE CASCADE,
  FOREIGN KEY (masp) REFERENCES sanpham(masp) ON DELETE CASCADE
);

-- Bảng đơn hàng
CREATE TABLE donhang (
  madh INT AUTO_INCREMENT PRIMARY KEY,
  makh INT NOT NULL,
  ngaydat DATETIME NOT NULL,
  manv INT NOT NULL,
  trigia DOUBLE NOT NULL,
  FOREIGN KEY (makh) REFERENCES khachhang(makh) ON DELETE CASCADE,
  FOREIGN KEY (manv) REFERENCES nhanvien(manv) ON DELETE CASCADE
);

-- Bảng chi tiết đơn hàng
CREATE TABLE chitietdonhang (
  madh INT NOT NULL,
  masp INT NOT NULL,
  sl INT NOT NULL,
  PRIMARY KEY (madh, masp),
  FOREIGN KEY (madh) REFERENCES donhang(madh) ON DELETE CASCADE,
  FOREIGN KEY (masp) REFERENCES sanpham(masp) ON DELETE CASCADE
);

-- Bảng thanh toán
CREATE TABLE thanhtoan (
  matt INT AUTO_INCREMENT PRIMARY KEY,
  madh INT NOT NULL,
  phuongthuc VARCHAR(50) NOT NULL COMMENT 'Tiền mặt, Chuyển khoản, Thẻ, Ví điện tử',
  ngaythanhtoan DATETIME NOT NULL,
  sotien DOUBLE NOT NULL,
  trangthai VARCHAR(30) NOT NULL COMMENT 'Chờ xác nhận, Đã thanh toán, Thất bại',
  ghichu VARCHAR(255),
  FOREIGN KEY (madh) REFERENCES donhang(madh) ON DELETE CASCADE
);

-- Bảng vận chuyển
CREATE TABLE vanchuyen (
  mavc INT AUTO_INCREMENT PRIMARY KEY,
  madh INT NOT NULL,
  makh INT NOT NULL,
  ngaygiao DATE NOT NULL,
  FOREIGN KEY (madh) REFERENCES donhang(madh) ON DELETE CASCADE,
  FOREIGN KEY (makh) REFERENCES khachhang(makh) ON DELETE CASCADE
);

-- Dữ liệu mẫu
INSERT INTO danhmuc (tendm) VALUES 
('Điện thoại'),
('Laptop'),
('Tai nghe'),
('Sạc dự phòng');

INSERT INTO taikhoan (tentk, mk, role) VALUES
('admin', '123', 1),
('Hùng', '123456', 2),
('theanh', '123456', 0),
('nguyenvana', '123456', 0),
('tranthib', '123456', 0),
('phamvanc', '123456', 0),
('lethid', '123456', 0),
('nhanvien1', '123456', 2),
('nhanvien2', '123456', 2);

INSERT INTO khachhang (makh, tenkh, diachi, sdt) VALUES
(3, 'theanh', 'a', '1'),
(4, 'Nguyễn Văn A', '123 Nguyễn Huệ, Q1, TP.HCM', '0901234567'),
(5, 'Trần Thị B', '456 Lê Lợi, Q3, TP.HCM', '0912345678'),
(6, 'Phạm Văn C', '789 Trần Hưng Đạo, Hà Nội', '0923456789'),
(7, 'Lê Thị D', '321 Hai Bà Trưng, Đà Nẵng', '0934567890');

INSERT INTO nhanvien (manv, tennv, diachi, sdt, ns) VALUES
(2, 'Hùng', 'a', '0', '2025-12-01'),
(8, 'Hoàng Văn E', '111 Võ Văn Tần, Q3, TP.HCM', '0945678901', '1995-05-15'),
(9, 'Vũ Thị F', '222 Điện Biên Phủ, Hà Nội', '0956789012', '1998-08-20');

INSERT INTO sanpham (tensp, gia, sl, hang, baohanh, ghichu, hinhanh, madm) VALUES
('Samsung S22 Ultra', 15000000, 495, 'Samsung', 12, 'Samsung S22 Ultra 256Gb', 'galaxy-s22-ultra-burgundy.jpg.webp', 1),
('Iphone 16e', 17500000, 189, 'Apple', 12, 'Iphone 16e 256GB - Trắng', 'iphone-16e-trang.jpg.webp', 1),
('IPhone 17', 24690000, 97, 'Apple', 12, 'IPhone 17 Tím', 'iphone-17-tim.jpg.webp', 1),
('IPhone 12', 6800000, 99, 'Apple', 12, 'IPhone 12 Pro Max Xanh', 'iphone-12-pro-max-xanh.jpg.webp', 1),
('iPhone 16 Pro', 29990000, 50, 'Apple', 12, 'iPhone 16 Pro 256GB - Titan Tự Nhiên', 'iphone-16-pro-titan-tu-nhien.jpg.webp', 1),
('iPhone 15 Plus', 19990000, 80, 'Apple', 12, 'iPhone 15 Plus 128GB - Xanh Dương', 'iphone-15-plus-xanh-duong.jpg.webp', 1),
('iPhone 14 Pro', 24990000, 45, 'Apple', 12, 'iPhone 14 Pro 256GB - Tím', 'iphone-14pro-tim-chinh-thuc.png.webp', 1),
('Samsung Galaxy S24 Ultra', 27990000, 60, 'Samsung', 12, 'Samsung S24 Plus Xám Marble', 'samsung-galaxy-s24-plus-xam-marble.jpg.webp', 1),
('Samsung Galaxy Z Fold 5', 35990000, 30, 'Samsung', 12, 'Samsung Z Fold 5 512GB - Màu kem', 'samsung-galaxy-z-fold-5-cream.jpg.webp', 1),
('Samsung Galaxy A54', 9990000, 120, 'Samsung', 12, 'Samsung A54 5G 128GB - Trắng', 'samsung a54 trang.png', 1),
('Xiaomi 14 Civi', 21990000, 40, 'Xiaomi', 12, 'Xiaomi 14 Civi 5G 512GB - Xanh lá', 'xiaomi-14-civi-5g-xanh-la.jpg.webp', 1),
('Xiaomi 13 Ultra', 7990000, 150, 'Xiaomi', 12, 'Xiaomi 13 Ultra 256GB - Xanh Lá', 'xiaomi-13-ultra-xanh.jpg.webp', 1),
('OPPO Find N3 Flip', 24990000, 25, 'OPPO', 12, 'OPPO Find N3 Flip 256GB - Hồng', 'oppo-find-n3-flip-hong.jpg.webp', 1),
('OPPO Reno 14', 10990000, 90, 'OPPO', 12, 'OPPO Reno 14 5G 256GB - Tím', 'oppo-reno14-pro-tim.jpg.webp', 1),
('Vivo Y29', 9990000, 70, 'Vivo', 12, 'Vivo Y29 256GB - Trắng', 'vivo-y29-trang.jpg.webp', 1),
('Realme 11', 8990000, 85, 'Realme', 12, 'Realme 11 256GB - Vàng', 'realme-11-vang.jpg.webp', 1),
('Realme 14 Pro Plus', 23990000, 35, 'Google', 12, 'Realme Pro Plus 256GB - Trắng mạ vàng', 'realme-14-pro-plus-trang-ma-vang.jpg.webp', 1),
('Realme 9 Pro Plus', 5990000, 65, 'Nokia', 12, 'Realme 9 Pro Plus 128GB - Xanh bình minh', 'realme-9-pro-plus-xanh-binh-minh.jpg.webp', 1),
('Realme GT5', 19990000, 45, 'OnePlus', 12, 'Realme GT5 256GB - Xanh Lá', 'realme-gt5-xanh.jpg.webp', 1);

INSERT INTO thongso (tents, masp, giatri) VALUES
('RAM', 2, '8GB'),
('RAM', 3, '8GB'),
('RAM', 1, '12GB'),
('RAM', 5, '8GB'),
('Bộ nhớ', 5, '256GB'),
('Chip', 5, 'Apple A17 Pro'),
('RAM', 6, '6GB'),
('Bộ nhớ', 6, '128GB'),
('Chip', 6, 'Apple A16 Bionic'),
('RAM', 7, '8GB'),
('Bộ nhớ', 7, '256GB'),
('Chip', 7, 'Apple A16 Bionic'),
('RAM', 8, '12GB'),
('Bộ nhớ', 8, '512GB'),
('Chip', 8, 'Snapdragon 8 Gen 3'),
('RAM', 9, '12GB'),
('Bộ nhớ', 9, '512GB'),
('Chip', 9, 'Snapdragon 8 Gen 2'),
('RAM', 10, '8GB'),
('Bộ nhớ', 10, '128GB'),
('Chip', 10, 'Exynos 1380'),
('RAM', 11, '16GB'),
('Bộ nhớ', 11, '512GB'),
('Chip', 11, 'Snapdragon 8 Gen 3'),
('RAM', 12, '8GB'),
('Bộ nhớ', 12, '256GB'),
('Chip', 12, 'Snapdragon 7s Gen 2'),
('RAM', 13, '12GB'),
('Bộ nhớ', 13, '256GB'),
('Chip', 13, 'MediaTek Dimensity 9200'),
('RAM', 14, '8GB'),
('Bộ nhớ', 14, '256GB'),
('Chip', 14, 'MediaTek Dimensity 8200');

INSERT INTO giohang (makh) VALUES (3), (4), (5), (6), (7);

INSERT INTO giohang_item (magio, masp, sl) VALUES -- theanh - magio=1
(1, 2, 1),   -- Iphone 16e
(1, 10, 2),  -- Samsung Galaxy A54
(1, 16, 1);  -- Realme 11

INSERT INTO giohang_item (magio, masp, sl) VALUES -- (Nguyễn Văn A) - magio=2
(2, 5, 1),   -- iPhone 16 Pro
(2, 8, 1),   -- Samsung Galaxy S24 Ultra
(2, 13, 2);  -- OPPO Find N3 Flip

INSERT INTO giohang_item (magio, masp, sl) VALUES -- (Trần Thị B) - magio=3
(3, 4, 1),   -- IPhone 12
(3, 14, 1),  -- OPPO Reno 14
(3, 15, 3);  -- Vivo Y29

INSERT INTO giohang_item (magio, masp, sl) VALUES -- (Phạm Văn C) - magio=4
(4, 3, 1),   -- IPhone 17
(4, 9, 1),   -- Samsung Galaxy Z Fold 5
(4, 11, 2);  -- Xiaomi 14 Civi

INSERT INTO giohang_item (magio, masp, sl) VALUES -- (Lê Thị D) - magio=5
(5, 1, 2),   -- Samsung S22 Ultra
(5, 12, 1),  -- Xiaomi 13 Ultra
(5, 17, 1);  -- Realme 14 Pro Plus

INSERT INTO donhang (makh, ngaydat, manv, trigia) VALUES
(3, '2025-12-24', 2, 39690000),
(3, '2025-12-24', 2, 15000000),
(3, '2025-12-24', 2, 206490000),
(4, '2025-01-10', 8, 49980000),
(5, '2025-01-12', 8, 27990000),
(6, '2026-01-14', 9, 39980000),
(7, '2026-01-15', 9, 17990000),
(4, '2026-01-16', 8, 35990000),
(5, '2026-01-17', 9, 44980000),
(6, '2025-06-18', 8, 19990000),
(7, '2025-07-19', 9, 52980000),
(3, '2025-08-20', 2, 33980000),
(4, '2025-09-21', 8, 9990000),
(5, '2025-10-22', 9, 48980000);

INSERT INTO chitietdonhang (madh, masp, sl) VALUES
(1, 1, 1),
(1, 3, 1),
(2, 1, 1),
(3, 2, 10),
(3, 3, 1),
(3, 4, 1),
(4, 5, 1),
(4, 6, 1),
(5, 8, 1),
(6, 7, 1),
(6, 10, 1),
(7, 12, 2),
(8, 9, 1),
(9, 5, 1),
(9, 12, 2),
(10, 6, 1),
(11, 7, 1),
(11, 8, 1),
(12, 11, 1),
(12, 10, 1),
(12, 16, 1),
(13, 10, 1),
(14, 13, 1),
(14, 14, 2),
(14, 15, 1);

INSERT INTO vanchuyen (madh, makh, ngaygiao) VALUES
(2, 3, '2025-12-26'),
(4, 4, '2025-01-15'),
(5, 5, '2025-01-17'),
(6, 6, '2025-01-19');

INSERT INTO thanhtoan (madh, phuongthuc, ngaythanhtoan, sotien, trangthai, ghichu) VALUES
(4, 'Chuyển khoản', '2025-01-10 14:30:00', 49980000, 'Đã thanh toán', 'Thanh toán qua VietQR'),
(5, 'Tiền mặt', '2025-01-12 10:15:00', 27990000, 'Đã thanh toán', 'Thanh toán khi nhận hàng'),
(6, 'Thẻ', '2025-01-14 16:45:00', 39980000, 'Đã thanh toán', 'Thanh toán bằng thẻ Visa'),
(1, 'Chuyển khoản', '2025-12-24 09:20:00', 39690000, 'Đã thanh toán', 'Thanh toán qua MoMo'),
(2, 'Tiền mặt', '2025-12-24 11:45:00', 15000000, 'Đã thanh toán', 'Thanh toán tại cửa hàng'),
(3, 'Ví điện tử', '2025-12-24 15:30:00', 206490000, 'Đã thanh toán', 'Thanh toán qua ZaloPay'),
(7, 'Chuyển khoản', '2026-01-15 13:25:00', 17990000, 'Đã thanh toán', 'Thanh toán qua VietQR'),
(8, 'Ví điện tử', '2026-01-16 10:30:00', 35990000, 'Đã thanh toán', 'Thanh toán qua MoMo'),
(9, 'Thẻ', '2026-01-17 14:15:00', 44980000, 'Đã thanh toán', 'Thanh toán bằng thẻ Mastercard'),
(10, 'Chuyển khoản', '2025-02-18 09:45:00', 19990000, 'Đã thanh toán', 'Thanh toán qua VietQR'),
(11, 'Tiền mặt', '2025-04-19 16:20:00', 52980000, 'Đã thanh toán', 'Thanh toán khi nhận hàng'),
(12, 'Ví điện tử', '2025-11-20 11:00:00', 33980000, 'Đã thanh toán', 'Thanh toán qua ZaloPay'),
(13, 'Chuyển khoản', '2025-7-21 15:30:00', 9990000, 'Chờ xác nhận', 'Đang chờ xác nhận giao dịch'),
(14, 'Thẻ', '2025-10-22 10:45:00', 48980000, 'Đã thanh toán', 'Thanh toán bằng thẻ Visa'),
(4, 'Ví điện tử', '2026-01-10 17:00:00', 49980000, 'Chờ xác nhận', 'Đang xử lý thanh toán qua ShopeePay'),
(5, 'Thẻ', '2026-01-12 12:30:00', 27990000, 'Thất bại', 'Giao dịch bị từ chối - Thẻ hết hạn'),
(6, 'Tiền mặt', '2026-01-14 18:00:00', 39980000, 'Chờ xác nhận', 'Chờ khách hàng thanh toán khi nhận hàng');
