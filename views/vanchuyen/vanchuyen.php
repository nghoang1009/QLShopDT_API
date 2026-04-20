<?php
session_start();
include "../../includes/api_helper.php";
requireLogin();

$page_title = 'Quản lý Vận chuyển';
$active_nav = 'vanchuyen';
$extra_css = '<link rel="stylesheet" href="/QLShopDT_API/assets/css/footer.css">';
include "../../includes/header.php";
include "../../includes/footer.php";

// Kiểm tra quyền chỉnh sửa
$can_edit = isAdminOrStaff();

// Lấy danh sách vận chuyển từ API
if (!isAdminOrStaff()) {
    // Khách hàng: lấy makh từ API
    $username = $_SESSION['username'];
    $kh_result = callKhachhangAPI(['action' => 'getbyusername', 'username' => $username]);
    if ($kh_result && $kh_result['status']) {
        $makh = $kh_result['makh'];
        $result = callVanchuyenAPI(['action' => 'getbycustomer', 'makh' => $makh]);
        $vanchuyen_list = ($result && $result['status']) ? $result['data'] : [];
    } else {
        $vanchuyen_list = [];
    }
} else {
    // Admin/Nhân viên: lấy tất cả
    $result = callVanchuyenAPI(['action' => 'getall']);
    $vanchuyen_list = ($result && $result['status']) ? $result['data'] : [];
}
?>
<html>
    <link rel="stylesheet" href="/QLShopDT_API/assets/css/vanchuyen.css">
</html>
<h1 align="center">DANH SÁCH VẬN CHUYỂN</h1>

<table width="1300" align="center" border="1">
    <tr>
        <th>STT</th>
        <th>Mã vận chuyển</th>
        <th>Mã đơn hàng</th>
        <th>Tên khách hàng</th>
        <th>Địa chỉ giao hàng</th>
        <th>SĐT</th>
        <th>Ngày đặt</th>
            <th>Ngày giao</th>
        <th>Tổng tiền</th>
        <?php if ($can_edit): ?>
            <th width="180"><a href="vanchuyen_add.php">Thêm vận chuyển</a></th>
        <?php endif; ?>
        <th>Chi tiết</th>
    </tr>

    <?php if (empty($vanchuyen_list)): ?>
        <tr><td colspan="<?php echo $can_edit ? '10' : '9'; ?>" align="center">Không có thông tin vận chuyển nào</td></tr>
    <?php else: ?>
        <?php foreach ($vanchuyen_list as $i => $vc): ?>
            <tr align="center">
                <td><?php echo $i + 1; ?></td>
                <td><?php echo htmlspecialchars($vc['mavc']); ?></td>
                <td><?php echo htmlspecialchars($vc['madh']); ?></td>
                <td><?php echo htmlspecialchars($vc['tenkh']); ?></td>
                <td><?php echo htmlspecialchars($vc['diachi']); ?></td>
                <td><?php echo htmlspecialchars($vc['sdt']); ?></td>
                <td><?php echo date('d/m/Y', strtotime($vc['ngaydat'] ?? '')); ?></td>
                <td><?php echo date('d/m/Y', strtotime($vc['ngaygiao'])); ?></td>
                <td><?php echo number_format($vc['trigia'] ?? 0); ?> VNĐ</td>
                <?php if ($can_edit): ?>
                    <td>
                        <a href="vanchuyen_edit.php?mavc=<?php echo $vc['mavc']; ?>">Sửa</a> |
                        <a href="vanchuyen_del.php?mavc=<?php echo $vc['mavc']; ?>"
                           onclick="return confirm('Bạn có chắc muốn xóa vận chuyển này?')">Xóa</a>
                    </td>
                <?php endif; ?>
                <td>
                    <a href="../donhang/donhang_chitiet.php?madh=<?php echo $vc['madh']; ?>">Xem chi tiết</a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>

    <tr>
        <td colspan="<?php echo $can_edit ? '10' : '9'; ?>" align="right">
            Bảng có <?php echo count($vanchuyen_list); ?> vận chuyển
        </td>
    </tr>
</table>

</body>
</html>
