<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm khách hàng</title>
</head>
<body>
    <h1 align="center">THÊM KHÁCH HÀNG</h1>

    <?php
    include "../../includes/api_helper.php";

    $thongbao = "";

    // Xử lý khi submit form
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $tenkh  = $_POST['txt_tenkh']  ?? '';
        $diachi = $_POST['txt_diachi'] ?? '';
        $sdt    = $_POST['txt_sdt']    ?? '';

        // Gọi API để thêm khách hàng
        $result = callKhachhangAPI([
            "action" => "add",
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
    ?>

    <?php if ($thongbao): ?>
        <p align="center" style="color:red;"><?php echo $thongbao; ?></p>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <table border="1" align="center">
            <tr>
                <td colspan="2" align="center">Thông tin khách hàng</td>
            </tr>
            <tr>
                <td>Tên khách hàng:</td>
                <td><input type="text" name="txt_tenkh" required></td>
            </tr>
            <tr>
                <td>Địa chỉ:</td>
                <td><input type="text" name="txt_diachi"></td>
            </tr>
            <tr>
                <td>Số điện thoại:</td>
                <td><input type="text" name="txt_sdt"></td>
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