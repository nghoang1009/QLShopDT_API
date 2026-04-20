<?php
/**
 * Tạo Đơn hàng mới - views/donhang/donhang_create.php
 * Khách hàng: xác nhận giỏ hàng
 * Admin/Staff: tạo đơn từ form
 */

require_once __DIR__ . '/../../includes/api_helper.php';

// Biến từ controller
$role = $role ?? $_SESSION['role'] ?? 0;
$cartItems = $cartItems ?? [];
$total = $total ?? 0;
$customer = $customer ?? null;
$customers = $customers ?? [];

$extra_css  = '<link rel="stylesheet" href="/QLShopDT_API/assets/css/donhang.css">
<link rel="stylesheet" href="/QLShopDT_API/assets/css/footer.css">';

include "../../includes/header.php";

function fmtVnd($n) {
    return number_format((float)$n, 0, ',', '.') . ' ₫';
}
?>

<!-- Page Header -->
<div class="dh-page-header">
    <div class="dh-page-header-inner">
        <div>
            <h1 class="dh-page-title">
                <?php if ($role == 0): ?>
                    <i class="fas fa-shopping-cart"></i> Đặt Hàng
                <?php else: ?>
                    <i class="fas fa-receipt"></i> Tạo Đơn Hàng
                <?php endif; ?>
            </h1>
        </div>
        <a href="/QLShopDT_API/donhang" class="dh-cancel-btn">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>
</div>

<main class="container">

    <?php if ($role == 0): ?>
        <!-- ==================== KHÁCH HÀNG: ĐẶT HÀNG TỪ GIỎ ==================== -->
        
        <?php if (empty($cartItems)): ?>
            <div class="dh-empty" style="text-align: center; padding: 60px 20px;">
                <i class="fas fa-shopping-cart" style="font-size: 48px; color: #ccc; margin-bottom: 20px; display: block;"></i>
                <h2>Giỏ hàng trống</h2>
                <p>Vui lòng thêm sản phẩm trước khi đặt hàng</p>
                <a href="/QLShopDT_API/" style="display: inline-block; margin-top: 20px; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px;">
                    <i class="fas fa-shopping-bag"></i> Tiếp tục mua sắm
                </a>
            </div>
        <?php else: ?>
            <!-- Thông tin khách hàng -->
            <div class="dh-form-card" style="margin-bottom: 20px;">
                <div class="dh-form-card-header">
                    <div class="dh-form-icon"><i class="fas fa-user"></i></div>
                    <h2>Thông tin khách hàng</h2>
                </div>
                <div class="dh-form-body">
                    <?php if ($customer): ?>
                        <div style="display: grid; grid-template-columns: 150px 1fr; gap: 20px; line-height: 2;">
                            <span><strong>Tên:</strong></span>
                            <span><?= e($customer['tenkh']) ?></span>
                            
                            <span><strong>Địa chỉ:</strong></span>
                            <span><?= e($customer['diachi']) ?></span>
                            
                            <span><strong>SĐT:</strong></span>
                            <span><?= e($customer['sdt']) ?></span>
                        </div>
                    <?php else: ?>
                        <p style="color: #ef4444;"><i class="fas fa-exclamation-circle"></i> Không tìm thấy thông tin khách hàng</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Danh sách sản phẩm -->
            <div class="dh-form-card" style="margin-bottom: 20px;">
                <div class="dh-form-card-header">
                    <div class="dh-form-icon"><i class="fas fa-list"></i></div>
                    <h2>Danh sách sản phẩm</h2>
                </div>
                <div class="dh-form-body">
                    <table border="1" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f5f5f5;">
                                <th style="padding: 10px; text-align: left;">#</th>
                                <th style="padding: 10px; text-align: left;">Tên sản phẩm</th>
                                <th style="padding: 10px; text-align: left;">Hãng</th>
                                <th style="padding: 10px; text-align: center;">SL</th>
                                <th style="padding: 10px; text-align: right;">Đơn giá</th>
                                <th style="padding: 10px; text-align: right;">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cartItems as $i => $item): ?>
                            <tr>
                                <td style="padding: 10px;"><?= $i + 1 ?></td>
                                <td style="padding: 10px;"><?= e($item['tensp']) ?></td>
                                <td style="padding: 10px;"><?= e($item['hang']) ?></td>
                                <td style="padding: 10px; text-align: center;"><?= (int)$item['sl'] ?></td>
                                <td style="padding: 10px; text-align: right;"><?= fmtVnd($item['gia']) ?></td>
                                <td style="padding: 10px; text-align: right; font-weight: bold;"><?= fmtVnd($item['thanhtien']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr style="background: #f9f9f9; font-weight: bold;">
                                <td colspan="5" style="padding: 10px; text-align: right;">Tổng cộng:</td>
                                <td style="padding: 10px; text-align: right; color: #007bff; font-size: 16px;">
                                    <?= fmtVnd($total) ?>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Form xác nhận -->
            <div class="dh-form-card">
                <div class="dh-form-body">
                    <form method="POST" action="/QLShopDT_API/donhang">
                        <?php echo csrf_field(); ?>
                        
                        <div class="dh-form-actions">
                            <button type="submit" class="dh-submit-btn" style="background: #28a745;">
                                <i class="fas fa-check-circle"></i> Xác nhận đặt hàng
                            </button>
                            <a href="/QLShopDT_API/giohang" class="dh-cancel-btn">
                                <i class="fas fa-arrow-left"></i> Quay lại giỏ hàng
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <!-- ==================== ADMIN/STAFF: TẠO ĐƠN TỪ FORM ==================== -->
        
        <div class="dh-form-wrap">
            <div class="dh-form-card">
                <div class="dh-form-card-header">
                    <div class="dh-form-icon"><i class="fas fa-receipt"></i></div>
                    <h2>Thông tin đơn hàng</h2>
                </div>
                <div class="dh-form-body">
                    <form method="POST" action="/QLShopDT_API/donhang">
                        <?php echo csrf_field(); ?>

                        <div class="dh-form-group">
                            <label for="txt_makh" class="dh-label">
                                Khách hàng <span class="dh-required">*</span>
                            </label>
                            <select id="txt_makh" name="makh" class="dh-input" required>
                                <option value="">-- Chọn khách hàng --</option>
                                <?php if (!empty($customers)): ?>
                                    <?php foreach ($customers as $kh): ?>
                                        <option value="<?= (int)$kh['makh'] ?>">
                                            <?= e($kh['tenkh']) ?> (<?= e($kh['sdt']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="dh-form-group">
                            <label for="num_trigia" class="dh-label">
                                Tổng tiền (VNĐ) <span class="dh-required">*</span>
                            </label>
                            <input type="number" id="num_trigia" name="trigia" 
                                   placeholder="0" class="dh-input" 
                                   min="0" step="1000" value="0" required>
                        </div>

                        <div class="dh-form-actions">
                            <button type="submit" class="dh-submit-btn">
                                <i class="fas fa-plus"></i> Tạo đơn hàng
                            </button>
                            <a href="/QLShopDT_API/donhang" class="dh-cancel-btn">
                                <i class="fas fa-times"></i> Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    <?php endif; ?>

</main>

<?php include "../../includes/footer.php"; ?>