<?php
// Nhận form đánh giá từ chi-tiet.php (POST: id_san_pham, so_sao, binh_luan)
// Người dùng đã đăng nhập đều có thể đánh giá; mỗi người 1 đánh giá / sản phẩm
// (gửi lần nữa thì cập nhật đánh giá cũ).
require_once __DIR__ . '/../includes/bootstrap.php';

require_post(BASE_URL . 'index.php');

$id_sp  = (int)($_POST['id_san_pham'] ?? 0);
$so_sao = (int)($_POST['so_sao'] ?? 0);
$bl     = trim($_POST['binh_luan'] ?? '');
$back   = BASE_URL . 'chi-tiet.php?id=' . $id_sp . '#danh-gia';

if (!is_logged_in()) {
    flash('Vui lòng đăng nhập để đánh giá sản phẩm.', true);
    redirect(BASE_URL . 'dang-nhap.php');
}
$uid = (int)$_SESSION['user']['id'];

if ($id_sp < 1 || $so_sao < 1 || $so_sao > 5 || mb_strlen($bl) > 1000) {
    flash('Dữ liệu đánh giá không hợp lệ.', true);
    redirect($back);
}

// Kiểm tra sản phẩm tồn tại
$st = $pdo->prepare('SELECT 1 FROM san_pham WHERE id_san_pham = ?');
$st->execute([$id_sp]);
if (!$st->fetchColumn()) {
    flash('Sản phẩm không tồn tại.', true);
    redirect(BASE_URL . 'san-pham.php');
}

$pdo->prepare('INSERT INTO danh_gia (id_san_pham, id_khach_hang, so_sao, binh_luan) VALUES (?, ?, ?, ?)')
    ->execute([$id_sp, $uid, $so_sao, $bl !== '' ? $bl : null]);
flash('Cảm ơn bạn đã đánh giá!');
redirect($back);
