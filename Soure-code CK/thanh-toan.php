<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

require_login(); // Phải đăng nhập mới được thanh toán

$page_title = 'Xác Nhận Đặt Hàng Nhạc Cụ';
$extra_css  = ['thanhtoan.css'];

$cart = $_SESSION['cart'] ?? [];
if (!$cart) {
    flash('Giỏ hàng đang trống, vui lòng thêm sản phẩm trước.', true);
    redirect(BASE_URL . 'san-pham.php');
}

// Lấy thông tin sản phẩm trong giỏ từ DB
$ids = array_map('intval', array_keys($cart));
$in  = implode(',', array_fill(0, count($ids), '?'));
$st  = $pdo->prepare("SELECT * FROM san_pham WHERE id_san_pham IN ($in) AND so_luong_ton > 0");
$st->execute($ids);
$products_db = array_column($st->fetchAll(), null, 'id_san_pham');

$items = [];
$subtotal = 0;
foreach ($cart as $id => $qty) {
    $id  = (int)$id;
    $qty = (int)$qty;
    if ($qty < 1 || !isset($products_db[$id])) continue;
    $sp = $products_db[$id];
    $line = (float)$sp['gia_ban'] * $qty;
    $subtotal += $line;
    $items[] = ['sp' => $sp, 'qty' => $qty, 'line' => $line];
}

if (!$items) {
    flash('Một số sản phẩm trong giỏ đã hết hàng.', true);
    redirect(BASE_URL . 'gio-hang.php');
}

$total = $subtotal + SHIPPING_FEE;
$user  = current_user();

require __DIR__ . '/includes/header.php';
?>

<main class="main-content-wrap">
    <div class="container checkout-container">
        <div class="breadcrumb" style="margin-top:15px; margin-bottom:15px; font-size:14px; color:#64748b;">
            <a href="<?= BASE_URL ?>index.php" style="color:#0088cc; text-decoration:none;">Trang chủ</a> &raquo;
            <a href="<?= BASE_URL ?>gio-hang.php" style="color:#0088cc; text-decoration:none;">Giỏ hàng</a> &raquo;
            <span>Xác nhận đặt hàng</span>
        </div>

        <div class="checkout-card">
            <div class="checkout-header">
                <span>📝</span>
                <span>XÁC NHẬN ĐẶT HÀNG NHẠC CỤ</span>
            </div>

            <div class="checkout-body">
                <!-- Tóm tắt các nhạc cụ trong giỏ -->
                <div class="order-preview">
                    <div class="order-preview-title">Danh sách nhạc cụ đặt mua (<?= count($items) ?> món):</div>
                    <?php foreach ($items as $item): $sp = $item['sp']; ?>
                        <div class="order-item-line">
                            <span><?= e($sp['ten_san_pham']) ?> × <strong><?= $item['qty'] ?></strong></span>
                            <span style="font-weight: 500;"><?= format_price($item['line']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <form id="checkoutForm" method="post" action="<?= BASE_URL ?>handlers/xu-ly-dat-hang.php">
                    <div class="form-group">
                        <label for="receiver_name">Họ tên người nhận hàng *</label>
                        <input type="text" id="receiver_name" name="receiver_name"
                               value="<?= old('receiver_name', $user['ho_ten'] ?? '') ?>" required maxlength="150">
                    </div>

                    <div class="form-group">
                        <label for="phone">Số điện thoại liên hệ *</label>
                        <input type="tel" id="phone" name="phone"
                               value="<?= old('phone') ?>" placeholder="0901234567" required maxlength="10">
                    </div>

                    <div class="form-group">
                        <label for="address">Địa chỉ giao nhạc cụ tận nơi *</label>
                        <textarea id="address" name="address" rows="3" required placeholder="Số nhà, đường, quận/huyện, TP..."><?= old('address') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="note">Ghi chú đơn hàng (tùy chọn)</label>
                        <textarea id="note" name="note" rows="2" placeholder="Yêu cầu về giờ giao hàng, đóng gói..."><?= old('note') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Phương thức thanh toán</label>
                        <div class="payment-method-box">
                            <label style="margin-bottom: 8px; display:block; cursor:pointer;">
                                <input type="radio" name="payment_method" value="COD" checked>
                                <span>💵 Thanh toán tiền mặt khi nhận hàng (COD)</span>
                            </label>
                            <label style="display:block; cursor:pointer;">
                                <input type="radio" name="payment_method" value="BANK">
                                <span>🏦 Chuyển khoản ngân hàng qua mã VietQR</span>
                            </label>
                        </div>
                    </div>

                    <div class="total-display-box">
                        <div>
                            <div class="total-display-label">Tổng thanh toán:</div>
                            <div style="font-size: 13px; color: #7f8c8d;">Đã bao gồm <?= format_price(SHIPPING_FEE) ?> phí vận chuyển</div>
                        </div>
                        <div class="total-display-amount">
                            <?= format_price($total) ?>
                        </div>
                    </div>

                    <button type="submit" class="btn-order-submit">
                        HOÀN TẤT ĐẶT HÀNG
                    </button>

                    <a href="<?= BASE_URL ?>gio-hang.php" class="back-to-cart">
                        ← Quay lại giỏ hàng để sửa
                    </a>
                </form>
            </div>
        </div>
    </div>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
