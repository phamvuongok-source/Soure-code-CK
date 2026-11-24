<?php
// Handler cập nhật đơn hàng (đặt tại handlers/ để admin/don-hang.php POST về đây)
require_once __DIR__ . '/../includes/bootstrap.php';

$id   = (int)($_POST['id'] ?? 0);
$back = BASE_URL . 'admin/don-hang.php' . ($id > 0 ? '?id=' . $id : '');
require_post(BASE_URL . 'admin/don-hang.php');
require_role([ROLE_ADMIN, ROLE_SALES]);

$action = $_POST['action'] ?? '';
if (!in_array($action, ['update', 'cancel'], true)) {
    flash('Thao tác không hợp lệ.', true);
    redirect($back);
}

$moi = $action === 'cancel' ? 'DaHuy' : (string)($_POST['trang_thai'] ?? '');
if (!isset(ORDER_STATUSES[$moi])) {
    flash('Trạng thái đơn hàng không hợp lệ.', true);
    redirect($back);
}

$sdt     = trim($_POST['sdt'] ?? '');
$ghi_chu = trim($_POST['ghi_chu'] ?? '');
if ($action === 'update') {
    if ($sdt !== '' && !valid_phone($sdt)) {
        flash('Số điện thoại phải gồm 10 chữ số và bắt đầu bằng 0.', true);
        redirect($back);
    }
    if (mb_strlen($ghi_chu) > 255) {
        flash('Ghi chú tối đa 255 ký tự.', true);
        redirect($back);
    }
}

try {
    $pdo->beginTransaction();

    $st = $pdo->prepare('SELECT trang_thai_don_hang FROM don_hang WHERE id_don_hang = ? FOR UPDATE');
    $st->execute([$id]);
    $cu = $st->fetchColumn();

    if ($cu === false) throw new RuntimeException('Đơn hàng không tồn tại.');
    if ($cu === 'DaHuy' && $moi !== 'DaHuy') throw new RuntimeException('Đơn #' . $id . ' đã bị hủy, không thể mở lại.');
    if ($cu === 'HoanThanh' && $moi === 'DaHuy') throw new RuntimeException('Đơn #' . $id . ' đã hoàn thành, không thể hủy.');

    if ($moi === 'DaHuy' && $cu !== 'DaHuy') {
        $st = $pdo->prepare('SELECT id_san_pham, so_luong FROM chi_tiet_don_hang WHERE id_don_hang = ?');
        $st->execute([$id]);
        $cong = $pdo->prepare('UPDATE san_pham SET so_luong_ton = so_luong_ton + ? WHERE id_san_pham = ?');
        foreach ($st->fetchAll() as $ct) {
            $cong->execute([$ct['so_luong'], $ct['id_san_pham']]);
        }
    }

    $uid = (int)current_user()['id'];
    if ($action === 'update') {
        $pdo->prepare(
            'UPDATE don_hang SET trang_thai_don_hang = ?, sdt_nguoi_nhan = ?, ghi_chu = ?,
                                 id_nhan_vien = COALESCE(id_nhan_vien, ?)
             WHERE id_don_hang = ?'
        )->execute([$moi, $sdt !== '' ? $sdt : null, $ghi_chu !== '' ? $ghi_chu : null, $uid, $id]);
    } else {
        $pdo->prepare(
            'UPDATE don_hang SET trang_thai_don_hang = ?, id_nhan_vien = COALESCE(id_nhan_vien, ?) WHERE id_don_hang = ?'
        )->execute([$moi, $uid, $id]);
    }

    if ($moi === 'HoanThanh') {
        $pdo->prepare("UPDATE thanh_toan SET trang_thai = 'DaThanhToan' WHERE id_don_hang = ?")->execute([$id]);
    }

    $pdo->commit();
    if ($moi === 'DaHuy') {
        flash('Đã hủy đơn hàng #' . $id . ' và trả hàng về kho.', true);
    } else {
        flash('Cập nhật đơn hàng #' . $id . ' thành công!');
    }
} catch (RuntimeException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    flash($e->getMessage(), true);
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    error_log('Cập nhật đơn hàng lỗi: ' . $e->getMessage());
    flash('Có lỗi khi cập nhật đơn hàng, vui lòng thử lại.', true);
}
redirect($back);
