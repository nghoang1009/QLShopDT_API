<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include "../../includes/header.php";
include "../../includes/api_helper.php";

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

// Lấy thông số kỹ thuật (vẫn dùng DB trực tiếp vì chưa có API thongso)
include($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');
$sql_ts    = "SELECT * FROM thongso WHERE masp = '$masp'";
$result_ts = mysqli_query($conn, $sql_ts);

// Lấy matk từ session
$username = $_SESSION['username'];
$sql_tk   = "SELECT matk FROM taikhoan WHERE tentk = '$username'";
$row_tk   = mysqli_fetch_assoc(mysqli_query($conn, $sql_tk));
$matk     = $row_tk['matk'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết sản phẩm - <?php echo htmlspecialchars($sp['tensp']); ?></title>
    <link rel="stylesheet" href="../../assets/css/main.css">
</head>
<body>
    <br>
    <h1 align="center">CHI TIẾT SẢN PHẨM</h1>

    <table width="1200" align="center" border="1" cellpadding="10">
        <tr>
            <!-- Hình ảnh sản phẩm -->
            <td width="400" align="center" valign="top">
                <?php if (!empty($sp['hinhanh'])): ?>
                    <img src="./img/<?php echo htmlspecialchars($sp['hinhanh']); ?>"
                         alt="<?php echo htmlspecialchars($sp['tensp']); ?>"
                         width="350">
                <?php else: ?>
                    <p>Không có hình ảnh</p>
                <?php endif; ?>
            </td>

            <!-- Thông tin sản phẩm -->
            <td valign="top">
                <h2><?php echo htmlspecialchars($sp['tensp']); ?></h2>

                <table width="100%" border="0">
                    <tr>
                        <th colspan="2">Thông tin sản phẩm</th>
                    </tr>
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
            </td>
        </tr>

        <!-- Thông số kỹ thuật -->
        <tr>
            <td colspan="2">
                <h2>THÔNG SỐ KỸ THUẬT</h2>
                <table width="100%" border="1" cellpadding="8">
                    <tr>
                        <th width="200">Tên thông số</th>
                        <th>Giá trị</th>
                    </tr>
                    <?php if ($result_ts && mysqli_num_rows($result_ts) > 0): ?>
                        <?php while ($row_ts = mysqli_fetch_assoc($result_ts)): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($row_ts['tents']); ?></strong></td>
                                <td><?php echo nl2br(htmlspecialchars($row_ts['giatri'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2" align="center">Chưa có thông số kỹ thuật</td>
                        </tr>
                    <?php endif; ?>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>