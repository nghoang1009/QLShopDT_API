<?php
/**
 * header.php — Dùng chung cho tất cả các trang
 *
 * Cách dùng ở mỗi trang:
 *   session_start();
 *   require_once($_SERVER['DOCUMENT_ROOT'] . '/header.php');
 *
 * Biến tùy chọn (khai báo TRƯỚC khi require header):
 *   $page_title = 'Tên trang';    // tiêu đề tab trình duyệt
 *   $active_nav = 'trangchu';     // key để highlight menu (xem danh sách bên dưới)
 *   $extra_css  = '<link ...>';   // CSS bổ sung riêng của trang
 *
 * Các key active_nav hợp lệ:
 *   trangchu | sanpham | danhmuc | khachhang | nhanvien
 *   thongke  | donhang | vanchuyen | giohang  | thanhtoan
 */

// ── Kết nối DB nếu chưa có ─────────────────────────────────────────────
if (!isset($conn)) {
     include($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');
}

// ── Lấy role người dùng ────────────────────────────────────────────────
$role   = -1;
$chucvu = '';
if (isset($_SESSION['username'])) {
    if (isset($_SESSION['role'])) {
        $role = (int)$_SESSION['role'];
    } else {
        $u   = mysqli_real_escape_string($conn, $_SESSION['username']);
        $res = mysqli_query($conn, "SELECT role FROM taikhoan WHERE tentk = '$u'");
        if ($res && $r = mysqli_fetch_assoc($res))
            $role = (int)$r['role'];
    }

    switch ($role) {
        case 1: $chucvu = 'Admin';      break;
        case 2: $chucvu = 'Nhân viên';  break;
        case 0: $chucvu = 'Khách hàng'; break;
    }
}

// ── Giá trị mặc định ────────────────────────────────────────────────────
if (!isset($page_title)) $page_title = 'PhoneShop';
if (!isset($active_nav)) $active_nav = '';
if (!isset($extra_css))  $extra_css  = '';

// ── Helper in class active ──────────────────────────────────────────────
function nav_active(string $key): string {
    global $active_nav;
    return $active_nav === $key ? ' class="active"' : '';
}

// ── Đường dẫn gốc (để link CSS/assets luôn đúng dù ở subfolder) ────────
$base_url = '/QLShopDT_API';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> — PhoneShop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/main.css">
    <?php echo $extra_css; ?>
</head>
<body>

<header class="ps-header">
    <div class="ps-header-top">

        <a href="/QLShopDT_API/views/trangchu.php" class="ps-logo">PHONE<span>SHOP</span></a>

        <form class="ps-search" method="GET" action="/QLShopDT_API/views/trangchu.php">
            <input type="text" name="search" placeholder="Tìm kiếm sản phẩm..."
                   value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit"><i class="fa fa-search"></i></button>
        </form>

        <div class="ps-user-actions">
            <?php if (isset($_SESSION['username'])): ?>
                <div class="ps-avatar-wrap">
                    <input type="checkbox" id="ps-dropdown-toggle" class="ps-dropdown-toggle">
                    <label for="ps-dropdown-toggle" class="ps-avatar">
                        <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                    </label>
                    <div class="ps-dropdown">
                        <div class="ps-dropdown-info">
                            <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                            <span><?php echo $chucvu; ?></span>
                        </div>
                        <a href="/QLShopDT_API/profile.php"><i class="fa fa-user"></i> Thông tin cá nhân</a>
                        <a href="/QLShopDT_API/views/donhang/donhang.php"><i class="fa fa-box"></i> Đơn hàng của tôi</a>
                        <a href="/QLShopDT_API/views/logout.php" class="ps-logout"><i class="fa fa-sign-out-alt"></i> Đăng xuất</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="/QLShopDT_API/views/login.php" class="ps-btn-outline"><i class="fa fa-user"></i> Đăng nhập</a>
                <a href="/QLShopDT_API/views/register.php" class="ps-btn-fill"><i class="fa fa-key"></i> Đăng ký</a>
            <?php endif; ?>
        </div>
    </div>

    <nav class="ps-nav">
        <a href="/QLShopDT_API/views/trangchu.php"<?php echo nav_active('trangchu'); ?>>
            <i class="fa fa-home"></i> Trang chủ
        </a>
        <?php if ($role === 1 || $role === 2): ?>
            <a href="/QLShopDT_API/views/sanpham/sanpham.php"<?php echo nav_active('sanpham'); ?>>
                <i class="fa fa-mobile-alt"></i> Sản phẩm
            </a>
            <a href="/QLShopDT_API/views/danhmuc/danhmuc.php"<?php echo nav_active('danhmuc'); ?>>
                <i class="fa fa-list"></i> Danh mục
            </a>
            <a href="/QLShopDT_API/views/khachhang/khachhang.php"<?php echo nav_active('khachhang'); ?>>
                <i class="fa fa-users"></i> Khách hàng
            </a>
        <?php endif; ?>
        <?php if ($role === 1): ?>
            <a href="/QLShopDT_API/views/nhanvien/nhanvien.php"<?php echo nav_active('nhanvien'); ?>>
                <i class="fa fa-user-tie"></i> Nhân viên
            </a>
            <a href="/QLShopDT_API/views/thongke/thongke.php"<?php echo nav_active('thongke'); ?>>
                <i class="fa fa-chart-bar"></i> Thống kê
            </a>
            
        <a href="/QLShopDT_API/views/donhang/donhang.php"<?php echo nav_active('donhang'); ?>>
            <i class="fa fa-archive"></i> Đơn hàng
        </a>
        <a href="/QLShopDT_API/views/vanchuyen/vanchuyen.php"<?php echo nav_active('vanchuyen'); ?>>
            <i class="fa fa-truck"></i> Giao hàng
        </a>
        <a href="/QLShopDT_API/views/giohang/giohang.php"<?php echo nav_active('giohang'); ?>>
            <i class="fa fa-shopping-cart"></i> Giỏ hàng
        </a>
        <a href="/QLShopDT_API/views/thanhtoan/thanhtoan.php"<?php echo nav_active('thanhtoan'); ?>>
            <i class="fa fa-credit-card"></i> Thanh toán
        </a>
        <?php endif; ?>
    </nav>
</header>

<!-- Nút giỏ hàng nổi -->
<a href="/QLShopDT_API/views/giohang/giohang.php" class="ps-cart-fab">
    <i class="fa fa-shopping-cart"></i>
</a>