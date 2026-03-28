<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

$page_title = 'Quản lý Khách hàng';
$active_nav = 'khachhang';
include "../../includes/header.php";
include "../../includes/api_helper.php";

// Gọi API lấy danh sách khách hàng
$result = callKhachhangAPI(['action' => 'getall']);

$customers = ($result && $result['status']) ? $result['data'] : [];
$tong_bg   = count($customers);
?>

<h1 align="center">DANH SÁCH KHÁCH HÀNG</h1>
<table width="1300" align="center" border="1">
        <tr>
            <th>STT</th>
            <th>Tên khách hàng</th>
            <th>Địa chỉ</th>
            <th>Số điện thoại</th>
            <th><a href="khachhang_add.php">Thêm khách hàng</a></th>
        </tr>

        <?php foreach ($customers as $i => $kh): ?>
            <tr align="center">
                <td><?php echo $i + 1; ?></td>
                <td><?php echo htmlspecialchars($kh['tenkh']); ?></td>
                <td><?php echo htmlspecialchars($kh['diachi']); ?></td>
                <td><?php echo htmlspecialchars($kh['sdt']); ?></td>
                <td>
                    <a href="khachhang_edit.php?makh=<?php echo $kh['makh']; ?>">Sửa</a> |
                    <a href="khachhang_del.php?makh=<?php echo $kh['makh']; ?>"
                       onclick="return confirm('Bạn có chắc muốn xóa khách hàng này?')">Xóa</a>
                </td>
            </tr>
        <?php endforeach; ?>

        <tr>
            <td colspan="5" align="right">Bảng có <?php echo $tong_bg; ?> khách hàng</td>
        </tr>
    </table>

</body>
</html>