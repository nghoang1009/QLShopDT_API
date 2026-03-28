<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

$page_title = 'Quản lý Danh mục';
$active_nav = 'danhmuc';
$extra_css = '<link rel="stylesheet" href="/QLShopDT_API/assets/css/danhmuc.css">';
include "../../includes/header.php";
include "../../includes/api_helper.php";

// Gọi API lấy danh sách danh mục
$result = callDanhmucAPI(['action' => 'getall']);

$categories = ($result && $result['status']) ? $result['data'] : [];
$tong_bg = count($categories);
?>

<h1>QUẢN LÝ DANH MỤC</h1>

<table>
    <thead>
        <tr>
            <th>STT</th>
            <th>Mã danh mục</th>
            <th>Tên danh mục</th>
            <th><a href="danhmuc_add.php">Thêm danh mục</a></th>
        </tr>
    </thead>
    <tbody>
        <?php
        if (count($categories) > 0) {
            foreach ($categories as $i => $dm) {
                $stt = $i + 1;
        ?>
            <tr>
                <td><?php echo $stt; ?></td>
                <td><?php echo htmlspecialchars($dm['madm']); ?></td>
                <td><?php echo htmlspecialchars($dm['tendm']); ?></td>
                <td> 
                    <a href="danhmuc_edit.php?madm=<?php echo $dm['madm']; ?>">Sửa</a>
                    <a href="danhmuc_del.php?madm=<?php echo $dm['madm']; ?>" 
                       onclick="return confirm('Bạn có chắc muốn xóa danh mục này?')">Xóa</a>
                </td>
            </tr>
        <?php
            }
        } else {
        ?>
            <tr>
                <td colspan="4" class="dm-empty-state">
                    <strong>Chưa có danh mục nào</strong>
                    <p>Hãy thêm danh mục đầu tiên của bạn</p>
                </td>
            </tr>
        <?php
        }
        ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4">Tổng số: <strong><?php echo $tong_bg; ?></strong> danh mục</td>
        </tr>
    </tfoot>
</table>

</body>
</html>