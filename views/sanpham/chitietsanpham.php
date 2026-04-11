<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require "../../includes/api_helper.php";

$masp = isset($_REQUEST['masp']) ? $_REQUEST['masp'] : 0;

// Lấy thông tin sản phẩm qua API
$result_sp = callSanphamAPI([
    "action" => "getone",
    "masp"   => $masp
]);

if (!($result_sp && $result_sp['status'])) {
    echo "<script>alert('Không tìm thấy sản phẩm!'); window.location.href='sanpham.php';</script>";
    exit();
}
$sp = $result_sp['data'];

// Lấy thông số kỹ thuật qua API
$result_ts = callThongsoAPI([
    "action" => "getall",
    "masp"   => $masp
]);
$thongsos = ($result_ts && $result_ts['status']) ? $result_ts['data'] : [];

// Lấy matk từ session
$matk = $_SESSION['matk'] ?? 0;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết sản phẩm - <?php echo htmlspecialchars($sp['tensp']); ?></title>
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/chitietsanpham.css">
</head>
<body>
    <?php 
    include "../../includes/header.php";
    ?>
    <br>
    <div class="product-detail-container">
        <!-- Header -->
        <div class="product-detail-header">
            <h1>Chi tiết sản phẩm</h1>
        </div>

        <!-- Main Content -->
        <div class="product-detail-content">
            <!-- Product Image Section -->
            <div class="product-image-section">
                <?php if (!empty($sp['hinhanh'])): ?>
                    <img src="/QLShopDT_API/includes/img/<?php echo htmlspecialchars($sp['hinhanh']); ?>" 
                         alt="<?php echo htmlspecialchars($sp['tensp']); ?>">
                <?php else: ?>
                    <div class="product-no-image">
                        <p>Không có hình ảnh</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Product Info Section -->
            <div class="product-info-section">
                <h2><?php echo htmlspecialchars($sp['tensp']); ?></h2>

                <table class="product-info-table">
                    <tr>
                        <td><strong>Danh mục:</strong></td>
                        <td><?php echo htmlspecialchars($sp['tendm']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Hãng:</strong></td>
                        <td><?php echo htmlspecialchars($sp['hang']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Giá:</strong></td>
                        <td style="color:red; font-size:24px; font-weight:bold;">
                            <?php echo number_format($sp['gia'], 0, ',', '.'); ?> đ
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Bảo hành:</strong></td>
                        <td><?php echo htmlspecialchars($sp['baohanh']); ?> tháng</td>
                    </tr>
                    <tr>
                        <td><strong>Số lượng còn:</strong></td>
                        <td><?php echo htmlspecialchars($sp['sl']); ?> sản phẩm</td>
                    </tr>
                    <tr>
                        <td><strong>Ghi chú:</strong></td>
                        <td><?php echo htmlspecialchars($sp['ghichu']); ?></td>
                    </tr>
                </table>

                <br>

                <!-- Form thêm vào giỏ hàng -->
                <form method="post" action="../giohang/giohang_insert.php?txt_masp=<?php echo $masp; ?>">
                    <input type="hidden" name="masp" value="<?php echo $masp; ?>">
                    <table border="0">
                        <tr>
                            <td><strong>Số lượng:</strong></td>
                            <td>
                                <input type="number" name="soluong" value="1" min="1"
                                       max="<?php echo $sp['sl']; ?>" style="width:80px;">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><br>
                                <input type="submit" value="THÊM VÀO GIỎ HÀNG"
                                       style="padding:10px 20px; font-size:16px; cursor:pointer;">
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>

        <!-- Thông số kỹ thuật -->
        <tr>
            <td colspan="2">
                <h2>THÔNG SỐ KỸ THUẬT</h2>
                <table width="100%" border="1" cellpadding="8">
                    <tr>
                        <th width="200">Tên thông số</th>
                        <th>Giá trị</th>
                    </tr>
                    <?php if (!empty($thongsos)): ?>
                        <?php foreach ($thongsos as $ts): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($ts['tents']); ?></strong></td>
                                <td><?php echo nl2br(htmlspecialchars($ts['giatri'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2" align="center">Chưa có thông số kỹ thuật</td>
                        </tr>
                    <?php endif; ?>
                </table>
            </td>
        </tr>
    </div>
    <?php include __DIR__ . "/../../includes/footer.php"; ?>
</body>
</html>