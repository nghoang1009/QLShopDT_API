<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

$page_title = 'Quản lý Sản phẩm';
$active_nav = 'sanpham';
include "../../includes/header.php";
include "../../includes/api_helper.php";

// Gọi RESTful API lấy danh sách sản phẩm
$api_url = "http://localhost/QLShopDT_API/api/sanpham";

$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);
$sanpham_list = ($result && $result['status']) ? $result['data'] : [];
?>

<h1 align="center">DANH SÁCH SẢN PHẨM</h1>

<table width="1300" align="center" border="1">
        <tr>
            <th>STT</th>
            <th width="250">Tên sản phẩm</th>
            <th>Giá</th>
            <th>Số lượng</th>
            <th>Hãng</th>
            <th>Bảo hành</th>
            <th>Hình ảnh</th>
            <th>Ghi chú</th>
            <th>Danh mục</th>
            <th width="180"><a href="sanpham_add.php">Thêm sản phẩm</a></th>
            <th>Thông số sản phẩm</th>
        </tr>

        <?php if (empty($sanpham_list)): ?>
            <tr><td colspan="11" align="center">Không có sản phẩm nào</td></tr>
        <?php else: ?>
            <?php foreach ($sanpham_list as $i => $sp): ?>
                <tr align="center">
                    <td><?php echo $i + 1; ?></td>
                    <td><?php echo htmlspecialchars($sp['tensp']); ?></td>
                    <td><?php echo htmlspecialchars($sp['gia']); ?></td>
                    <td><?php echo htmlspecialchars($sp['sl']); ?></td>
                    <td><?php echo htmlspecialchars($sp['hang']); ?></td>
                    <td><?php echo htmlspecialchars($sp['baohanh']); ?></td>
                    <td><img src="../img/<?php echo htmlspecialchars($sp['hinhanh']); ?>" width="50"></td>
                    <td><?php echo htmlspecialchars($sp['ghichu']); ?></td>
                    <td><?php echo htmlspecialchars($sp['tendm']); ?></td>
                    <td>
                        <a href="sanpham_edit.php?masp=<?php echo $sp['masp']; ?>">Sửa</a> |
                        <a href="sanpham_del.php?masp=<?php echo $sp['masp']; ?>"
                           onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">Xóa</a>
                    </td>
                    <td>
                        <a href="../thongso/thongso.php?masp=<?php echo $sp['masp']; ?>">Xem thông số</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>

        <tr>
            <td colspan="11" align="right">
                Bảng có <?php echo count($sanpham_list); ?> sản phẩm
            </td>
        </tr>
    </table>

</body>
</html>