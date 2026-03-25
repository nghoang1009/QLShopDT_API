<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xóa sản phẩm</title>
</head>
<body>
    <?php
    $masp = $_REQUEST['masp'];

    $post_data = json_encode([
        "masp" => $masp
    ]);

    // ← đổi Test1 thành tên project của bạn
    $api_url = "http://localhost/QLShopDT_API/api/delete_sanpham_api.php";

    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);

    if($result && $result['status'])
        {
            header("Location: sanpham.php");
            exit();
        }
    else
        {
            echo "Xóa thất bại: " . $result['message'];
        }
    ?>
</body>
</html>