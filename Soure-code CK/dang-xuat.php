<?php
// Đăng xuất (link ở header trỏ vào đây, nên dùng GET)
require_once __DIR__ . '/includes/bootstrap.php';

$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}
session_destroy();

// Mở session mới chỉ để hiện thông báo
session_start();
flash('Bạn đã đăng xuất.');
redirect(BASE_URL . 'index.php');
