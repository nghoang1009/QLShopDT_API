<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/nv.css">
    <title>Thêm danh mục</title>
</head>
<body>
    <h1 align="center">THÊM DANH MỤC</h1>
    
    <?php
    include "api_helper.php";
    
    $thongbao = "";
    
    // Xử lý khi submit form
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $tendm = $_POST['txt_tendm'] ?? '';
        
        // Gọi API để thêm danh mục
        $result = callDanhmucAPI([
            "action" => "add",
            "tendm" => $tendm
        ]);
        
        if($result && $result['status']) {
            header("Location: danhmuc.php");
            exit();
        } else {
            $thongbao = "Lỗi: " . ($result['message'] ?? 'Không xác định');
        }
    }
    ?>
    
    <?php if($thongbao): ?>
        <p align="center" style="color:red;"><?php echo $thongbao; ?></p>
    <?php endif; ?>
    
    <form method="POST" action="" enctype="multipart/form-data">        
        <table align="center" border="1">
            <tr>
                <td colspan="2" align="center">Thông tin danh mục</td>
            </tr>
            <tr>
                <td>Tên danh mục</td>
                <td>
                    <input type="text" name="txt_tendm" required>
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
