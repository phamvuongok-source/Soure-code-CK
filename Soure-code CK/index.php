<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = 'Trang Chủ';
$nav_active = 'home';

// Lấy danh sách danh mục từ database
$categories = $pdo->query("SELECT id_danh_muc, ten_danh_muc FROM danh_muc ORDER BY id_danh_muc ASC")->fetchAll(PDO::FETCH_KEY_PAIR);

// 1) Lấy 12 sản phẩm nổi bật / bán chạy
$banchayProducts = $pdo->query(
    "SELECT * FROM san_pham WHERE so_luong_ton > 0 ORDER BY id_san_pham DESC LIMIT 12"
)->fetchAll();

// 2) Lấy sản phẩm cho từng danh mục (mỗi mục có ít nhất 10-12 sản phẩm)
$categoryProducts = [];
$catStmt = $pdo->prepare("SELECT * FROM san_pham WHERE id_danh_muc = ? AND so_luong_ton > 0 ORDER BY id_san_pham DESC");
foreach ($categories as $catId => $catName) {
    $catStmt->execute([$catId]);
    $categoryProducts[$catId] = $catStmt->fetchAll();
}

require __DIR__ . '/includes/header.php';

/**
 * Hàm hiển thị từng mục sản phẩm dạng Slider / Carousel:
 * - Hiển thị 6 sản phẩm mỗi lần
 * - Có nút chuyển qua để hiển thị các sản phẩm tiếp theo
 * - Dưới 6 sản phẩm là các dấu chấm hiển thị trang hiện tại và số sản phẩm còn lại
 */
function render_section_slider(string $sliderId, string $title, string $icon, array $products, string $viewAllUrl = ''): void {
    $totalItems = count($products);
    $pageSize = 6;
    $totalPages = max(1, (int)ceil($totalItems / $pageSize));
    $chunks = array_chunk($products, $pageSize);
    ?>
    <section class="container category-showcase-section" id="<?= e($sliderId) ?>">
        <div class="section-head-flex">
            <h3 class="section-head-title">
                <span class="cat-icon"><?= $icon ?></span>
                <span><?= e($title) ?></span>
                <span class="category-total-badge"><?= $totalItems ?> sản phẩm</span>
            </h3>
            <?php if ($viewAllUrl !== ''): ?>
                <a class="btn-view-all-cat" href="<?= $viewAllUrl ?>">Xem tất cả &rarr;</a>
            <?php endif; ?>
        </div>

        <?php if ($totalItems > 0): ?>
            <div class="product-slider-component" data-slider-id="<?= e($sliderId) ?>" data-total-items="<?= $totalItems ?>" data-page-size="<?= $pageSize ?>">
                <div class="slider-nav-wrapper">
                    <?php if ($totalPages > 1): ?>
                        <button type="button" class="slider-btn prev-btn" aria-label="sản phẩm trước" title="Chuyển về trước">&#10094;</button>
                        <button type="button" class="slider-btn next-btn" aria-label="sản phẩm tiếp theo" title="Chuyển qua sản phẩm tiếp theo">&#10095;</button>
                    <?php endif; ?>

                    <div class="slider-viewport">
                        <div class="slider-track">
                            <?php foreach ($chunks as $pageIndex => $pageProducts): ?>
                                <div class="slider-page" data-page="<?= $pageIndex + 1 ?>">
                                    <div class="slider-grid">
                                        <?php foreach ($pageProducts as $sp): ?>
                                            <div class="product-card">
                                                <div class="img-placeholder" style="font-size:40px; flex-direction:column;">
                                                    <?= product_image($sp) ?>
                                                    <?php if (!empty($sp['thuong_hieu'])): ?>
                                                        <span style="font-size:13px; font-weight:normal; margin-top:5px; color:#64748b;"><?= e($sp['thuong_hieu']) ?></span>
                                                    <?php endif; ?>
                                                </div>
                                                <h4><a href="<?= BASE_URL ?>chi-tiet.php?id=<?= (int)$sp['id_san_pham'] ?>"><?= e($sp['ten_san_pham']) ?></a></h4>
                                                <p class="price"><?= format_price($sp['gia_ban']) ?></p>
                                                <form method="post" action="<?= BASE_URL ?>handlers/xu-ly-gio-hang.php">
                                                    <input type="hidden" name="action" value="add">
                                                    <input type="hidden" name="id" value="<?= (int)$sp['id_san_pham'] ?>">
                                                    <button type="submit" class="btn-add">🛒 Thêm vào giỏ</button>
                                                </form>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Điều khiển dưới 6 sản phẩm: Dấu chấm trang & Hiển thị trang hiện tại -->
                <div class="slider-bottom-controls">
                    <div class="slider-dots">
                        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                            <button type="button" class="slider-dot <?= $p === 1 ? 'active' : '' ?>" 
                                    data-page="<?= $p ?>" 
                                    aria-label="Chuyển đến trang <?= $p ?>"
                                    title="Trang <?= $p ?> / <?= $totalPages ?>"></button>
                        <?php endfor; ?>
                    </div>
                    <div class="slider-info-badge">
                        <span>Trang <strong class="status-current-page">1</strong> / <span class="status-total-pages"><?= $totalPages ?></span></span>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <p style="text-align:center; color:#94a3b8; padding:30px 0;">Mục này chưa có sản phẩm nào.</p>
        <?php endif; ?>
    </section>
    <?php
}
?>

<!-- BANNER ĐẦU TRANG -->
<section class="hero-banner">
    <h2>ÂM NHẠC KHƠI NGUỒN CẢM HỨNG</h2>
    <p>Ưu đãi đến 30% cho các dòng Piano &amp; Guitar điện chính hãng tháng này</p>
    <button class="btn-shop" type="button" onclick="location.href='#banchay'">Khám phá ngay</button>
</section>

<!-- MỤC 1: NHẠC CỤ BÁN CHẠY -->
<?php 
render_section_slider(
    'banchay', 
    'NHẠC CỤ BÁN CHẠY & NỔI BẬT', 
    '🔥', 
    $banchayProducts, 
    BASE_URL . 'san-pham.php?sort=newest'
);
?>

<!-- CÁC MỤC THEO TỪNG DANH MỤC SẢN PHẨM (MỖI MỤC >= 10 SẢN PHẨM, HIỂN THỊ 6 SP/LẦN) -->
<?php foreach ($categories as $catId => $catName): ?>
    <?php
    $catProds = $categoryProducts[$catId] ?? [];
    render_section_slider(
        'cat-' . $catId, 
        mb_strtoupper($catName, 'UTF-8'), 
        category_icon($catId), 
        $catProds, 
        BASE_URL . 'san-pham.php?category=' . $catId
    );
    ?>
<?php endforeach; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
