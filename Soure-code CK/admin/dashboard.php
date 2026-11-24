<?php
$admin_page = 'dashboard';
$page_title  = 'Dashboard Overview';
$extra_scripts = ['https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js'];
require __DIR__ . '/../includes/admin-header.php';

// Thống kê tổng quan
$stats = [];
$stats['total_orders']   = (int)$pdo->query("SELECT COUNT(*) FROM don_hang")->fetchColumn();
$stats['pending_orders'] = (int)$pdo->query("SELECT COUNT(*) FROM don_hang WHERE trang_thai_don_hang = 'ChoXuLy'")->fetchColumn();
$stats['total_products'] = (int)$pdo->query("SELECT COUNT(*) FROM san_pham")->fetchColumn();
$stats['low_stock']      = (int)$pdo->query("SELECT COUNT(*) FROM san_pham WHERE so_luong_ton <= 5 AND so_luong_ton > 0")->fetchColumn();
$stats['total_users']    = (int)$pdo->query("SELECT COUNT(*) FROM nguoi_dung WHERE id_vai_tro = " . ROLE_CUSTOMER)->fetchColumn();
$stats['revenue_total']  = (float)$pdo->query(
    "SELECT COALESCE(SUM(tong_thanh_toan), 0) FROM don_hang WHERE trang_thai_don_hang = 'HoanThanh'"
)->fetchColumn();

// 10 đơn hàng mới nhất
$recent_orders = $pdo->query(
    "SELECT h.id_don_hang, h.ngay_dat, h.trang_thai_don_hang, h.tong_thanh_toan,
            u.ho_ten AS ten_khach
     FROM don_hang h
     JOIN nguoi_dung u ON u.id_nguoi_dung = h.id_khach_hang
     ORDER BY h.id_don_hang DESC
     LIMIT 10"
)->fetchAll();

// Doanh thu 6 tháng gần nhất để vẽ chart
$rev_rows = $pdo->query(
    "SELECT DATE_FORMAT(ngay_dat, '%m/%Y') AS thang, COALESCE(SUM(tong_thanh_toan), 0) AS doanh_thu
     FROM don_hang
     WHERE trang_thai_don_hang = 'HoanThanh'
     GROUP BY thang
     ORDER BY ngay_dat DESC
     LIMIT 6"
)->fetchAll();
$rev_rows = array_reverse($rev_rows);
$chart_labels = array_column($rev_rows, 'thang');
$chart_data   = array_column($rev_rows, 'doanh_thu');
?>

<h1 class="section-title">Dashboard Overview</h1>

<!-- Stat Cards -->
<div class="stats-grid">
    <div class="card">
        <div class="card-info">
            <h3>Khách Hàng</h3>
            <div class="value"><?= number_format($stats['total_users']) ?></div>
        </div>
        <i class="fa-solid fa-users card-icon"></i>
    </div>
    <div class="card">
        <div class="card-info">
            <h3>Tổng Đơn Hàng</h3>
            <div class="value"><?= number_format($stats['total_orders']) ?></div>
        </div>
        <i class="fa-solid fa-box card-icon"></i>
    </div>
    <div class="card">
        <div class="card-info">
            <h3>Sản Phẩm</h3>
            <div class="value"><?= number_format($stats['total_products']) ?></div>
        </div>
        <i class="fa-solid fa-guitar card-icon"></i>
    </div>
    <div class="card">
        <div class="card-info">
            <h3>Tổng Doanh Thu</h3>
            <div class="value"><?= format_price($stats['revenue_total']) ?></div>
        </div>
        <i class="fa-solid fa-coins card-icon"></i>
    </div>
</div>

<!-- Chart Doanh Thu -->
<div class="dashboard-section">
    <h3>📈 Biểu Đồ Doanh Thu</h3>
    <canvas id="revenueChart" height="90"></canvas>
</div>

<!-- Đơn hàng gần đây -->
<div class="dashboard-section">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
        <h3 style="margin:0;">📋 Đơn Hàng Gần Đây</h3>
        <a href="<?= BASE_URL ?>admin/don-hang.php" style="color:#0088cc; font-size:14px; text-decoration:none; font-weight:600;">Xem tất cả →</a>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Mã Đơn</th>
                    <th>Khách Hàng</th>
                    <th>Ngày Đặt</th>
                    <th>Tổng Tiền</th>
                    <th>Trạng Thái</th>
                    <th>Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_orders as $o):
                    $badgeClass = match($o['trang_thai_don_hang']) {
                        'HoanThanh' => 'badge-success',
                        'DangGiao'  => 'badge-info',
                        'ChoXuLy'   => 'badge-warning',
                        'DaHuy'     => 'badge-danger',
                        default     => 'badge-info'
                    };
                ?>
                    <tr>
                        <td>#ORD-<?= (int)$o['id_don_hang'] ?></td>
                        <td><?= e($o['ten_khach']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($o['ngay_dat'])) ?></td>
                        <td style="font-weight:bold; color:#e67e22;"><?= format_price($o['tong_thanh_toan']) ?></td>
                        <td>
                            <span class="badge <?= $badgeClass ?>">
                                <?= e(ORDER_STATUSES[$o['trang_thai_don_hang']] ?? $o['trang_thai_don_hang']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?= BASE_URL ?>admin/don-hang.php?id=<?= (int)$o['id_don_hang'] ?>" style="color:#0088cc; text-decoration:none; font-weight:bold;">Chi tiết</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$recent_orders): ?>
                    <tr><td colspan="6" style="text-align:center; padding:20px; color:#94a3b8;">Chưa có đơn hàng nào.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
const ctx = document.getElementById('revenueChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($chart_labels ?: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6'], JSON_UNESCAPED_UNICODE) ?>,
        datasets: [{
            label: 'Doanh thu (VNĐ)',
            data: <?= json_encode(array_map('floatval', $chart_data ?: [0,0,0,0,0,0])) ?>,
            borderColor: '#0088cc',
            backgroundColor: 'rgba(0, 136, 204, 0.1)',
            fill: true,
            tension: 0.3
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: {
                ticks: {
                    callback: val => Number(val).toLocaleString('vi-VN') + ' đ'
                }
            }
        }
    }
});
</script>

<?php require __DIR__ . '/../includes/admin-footer.php'; ?>
