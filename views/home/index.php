<?php
/**
 * Home View
 */

// Chuẩn bị đường dẫn ảnh
$imgBaseUrl = BASE_URL . '/includes/img/';

// Extra CSS
$extra_css = '<link rel="stylesheet" href="' . BASE_URL . '/assets/css/trangchu.css">';

// Include header
include BASE_PATH . '/includes/header.php';
include BASE_PATH . '/includes/footer.php';
?>

<!-- ===== HERO (chỉ hiện khi không có điều kiện lọc/tìm kiếm) ===== -->
<?php if ($showHero): ?>
<section class="ps-hero">
    <div class="ps-hero-text">
        <h1>Công nghệ đỉnh cao<br>— <em>Giá tốt nhất</em></h1>
        <p>Khám phá hàng ngàn sản phẩm điện thoại, laptop, phụ kiện chính hãng với giá cả cạnh tranh nhất thị trường.</p>
        <div class="ps-hero-cta">
            <a href="#products" class="ps-cta-primary">Mua ngay</a>
            <a href="<?php echo BASE_URL; ?>/app.php/sanpham" class="ps-cta-secondary">Xem tất cả</a>
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
    <h2 class="ps-section-title" style="margin:0"><?php echo $sectionTitle; ?></h2>

    <form method="GET" action="<?php echo $formAction ?? (BASE_URL . '/app.php/'); ?>" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap; margin-top:12px;">
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
                    <?php echo ($dm['madm'] == $madm) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($dm['tendm']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit"
                style="padding:8px 20px; border-radius:6px; background:#e74c3c; color:#fff; border:none; cursor:pointer; font-weight:bold;">
            🔍 Tìm
        </button>

        <?php if ($search !== '' || ($madm !== '' && $madm != '0')): ?>
            <a href="<?php echo $formAction ?? (BASE_URL . '/app.php/'); ?>"
               style="padding:8px 16px; border-radius:6px; background:#6c757d; color:#fff; text-decoration:none; font-weight:bold;">
                ✕ Xóa lọc
            </a>
        <?php endif; ?>
    </form>
</div>

<!-- ===== DANH SÁCH SẢN PHẨM ===== -->
<div class="ps-products">
    <div class="ps-product-grid">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $sp):
                $masp    = $sp['masp'];
                $tensp   = htmlspecialchars($sp['tensp']);
                $gia     = number_format($sp['gia'], 0, ',', '.');
                $hinhanh = !empty($sp['hinhanh'])
                           ? $imgBaseUrl . htmlspecialchars($sp['hinhanh'])
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
                        <a href="/QLShopDT_API/views/sanpham/chitietsanpham.php?masp=<?php echo $masp; ?>"
                           class="ps-btn ps-btn-primary"
                           style="<?php echo (isset($_SESSION['role']) && $_SESSION['role'] == 0) ? 'width:60%;' : 'width:100%;' ?>justify-content:center">
                            <i class="fa fa-eye"></i> Xem chi tiết
                        </a>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 0): ?>
                        <button onclick="addToCart(<?php echo (int)$masp; ?>, this)"
                                class="ps-btn"
                                style="width:36%;background:#e94560;color:#fff;border:none;cursor:pointer;justify-content:center;">
                            <i class="fa fa-cart-plus"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="ps-empty" style="grid-column:1/-1">
                <div class="ps-empty-icon">🔍</div>
                <p>Không tìm thấy sản phẩm nào!</p>
                <a href="<?php echo BASE_URL; ?>/app.php/" style="color:#e74c3c;">← Quay lại trang chủ</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if (isset($_SESSION['role']) && $_SESSION['role'] == 0): ?>
<a href="/QLShopDT_API/app.php/giohang" class="ps-cart-fab">
    <i class="fa fa-shopping-cart"></i>
</a>
<script>
function addToCart(masp, btn) {
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
    fetch('/QLShopDT_API/app.php/giohang/add', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'masp=' + masp + '&soluong=1'
    }).then(function() {
        btn.innerHTML = '<i class="fa fa-check"></i>';
        btn.style.background = '#10b981';
        setTimeout(function() {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-cart-plus"></i>';
            btn.style.background = '#e94560';
        }, 1500);
    }).catch(function() {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-cart-plus"></i>';
    });
}
</script>
<?php endif; ?>

</body>
</html>
