<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
    include "api_helper.php";
    
    $madm = $_GET['madm'] ?? 0;
    
    // Gọi API để xóa danh mục
    $result = callDanhmucAPI([
        "action" => "delete",
        "madm" => $madm
    ]);
    
    if($result && $result['status']) {
        header("Location: danhmuc.php");
        exit();
    } else {
        echo "<h3>Xóa thất bại</h3>";
        echo "<p>" . ($result['message'] ?? 'Lỗi không xác định') . "</p>";
    }
	?>
</body>
</html>
