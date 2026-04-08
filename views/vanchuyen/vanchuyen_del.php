<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xóa vận chuyển</title>
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

    mysqli_set_charset($conn, "utf8");

    $role = $_SESSION['role'] ?? 0;

    if ($role == '0') {
        echo "<p align='center'>Bạn không có quyền xóa vận chuyển!</p>";
        echo "<p align='center'><a href='vanchuyen.php'>Quay lại</a></p>";
        exit();
    }

    $mavc = isset($_REQUEST["mavc"]) ? $_REQUEST["mavc"] : "";

    if (empty($mavc)) {
        echo "<p align='center'>Không tìm thấy thông tin vận chuyển!</p>";
        echo "<p align='center'><a href='vanchuyen.php'>Quay lại</a></p>";
        exit();
    }

    $sql_delete = "DELETE FROM vanchuyen WHERE mavc = '$mavc'";

    if (mysqli_query($conn, $sql_delete)) {
        echo "<p align='center'>Xóa thông tin vận chuyển thành công!</p>";
        echo "<p align='center'><a href='vanchuyen.php'>Quay lại danh sách</a></p>";
    } else {
        echo "<p align='center'>Lỗi: " . mysqli_error($conn) . "</p>";
        echo "<p align='center'><a href='vanchuyen.php'>Quay lại</a></p>";
    }

    mysqli_close($conn);
    ?>
</body>
</html>
