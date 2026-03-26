<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/nv.css">
    <title>Danh mục</title>
</head>
<body>
    <?php
        include "../../includes/header.php";
        include "api_helper.php";
        
        // Gọi API lấy danh sách danh mục
        $result = callDanhmucAPI(['action' => 'getall']);
        
        $categories = ($result && $result['status']) ? $result['data'] : [];
        $tong_bg = count($categories);
    ?>
    <br>
    <H1 align = "center">QUẢN LÝ DANH MỤC</H1>
    <table width = 1500 align="center" border="1">
        <tr>
            <th>STT</th>
            <th>Mã danh mục</th>
            <th>Tên danh mục</th>
            <th><a href="danhmuc_add.php">Thêm danh mục</a></th>
       
        </tr>

        <?php
        foreach ($categories as $i => $dm) {
            $stt = $i + 1;
        ?>
            <tr align="center">
                <td><?php echo $stt; ?></td>
                <td><?php echo $dm['madm']; ?></td>
                <td><?php echo $dm['tendm']; ?></td>
                <td> 
                    <a href="danhmuc_edit.php?madm=<?php echo $dm['madm']; ?>">Sửa</a> |
                    <a href="danhmuc_del.php?madm=<?php echo $dm['madm']; ?>" 
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
