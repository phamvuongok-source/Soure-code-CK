<?php
$admin_page = 'don-hang';
$page_title  = 'Quản Lý Đơn Hàng';
$extra_css   = ['(admin) QUẢN LÝ ĐƠN HÀNG.css'];
require __DIR__ . '/../includes/admin-header.php';

$status_f = $_GET['status'] ?? '';
$keyword  = trim($_GET['keyword'] ?? '');
$page     = max(1, (int)($_GET['page'] ?? 1));
$per      = 15;

$where  = ['1=1'];
$params = [];
if (isset(ORDER_STATUSES[$status_f])) {
    $where[]  = 'h.trang_thai_don_hang = ?';
    $params[] = $status_f;
}
if ($keyword !== '') {
    $where[]  = '(h.id_don_hang = ? OR u.ho_ten LIKE ? OR h.ten_nguoi_nhan LIKE ? OR h.sdt_nguoi_nhan LIKE ?)';
    $kw = '%' . $keyword . '%';
    array_push($params, (int)$keyword, $kw, $kw, $kw);
}
$where_sql = implode(' AND ', $where);

$cnt_st = $pdo->prepare("SELECT COUNT(*) FROM don_hang h JOIN nguoi_dung u ON u.id_nguoi_dung = h.id_khach_hang WHERE $where_sql");
$cnt_st->execute($params);
$total      = (int)$cnt_st->fetchColumn();
$total_page = max(1, (int)ceil($total / $per));
$page       = min($page, $total_page);
$offset     = ($page - 1) * $per;

$st = $pdo->prepare(
    "SELECT h.*, u.ho_ten AS ten_khach, u.email AS email_khach,
            tp.loai_thanh_toan, tp.trang_thai AS trang_thai_thanh_toan
     FROM don_hang h
     JOIN nguoi_dung u ON u.id_nguoi_dung = h.id_khach_hang
     LEFT JOIN thanh_toan tp ON tp.id_don_hang = h.id_don_hang
     WHERE $where_sql
     ORDER BY h.id_don_hang DESC
     LIMIT $per OFFSET $offset"
);
$st->execute($params);
$orders = $st->fetchAll();

// Chi tiết các đơn trong trang này
$order_ids = array_column($orders, 'id_don_hang');
$items_map = [];
if ($order_ids) {
    $in = implode(',', array_fill(0, count($order_ids), '?'));
    $ist = $pdo->prepare(
        "SELECT ct.*, sp.ten_san_pham
         FROM chi_tiet_don_hang ct
         JOIN san_pham sp ON sp.id_san_pham = ct.id_san_pham
         WHERE ct.id_don_hang IN ($in)"
    );
    $ist->execute($order_ids);
    foreach ($ist->fetchAll() as $r) {
        $items_map[$r['id_don_hang']][] = $r;
    }
}

// Đơn hàng đang được chọn (mặc định lấy đơn đầu tiên hoặc theo ?id=)
$selected_id = (int)($_GET['id'] ?? ($orders[0]['id_don_hang'] ?? 0));
$selected_order = null;
foreach ($orders as $o) {
    if ((int)$o['id_don_hang'] === $selected_id) {
        $selected_order = $o;
        break;
    }
}
if (!$selected_order && $selected_id > 0) {
    // Nếu đơn nằm ngoài trang hiện tại, query trực tiếp
    $s_st = $pdo->prepare(
        "SELECT h.*, u.ho_ten AS ten_khach, u.email AS email_khach,
                tp.loai_thanh_toan, tp.trang_thai AS trang_thai_thanh_toan
         FROM don_hang h
         JOIN nguoi_dung u ON u.id_nguoi_dung = h.id_khach_hang
         LEFT JOIN thanh_toan tp ON tp.id_don_hang = h.id_don_hang
         WHERE h.id_don_hang = ?"
    );
    $s_st->execute([$selected_id]);
    $selected_order = $s_st->fetch();
    if ($selected_order) {
        $i_st = $pdo->prepare("SELECT ct.*, sp.ten_san_pham FROM chi_tiet_don_hang ct JOIN san_pham sp ON sp.id_san_pham = ct.id_san_pham WHERE ct.id_don_hang = ?");
        $i_st->execute([$selected_id]);
        $items_map[$selected_id] = $i_st->fetchAll();
    }
}
?>

<h1 class="section-title">Quản lý đơn hàng</h1>

<div class="filter-box">
    <form method="get" id="orderFilterForm" style="display:flex; justify-content:space-between; align-items:center; width:100%; flex-wrap:wrap; gap:10px;">
        <div class="search-line">
            🔍 <input type="text" name="keyword" value="<?= e($keyword) ?>" placeholder="Mã đơn / khách hàng / SĐT...">
            <button type="submit" class="btn-primary">Search</button>
        </div>
        <div>
            <label for="filterStatus">Status: </label>
            <select name="status" id="filterStatus" onchange="this.form.submit()">
                <option value="">[Tất cả ▼]</option>
                <?php foreach (ORDER_STATUSES as $k => $l): ?>
                    <option value="<?= $k ?>" <?= $status_f === $k ? 'selected' : '' ?>><?= e($l) ?></option>
                <?php endforeach; ?>
            </select>
            <?php if ($status_f !== '' || $keyword !== ''): ?>
                <a href="<?= BASE_URL ?>admin/don-hang.php" style="margin-left:8px; font-size:12px; color:#e74c3c; text-decoration:none;">✕ Xóa lọc</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="order-panels">
    <!-- CỘT DANH SÁCH BÊN TRÁI -->
    <div class="left-panel">
        <table class="order-table" id="orderTable">
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>Ngày</th>
                    <th>Tổng</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="orderTableBody">
                <?php foreach ($orders as $o):
                    $oid = (int)$o['id_don_hang'];
                    $isSelected = $oid === ($selected_order['id_don_hang'] ?? 0);
                    $stKey = $o['trang_thai_don_hang'];
                    $badgeClass = match($stKey) {
                        'HoanThanh' => 'status-paid',
                        'DangGiao'  => 'status-shipping',
                        'ChoXuLy'   => 'status-pending',
                        'DaHuy'     => 'status-cancelled',
                        default     => 'status-pending'
                    };
                ?>
                    <tr class="<?= $isSelected ? 'selected' : '' ?>" onclick="location.href='?<?= http_build_query(array_merge($_GET, ['id' => $oid])) ?>'" style="cursor:pointer;">
                        <td class="col-id">#DH<?= $oid ?></td>
                        <td class="col-customer"><?= e($o['ten_khach']) ?></td>
                        <td class="col-date"><?= date('d/m', strtotime($o['ngay_dat'])) ?></td>
                        <td class="col-total"><?= format_price($o['tong_thanh_toan']) ?></td>
                        <td class="col-status <?= $badgeClass ?>"><?= e(ORDER_STATUSES[$stKey] ?? $stKey) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$orders): ?>
                    <tr><td colspan="5" style="text-align:center; padding:20px; color:#94a3b8;">Không có đơn hàng nào.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Phân trang -->
        <?php if ($total_page > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_page; $i++): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- CỘT CHI TIẾT BÊN PHẢI -->
    <div class="right-panel">
        <?php if ($selected_order):
            $selId = (int)$selected_order['id_don_hang'];
            $curItems = $items_map[$selId] ?? [];
            $curStatus = $selected_order['trang_thai_don_hang'];
        ?>
            <div class="detail-box" id="detailBox">
                <h4 id="detailTitle">CHI TIẾT ĐƠN HÀNG #DH<?= $selId ?></h4>
                <div id="customerInfo" style="margin: 0 0 15px; line-height: 1.7;">
                    <div><strong>Khách hàng:</strong> <span><?= e($selected_order['ten_khach']) ?></span></div>
                    <div><strong>Email:</strong> <span><?= e($selected_order['email_khach']) ?></span></div>
                    <div><strong>Người nhận:</strong> <span><?= e($selected_order['ten_nguoi_nhan'] ?? $selected_order['ten_khach']) ?></span></div>
                    <div><strong>Địa chỉ:</strong> <span><?= e($selected_order['dia_chi_giao'] ?? '—') ?></span></div>
                    <div><strong>Ngày đặt:</strong> <span><?= date('d/m/Y H:i', strtotime($selected_order['ngay_dat'])) ?></span></div>
                    <div><strong>Thanh toán:</strong> <span><?= e($selected_order['loai_thanh_toan'] === 'COD' ? 'Tiền mặt khi nhận (COD)' : 'Chuyển khoản') ?> (<?= $selected_order['trang_thai_thanh_toan'] === 'DaThanhToan' ? 'Đã thanh toán' : 'Chưa thanh toán' ?>)</span></div>
                </div>

                <strong>Sản phẩm đặt mua:</strong>
                <div class="items-box" id="detailItems" style="margin-top:6px; margin-bottom:12px;">
                    <?php foreach ($curItems as $it): ?>
                        <div style="display:flex; justify-content:space-between; margin-bottom:5px;">
                            <span><?= e($it['ten_san_pham']) ?></span>
                            <span>x<?= (int)$it['so_luong'] ?></span>
                            <span style="font-weight:bold;"><?= format_price((float)$it['don_gia'] * (int)$it['so_luong']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="total-line" id="detailTotal" style="font-size:16px; font-weight:bold; color:#e67e22; margin-bottom:12px;">
                    Tổng tiền: <?= format_price($selected_order['tong_thanh_toan']) ?>
                </div>

                <form method="post" action="<?= BASE_URL ?>handlers/don-hang.php">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?= $selId ?>">

                    <div style="margin-bottom: 10px;">
                        <strong>SĐT người nhận: </strong>
                        <input type="tel" name="sdt" value="<?= e($selected_order['sdt_nguoi_nhan'] ?? '') ?>" maxlength="10" style="width: 100%; margin-top: 4px; padding:6px; box-sizing: border-box;">
                    </div>

                    <div style="margin-bottom: 12px;">
                        <strong>Ghi chú: </strong>
                        <input type="text" name="ghi_chu" value="<?= e($selected_order['ghi_chu'] ?? '') ?>" placeholder="Thêm ghi chú cho đơn hàng..." style="width: 100%; margin-top: 4px; padding:6px; box-sizing: border-box;">
                    </div>

                    <div style="margin-bottom: 15px;">
                        <strong>Trạng thái: </strong>
                        <select name="trang_thai" id="detailStatus" style="font-weight: bold; min-width: 140px; padding:6px;" <?= in_array($curStatus, ['HoanThanh', 'DaHuy'], true) ? 'disabled' : '' ?>>
                            <?php foreach (ORDER_STATUSES as $k => $l): ?>
                                <option value="<?= $k ?>" <?= $curStatus === $k ? 'selected' : '' ?>><?= e($l) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <?php if (!in_array($curStatus, ['HoanThanh', 'DaHuy'], true)): ?>
                        <div class="detail-actions" style="display:flex; gap:10px;">
                            <button type="submit" formaction="<?= BASE_URL ?>handlers/don-hang.php" name="action" value="cancel" class="btn-danger" onclick="this.form.querySelector('[name=action]').value='cancel'; return confirm('Hủy đơn này và trả hàng về kho?')" style="padding:8px 16px; border:none; border-radius:4px; background:#e74c3c; color:#fff; cursor:pointer; font-weight:bold;">
                                Hủy đơn
                            </button>
                            <button type="submit" class="btn-success" style="padding:8px 16px; border:none; border-radius:4px; background:#2ecc71; color:#fff; cursor:pointer; font-weight:bold;">
                                Cập nhật
                            </button>
                        </div>
                    <?php else: ?>
                        <p style="color:#888; font-style:italic;">Đơn hàng này đã <?= $curStatus === 'HoanThanh' ? 'hoàn thành' : 'bị hủy' ?>, không thể sửa đổi.</p>
                    <?php endif; ?>
                </form>
            </div>
        <?php else: ?>
            <div style="padding:40px; text-align:center; color:#94a3b8; background:#fff; border-radius:8px;">
                Chọn một đơn hàng bên trái để xem chi tiết
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../includes/admin-footer.php'; ?>
