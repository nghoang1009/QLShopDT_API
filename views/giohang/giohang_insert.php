<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm vào giỏ hàng</title>
</head>
<body>
    <?php
    include($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');
    session_start();
    
    if (!isset($_SESSION['username'])) {
        header("Location: ../login.php");
        exit();
    }

    $masp = isset($_REQUEST["txt_masp"]) ? $_REQUEST["txt_masp"] : "";
    $soluong = isset($_REQUEST["num_soluong"]) ? $_REQUEST["num_soluong"] : 1;

    if (empty($masp)) {
        echo "Vui lòng chọn sản phẩm";
        echo "<br><a href='giohang_add.php'>Quay lại</a>";
        exit();
    }

    if ($soluong < 1) {
        echo "Số lượng phải lớn hơn 0";
        echo "<br><a href='giohang_add.php'>Quay lại</a>";
        exit();
    }

    $username = $_SESSION['username'];

    $sql_get_user = "SELECT kh.makh 
                     FROM taikhoan tk
                     JOIN khachhang kh ON tk.matk = kh.makh
                     WHERE tk.tentk = '$username'";
    $result_user = mysqli_query($conn, $sql_get_user);

    if (!$result_user || mysqli_num_rows($result_user) == 0) {
        echo "Không tìm thấy thông tin khách hàng. Vui lòng đăng ký thông tin khách hàng trước.";
        echo "<br><a href='giohang_add.php'>Quay lại</a>";
        exit();
    }

    $row_user = mysqli_fetch_object($result_user);
    $makh = $row_user->makh;

    $sql_check_sp = "SELECT sl FROM sanpham WHERE masp = '$masp'";
    $result_check_sp = mysqli_query($conn, $sql_check_sp);
    
    if (!$result_check_sp || mysqli_num_rows($result_check_sp) == 0) {
        echo "Sản phẩm không tồn tại";
        echo "<br><a href='giohang_add.php'>Quay lại</a>";
        exit();
    }

    $row_sp = mysqli_fetch_object($result_check_sp);
    if ($row_sp->sl < $soluong) {
        echo "Sản phẩm không đủ số lượng trong kho (còn " . $row_sp->sl . " sản phẩm)";
        echo "<br><a href='giohang_add.php'>Quay lại</a>";
        exit();
    }

    $sql_check_giohang = "SELECT magio FROM giohang WHERE makh = '$makh'";
    $result_giohang = mysqli_query($conn, $sql_check_giohang);

    if (!$result_giohang) {
        die("Lỗi: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($result_giohang) == 0) {
        $sql_create_giohang = "INSERT INTO giohang (magio, makh) VALUES (NULL, '$makh')";
        mysqli_query($conn, $sql_create_giohang);
        $magio = mysqli_insert_id($conn);
    } else {
        $row_giohang = mysqli_fetch_object($result_giohang);
        $magio = $row_giohang->magio;
    }

    $sql_check = "SELECT * FROM giohang_item WHERE magio = '$magio' AND masp = '$masp'";
    $result_check = mysqli_query($conn, $sql_check);

    if (!$result_check) {
        die("Lỗi: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($result_check) > 0) {
        $row = mysqli_fetch_object($result_check);
        $soluong_moi = $row->sl + $soluong;

        if ($soluong_moi > $row_sp->sl) {
            echo "Không thể thêm. Tổng số lượng vượt quá số lượng trong kho (còn " . $row_sp->sl . " sản phẩm)";
            echo "<br><a href='giohang_add.php'>Quay lại</a>";
            exit();
        }
        
        $sql_update = "UPDATE giohang_item SET sl = '$soluong_moi' WHERE magio = '$magio' AND masp = '$masp'";
        mysqli_query($conn, $sql_update);
    } else {
        $sql_insert = "INSERT INTO giohang_item (maitem, magio, masp, sl) 
                       VALUES (NULL, '$magio', '$masp', '$soluong')";
        mysqli_query($conn, $sql_insert);
    }

    header("Location: giohang.php");
    ?>
</body>
</html>
