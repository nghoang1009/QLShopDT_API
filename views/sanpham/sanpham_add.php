<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm sản phẩm</title>
</head>
<body>
    <h1 align="center">THÊM SẢN PHẨM</h1>

    <?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: ../login.php");
        exit();
    }

    include "../../includes/api_helper.php";
    // Lấy danh sách danh mục qua API
    $result_dm = callDanhmucAPI(['action' => 'getall']);
    $danhmucs  = ($result_dm && $result_dm['status']) ? $result_dm['data'] : [];

    $thongbao = "";

    // Xử lý khi submit form
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Xử lý upload ảnh
        $hinhanh = "";
        if (!empty($_FILES['img_hinhanh']['name'])) {
            $hinhanh     = basename($_FILES['img_hinhanh']['name']);
            $upload_path = "../img/" . $hinhanh;
            move_uploaded_file($_FILES['img_hinhanh']['tmp_name'], $upload_path);
        }

        // Gọi API thêm sản phẩm
        $result = callSanphamAPI([
            "action"  => "add",
            "madm"    => $_POST['txt_madm'],
            "tensp"   => $_POST['txt_tensp'],
            "gia"     => $_POST['num_gia'],
            "sl"      => $_POST['num_sl'],
            "hang"    => $_POST['txt_hang'],
            "baohanh" => $_POST['txt_baohanh'],
            "ghichu"  => $_POST['txt_ghichu'],
            "hinhanh" => $hinhanh
        ]);

        if ($result && $result['status']) {
            header("Location: sanpham.php");
            exit();
        } else {
            $thongbao = "Lỗi: " . ($result['message'] ?? 'Không xác định');
        }
    }
    ?>

    <?php if ($thongbao): ?>
        <p align="center" style="color:red;"><?php echo $thongbao; ?></p>
    <?php endif; ?>

    <form action="sanpham_add.php" method="post" enctype="multipart/form-data">
        <table align="center" border="1">
            <tr>
                <td colspan="2" align="center">Thông tin sản phẩm</td>
            </tr>
            <tr>
                <td>Danh mục</td>
                <td>
                    <select name="txt_madm">
                        <option value="0">--Chọn danh mục--</option>
                        <?php foreach ($danhmucs as $dm): ?>
                            <option value="<?php echo $dm['madm']; ?>">
                                <?php echo htmlspecialchars($dm['tendm']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Tên sản phẩm</td>
                <td><input type="text" name="txt_tensp" required></td>
            </tr>
            <tr>
                <td>Giá</td>
                <td><input type="number" name="num_gia" min="0" required></td>
            </tr>
            <tr>
                <td>Số lượng</td>
                <td><input type="number" name="num_sl" min="0" required></td>
            </tr>
            <tr>
                <td>Hãng</td>
                <td><input type="text" name="txt_hang"></td>
            </tr>
            <tr>
                <td>Bảo hành (tháng)</td>
                <td><input type="text" name="txt_baohanh"></td>
            </tr>
            <tr>
                <td>Hình ảnh</td>
                <td><input type="file" name="img_hinhanh" accept="image/*"></td>
            </tr>
            <tr>
                <td>Ghi chú</td>
                <td><input type="text" name="txt_ghichu"></td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <input type="submit" value="OK">
                    <input type="reset" value="Reset">
                    <input type="button" value="Quay lại" onclick="window.location.href='sanpham.php'">
                </td>
            </tr>
        </table>
    </form>
</body>
</html>