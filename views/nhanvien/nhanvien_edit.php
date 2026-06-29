<?php
/**
 * Sửa Nhân viên
 */
session_start();
require_once "../../includes/api_helper.php";

requireLogin();
requireRole([1]);

$manv = (int)($_GET['manv'] ?? $_POST['manv'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    do {
        $tennv = trim($_POST['txt_tennv'] ?? '');
        $sdt   = trim($_POST['txt_sdt']   ?? '');
        $ns    = trim($_POST['date_ns']   ?? '');

    if (empty($tennv)) {
        setFlash('error', 'Tên nhân viên không được để trống');
        header("Location: nhanvien_edit.php?manv=" . $manv);
        exit();
    }
    if (mb_strlen($tennv) > 100) {
        setFlash('error', 'Tên nhân viên không được quá 100 ký tự');
        header("Location: nhanvien_edit.php?manv=" . $manv);
        exit();
    }
    if (!empty($sdt) && !preg_match('/^[0-9]{10,11}$/', $sdt)) {
        setFlash('error', 'Số điện thoại không hợp lệ (chỉ gồm 10-11 chữ số)');
        header("Location: nhanvien_edit.php?manv=" . $manv);
        exit();
    }
    if (!empty($ns) && strtotime($ns) > time()) {
        setFlash('error', 'Ngày sinh không được là ngày trong tương lai');
        header("Location: nhanvien_edit.php?manv=" . $manv);
        exit();
    }
    if (!empty($ns)) {
        $birthDate = new DateTime($ns);
        $age = (int)(new DateTime())->diff($birthDate)->y;
        if ($age < 18) {
            setFlash('error', 'Nhân viên phải đủ 18 tuổi');
            header("Location: nhanvien_edit.php?manv=" . $manv);
            exit();
        }
    }

        if ($sdt !== '' && !preg_match('/^0[0-9]{9,10}$/', $sdt)) {
            setFlash('error', 'Số điện thoại không hợp lệ');
            break;
        }

        if ($ns !== '') {
            if (strtotime($ns) === false || strtotime($ns) > time()) {
                setFlash('error', 'Ngày sinh không hợp lệ');
                break;
            }
            $tuoi = (int)date_diff(date_create($ns), date_create('today'))->y;
            if ($tuoi < 18) {
                setFlash('error', 'Nhân viên phải đủ 18 tuổi');
                break;
            }
        }

        $result = callAPI('PUT', '/api/nhanvien/' . $manv, [
            'tennv' => $tennv,
            'sdt'   => $sdt,
            'ns'    => $ns,
        ]);

        if ($result && $result['status']) {
            setFlash('success', 'Cập nhật nhân viên thành công');
            header("Location: nhanvien.php");
            exit();
        }
        setFlash('error', $result['message'] ?? 'Cập nhật thất bại');
    } while (false);
    header("Location: nhanvien_edit.php?manv=" . $manv);
    exit();
}

$result_nv = callAPI('GET', '/api/nhanvien/' . $manv);
if (!($result_nv && $result_nv['status'])) {
    setFlash('error', 'Không tìm thấy nhân viên');
    header("Location: nhanvien.php");
    exit();
}
$nv = $result_nv['data'];

$error = getFlash('error');

$page_title = 'Sửa Nhân viên';
$active_nav = 'nhanvien';
$extra_css  = '<link rel="stylesheet" href="/QLShopDT_API/assets/css/nhanvien.css">
<link rel="stylesheet" href="/QLShopDT_API/assets/css/footer.css">';

include "../../includes/header.php";
?>

<main class="container">
    <h1>SỬA NHÂN VIÊN</h1>

    <?php if ($error): ?>
        <div class="dm-alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="nhanvien_edit.php?manv=<?= e($manv) ?>" class="dm-form">

        <div class="dm-id-badge">
            <small>Mã nhân viên</small>
            <strong>#<?= e($nv['manv']) ?></strong>
        </div>

        <div class="dm-form-group">
            <label for="txt_tennv" class="dm-label">
                Tên nhân viên <span class="dm-required">*</span>
            </label>
            <input type="text" id="txt_tennv" name="txt_tennv"
                   value="<?= e($nv['tennv']) ?>"
                   class="dm-input" required maxlength="100">
        </div>

        <div class="dm-form-group">
            <label for="txt_sdt" class="dm-label">Số điện thoại</label>
            <input type="text" id="txt_sdt" name="txt_sdt"
                   value="<?= e($nv['sdt']) ?>"
                   class="dm-input" pattern="[0-9]{10,11}" title="Số điện thoại gồm 10-11 chữ số">
        </div>

        <div class="dm-form-group">
            <label for="date_ns" class="dm-label">Ngày sinh</label>
            <input type="date" id="date_ns" name="date_ns"
                   value="<?= e($nv['ns']) ?>"
                   class="dm-input" max="<?= date('Y-m-d') ?>">
        </div>

        <div class="dm-form-actions">
            <button type="submit" class="dm-btn dm-btn-primary">Cập nhật</button>
            <a href="nhanvien.php" class="dm-btn dm-btn-default">Quay lại</a>
        </div>
    </form>
</main>

<?php include "../../includes/footer.php"; ?>