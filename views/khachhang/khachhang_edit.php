<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa khách hàng</title>
</head>
<body>
    <h1 align="center">SỬA KHÁCH HÀNG</h1>

    <?php
    include "../../includes/api_helper.php";

    $makh     = $_GET['makh'] ?? $_POST['makh'] ?? 0;
    $thongbao = "";

    // Xử lý khi submit form (UPDATE)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $tenkh  = $_POST['txt_tenkh']  ?? '';
        $diachi = $_POST['txt_diachi'] ?? '';
        $sdt    = $_POST['txt_sdt']    ?? '';

        // Gọi API để cập nhật khách hàng
        $result = callKhachhangAPI([
            "action" => "update",
            "makh"   => $makh,
            "tenkh"  => $tenkh,
            "diachi" => $diachi,
            "sdt"    => $sdt
        ]);

        if ($result && $result['status']) {
            header("Location: khachhang.php");
            exit();
        } else {
            $thongbao = "Lỗi: " . ($result['message'] ?? 'Không xác định');
        }
    }

    // Lấy thông tin khách hàng để hiển thị form
    $result = callKhachhangAPI([
        "action" => "getone",
        "makh"   => $makh
    ]);

    if ($result && $result['status']) {
        $kh     = $result['data'];
        $tenkh  = $kh['tenkh'];
        $diachi = $kh['diachi'];
        $sdt    = $kh['sdt'];
    } else {
        echo "<p align='center' style='color:red;'>Không tìm thấy khách hàng</p>";
        echo "<p align='center'><a href='khachhang.php'>Quay lại</a></p>";
        exit();
    }
    ?>

    <?php if ($thongbao): ?>
        <p align="center" style="color:red;"><?php echo $thongbao; ?></p>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="makh" value="<?php echo $makh; ?>">
        <table border="1" align="center">
            <tr>
                <td colspan="2" align="center">Thông tin khách hàng</td>
            </tr>
            <tr>
                <td>Tên khách hàng:</td>
                <td>
                    <input type="text" name="txt_tenkh"
                           value="<?php echo htmlspecialchars($tenkh); ?>" required>
                </td>
            </tr>
            <tr>
                <td>Địa chỉ:</td>
                <td>
                    <input type="text" name="txt_diachi"
                           value="<?php echo htmlspecialchars($diachi); ?>">
                </td>
            </tr>
            <tr>
                <td>Số điện thoại:</td>
                <td>
                    <input type="text" name="txt_sdt"
                           value="<?php echo htmlspecialchars($sdt); ?>">
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <input type="submit" value="OK">
                    <input type="reset"  value="Reset">
                    <input type="button" value="Quay lại" onclick="window.location.href='khachhang.php'">
                </td>
            </tr>
        </table>
    </form>
</body>
</html>