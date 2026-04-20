<?php
/**
 * Quản lý Danh mục - Danh sách
 */
session_start();
require_once "../../includes/api_helper.php";

// Auth check
requireLogin();

// Lấy danh sách danh mục từ API
$result = callAPI('GET', '/api/danhmuc');
$categories = ($result && $result['status']) ? $result['data'] : [];
$tong_bg = count($categories);

// Lấy role từ session
$can_edit = isAdminOrStaff();

// Flash messages
$success = getFlash('success');
$error = getFlash('error');

// Header variables
$page_title = 'Quản lý Danh mục';
$active_nav = 'danhmuc';
$extra_css = '<link rel="stylesheet" href="/QLShopDT_API/assets/css/danhmuc.css">
<link rel="stylesheet" href="/QLShopDT_API/assets/css/footer.css">';

include "../../includes/header.php";
?>

<main class="container">
    <h1>QUẢN LÝ DANH MỤC</h1>
    
    <?php if ($success): ?>
        <div class="alert alert-success" style="text-align: center;"><?= e($success) ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-error" style="text-align: center;"><?= e($error) ?></div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Mã danh mục</th>
                <th>Tên danh mục</th>
                <?php if ($can_edit): ?>
                    <th><a href="danhmuc_add.php">Thêm danh mục</a></th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if (count($categories) > 0): ?>
                <?php foreach ($categories as $i => $dm): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= e($dm['madm']) ?></td>
                    <td><?= e($dm['tendm']) ?></td>
                    <?php if ($can_edit): ?>
                        <td> 
                            <a href="danhmuc_edit.php?madm=<?= $dm['madm'] ?>">Sửa</a> |
                            <a href="danhmuc_del.php?madm=<?= $dm['madm'] ?>" 
                               onclick="return confirm('Bạn có chắc muốn xóa danh mục này?')">Xóa</a>
                        </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="<?= $can_edit ? '4' : '3' ?>" class="dm-empty-state">
                        <strong>Chưa có danh mục nào</strong>
                        <p>Hãy thêm danh mục đầu tiên của bạn</p>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="<?= $can_edit ? '4' : '3' ?>">Tổng số: <strong><?= $tong_bg ?></strong> danh mục</td>
            </tr>
        </tfoot>
    </table>
</main>

<?php include "../../includes/footer.php"; ?>