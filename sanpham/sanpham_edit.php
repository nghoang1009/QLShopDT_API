<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/nv.css">
    <title>Sửa sản phẩm</title>
</head>
<body>
    <h1 align="center">SỬA SẢN PHẨM</h1>
    <?php
        // Lấy dữ liệu sản phẩm hiện tại từ DB để điền vào form
        // (vẫn dùng DB trực tiếp để load form — chỉ phần lưu mới gọi API)
        require_once($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');;

        $sql_select = "SELECT * FROM sanpham WHERE masp = '$masp'";
        $result = mysqli_query($conn, $sql_select);
        $row = mysqli_fetch_assoc($result);

        $tensp   = $row['tensp'];
        $gia     = $row['gia'];
        $sl      = $row['sl'];
        $hang    = $row['hang'];
        $baohanh = $row['baohanh'];
        $ghichu  = $row['ghichu'];
        $hinhanh = $row['hinhanh'];
        $madm    = $row['madm'];

        // Lấy danh sách danh mục
        $result_dm = mysqli_query($conn, "SELECT madm, tendm FROM danhmuc");
        $tong_bg = mysqli_num_rows($result_dm);
        $stt = 0;
        while($row_dm = mysqli_fetch_assoc($result_dm))
            {
                $stt++;
                $iddm[$stt]  = $row_dm['madm'];
                $tendm[$stt] = $row_dm['tendm'];
            }
    ?>

    <form method="post" action="sanpham_edit_save.php?masp=<?php echo $masp ?>" enctype="multipart/form-data">
        <table align="center" border="1">
            <tr>
                <td colspan="2" align="center">Thông tin sản phẩm</td>
            </tr>
            <tr>
                <td>Danh mục</td>
                <td>
                    <select name="txt_madm">
                        <option value="0">--Chọn danh mục--</option>
                        <?php for($i = 1; $i <= $tong_bg; $i++): ?>
                            <option value="<?php echo $iddm[$i]; ?>"
                                <?php echo ($iddm[$i] == $madm) ? 'selected' : ''; ?>>
                                <?php echo $tendm[$i]; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Tên sản phẩm</td>
                <td><input type="text" name="txt_tensp" value="<?php echo $tensp; ?>"></td>
            </tr>
            <tr>
                <td>Giá</td>
                <td><input type="number" name="num_gia" value="<?php echo $gia; ?>"></td>
            </tr>
            <tr>
                <td>Số lượng</td>
                <td><input type="number" name="num_sl" value="<?php echo $sl; ?>"></td>
            </tr>
            <tr>
                <td>Hãng</td>
                <td><input type="text" name="txt_hang" value="<?php echo $hang; ?>"></td>
            </tr>
            <tr>
                <td>Bảo hành</td>
                <td><input type="text" name="txt_baohanh" value="<?php echo $baohanh; ?>"></td>
            </tr>
            <tr>
                <td>Hình ảnh hiện tại</td>
                <td><img src="../img/<?php echo $hinhanh; ?>" width="80"></td>
            </tr>
            <tr>
                <td>Đổi hình ảnh</td>
                <td><input type="file" name="img_hinhanh"></td>
            </tr>
            <!-- giữ tên ảnh cũ để truyền sang edit_save khi không đổi ảnh -->
            <input type="hidden" name="hinhanh_cu" value="<?php echo $hinhanh; ?>">
            <tr>
                <td>Ghi chú</td>
                <td><input type="text" name="txt_ghichu" value="<?php echo $ghichu; ?>"></td>
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