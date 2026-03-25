<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/nv.css">
    <title>Document</title>
</head>
<body>
    <h1 align = "center">DANH SÁCH KHÁCH HÀNG<h1>
    <h2 align = "center"><a href="../trangchu.php">Trang chủ</a></h2>
    <?php
        include($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');
        require_once($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');
        include "../header.php";
        $sql_select = "Select * from `khachhang`";
        $result = mysqli_query($conn,$sql_select);
        $tong_bg=mysqli_num_rows($result);

        $stt = 0;
        while($row = mysqli_fetch_object($result))
        {
            $stt++;
            $makh[$stt] = $row->makh;
            $tenkh[$stt] = $row->tenkh;
            $diachi[$stt] = $row->diachi;
            $sdt[$stt] = $row->sdt;
        }
    ?>

    <table width = 1300 align="center" border="1">
        <tr>
            <th>STT</th>
            <th>Tên khách hàng</th>
            <th>Địa chỉ</th>
            <th>Số điện thoại</th>
            <th><a href="khachhang_add.php">Thêm khách hàng</a></th>
        </tr>

        <?php
        for ($i=1; $i<=$tong_bg; $i++)
        {
        ?>
            <tr align="center">
                <td><?php echo $i; ?></td>
                <td><?php echo $tenkh[$i] ?></td>
                <td><?php echo $diachi[$i] ?></td>
                <td><?php echo $sdt[$i] ?></td>
                <td> 
                    <a href="khachhang_edit.php?makh=<?php echo $makh[$i] ?>">Sửa</a> |
                    <a href="khachhang_del.php?makh=<?php echo $makh[$i]; ?>" 
                       onclick="return confirm('Bạn có chắc muốn xóa khách hàng này?')">Xóa</a>
                </td>
            </tr>
        <?php
        }
	  ?>
      <tr>
      <td colspan="10" align="right">Bảng có <?php echo $tong_bg?> khách hàng</td>
      </tr>
    </table>
</body>
</html>