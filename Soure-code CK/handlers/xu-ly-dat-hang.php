<?php
// Nhận form từ thanh-toan.php
// POST: receiver_name, phone, address, payment_method (COD | BANK), note (tùy chọn)
//
// Nguyên tắc: KHÔNG tin giá hay tổng tiền từ trình duyệt. Chỉ nhận id và số lượng từ giỏ (session),
// giá lấy lại từ bảng san_pham, tồn kho kiểm tra lại, tất cả nằm trong 1 transaction.
require_once __DIR__ . '/../includes/bootstrap.php';

$checkout = BASE_URL . 'thanh-toan.php';
require_post($checkout);

if (!is_logged_in()) {
    flash('Vui lòng đăng nhập để đặt hàng.', true);
    redirect(BASE_URL . 'dang-nhap.php');
}

$cart = $_SESSION['cart'] ?? [];
if (!$cart) {
    flash('Giỏ hàng đang trống.', true);
    redirect(BASE_URL . 'gio-hang.php');
}

$ten    = trim($_POST['receiver_name'] ?? '');
$sdt    = trim($_POST['phone'] ?? '');
$dia_chi = trim($_POST['address'] ?? '');
$ghi_chu = trim($_POST['note'] ?? '');
$pt     = $_POST['payment_method'] ?? '';

$loi = null;
if ($ten === '' || $sdt === '' || $dia_chi === '') {
    $loi = 'Vui lòng điền đầy đủ họ tên, số điện thoại và địa chỉ nhận hàng.';
} elseif (mb_strlen($ten) > 150 || mb_strlen($dia_chi) > 255 || mb_strlen($ghi_chu) > 255) {
    $loi = 'Thông tin nhập vào quá dài.';
} elseif (!valid_phone($sdt)) {
    $loi = 'Số điện thoại phải gồm 10 chữ số và bắt đầu bằng 0.';
} elseif (!in_array($pt, ['COD', 'BANK'], true)) {
    $loi = 'Phương thức thanh toán không hợp lệ.';
}
if ($loi) {
    keep_old($_POST);
    flash($loi, true);
    redirect($checkout);
}

$ids = array_map('intval', array_keys($cart));

try {
    $pdo->beginTransaction();

    // Khóa các dòng sản phẩm đang mua để 2 người không cùng mua món cuối cùng
    $in = implode(',', array_fill(0, count($ids), '?'));
    $st = $pdo->prepare(
        "SELECT id_san_pham, ten_san_pham, gia_ban, so_luong_ton
         FROM san_pham WHERE id_san_pham IN ($in) FOR UPDATE"
    );
    $st->execute($ids);
    $products = [];
    foreach ($st->fetchAll() as $p) {
        $products[(int)$p['id_san_pham']] = $p;
    }

    $subtotal = 0;
    $lines    = [];
    foreach ($cart as $id => $qty) {
        $id  = (int)$id;
        $qty = (int)$qty;
        if ($qty < 1) {
            continue;
        }
        if (!isset($products[$id])) {
            throw new RuntimeException('Có sản phẩm trong giỏ không còn được bán. Vui lòng kiểm tra lại giỏ hàng.');
        }
        $p = $products[$id];
        if ($qty > (int)$p['so_luong_ton']) {
            throw new RuntimeException('"' . $p['ten_san_pham'] . '" chỉ còn ' . (int)$p['so_luong_ton'] . ' sản phẩm trong kho.');
        }
        $subtotal += (float)$p['gia_ban'] * $qty;
        $lines[] = ['id' => $id, 'qty' => $qty, 'gia' => $p['gia_ban']];
    }
    if (!$lines) {
        throw new RuntimeException('Giỏ hàng đang trống.');
    }

    $tong = $subtotal + SHIPPING_FEE;

    $pdo->prepare(
        'INSERT INTO don_hang (id_khach_hang, phi_van_chuyen, tong_thanh_toan, trang_thai_don_hang,
                               ten_nguoi_nhan, sdt_nguoi_nhan, dia_chi_giao, ghi_chu)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
    )->execute([
        $_SESSION['user']['id'], SHIPPING_FEE, $tong, 'ChoXuLy',
        $ten, $sdt, $dia_chi, $ghi_chu !== '' ? $ghi_chu : null,
    ]);
    $id_don = (int)$pdo->lastInsertId();

    $ins = $pdo->prepare('INSERT INTO chi_tiet_don_hang (id_don_hang, id_san_pham, so_luong, don_gia) VALUES (?, ?, ?, ?)');
    $tru = $pdo->prepare('UPDATE san_pham SET so_luong_ton = so_luong_ton - ? WHERE id_san_pham = ? AND so_luong_ton >= ?');
    foreach ($lines as $l) {
        $ins->execute([$id_don, $l['id'], $l['qty'], $l['gia']]);
        $tru->execute([$l['qty'], $l['id'], $l['qty']]);
        if ($tru->rowCount() !== 1) {
            throw new RuntimeException('Không đủ hàng trong kho, vui lòng thử lại.');
        }
    }

    $pdo->prepare('INSERT INTO thanh_toan (id_don_hang, so_tien, loai_thanh_toan, trang_thai) VALUES (?, ?, ?, ?)')
        ->execute([$id_don, $tong, $pt === 'COD' ? 'COD' : 'ChuyenKhoan', 'ChuaThanhToan']);

    $pdo->commit();
} catch (RuntimeException $e) {
    // Lỗi nghiệp vụ (hết hàng...): báo đúng nội dung cho khách
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    flash($e->getMessage(), true);
    redirect(BASE_URL . 'gio-hang.php');
} catch (Throwable $e) {
    // Lỗi hệ thống: ghi log, chỉ báo chung chung cho khách
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Đặt hàng lỗi: ' . $e->getMessage());
    flash('Có lỗi khi đặt hàng, vui lòng thử lại sau.', true);
    redirect($checkout);
}

unset($_SESSION['cart']);
clear_old();
flash('🎉 Đặt hàng thành công! Mã đơn hàng của bạn là #' . $id_don . '.');
redirect(BASE_URL . 'don-hang-cua-toi.php');
