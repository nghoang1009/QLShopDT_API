<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lưu sản phẩm</title>
</head>
<body>
    <?php
    $masp    = $_REQUEST['masp'];
    $tensp   = $_REQUEST['txt_tensp'];
    $gia     = $_REQUEST['num_gia'];
    $sl      = $_REQUEST['num_sl'];
    $hang    = $_REQUEST['txt_hang'];
    $baohanh = $_REQUEST['txt_baohanh'];
    $ghichu  = $_REQUEST['txt_ghichu'];
    $madm    = $_REQUEST['txt_madm'];
    $hinhanh_cu = $_REQUEST['hinhanh_cu']; // tên ảnh cũ truyền từ form

    $file_tmp  = $_FILES['img_hinhanh']['tmp_name'];
    $file_name = $_FILES['img_hinhanh']['name'];

    // Nếu có upload ảnh mới thì copy sang img/ và dùng tên mới
    // Nếu không thì giữ nguyên tên ảnh cũ
    if($file_tmp != "")
        {
            $datetime   = date("Y-m-d_H-i-s_");
            $file__name = $datetime . $file_name;
            copy($file_tmp, "../img/" . $file__name);
            $hinhanh = $file__name;
        }
    else
        {
            $hinhanh = $hinhanh_cu;
        }

    $post_data = json_encode([
        "masp"    => $masp,
        "tensp"   => $tensp,
        "gia"     => $gia,
        "sl"      => $sl,
        "hang"    => $hang,
        "baohanh" => $baohanh,
        "ghichu"  => $ghichu,
        "hinhanh" => $hinhanh,
        "madm"    => $madm
    ]);

    // ← đổi Test1 thành tên project của bạn
    $api_url = "http://localhost/QLShopDT_API/api/update_sanpham_api.php";

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
            echo "Sửa thất bại: " . $result['message'];
        }
    ?>
</body>
</html>