<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông số sản phẩm</title>
</head>
<body>
    <?php
    $extra_css = '<link rel="stylesheet" href="/QLShopDT_API/assets/css/footer.css">';
    
    include "../../includes/header.php";
    include "../../includes/api_helper.php";
    include "../../includes/footer.php";

    $masp = $_GET['masp'] ?? $_REQUEST['masp'] ?? '';

    // Lấy role từ session
    $role           = $_SESSION['role'] ?? 0;
    $isAdminOrStaff = ($role == 1 || $role == 2);

    // Gọi API lấy danh sách thông số theo sản phẩm
    $result = callThongsoAPI([
        'action' => 'getall',
        'masp'   => $masp
    ]);

    $thongsos = ($result && $result['status']) ? $result['data'] : [];
    $tong_bg  = count($thongsos);

    // Lấy tên sản phẩm từ dòng đầu (nếu có)
    $tensp = (!empty($thongsos)) ? $thongsos[0]['tensp'] : 'Sản phẩm #' . $masp;
    ?>

    <h1 align="center">THÔNG SỐ SẢN PHẨM</h1>

    <table width="1300" align="center" border="1">
        <tr>
            <th>STT</th>
            <th>Tên sản phẩm</th>
            <th>Tên thông số</th>
            <th width="700">Giá trị</th>
            <?php if ($isAdminOrStaff): ?>
            <th width="180">
                <a href="thongso_add.php?masp=<?php echo $masp; ?>">Thêm thông số</a>
            </th>
            <?php endif; ?>
        </tr>

        <?php foreach ($thongsos as $i => $ts): ?>
            <tr align="center">
                <td><?php echo $i + 1; ?></td>
                <td><?php echo htmlspecialchars($ts['tensp']); ?></td>
                <td><?php echo htmlspecialchars($ts['tents']); ?></td>
                <td><?php echo htmlspecialchars($ts['giatri']); ?></td>
                <?php if ($isAdminOrStaff): ?>
                <td>
                    <a href="thongso_edit.php?mats=<?php echo $ts['mats']; ?>&masp=<?php echo $masp; ?>">Sửa</a> |
                    <a href="thongso_del.php?mats=<?php echo $ts['mats']; ?>&masp=<?php echo $masp; ?>"
                       onclick="return confirm('Bạn có chắc muốn xóa thông số này?')">Xóa</a>
                </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>

        <tr>
            <td colspan="<?php echo $isAdminOrStaff ? '5' : '4'; ?>" align="right">
                Bảng có <?php echo $tong_bg; ?> thông số
            </td>
        </tr>
    </table>
</body>
</html>