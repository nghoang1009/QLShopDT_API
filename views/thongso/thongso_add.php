<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1 align = "center">THÊM THÔNG SỐ</h1>
    <?php
        include($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');
        require_once($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');
        $sql_select = "Select * from sanpham";
        $result = mysqli_query($conn,$sql_select);
        $tong_bg=mysqli_num_rows($result);

        $stt = 0;
        while($row = mysqli_fetch_object($result))
        {
            $stt++;
            $masp[$stt] = $row->masp;
            $tensp[$stt] = $row->tensp;
            $gia[$stt] = $row->gia;
            $sl[$stt] = $row->sl;
            $hang[$stt] = $row->hang;
            $baohanh[$stt] = $row->baohanh;
            $ghichu[$stt] = $row->ghichu;
            $hinhanh[$stt] = $row->hinhanh;
            $madm[$stt] = $row->madm;
        }
    ?>

    <form method="post" action="thongso_insert.php" enctype="multipart/form-data">        
        <table align="center" border="1">
            <tr>
                <td colspan="2" align="center">Thêm danh mục</td>
            </tr>
            <tr>
                <td>Tên thông số</td>
                <td>
                    <input type="text" name="txt_tents">
                </td>
            </tr>

            <tr>
                <td>Mã sản phẩm</td>
                <td>
                <select name="masp">
                    <option value="0">--Chọn sản phẩm--</option>
                    <?php
                    for ($i=1; $i<=$tong_bg; $i++)
                    {
                    ?>
                        <option value="<?php echo $masp[$i] ?>">
                            <?php echo $tensp[$i]?>
                        </option>
                    <?php
                    }
                    ?>
                </select>
            </td>

            <tr>
                <td>Giá trị</td>
                <td>
                    <input type="text" name="txt_giatri">
                </td>
            </tr>
            
            <tr>
                <td colspan="2" align="center">
                <input type="submit" value="OK">
                <input type="reset" value="Reset">
                <input type="button" value="Quay lại" onclick="window.location.href='thongso.php?masp=<?php echo $masp; ?>'">
            </td>
            </tr>
        </table>
    </form>
</body>
</html>
