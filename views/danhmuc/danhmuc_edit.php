<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/nv.css">
    <title>Sửa danh mục</title>
</head>
<body>
    <h1 align="center">SỬA DANH MỤC</h1>
    
    <?php
    include "api_helper.php";
    
    $madm = $_GET['madm'] ?? $_POST['madm'] ?? 0;
    $thongbao = "";
    
    // Xử lý khi submit form (UPDATE)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $tendm = $_POST['txt_tendm'] ?? '';
        
        // Gọi API để cập nhật danh mục
        $result = callDanhmucAPI([
            "action" => "update",
            "madm" => $madm,
            "tendm" => $tendm
        ]);
        
        if($result && $result['status']) {
            header("Location: danhmuc.php");
            exit();
        } else {
            $thongbao = "Lỗi: " . ($result['message'] ?? 'Không xác định');
        }
    }
    
    // Lấy thông tin danh mục để hiển thị form
    $result = callDanhmucAPI([
        "action" => "getone",
        "madm" => $madm
    ]);
    
    if($result && $result['status']) {
        $category = $result['data'];
        $tendm = $category['tendm'];
    } else {
        echo "<p align='center' style='color:red;'>Không tìm thấy danh mục</p>";
        echo "<p align='center'><a href='danhmuc.php'>Quay lại</a></p>";
        exit();
    }
    ?>
    
    <?php if($thongbao): ?>
        <p align="center" style="color:red;"><?php echo $thongbao; ?></p>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="madm" value="<?php echo $madm; ?>">
        <table align="center" border="1">
            <tr>
                <td colspan="2" align="center">Sửa danh mục</td>
            </tr>
            <tr>
                <td>Tên danh mục</td>
                <td>
                    <input type="text" name="txt_tendm" value="<?php echo htmlspecialchars($tendm); ?>" required>
                </td>
            </tr>
            
            <tr>
                <td colspan="2" align="center">
                <input type="submit" value="OK">
                <input type="reset" value="Reset">
                <input type="button" value="Quay lại" onclick="window.location.href='danhmuc.php'">
            </td>
            </tr>
        </table>
    </form>
</body>
</html>
