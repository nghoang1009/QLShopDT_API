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
    $role = $_SESSION['role'] ?? 0;

    if ($role != 1 && $role != 2) {
        echo "<script>alert('Bạn không có quyền!'); window.location.href='thanhtoan.php';</script>";
        exit();
    }

    $matt = $_REQUEST["matt"];
    $sql_del = "DELETE FROM thanhtoan WHERE matt = $matt";
    mysqli_query($conn, $sql_del);
    header("Location: thanhtoan.php");
    ?>
</body>
</html>
