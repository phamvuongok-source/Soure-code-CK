<?php
// Nhận form từ dang-ky.php
// POST: fullname, email, phone, address, password, confirm-password
require_once __DIR__ . '/../includes/bootstrap.php';

$back = BASE_URL . 'dang-ky.php';
require_post($back);

$fullname = trim($_POST['fullname'] ?? '');
$username = trim($_POST['username'] ?? '');
$email    = trim($_POST['email'] ?? '');
$phone    = trim($_POST['phone'] ?? '');
$address  = trim($_POST['address'] ?? '');
$password = (string)($_POST['password'] ?? '');
$confirm  = (string)($_POST['confirm-password'] ?? '');

// Nếu có lỗi: giữ lại dữ liệu đã nhập (trừ mật khẩu) rồi quay lại form
$fail = function (string $msg) use ($back) {
    keep_old($_POST, ['password', 'confirm-password']);
    flash($msg, true);
    redirect($back);
};

if ($fullname === '' || $username === '' || $email === '' || $phone === '' || $password === '') {
    $fail('Vui lòng điền đầy đủ các trường thông tin bắt buộc (*)');
}
if (mb_strlen($fullname) > 150) {
    $fail('Họ tên quá dài (tối đa 150 ký tự).');
}
if (mb_strlen($username) < 3 || mb_strlen($username) > 100) {
    $fail('Tên tài khoản phải từ 3 đến 100 ký tự.');
}
if (!preg_match('/^[a-zA-Z0-9_.-]+$/', $username)) {
    $fail('Tên tài khoản chỉ được chứa chữ cái, số, gạch dưới, gạch ngang hoặc chấm.');
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 150) {
    $fail('Địa chỉ email không hợp lệ.');
}
if (!valid_phone($phone)) {
    $fail('Số điện thoại phải gồm 10 chữ số và bắt đầu bằng 0.');
}
if (mb_strlen($address) > 255) {
    $fail('Địa chỉ quá dài (tối đa 255 ký tự).');
}
if (strlen($password) < 6) {
    $fail('Mật khẩu phải có tối thiểu 6 ký tự!');
}
if ($password !== $confirm) {
    $fail('Mật khẩu xác nhận không khớp!');
}

// Kiểm tra trùng tài khoản
$st = $pdo->prepare('SELECT 1 FROM nguoi_dung WHERE tai_khoan = ?');
$st->execute([$username]);
if ($st->fetchColumn()) {
    $fail('Tên tài khoản này đã được sử dụng. Vui lòng chọn tên khác.');
}

// Email phải là duy nhất
$st = $pdo->prepare('SELECT 1 FROM nguoi_dung WHERE email = ?');
$st->execute([$email]);
if ($st->fetchColumn()) {
    $fail('Email này đã được đăng ký. Hãy đăng nhập hoặc dùng email khác.');
}

// Đăng ký luôn tạo tài khoản KHÁCH HÀNG (vai trò 2); không bao giờ nhận vai trò từ form
$pdo->prepare(
    'INSERT INTO nguoi_dung (id_vai_tro, tai_khoan, mat_khau, ho_ten, email, so_dien_thoai, dia_chi)
     VALUES (?, ?, ?, ?, ?, ?, ?)'
)->execute([
    ROLE_CUSTOMER,
    $username,
    password_hash($password, PASSWORD_DEFAULT),
    $fullname,
    $email,
    $phone,
    $address !== '' ? $address : null,
]);

clear_old();
flash('Đăng ký thành công! Hãy đăng nhập bằng tài khoản hoặc email của bạn.');
redirect(BASE_URL . 'dang-nhap.php');
