<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/nv.css">
    <title>Danh mục</title>
</head>
<body>
    <H1 align = "center">QUẢN LÝ DANH MỤC</H1>
    <h2 align = "center"><a href="../trangchu.php">Trang chủ</a></h2>
    <?php
        include($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');
        require_once($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');
        include "../header.php";
        $sql_select = "Select * from `danhmuc`";
        $result = mysqli_query($conn,$sql_select);
        $tong_bg=mysqli_num_rows($result);

        $stt = 0;
        while($row = mysqli_fetch_object($result))
        {
            $stt++;
            $madm[$stt] = $row->madm;
            $tendm[$stt] = $row->tendm;
        }
    ?>
    <table width = 1000 align="center" border="1">
        <tr>
            <th>STT</th>
            <th>Mã danh mục</th>
            <th>Tên danh mục</th>
            <th><a href="danhmuc_add.php">Thêm danh mục</a></th>
       
        </tr>

        <?php
        for ($i=1; $i<=$tong_bg; $i++)
        {
        ?>
            <tr align="center">
                <td><?php echo $i; ?></td>
                <td><?php echo $madm[$i] ?></td>
                <td><?php echo $tendm[$i] ?></td>
                <td> 
                    <a href="danhmuc_edit.php?madm=<?php echo $madm[$i] ?>">Sửa</a> |
                    <a href="danhmuc_del.php?madm=<?php echo $madm[$i]; ?>" 
                       onclick="return confirm('Bạn có chắc muốn xóa danh mục này?')">Xóa</a>
                </td>
            </tr>
        <?php
        }
	  ?>
      <tr>
      <td colspan="10" align="right">Bảng có <?php echo $tong_bg?> danh mục</td>
      </tr>
    </table>
</body>
</html>