<?php
// Trước khi include file này, trang con có thể đặt:
//   $page_title   = 'Tên trang';
//   $nav_active   = 'home' | 'cart' | 'cat1'..'cat5'
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

$page_title = $page_title ?? 'Cửa Hàng Nhạc Cụ RE:QUIEM';
$nav_active = $nav_active ?? '';
$user       = current_user();

$categories = [
    1 => 'Đàn Guitar',
    2 => 'Đàn Piano & Keyboard',
    3 => 'Trống & Bộ gõ',
    4 => 'Kèn & Nhạc cụ hơi',
    5 => 'Phụ kiện âm nhạc',
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title) ?> - RE:QUIEM</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/Trangchu.css?v=<?= filemtime(__DIR__ . '/../assets/css/Trangchu.css') ?>">
    <?php foreach (($extra_css ?? []) as $css): ?>
        <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/<?= e($css) ?>">
    <?php endforeach; ?>
</head>
<body>
<div class="sticky-header">
    <header class="header">
        <a href="<?= BASE_URL ?>index.php" class="logo">🎼 RE:QUIEM</a>

        <div class="search-bar">
            <form action="<?= BASE_URL ?>san-pham.php" method="GET" class="search-form">
                <input type="text" name="keyword" placeholder="Nhập tên nhạc cụ cần tìm..."
                       value="<?= e($_GET['keyword'] ?? '') ?>">
                <button type="submit">Tìm kiếm</button>
            </form>
        </div>

        <div class="user-actions">
            <?php if (is_staff()): ?>
                <a href="<?= BASE_URL ?>admin/dashboard.php" class="btn-admin-link" title="Chuyển sang trang Quản trị">
                    <span style="font-size:15px;">⚙️</span> Trang quản trị
                </a>
            <?php endif; ?>

            <?php if ($user): ?>
                <span class="welcome-text">👤 Xin chào, <strong><?= e($user['ho_ten']) ?></strong></span>
                | <a href="<?= BASE_URL ?>dang-xuat.php" class="logout-link"
                     onclick="return confirm('Bạn có chắc chắn muốn đăng xuất không?');">Đăng xuất</a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>dang-nhap.php">Đăng nhập</a> |
                <a href="<?= BASE_URL ?>dang-ky.php">Đăng ký</a>
            <?php endif; ?>
            <span class="cart-icon"><a href="<?= BASE_URL ?>gio-hang.php">🛒 (<span id="cartCount"><?= cart_count() ?></span>)</a></span>
        </div>
    </header>

    <nav class="navbar">
        <ul>
            <li><a href="<?= BASE_URL ?>index.php" class="<?= $nav_active === 'home' ? 'active' : '' ?>">Trang chủ</a></li>
            <?php foreach ($categories as $id => $name): ?>
                <li><a href="<?= BASE_URL ?>san-pham.php?category=<?= $id ?>"
                       class="<?= $nav_active === "cat$id" ? 'active' : '' ?>"><?= e($name) ?></a></li>
            <?php endforeach; ?>
            <li><a href="<?= BASE_URL ?>gio-hang.php" class="<?= $nav_active === 'cart' ? 'active' : '' ?>">Giỏ hàng</a></li>
            <li><a href="<?= BASE_URL ?>don-hang-cua-toi.php" class="<?= $nav_active === 'orders' ? 'active' : '' ?>">Lịch sử mua hàng</a></li>
        </ul>
    </nav>
</div>
