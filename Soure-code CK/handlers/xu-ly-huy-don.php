<?php
// Handler khách hàng tự hủy đơn hàng (chỉ khi đơn còn ở trạng thái ChoXuLy)
require_once __DIR__ . '/../includes/bootstrap.php';

$back = BASE_URL . 'don-hang-cua-toi.php';
require_post($back);

if (!is_logged_in()) {
    flash('Vui lòng đăng nhập.', true);
    redirect(BASE_URL . 'dang-nhap.php');
}

$id  = (int)($_POST['id'] ?? 0);
$uid = (int)$_SESSION['user']['id'];

if ($id < 1) {
    flash('Đơn hàng không hợp lệ.', true);
    redirect($back);
}

try {
    $pdo->beginTransaction();

    $st = $pdo->prepare('SELECT trang_thai_don_hang FROM don_hang WHERE id_don_hang = ? AND id_khach_hang = ? FOR UPDATE');
    $st->execute([$id, $uid]);
    $status = $st->fetchColumn();

    if ($status === false) {
        throw new RuntimeException('Đơn hàng không tồn tại hoặc không thuộc quyền quản lý của bạn.');
    }

    if ($status !== 'ChoXuLy') {
        throw new RuntimeException('Đơn hàng đang ở trạng thái "' . (ORDER_STATUSES[$status] ?? $status) . '", không thể hủy.');
    }

    // Hoàn trả số lượng tồn kho
    $item_st = $pdo->prepare('SELECT id_san_pham, so_luong FROM chi_tiet_don_hang WHERE id_don_hang = ?');
    $item_st->execute([$id]);
    $restore_st = $pdo->prepare('UPDATE san_pham SET so_luong_ton = so_luong_ton + ? WHERE id_san_pham = ?');
    foreach ($item_st->fetchAll() as $item) {
        $restore_st->execute([(int)$item['so_luong'], (int)$item['id_san_pham']]);
    }

    // Cập nhật trạng thái đơn thành DaHuy
    $pdo->prepare("UPDATE don_hang SET trang_thai_don_hang = 'DaHuy' WHERE id_don_hang = ?")->execute([$id]);

    $pdo->commit();
    flash('Đã hủy thành công đơn hàng #' . $id . '. Sản phẩm đã được hoàn lại số lượng tồn kho.');
} catch (RuntimeException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    flash($e->getMessage(), true);
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Khách hủy đơn lỗi: ' . $e->getMessage());
    flash('Không thể hủy đơn hàng lúc này, vui lòng liên hệ hotline.', true);
}

redirect($back);
