<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = 'Danh Sách Nhạc Cụ';
$extra_css  = ['(custom) Danh sách.css'];

// --- Lọc và tìm kiếm ---
$keyword    = trim($_GET['keyword'] ?? '');
$cat_id     = (int)($_GET['category'] ?? 0);
$min_price  = (int)($_GET['min_price'] ?? 0);
$max_price  = (int)($_GET['max_price'] ?? 0);
$sort       = $_GET['sort'] ?? 'newest';
$page       = max(1, (int)($_GET['page'] ?? 1));
$per_page   = 6;

$where  = ['1=1'];
$params = [];

if ($keyword !== '') {
    $words = array_values(array_filter(explode(' ', preg_replace('/\s+/', ' ', $keyword))));
    if ($words) {
        $wordWheres = [];
        foreach ($words as $w) {
            $wordWheres[] = '(sp.ten_san_pham LIKE ? OR sp.thuong_hieu LIKE ? OR IFNULL(sp.mo_ta, \'\') LIKE ?)';
            $kw = '%' . $w . '%';
            $params = array_merge($params, [$kw, $kw, $kw]);
        }
        $where[] = '(' . implode(' AND ', $wordWheres) . ')';
    }
}
if ($cat_id > 0) {
    $where[]  = 'sp.id_danh_muc = ?';
    $params[] = $cat_id;
}
if ($min_price > 0) {
    $where[]  = 'sp.gia_ban >= ?';
    $params[] = $min_price;
}
if ($max_price > 0 && $max_price >= $min_price) {
    $where[]  = 'sp.gia_ban <= ?';
    $params[] = $max_price;
}
$where[] = 'sp.so_luong_ton > 0';

$where_sql = implode(' AND ', $where);

$order_sql = match($sort) {
    'price_asc'  => 'sp.gia_ban ASC',
    'price_desc' => 'sp.gia_ban DESC',
    'name_asc'   => 'sp.ten_san_pham ASC',
    default      => 'sp.id_san_pham DESC',
};

// Tổng số sản phẩm để phân trang
$cnt_st = $pdo->prepare("SELECT COUNT(*) FROM san_pham sp WHERE $where_sql");
$cnt_st->execute($params);
$total      = (int)$cnt_st->fetchColumn();
$total_page = max(1, (int)ceil($total / $per_page));
$page       = min($page, $total_page);
$offset     = ($page - 1) * $per_page;

$st = $pdo->prepare(
    "SELECT sp.*, dm.ten_danh_muc
     FROM san_pham sp
     LEFT JOIN danh_muc dm ON dm.id_danh_muc = sp.id_danh_muc
     WHERE $where_sql
     ORDER BY $order_sql
     LIMIT $per_page OFFSET $offset"
);
$st->execute($params);
$products = $st->fetchAll();

if ($cat_id > 0) {
    $nav_active = "cat$cat_id";
}

require __DIR__ . '/includes/header.php';
?>

<main class="main-content-wrap">
    <div class="container">
        <div class="breadcrumb" style="margin-top:15px; margin-bottom:15px; font-size:14px; color:#64748b;">
            <a href="<?= BASE_URL ?>index.php" style="color:#0088cc; text-decoration:none;">Trang chủ</a> &raquo;
            <span><?= $cat_id > 0 && isset($categories[$cat_id]) ? e($categories[$cat_id]) : ($keyword !== '' ? 'Tìm kiếm: "' . e($keyword) . '"' : 'Tất cả nhạc cụ') ?></span>
        </div>

        <div class="shop-container">
            <!-- CỘT BỘ LỌC BÊN TRÁI -->
            <aside class="filter-sidebar">
                <form id="filterForm" method="get" action="<?= BASE_URL ?>san-pham.php">
                    <?php if ($keyword !== ''): ?>
                        <input type="hidden" name="keyword" value="<?= e($keyword) ?>">
                    <?php endif; ?>

                    <!-- Bộ lọc Danh Mục -->
                    <div class="filter-group">
                        <h4>Danh Mục</h4>
                        <label class="filter-item">
                            <input type="radio" name="category" value="0" <?= $cat_id === 0 ? 'checked' : '' ?> onchange="this.form.submit()"> Tất cả
                        </label>
                        <?php foreach ($categories as $id => $name): ?>
                            <label class="filter-item">
                                <input type="radio" name="category" value="<?= $id ?>" <?= $cat_id === $id ? 'checked' : '' ?> onchange="this.form.submit()"> <?= e($name) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <!-- Bộ lọc Khoảng Giá -->
                    <div class="filter-group">
                        <h4>Khoảng Giá (VNĐ)</h4>
                        <div class="price-inputs">
                            <input type="number" name="min_price" placeholder="Từ (đ)" step="100000" value="<?= $min_price > 0 ? $min_price : '' ?>">
                            <span>-</span>
                            <input type="number" name="max_price" placeholder="Đến (đ)" step="100000" value="<?= $max_price > 0 ? $max_price : '' ?>">
                        </div>
                        <button type="submit" class="btn-filter-apply" style="margin-top:10px; width:100%; padding:8px; background:#1ca9d4; color:#fff; border:none; border-radius:4px; cursor:pointer; font-weight:bold;">Áp dụng giá</button>
                    </div>

                    <a href="<?= BASE_URL ?>san-pham.php" class="btn-reset-filter" style="display:block; text-align:center; margin-top:12px; color:#e74c3c; font-size:13px; text-decoration:none;">✕ Xóa bộ lọc</a>
                </form>
            </aside>

            <!-- CỘT DANH SÁCH NHẠC CỤ BÊN PHẢI -->
            <section class="shop-main" style="flex-grow:1;">
                <div class="sort-bar" style="display:flex; justify-content:space-between; align-items:center; background:#fff; padding:12px 18px; border-radius:8px; border:1px solid #edf2f7; margin-bottom:20px;">
                    <span>Tìm thấy <strong style="color:#e67e22;"><?= $total ?></strong> nhạc cụ</span>
                    <div>
                        <label for="sortSelect">Sắp xếp: </label>
                        <select id="sortSelect" onchange="location.href='?<?= http_build_query(array_merge($_GET, ['sort' => ''])) ?>' + this.value" style="padding:6px 10px; border-radius:4px; border:1px solid #cbd5e1;">
                            <option value="newest"     <?= $sort === 'newest'     ? 'selected' : '' ?>>Mới nhất</option>
                            <option value="price_asc"  <?= $sort === 'price_asc'  ? 'selected' : '' ?>>Giá tăng dần</option>
                            <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Giá giảm dần</option>
                            <option value="name_asc"   <?= $sort === 'name_asc'   ? 'selected' : '' ?>>Tên A - Z</option>
                        </select>
                    </div>
                </div>

                <?php if ($products): ?>
                    <div class="product-grid">
                        <?php foreach ($products as $sp): ?>
                            <div class="product-card">
                                <a href="<?= BASE_URL ?>chi-tiet.php?id=<?= (int)$sp['id_san_pham'] ?>" style="text-decoration:none; color:inherit; display:block;">
                                    <div class="product-thumb" style="font-size:45px; height:140px; display:flex; align-items:center; justify-content:center; background:#f8fafc; border-radius:6px; margin-bottom:10px;">
                                        <?= product_image($sp) ?>
                                    </div>
                                    <?php if ($sp['thuong_hieu']): ?>
                                        <span class="brand-tag" style="font-size:12px; color:#94a3b8; text-transform:uppercase; font-weight:600; display:block;"><?= e($sp['thuong_hieu']) ?></span>
                                    <?php endif; ?>
                                    <h3 class="product-title" style="font-size:15px; margin:6px 0; color:#1e293b; min-height:40px; line-height:1.3;"><?= e($sp['ten_san_pham']) ?></h3>
                                    <div class="price-tag" style="font-size:16px; font-weight:bold; color:#e67e22; margin-bottom:10px;"><?= format_price($sp['gia_ban']) ?></div>
                                </a>
                                <form method="post" action="<?= BASE_URL ?>handlers/xu-ly-gio-hang.php">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="id" value="<?= (int)$sp['id_san_pham'] ?>">
                                    <button type="submit" class="btn-add-cart" style="width:100%; padding:8px; background:#f39c12; color:#fff; border:none; border-radius:4px; font-weight:bold; cursor:pointer;">🛒 Thêm vào giỏ</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Phân trang 6 sản phẩm/lần với nút chuyển qua và dấu chấm -->
                    <?php if ($total_page > 1): ?>
                        <?php 
                        $queryParams = $_GET;
                        ?>
                        <div class="slider-bottom-controls" style="margin-top:30px;">
                            <!-- Nút chuyển trang & Dấu chấm -->
                            <div style="display:flex; align-items:center; gap:12px;">
                                <?php if ($page > 1): 
                                    $queryParams['page'] = $page - 1;
                                ?>
                                    <a href="?<?= http_build_query($queryParams) ?>" class="slider-btn prev-btn" style="position:static; transform:none;" title="Xem 6 sản phẩm trước">&#10094;</a>
                                <?php endif; ?>

                                <div class="slider-dots">
                                    <?php for ($i = 1; $i <= $total_page; $i++): 
                                        $queryParams['page'] = $i;
                                    ?>
                                        <a href="?<?= http_build_query($queryParams) ?>" 
                                           class="slider-dot <?= $i === $page ? 'active' : '' ?>" 
                                           title="Trang <?= $i ?> / <?= $total_page ?>"
                                           style="display:inline-block; text-decoration:none;"></a>
                                    <?php endfor; ?>
                                </div>

                                <?php if ($page < $total_page): 
                                    $queryParams['page'] = $page + 1;
                                ?>
                                    <a href="?<?= http_build_query($queryParams) ?>" class="slider-btn next-btn" style="position:static; transform:none;" title="Xem 6 sản phẩm tiếp theo">&#10095;</a>
                                <?php endif; ?>
                            </div>

                            <!-- Hiển thị đang ở trang nào -->
                            <div class="slider-info-badge">
                                <span>Trang <strong class="status-current-page"><?= $page ?></strong> / <span class="status-total-pages"><?= $total_page ?></span></span>
                            </div>
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <div style="background:#fff; padding:40px; text-align:center; border-radius:8px; border:1px solid #edf2f7;">
                        <p style="font-size:18px; color:#64748b;">😕 Không tìm thấy nhạc cụ nào phù hợp với bộ lọc.</p>
                        <a href="<?= BASE_URL ?>san-pham.php" style="display:inline-block; margin-top:12px; color:#0088cc; font-weight:bold; text-decoration:none;">Xem tất cả nhạc cụ →</a>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </div>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
