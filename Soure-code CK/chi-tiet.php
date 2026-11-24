<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$id = (int)($_GET['id'] ?? 0);
if ($id < 1) {
    header('Location: ' . BASE_URL . 'san-pham.php');
    exit;
}

// Lấy chi tiết sản phẩm
$st = $pdo->prepare(
    'SELECT sp.*, dm.ten_danh_muc
     FROM san_pham sp
     LEFT JOIN danh_muc dm ON dm.id_danh_muc = sp.id_danh_muc
     WHERE sp.id_san_pham = ?'
);
$st->execute([$id]);
$sp = $st->fetch();

if (!$sp) {
    http_response_code(404);
    echo '<h2 style="text-align:center;margin:60px">Sản phẩm không tồn tại.</h2>';
    exit;
}

// Đánh giá sản phẩm
$dg_st = $pdo->prepare(
    'SELECT g.*, u.ho_ten
     FROM danh_gia g
     JOIN nguoi_dung u ON u.id_nguoi_dung = g.id_khach_hang
     WHERE g.id_san_pham = ?
     ORDER BY g.ngay_danh_gia DESC'
);
$dg_st->execute([$id]);
$reviews = $dg_st->fetchAll();

$avg_star = 0;
if ($reviews) {
    $avg_star = array_sum(array_column($reviews, 'so_sao')) / count($reviews);
}

// Cho phép người dùng đã đăng nhập đánh giá
$can_review = is_logged_in();

// Sản phẩm cùng danh mục (gợi ý)
$rel_st = $pdo->prepare(
    'SELECT * FROM san_pham
     WHERE id_danh_muc = ? AND id_san_pham <> ? AND so_luong_ton > 0
     ORDER BY id_san_pham DESC LIMIT 4'
);
$rel_st->execute([(int)$sp['id_danh_muc'], $id]);
$related = $rel_st->fetchAll();

$page_title = $sp['ten_san_pham'];
$extra_css  = ['(custom) chi tiết sp.css'];
require __DIR__ . '/includes/header.php';
?>

<main class="main-content-wrap">
    <div class="container detail-wrapper">
        <div class="breadcrumb" style="margin-top:15px; margin-bottom:15px; font-size:14px; color:#64748b;">
            <a href="<?= BASE_URL ?>index.php" style="color:#0088cc; text-decoration:none;">Trang chủ</a> &raquo;
            <a href="<?= BASE_URL ?>san-pham.php?category=<?= (int)$sp['id_danh_muc'] ?>" style="color:#0088cc; text-decoration:none;"><?= e($sp['ten_danh_muc'] ?? 'Nhạc cụ') ?></a> &raquo;
            <span><?= e($sp['ten_san_pham']) ?></span>
        </div>

        <!-- Khối chi tiết nhạc cụ -->
        <section class="detail-card">
            <div class="detail-img-box">
                <div class="detail-img-icon">
                    <?= product_image($sp) ?>
                </div>
                <div style="font-weight: bold; color: #64748b; font-size: 15px; margin-top: 10px; text-transform: uppercase;">
                    <?= e($sp['thuong_hieu'] ?? 'Chính hãng') ?>
                </div>
            </div>

            <div class="detail-info">
                <h1><?= e($sp['ten_san_pham']) ?></h1>

                <div class="meta-line">
                    <?php if ($sp['thuong_hieu']): ?>
                        <span>Thương hiệu: <strong><?= e($sp['thuong_hieu']) ?></strong></span>
                    <?php endif; ?>
                    <span>Danh mục: <strong><?= e($sp['ten_danh_muc'] ?? 'Nhạc cụ') ?></strong></span>
                    <span>Mã SP: <strong>#<?= (int)$sp['id_san_pham'] ?></strong></span>
                    <?php if ($reviews): ?>
                        <span style="color:#f39c12;">
                            <?= str_repeat('★', (int)round($avg_star)) . str_repeat('☆', 5 - (int)round($avg_star)) ?>
                            (<?= number_format($avg_star, 1) ?>/5)
                        </span>
                    <?php endif; ?>
                </div>

                <div class="detail-price-box">
                    <span class="price-huge"><?= format_price($sp['gia_ban']) ?></span>
                    <?php if ((int)$sp['so_luong_ton'] > 0): ?>
                        <span class="stock-badge">✓ Còn <?= (int)$sp['so_luong_ton'] ?> sản phẩm</span>
                    <?php else: ?>
                        <span class="stock-badge" style="background:#fee2e2; color:#ef4444; border-color:#fca5a5;">❌ Hết hàng</span>
                    <?php endif; ?>
                </div>

                <div class="desc-section">
                    <h4>Đặc điểm nổi bật:</h4>
                    <p><?= !empty($sp['mo_ta']) ? nl2br(e($sp['mo_ta'])) : 'Nhạc cụ chính hãng phân phối bởi Cửa hàng RE:QUIEM. Sản phẩm đạt chuẩn âm thanh phòng thu, chất liệu hoàn thiện tỉ mỉ, độ bền cao và được bảo hành chính hãng toàn quốc 12 tháng.' ?></p>
                </div>

                <?php if ((int)$sp['so_luong_ton'] > 0): ?>
                    <form class="add-cart-form" method="post" action="<?= BASE_URL ?>handlers/xu-ly-gio-hang.php">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="id" value="<?= (int)$sp['id_san_pham'] ?>">
                        <label for="qtyInput" style="font-weight: bold; color: #2c3e50;">Số lượng:</label>
                        <input type="number" id="qtyInput" name="so_luong" value="1" min="1" max="<?= (int)$sp['so_luong_ton'] ?>" class="qty-input">
                        <button type="submit" class="btn-buy-now">🛒 THÊM VÀO GIỎ HÀNG</button>
                    </form>
                <?php endif; ?>
            </div>
        </section>

        <!-- Sản phẩm tương tự -->
        <?php if ($related): ?>
            <section class="related-section">
                <h3 class="related-title">Nhạc cụ cùng chuyên mục</h3>
                <div class="related-grid">
                    <?php foreach ($related as $r): ?>
                        <div class="related-card">
                            <div style="font-size: 36px; height: 70px; display:flex; align-items:center; justify-content:center; margin-bottom: 8px;">
                                <?= product_image($r) ?>
                            </div>
                            <a href="<?= BASE_URL ?>chi-tiet.php?id=<?= (int)$r['id_san_pham'] ?>"><?= e($r['ten_san_pham']) ?></a>
                            <div class="related-price"><?= format_price($r['gia_ban']) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <!-- Khu vực đánh giá -->
        <section class="desc-section" style="background:#fff; padding:25px; border-radius:8px; border:1px solid #edf2f7; margin-top:25px;" id="danh-gia">
            <h3 style="color:#2c3e50; font-size:18px; margin-bottom:15px; border-bottom:2px solid #1ca9d4; padding-bottom:8px; display:inline-block;">ĐÁNH GIÁ SẢN PHẨM</h3>

            <?php if ($can_review): ?>
                <div style="background:#f8fafc; padding:18px; border-radius:6px; margin-bottom:20px; border:1px solid #e2e8f0;">
                    <h4 style="margin-bottom:10px; color:#1e293b;">📝 Viết đánh giá của bạn</h4>
                    <form method="post" action="<?= BASE_URL ?>handlers/xu-ly-danh-gia.php">
                        <input type="hidden" name="id_san_pham" value="<?= $id ?>">
                        <div style="margin-bottom:10px;">
                            <label style="font-weight:600; margin-right:10px;">Số sao:</label>
                            <?php for ($s = 5; $s >= 1; $s--): ?>
                                <label style="margin-right:12px; cursor:pointer;">
                                    <input type="radio" name="so_sao" value="<?= $s ?>" <?= $s === 5 ? 'checked' : '' ?>> <?= $s ?> ★
                                </label>
                            <?php endfor; ?>
                        </div>
                        <textarea name="binh_luan" maxlength="1000" rows="3" placeholder="Cảm nhận của bạn về chất lượng âm thanh, độ hoàn thiện..." style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:4px;" required></textarea>
                        <button type="submit" style="margin-top:10px; background:#1ca9d4; color:#fff; border:none; padding:8px 18px; border-radius:4px; font-weight:bold; cursor:pointer;">Gửi đánh giá</button>
                    </form>
                </div>
            <?php else: ?>
                <div style="background:#f8fafc; padding:14px; border-radius:6px; margin-bottom:20px; border:1px dashed #cbd5e1; font-size:14px; color:#64748b;">
                    🔒 Vui lòng <a href="<?= BASE_URL ?>dang-nhap.php" style="color:#0088cc; font-weight:bold; text-decoration:none;">Đăng nhập</a> để viết đánh giá cho sản phẩm này.
                </div>
            <?php endif; ?>

            <?php if ($reviews): ?>
                <div style="display:flex; flex-direction:column; gap:12px;">
                    <?php foreach ($reviews as $r): ?>
                        <div style="border-bottom:1px solid #f1f5f9; padding-bottom:10px;">
                            <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                                <strong><?= e($r['ho_ten']) ?></strong>
                                <span style="font-size:12px; color:#94a3b8;"><?= date('d/m/Y', strtotime($r['ngay_danh_gia'])) ?></span>
                            </div>
                            <div style="color:#f39c12; font-size:14px; margin-bottom:4px;">
                                <?= str_repeat('★', (int)$r['so_sao']) . str_repeat('☆', 5 - (int)$r['so_sao']) ?>
                            </div>
                            <?php if ($r['binh_luan']): ?>
                                <p style="color:#475569; font-size:14px; margin:0;"><?= e($r['binh_luan']) ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="color:#94a3b8; font-style:italic;">Chưa có đánh giá nào cho sản phẩm này.</p>
            <?php endif; ?>
        </section>
    </div>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
