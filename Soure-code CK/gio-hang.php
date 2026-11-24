<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = 'Giỏ Hàng Của Bạn';
$nav_active  = 'cart';
$extra_css   = ['(custom) giỏ hàng.css'];

$cart = $_SESSION['cart'] ?? [];
$items = [];
$subtotal = 0;

if ($cart) {
    $ids = array_map('intval', array_keys($cart));
    $in  = implode(',', array_fill(0, count($ids), '?'));
    $st  = $pdo->prepare("SELECT * FROM san_pham WHERE id_san_pham IN ($in)");
    $st->execute($ids);
    foreach ($st->fetchAll() as $sp) {
        $qty = (int)($cart[$sp['id_san_pham']] ?? 0);
        if ($qty < 1) continue;
        $qty = min($qty, (int)$sp['so_luong_ton']);
        $line_total = (float)$sp['gia_ban'] * $qty;
        $subtotal += $line_total;
        $items[] = ['sp' => $sp, 'qty' => $qty, 'line_total' => $line_total];
    }
}
$shipping_fee = $items ? SHIPPING_FEE : 0;
$total = $subtotal + $shipping_fee;

require __DIR__ . '/includes/header.php';
?>

<main class="main-content-wrap">
    <div class="container cart-wrapper">
        <div class="breadcrumb" style="margin-top:15px; margin-bottom:15px; font-size:14px; color:#64748b;">
            <a href="<?= BASE_URL ?>index.php" style="color:#0088cc; text-decoration:none;">Trang chủ</a> &raquo;
            <span>Giỏ hàng của bạn</span>
        </div>

        <h2 class="section-title">🛒 GIỎ HÀNG CỦA BẠN</h2>

        <?php if ($items): ?>
            <div id="cartContent" class="cart-layout">
                <!-- Danh sách sản phẩm trong giỏ -->
                <div>
                    <form method="post" action="<?= BASE_URL ?>handlers/xu-ly-gio-hang.php" id="cartForm">
                        <input type="hidden" name="action" value="update">
                        <div class="cart-table-card">
                            <table class="requiem-table">
                                <thead>
                                    <tr>
                                        <th style="width: 50px; text-align: center;">STT</th>
                                        <th>Tên Nhạc Cụ</th>
                                        <th style="width: 140px;">Đơn Giá</th>
                                        <th style="width: 110px; text-align: center;">Số Lượng</th>
                                        <th style="width: 150px; text-align: right;">Thành Tiền</th>
                                        <th style="width: 80px; text-align: center;">Xóa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $idx => $item): $sp = $item['sp']; ?>
                                        <tr>
                                            <td style="text-align: center; color: #7f8c8d;"><?= $idx + 1 ?></td>
                                            <td>
                                                <a href="<?= BASE_URL ?>chi-tiet.php?id=<?= (int)$sp['id_san_pham'] ?>" style="color: #2c3e50; font-weight: bold; text-decoration: none;">
                                                    <?= e($sp['ten_san_pham']) ?>
                                                </a>
                                                <?php if ($sp['thuong_hieu']): ?>
                                                    <div style="font-size: 12px; color: #7f8c8d;"><?= e($sp['thuong_hieu']) ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= format_price($sp['gia_ban']) ?></td>
                                            <td style="text-align: center;">
                                                <input type="number" name="qty[<?= (int)$sp['id_san_pham'] ?>]" value="<?= $item['qty'] ?>" min="1" max="<?= (int)$sp['so_luong_ton'] ?>" style="width: 65px; padding: 4px; text-align: center; border: 1px solid #bdc3c7; border-radius: 4px;">
                                            </td>
                                            <td style="text-align: right; font-weight: bold; color: #e67e22;"><?= format_price($item['line_total']) ?></td>
                                            <td style="text-align: center;">
                                                <button type="submit" formaction="<?= BASE_URL ?>handlers/xu-ly-gio-hang.php" name="action" value="remove" onclick="this.form.querySelector('[name=action]').value='remove'; const i=document.createElement('input'); i.type='hidden'; i.name='id'; i.value='<?= (int)$sp['id_san_pham'] ?>'; this.form.appendChild(i);" style="background: none; border: none; cursor: pointer; color: #e74c3c; font-size: 16px;">❌</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="cart-actions-bar">
                            <button type="submit" formaction="<?= BASE_URL ?>handlers/xu-ly-gio-hang.php" name="action" value="clear" onclick="this.form.querySelector('[name=action]').value='clear'; return confirm('Làm trống toàn bộ giỏ hàng?');" class="btn-clear">
                                🗑️ Xóa toàn bộ giỏ
                            </button>
                            <button type="submit" class="btn-update">
                                🔄 Cập nhật số lượng
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Cột tóm tắt đơn hàng -->
                <div>
                    <div class="summary-card">
                        <div class="summary-header">
                            TỔNG GIÁ TRỊ ĐƠN HÀNG
                        </div>
                        <div class="summary-body">
                            <div class="summary-row">
                                <span>Tạm tính tiền hàng:</span>
                                <strong><?= format_price($subtotal) ?></strong>
                            </div>
                            <div class="summary-row">
                                <span>Phí giao hàng:</span>
                                <span style="color: #7f8c8d;"><?= format_price($shipping_fee) ?></span>
                            </div>
                            <div class="summary-total">
                                <span>Tổng thanh toán:</span>
                                <span class="total-price"><?= format_price($total) ?></span>
                            </div>

                            <a href="<?= BASE_URL ?>thanh-toan.php" class="btn-checkout">
                                TIẾN HÀNH ĐẶT HÀNG →
                            </a>
                            <a href="<?= BASE_URL ?>san-pham.php" class="btn-continue">
                                ← Tiếp tục mua sắm
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <div class="empty-cart-card">
                <div style="font-size: 50px; margin-bottom: 15px;">🛍️</div>
                <h3>Giỏ hàng hiện tại đang trống</h3>
                <p>Hãy khám phá các mẫu nhạc cụ tuyệt vời của RE:QUIEM và thêm vào giỏ nhé!</p>
                <a href="<?= BASE_URL ?>san-pham.php" class="btn-orange">← KHÁM PHÁ NHẠC CỤ NGAY</a>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
