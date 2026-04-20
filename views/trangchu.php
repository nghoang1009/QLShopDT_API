<?php
session_start();

require_once "../includes/footer.php";
require_once "../includes/api_helper.php";

// ── Đường dẫn thư mục img ────────────────────────────────────
// Chuẩn hóa dấu \ thành / để tránh lỗi trên Windows
$doc_root     = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '/');
$current_dir  = str_replace('\\', '/', dirname(dirname(__FILE__))); // lên 1 cấp: QLShopDT_API/
$project_root = rtrim(str_replace($doc_root, '', $current_dir), '/');
define('IMG_BASE_URL', $project_root . '/includes/img/');

// ── Lấy danh sách danh mục qua API ──────────────────────────
$result_dm  = callAPI('GET', '/api/danhmuc');
$danhmucs   = ($result_dm && $result_dm['status']) ? $result_dm['data'] : [];

// ── Đọc tham số tìm kiếm từ URL ─────────────────────────────
$search        = isset($_GET['search']) ? trim($_GET['search']) : '';
$madm_filter   = isset($_GET['madm'])   ? trim($_GET['madm'])   : '0';
$section_title = 'SẢN PHẨM NỔI BẬT';

// ── Gọi API lấy sản phẩm ────────────────────────────────────
if ($search !== '' || ($madm_filter !== '' && $madm_filter != '0')) {
    // Có từ khóa hoặc lọc danh mục → gọi /api/sanpham
    $result_sp = callAPI('GET', '/api/sanpham', [
        'keyword' => $search,
        'madm'    => $madm_filter
    ]);

    // Tạo tiêu đề section phù hợp
    if ($search !== '' && $madm_filter != '0') {
        $section_title = 'KẾT QUẢ: "' . htmlspecialchars($search) . '" trong danh mục đã chọn';
    } elseif ($search !== '') {
        $section_title = 'KẾT QUẢ TÌM KIẾM: "' . htmlspecialchars($search) . '"';
    } else {
        // Chỉ lọc theo danh mục, lấy tên danh mục để hiển thị
        $ten_dm = '';
        foreach ($danhmucs as $dm) {
            if ($dm['madm'] == $madm_filter) {
                $ten_dm = $dm['tendm'];
                break;
            }
        }
        $section_title = 'DANH MỤC: ' . htmlspecialchars($ten_dm);
    }
} else {
    // Không có điều kiện → lấy tất cả (giới hạn 12)
    $result_sp = callAPI('GET', '/api/sanpham');
}

$sanpham_list = ($result_sp && $result_sp['status']) ? $result_sp['data'] : [];

// Nếu getall thì chỉ lấy 12 sản phẩm mới nhất cho trang chủ
if ($search === '' && ($madm_filter === '' || $madm_filter == '0')) {
    $sanpham_list = array_slice(array_reverse($sanpham_list), 0, 12);
}

// ── Tất cả API calls xong, giờ mới xuất HTML ─────────────────
$page_title = 'Trang Chủ';
$active_nav = 'trangchu';
$extra_css  = '<link rel="stylesheet" href="/QLShopDT_API/assets/css/trangchu.css">';
require_once "../includes/header.php";
?>

<!-- ===== HERO (chỉ hiện khi không có điều kiện lọc/tìm kiếm) ===== -->
<?php if ($search === '' && ($madm_filter === '' || $madm_filter == '0')): ?>
<section class="ps-hero">
    <div class="ps-hero-text">
        <h1>Công nghệ đỉnh cao<br>— <em>Giá tốt nhất</em></h1>
        <p>Khám phá hàng ngàn sản phẩm điện thoại, laptop, phụ kiện chính hãng với giá cả cạnh tranh nhất thị trường.</p>
        <div class="ps-hero-cta">
            <a href="#products" class="ps-cta-primary">Mua ngay</a>
            <a href="./sanpham/sanpham.php" class="ps-cta-secondary">Xem tất cả</a>
        </div>
        <div class="ps-hero-stats">
            <div class="ps-stat"><div class="num">1.200+</div><div class="lbl">Sản phẩm</div></div>
            <div class="ps-stat"><div class="num">15k+</div><div class="lbl">Khách hàng</div></div>
            <div class="ps-stat"><div class="num">99%</div><div class="lbl">Hài lòng</div></div>
        </div>
    </div>
    <div class="ps-hero-visual">
        <div class="ps-phone-mock">
            <div class="ps-phone-icon">📱</div>
            <div class="ps-hot-tag">HOT DEAL</div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ===== THANH TÌM KIẾM + LỌC DANH MỤC ===== -->
<div class="ps-filter-bar" id="products">
    <h2 class="ps-section-title" style="margin:0"><?php echo $section_title; ?></h2>

    <form method="GET" action="trangchu.php" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap; margin-top:12px;">
        <!-- Ô tìm kiếm từ khóa -->
        <input type="text"
               name="search"
               value="<?php echo htmlspecialchars($search); ?>"
               placeholder="Tìm kiếm sản phẩm..."
               style="padding:8px 14px; border-radius:6px; border:1px solid #ccc; min-width:220px;">

        <!-- Dropdown lọc danh mục -->
        <select name="madm" style="padding:8px 14px; border-radius:6px; border:1px solid #ccc;">
            <option value="0">-- Tất cả danh mục --</option>
            <?php foreach ($danhmucs as $dm): ?>
                <option value="<?php echo $dm['madm']; ?>"
                    <?php echo ($dm['madm'] == $madm_filter) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($dm['tendm']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit"
                style="padding:8px 20px; border-radius:6px; background:#e74c3c; color:#fff; border:none; cursor:pointer; font-weight:bold;">
            🔍 Tìm
        </button>

        <?php if ($search !== '' || ($madm_filter !== '' && $madm_filter != '0')): ?>
            <a href="trangchu.php"
               style="padding:8px 16px; border-radius:6px; background:#6c757d; color:#fff; text-decoration:none; font-weight:bold;">
                ✕ Xóa lọc
            </a>
        <?php endif; ?>
    </form>
</div>

<!-- ===== DANH SÁCH SẢN PHẨM ===== -->
<div class="ps-products">
    <div class="ps-product-grid">
        <?php if (!empty($sanpham_list)): ?>
            <?php foreach ($sanpham_list as $sp):
                $masp    = $sp['masp'];
                $tensp   = htmlspecialchars($sp['tensp']);
                $gia     = number_format($sp['gia'], 0, ',', '.');
                // ✅ Đường dẫn ảnh tự động theo vị trí project
                $hinhanh = !empty($sp['hinhanh'])
                           ? IMG_BASE_URL . htmlspecialchars($sp['hinhanh'])
                           : '';
            ?>
                <div class="ps-product-card">
                    <span class="ps-badge">Hot</span>
                    <div class="ps-product-img">
                        <?php if ($hinhanh): ?>
                            <img src="<?php echo $hinhanh; ?>"
                                 alt="<?php echo $tensp; ?>"
                                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                            <div class="ps-img-placeholder" style="display:none">📦</div>
                        <?php else: ?>
                            <div class="ps-img-placeholder">📦</div>
                        <?php endif; ?>
                    </div>
                    <div class="ps-product-body">
                        <div class="ps-product-name"><?php echo $tensp; ?></div>
                        <?php if (!empty($sp['tendm'])): ?>
                            <div class="ps-product-cat" style="font-size:12px; color:#888; margin-top:4px;">
                                📂 <?php echo htmlspecialchars($sp['tendm']); ?>
                            </div>
                        <?php endif; ?>
                        <div class="ps-product-price"><?php echo $gia; ?>đ</div>
                    </div>
                    <div class="ps-product-action">
                        <a href="./sanpham/chitietsanpham.php?masp=<?php echo $masp; ?>"
                           class="ps-btn ps-btn-primary"
                           style="width:100%;justify-content:center">
                            <i class="fa fa-eye"></i> Xem chi tiết
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="ps-empty" style="grid-column:1/-1">
                <div class="ps-empty-icon">🔍</div>
                <p>Không tìm thấy sản phẩm nào!</p>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>