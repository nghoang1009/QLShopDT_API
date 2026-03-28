<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa sản phẩm</title>
</head>
<body>
    <h1 align="center">SỬA SẢN PHẨM</h1>

    <?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: ../login.php");
        exit();
    }

    include "../../includes/api_helper.php";

    $masp     = $_GET['masp'] ?? $_POST['masp'] ?? 0;
    $thongbao = "";

    // Xử lý khi submit form (UPDATE)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Xử lý ảnh: nếu có upload mới thì dùng tên mới, ngược lại giữ tên cũ
        $hinhanh = $_POST['hinhanh_cu'];
        if (!empty($_FILES['img_hinhanh']['name'])) {
            $datetime = date("Y-m-d_H-i-s_");
            $hinhanh  = $datetime . basename($_FILES['img_hinhanh']['name']);
            move_uploaded_file($_FILES['img_hinhanh']['tmp_name'], "../img/" . $hinhanh);
        }

        // Gọi API cập nhật sản phẩm
        $result = callSanphamAPI([
            "action"  => "update",
            "masp"    => $masp,
            "tensp"   => $_POST['txt_tensp'],
            "gia"     => $_POST['num_gia'],
            "sl"      => $_POST['num_sl'],
            "hang"    => $_POST['txt_hang'],
            "baohanh" => $_POST['txt_baohanh'],
            "ghichu"  => $_POST['txt_ghichu'],
            "hinhanh" => $hinhanh,
            "madm"    => $_POST['txt_madm']
        ]);

        if ($result && $result['status']) {
            header("Location: sanpham.php");
            exit();
        } else {
            $thongbao = "Lỗi: " . ($result['message'] ?? 'Không xác định');
        }
    }

    // Lấy thông tin sản phẩm hiện tại để điền vào form
    $result_sp = callSanphamAPI([
        "action" => "getone",
        "masp"   => $masp
    ]);

    if (!($result_sp && $result_sp['status'])) {
        echo "<p align='center' style='color:red;'>Không tìm thấy sản phẩm</p>";
        echo "<p align='center'><a href='sanpham.php'>Quay lại</a></p>";
        exit();
    }
    $sp = $result_sp['data'];

    // Lấy danh sách danh mục qua API
    $result_dm = callDanhmucAPI(['action' => 'getall']);
    $danhmucs  = ($result_dm && $result_dm['status']) ? $result_dm['data'] : [];
    ?>

    <?php if ($thongbao): ?>
        <p align="center" style="color:red;"><?php echo $thongbao; ?></p>
    <?php endif; ?>

    <form method="post" action="sanpham_edit.php?masp=<?php echo $masp; ?>" enctype="multipart/form-data">
        <input type="hidden" name="masp"        value="<?php echo $masp; ?>">
        <input type="hidden" name="hinhanh_cu"  value="<?php echo htmlspecialchars($sp['hinhanh']); ?>">

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
                            <option value="<?php echo $dm['madm']; ?>"
                                <?php echo ($dm['madm'] == $sp['madm']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($dm['tendm']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Tên sản phẩm</td>
                <td><input type="text" name="txt_tensp" value="<?php echo htmlspecialchars($sp['tensp']); ?>" required></td>
            </tr>
            <tr>
                <td>Giá</td>
                <td><input type="number" name="num_gia" value="<?php echo $sp['gia']; ?>" min="0" required></td>
            </tr>
            <tr>
                <td>Số lượng</td>
                <td><input type="number" name="num_sl" value="<?php echo $sp['sl']; ?>" min="0" required></td>
            </tr>
            <tr>
                <td>Hãng</td>
                <td><input type="text" name="txt_hang" value="<?php echo htmlspecialchars($sp['hang']); ?>"></td>
            </tr>
            <tr>
                <td>Bảo hành (tháng)</td>
                <td><input type="text" name="txt_baohanh" value="<?php echo htmlspecialchars($sp['baohanh']); ?>"></td>
            </tr>
            <tr>
                <td>Hình ảnh hiện tại</td>
                <td><img src="../img/<?php echo htmlspecialchars($sp['hinhanh']); ?>" width="80"></td>
            </tr>
            <tr>
                <td>Đổi hình ảnh</td>
                <td><input type="file" name="img_hinhanh" accept="image/*"></td>
            </tr>
            <tr>
                <td>Ghi chú</td>
                <td><input type="text" name="txt_ghichu" value="<?php echo htmlspecialchars($sp['ghichu']); ?>"></td>
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