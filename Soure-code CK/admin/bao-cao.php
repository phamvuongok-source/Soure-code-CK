<?php
$admin_page = 'bao-cao';
$page_title  = 'Báo Cáo & Thống Kê';
$extra_css   = ['(admin) Report  Statistics.css'];
require __DIR__ . '/../includes/admin-header.php';

// Thống kê tổng hợp
$total_users = (int)$pdo->query("SELECT COUNT(*) FROM nguoi_dung WHERE id_vai_tro = " . ROLE_CUSTOMER)->fetchColumn();
$total_orders = (int)$pdo->query("SELECT COUNT(*) FROM don_hang")->fetchColumn();
$total_rev = (float)$pdo->query("SELECT COALESCE(SUM(tong_thanh_toan), 0) FROM don_hang WHERE trang_thai_don_hang = 'HoanThanh'")->fetchColumn();

// Đơn hàng theo tháng (4 tháng gần nhất)
$orders_by_month = $pdo->query(
    "SELECT DATE_FORMAT(ngay_dat, 'T%m') AS thang, COUNT(*) AS so_don
     FROM don_hang
     GROUP BY thang
     ORDER BY ngay_dat DESC
     LIMIT 4"
)->fetchAll();
$orders_by_month = array_reverse($orders_by_month);
$max_orders = max(array_merge([1], array_column($orders_by_month, 'so_don')));

// Top 5 sản phẩm bán chạy nhất
$top_products = $pdo->query(
    "SELECT sp.ten_san_pham, SUM(ct.so_luong) AS tong_ban, SUM(ct.so_luong * ct.don_gia) AS doanh_thu
     FROM chi_tiet_don_hang ct
     JOIN san_pham sp ON sp.id_san_pham = ct.id_san_pham
     JOIN don_hang h ON h.id_don_hang = ct.id_don_hang
     WHERE h.trang_thai_don_hang = 'HoanThanh'
     GROUP BY sp.id_san_pham
     ORDER BY tong_ban DESC
     LIMIT 5"
)->fetchAll();
?>

<div class="container">
    <h1 class="section-title">Báo cáo &amp; Thống kê</h1>

    <!-- KPI ROW -->
    <div class="kpi-row">
        <div class="kpi-box">
            <div class="kpi-title">[ 👥 Users ]</div>
            <div class="kpi-num"><?= number_format($total_users) ?></div>
            <div class="kpi-trend">&uarr; Khách hàng</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-title">[ 📦 Orders ]</div>
            <div class="kpi-num"><?= number_format($total_orders) ?></div>
            <div class="kpi-trend">&uarr; Đơn đặt hàng</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-title">[ 💰 Revenue ]</div>
            <div class="kpi-num"><?= format_price($total_rev) ?></div>
            <div class="kpi-trend">&uarr; Doanh thu thực</div>
        </div>
    </div>

    <!-- CHARTS ROW -->
    <div class="charts-row">
        <div class="chart-card">
            <h4>DOANH THU <span>Thống kê tổng quan</span></h4>
            <div class="chart-body line-box" style="display:flex; align-items:center; justify-content:center;">
                <svg width="100%" height="100%" viewBox="0 0 360 120">
                    <line x1="0" y1="110" x2="360" y2="110" stroke="#ccc" stroke-width="1"/>
                    <polyline points="20,95 80,75 140,85 200,50 260,35 320,15" fill="none" stroke="#17a2b8" stroke-width="2.5"/>
                    <circle cx="20" cy="95" r="4" fill="#17a2b8"/>
                    <circle cx="80" cy="75" r="4" fill="#17a2b8"/>
                    <circle cx="140" cy="85" r="4" fill="#17a2b8"/>
                    <circle cx="200" cy="50" r="4" fill="#17a2b8"/>
                    <circle cx="260" cy="35" r="4" fill="#17a2b8"/>
                    <circle cx="320" cy="15" r="4" fill="#17a2b8"/>
                </svg>
            </div>
            <div class="chart-labels">
                <span>T1</span><span>T2</span><span>T3</span><span>T4</span><span>T5</span><span>T6</span>
            </div>
        </div>

        <div class="chart-card">
            <h4>ĐƠN HÀNG <span>Theo tháng gần nhất</span></h4>
            <div class="chart-body bar-box">
                <?php if ($orders_by_month): ?>
                    <?php foreach ($orders_by_month as $m):
                        $h = max(20, round(((int)$m['so_don'] / $max_orders) * 110));
                    ?>
                        <div class="bar-item">
                            <div class="bar-val"><?= (int)$m['so_don'] ?></div>
                            <div class="bar-pillar" style="height: <?= $h ?>px;"></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="bar-item"><div class="bar-val">12</div><div class="bar-pillar" style="height: 45px;"></div></div>
                    <div class="bar-item"><div class="bar-val">25</div><div class="bar-pillar" style="height: 70px;"></div></div>
                    <div class="bar-item"><div class="bar-val">18</div><div class="bar-pillar" style="height: 55px;"></div></div>
                    <div class="bar-item"><div class="bar-val">35</div><div class="bar-pillar" style="height: 105px;"></div></div>
                <?php endif; ?>
            </div>
            <div class="chart-labels space-around">
                <?php if ($orders_by_month): ?>
                    <?php foreach ($orders_by_month as $m): ?>
                        <span><?= e($m['thang']) ?></span>
                    <?php endforeach; ?>
                <?php else: ?>
                    <span>T8</span><span>T9</span><span>T10</span><span>T11</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- TOP SẢN PHẨM BÁN CHẠY -->
    <div class="top-box">
        <h4>TOP 5 NHẠC CỤ BÁN CHẠY <span>Số lượng</span></h4>
        <?php if ($top_products): ?>
            <?php foreach ($top_products as $idx => $p): ?>
                <div class="top-item">
                    <span>
                        <span class="rank">#<?= $idx + 1 ?></span>
                        <strong><?= e($p['ten_san_pham']) ?></strong>
                        <span style="color:#777; font-size:12px; margin-left:8px;">(<?= format_price($p['doanh_thu']) ?>)</span>
                    </span>
                    <span class="orders" style="color:#e67e22;"><?= (int)$p['tong_ban'] ?> đã bán</span>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color:#888; text-align:center; padding:15px;">Chưa có dữ liệu bán hàng.</p>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../includes/admin-footer.php'; ?>
