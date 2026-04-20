<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/config/database.php');
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: ../auth/login.php");
        exit();
    }

    $matt = $_REQUEST["matt"];
    $phuongthuc = $_REQUEST["phuongthuc"];
    $sotien = $_REQUEST["sotien"];
    $trangthai = $_REQUEST["trangthai"];
    $ghichu = $_REQUEST["ghichu"];

    $sql_edit = "UPDATE thanhtoan SET phuongthuc='$phuongthuc', sotien='$sotien', 
                trangthai='$trangthai', ghichu='$ghichu' WHERE matt=$matt";
    mysqli_query($conn, $sql_edit);
    header("Location: thanhtoan.php");
    ?>
</body>
</html>
