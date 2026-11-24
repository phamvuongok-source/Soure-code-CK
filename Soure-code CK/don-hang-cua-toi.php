<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

require_login();

$page_title = 'Lịch Sử Mua Hàng';
$nav_active = 'orders';
$extra_css  = ['(custom) Order  Booking History.css'];

$uid  = (int)$_SESSION['user']['id'];
$page = max(1, (int)($_GET['page'] ?? 1));
$per  = 8;

// Lọc theo trạng thái
$status_filter = $_GET['status'] ?? '';
$where  = ['h.id_khach_hang = ?'];
$params = [$uid];
if (isset(ORDER_STATUSES[$status_filter])) {
    $where[]  = 'h.trang_thai_don_hang = ?';
    $params[] = $status_filter;
}
$where_sql = implode(' AND ', $where);

$cnt_st = $pdo->prepare("SELECT COUNT(*) FROM don_hang h WHERE $where_sql");
$cnt_st->execute($params);
$total      = (int)$cnt_st->fetchColumn();
$total_page = max(1, (int)ceil($total / $per));
$page       = min($page, $total_page);
$offset     = ($page - 1) * $per;

$st = $pdo->prepare(
    "SELECT h.*, tp.loai_thanh_toan, tp.trang_thai AS trang_thai_thanh_toan
     FROM don_hang h
     LEFT JOIN thanh_toan tp ON tp.id_don_hang = h.id_don_hang
     WHERE $where_sql
     ORDER BY h.id_don_hang DESC
     LIMIT $per OFFSET $offset"
);
$st->execute($params);
$orders = $st->fetchAll();

// Lấy chi tiết tất cả đơn trong trang này (1 truy vấn)
$order_ids = array_column($orders, 'id_don_hang');
$items_map = [];
if ($order_ids) {
    $in = implode(',', array_fill(0, count($order_ids), '?'));
    $ist = $pdo->prepare(
        "SELECT ct.*, sp.ten_san_pham, sp.hinh_anh, sp.id_danh_muc
         FROM chi_tiet_don_hang ct
         JOIN san_pham sp ON sp.id_san_pham = ct.id_san_pham
         WHERE ct.id_don_hang IN ($in)"
    );
    $ist->execute($order_ids);
    foreach ($ist->fetchAll() as $r) {
        $items_map[$r['id_don_hang']][] = $r;
    }
}

require __DIR__ . '/includes/header.php';
?>

<div class="container" style="max-width:960px; margin:0 auto; padding:0 15px;">
    <div class="breadcrumb" style="margin-top:15px; margin-bottom:15px; font-size:14px; color:#64748b;">
        <a href="<?= BASE_URL ?>index.php" style="color:#0088cc; text-decoration:none;">Trang chủ</a> &raquo;
        <span>Lịch sử mua hàng</span>
    </div>
</div>

<div class="my-orders-wrapper">
    <h2 class="section-title">📦 LỊCH SỬ MUA HÀNG</h2>

    <!-- Lọc trạng thái -->
    <div class="status-tabs">
        <a href="?status=" class="<?= $status_filter === '' ? 'active' : '' ?>">Tất cả</a>
        <?php foreach (ORDER_STATUSES as $key => $label): ?>
            <a href="?status=<?= $key ?>"
               class="<?= $status_filter === $key ? 'active' : '' ?>"><?= e($label) ?></a>
        <?php endforeach; ?>
    </div>

    <?php if ($orders): ?>
        <?php foreach ($orders as $order): ?>
            <?php $order_items = $items_map[$order['id_don_hang']] ?? []; ?>
            <div class="order-card">
                <div class="order-card-header">
                    <div>
                        <strong>Đơn hàng #<?= (int)$order['id_don_hang'] ?></strong>
                        <span class="order-date"> — <?= date('d/m/Y H:i', strtotime($order['ngay_dat'])) ?></span>
                    </div>
                    <div style="display:flex; align-items:center; gap:8px;">
                        <span class="status-badge status-<?= e($order['trang_thai_don_hang']) ?>">
                            <?= e(ORDER_STATUSES[$order['trang_thai_don_hang']] ?? $order['trang_thai_don_hang']) ?>
                        </span>
                        <?php if ($order['trang_thai_don_hang'] === 'ChoXuLy'): ?>
                            <form method="post" action="<?= BASE_URL ?>handlers/xu-ly-huy-don.php" style="display:inline; margin:0;" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng #<?= (int)$order['id_don_hang'] ?> không?');">
                                <input type="hidden" name="id" value="<?= (int)$order['id_don_hang'] ?>">
                                <button type="submit" style="padding:4px 10px; background:#fee2e2; color:#dc2626; border:1px solid #fca5a5; border-radius:4px; font-size:12px; font-weight:600; cursor:pointer;">✕ Hủy đơn</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="order-items-list">
                    <?php foreach ($order_items as $item): ?>
                        <div class="order-line">
                            <div class="item-icon"><?= product_image($item) ?></div>
                            <span class="item-info">
                                <a href="<?= BASE_URL ?>chi-tiet.php?id=<?= (int)$item['id_san_pham'] ?>">
                                    <?= e($item['ten_san_pham']) ?>
                                </a>
                            </span>
                            <span class="item-qty">× <?= (int)$item['so_luong'] ?></span>
                            <span class="item-price"><?= format_price((float)$item['don_gia'] * (int)$item['so_luong']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="order-card-footer">
                    <div class="order-meta">
                        <span>🏠 <?= e($order['dia_chi_giao'] ?? '—') ?></span>
                        <span>📞 <?= e($order['sdt_nguoi_nhan'] ?? '—') ?></span>
                        <span>💳 <?= e($order['loai_thanh_toan'] === 'COD' ? 'Thanh toán khi nhận hàng' : 'Chuyển khoản') ?></span>
                        <span>💰 <?= e($order['trang_thai_thanh_toan'] === 'DaThanhToan' ? '✅ Đã thanh toán' : '⏳ Chưa thanh toán') ?></span>
                    </div>
                    <div class="order-total-row">
                        <strong>Tổng: <?= format_price($order['tong_thanh_toan']) ?></strong>
                    </div>
                    <?php if ($order['ghi_chu']): ?>
                        <div class="order-note">📝 <?= e($order['ghi_chu']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Phân trang -->
        <?php if ($total_page > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_page; $i++): ?>
                    <a href="?status=<?= e($status_filter) ?>&page=<?= $i ?>"
                       class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <div class="empty-state">
            <p>😊 Bạn chưa có đơn hàng nào<?= $status_filter ? ' ở trạng thái này' : '' ?>.</p>
            <a href="<?= BASE_URL ?>san-pham.php">Mua sắm ngay</a>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
