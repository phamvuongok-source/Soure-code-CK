<?php
// Nhận form từ dang-nhap.php (POST: username, password)
require_once __DIR__ . '/../includes/bootstrap.php';

$back = BASE_URL . 'dang-nhap.php';
require_post($back);

$username = trim($_POST['username'] ?? '');
$password = (string)($_POST['password'] ?? '');

if ($username === '' || $password === '') {
    flash('Vui lòng điền đầy đủ tài khoản và mật khẩu!', true);
    redirect($back);
}

// Cho đăng nhập bằng tài khoản, email hoặc số điện thoại (đúng như gợi ý trên form)
$st = $pdo->prepare('SELECT * FROM nguoi_dung WHERE tai_khoan = ? OR email = ? OR so_dien_thoai = ?');
$st->execute([$username, $username, $username]);

$found = null;
foreach ($st->fetchAll() as $u) {
    if (password_matches($password, (string)$u['mat_khau'], $needs_rehash)) {
        $found = $u;
        break;
    }
}

// Báo lỗi chung, không nói rõ là sai tài khoản hay sai mật khẩu
if (!$found) {
    flash('Tài khoản hoặc mật khẩu không đúng!', true);
    redirect($back);
}

if ((int)($found['trang_thai'] ?? 1) === 0) {
    flash('Tài khoản của bạn đã bị khóa. Vui lòng liên hệ cửa hàng.', true);
    redirect($back);
}

// Mật khẩu cũ lưu thô -> băm lại ngay lần đăng nhập đầu tiên
if ($needs_rehash) {
    $pdo->prepare('UPDATE nguoi_dung SET mat_khau = ? WHERE id_nguoi_dung = ?')
        ->execute([password_hash($password, PASSWORD_DEFAULT), $found['id_nguoi_dung']]);
}

session_regenerate_id(true); // chống session fixation
$_SESSION['user'] = [
    'id'         => (int)$found['id_nguoi_dung'],
    'ho_ten'     => $found['ho_ten'],
    'email'      => $found['email'],
    'id_vai_tro' => (int)$found['id_vai_tro'],
];
clear_old();
flash('Đăng nhập thành công! Xin chào ' . $found['ho_ten'] . '.');

// Khách -> trang chủ; nhân sự -> trang quản trị
redirect(is_staff() ? BASE_URL . 'admin/dashboard.php' : BASE_URL . 'index.php');
