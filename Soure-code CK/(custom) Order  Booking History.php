<?php
require_once __DIR__ . '/db.php';

$user = currentUser();

// Lấy danh sách đơn hàng từ DB
$orders = [];
$totalSpent = 0;
if ($pdo) {
    try {
        if ($user && $user['role'] !== 'Admin') {
            $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? OR receiver_phone = ? ORDER BY id DESC");
            $stmt->execute([$user['id'], $user['phone'] ?? '']);
            $orders = $stmt->fetchAll();
        } else {
            $stmt = $pdo->query("SELECT * FROM orders ORDER BY id DESC");
            $orders = $stmt->fetchAll();
        }

        foreach ($orders as $o) {
            if ($o['status'] !== 'Cancelled') {
                $totalSpent += (float)$o['total_price'];
            }
        }
    } catch (Exception $e) {}
}

if (empty($orders)) {
    $orders = [
        ['id' => 1, 'order_code' => '#ORD-1001', 'receiver_name' => 'Nguyễn Văn A', 'total_price' => 4500000, 'status' => 'Completed', 'created_at' => '2026-09-14'],
        ['id' => 2, 'order_code' => '#ORD-1002', 'receiver_name' => 'Trần Thị B', 'total_price' => 11500000, 'status' => 'Pending', 'created_at' => '2026-09-15']
    ];
    $totalSpent = 16000000;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch Sử Đơn Hàng - RE:QUIEM</title>
    <link rel="stylesheet" href="Trangchu.css">
    <link rel="stylesheet" href="(custom) Order  Booking History.css">
    <style>
        .order-history-wrap { max-width: 1000px; margin: 30px auto; padding: 0 15px; }
        .back-nav { margin-bottom: 20px; }
        .back-nav a { color: #2c3e50; text-decoration: none; font-weight: bold; }
        .back-nav a:hover { color: #e67e22; }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="header">
        <a href="Trangchu.php" class="logo">🎼 RE:QUIEM</a>
        <div class="user-actions">
            <?php if ($user && $user['role'] === 'Admin'): ?>
                <a href="(admin) dashboard.php" class="btn-admin-portal" title="Chuyển đến Trang Quản Trị Hệ Thống">
                    ⚙️ Trang Quản Trị
                </a>
            <?php endif; ?>
            <?php if ($user): ?>
                <span class="welcome-text">👤 Xin chào, <strong><?= htmlspecialchars($user['fullname']) ?></strong></span> |
                <a href="dangxuat.php" class="logout-link" onclick="return confirm('Bạn có chắc chắn muốn đăng xuất không?');">Đăng xuất</a>
            <?php else: ?>
                <a href="dangnhap.php">Đăng nhập</a> | <a href="dangky.php">Đăng ký</a>
            <?php endif; ?>
            <span class="cart-icon"><a href="(custom) giỏ hàng.php">🛒 (<?= getCartCount() ?>)</a></span>
        </div>
    </header>

    <div class="order-history-wrap">
        <div class="back-nav">
            <a href="Trangchu.php">← Quay lại Trang Chủ</a> | 
            <a href="(custom) giỏ hàng.php">🛒 Xem Giỏ Hàng</a>
        </div>

        <h2 class="section-title">LỊCH SỬ ĐẶT HÀNG NHẠC CỤ</h2>

        <div class="admin-dashboard">
            <div class="stat-card">
                <h3>TỔNG CHI TIÊU</h3>
                <div class="value"><?= formatMoney($totalSpent) ?></div>
            </div>
            <div class="stat-card">
                <h3>TỔNG ĐƠN HÀNG</h3>
                <div class="value"><?= count($orders) ?></div>
            </div>
            <div class="stat-card">
                <h3>SẢN PHẨM TRONG GIỎ</h3>
                <div class="value"><?= getCartCount() ?></div>
            </div>
        </div>

        <div class="admin-table-container">
            <h3>Danh sách đơn hàng của bạn</h3>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Mã Đơn</th>
                        <th>Khách hàng</th>
                        <th>Thành tiền</th>
                        <th>Trạng thái</th>
                        <th>Thời gian</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $ord): 
                        $status = $ord['status'];
                        $badgeClass = 'badge-pending';
                        if ($status === 'Paid' || $status === 'Completed') $badgeClass = 'badge-completed';
                        else if ($status === 'Shipping') $badgeClass = 'badge-shipping';
                        else if ($status === 'Cancelled') $badgeClass = 'badge-cancelled';
                    ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($ord['order_code']) ?></strong></td>
                            <td><?= htmlspecialchars($ord['receiver_name']) ?></td>
                            <td><strong style="color: #e74c3c;"><?= formatMoney($ord['total_price']) ?></strong></td>
                            <td><span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($status) ?></span></td>
                            <td><?= date('d/m/Y H:i', strtotime($ord['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
