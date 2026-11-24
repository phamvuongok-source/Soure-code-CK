<?php
// Header + sidebar dùng chung cho mọi trang trong admin/
//
// Trước khi include, mỗi trang admin đặt:
//   $admin_page    = 'dashboard' | 'san-pham' | 'nguoi-dung' | 'don-hang' | 'bao-cao'
//   $page_title    = 'Tên trang';
//   $extra_css     = ['admin-san-pham.css'];          (tùy chọn, nằm trong css/)
//   $extra_scripts = ['https://cdn.jsdelivr.net/npm/chart.js'];  (tùy chọn)
//
// Quyền truy cập lấy từ bảng $admin_menu bên dưới, nên không cần gọi require_role ở từng trang.
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

// key => [nhãn, icon FontAwesome, file, các vai trò được vào]
$admin_menu = [
    'dashboard'  => ['Dashboard',            'fa-chart-line',     'dashboard.php',  [ROLE_ADMIN, ROLE_SALES, ROLE_WAREHOUSE, ROLE_TECH]],
    'san-pham'   => ['Quản lý sản phẩm',     'fa-guitar',         'san-pham.php',   [ROLE_ADMIN, ROLE_WAREHOUSE]],
    'nguoi-dung' => ['Quản lý người dùng',   'fa-users',          'nguoi-dung.php', [ROLE_ADMIN]],
    'don-hang'   => ['Quản lý đơn hàng',     'fa-cart-shopping',  'don-hang.php',   [ROLE_ADMIN, ROLE_SALES]],
    'bao-cao'    => ['Báo cáo & Thống kê',   'fa-chart-bar',      'bao-cao.php',    [ROLE_ADMIN]],
];

$admin_page = $admin_page ?? 'dashboard';
$page_title = $page_title ?? 'Admin Portal';

// Chặn truy cập ở phía server: chưa đăng nhập hoặc sai vai trò thì dừng
require_role($admin_menu[$admin_page][3] ?? [ROLE_ADMIN]);

$user    = current_user();
$role_id = (int)$user['id_vai_tro'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title) ?> - Admin Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/(admin) dashboard.css">
    <?php foreach (($extra_css ?? []) as $css): ?>
        <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/<?= e($css) ?>">
    <?php endforeach; ?>
    <?php foreach (($extra_scripts ?? []) as $src): ?>
        <script src="<?= e($src) ?>"></script>
    <?php endforeach; ?>
</head>
<body>

    <!-- ===== HEADER ===== -->
    <header class="header">
        <div class="logo">&#127925; RE:QUIEM <small style="font-size:13px;color:#cce4f7;margin-left:8px;font-weight:normal;">| Admin Portal</small></div>
        <div class="header-right">
            <a href="<?= BASE_URL ?>index.php" class="btn-home-link" title="Quay lại trang chủ bán hàng">
                <i class="fa-solid fa-house"></i> Về trang chủ
            </a>
            <div class="user-actions" id="userActions">
                <span id="userInfo">
                    <span class="welcome-text">&#128075; Xin chào, <strong><?= e($user['ho_ten']) ?></strong></span> |
                    <a href="<?= BASE_URL ?>dang-xuat.php" class="logout-link"
                       onclick="return confirm('Bạn có chắc chắn muốn đăng xuất không?');">Đăng xuất</a>
                </span>
            </div>
        </div>
    </header>

    <!-- ===== SIDEBAR MENU (chỉ hiện mục mà vai trò hiện tại được vào) ===== -->
    <div class="sidebar">
        <h2>Admin Portal</h2>
        <ul class="nav-menu">
            <?php foreach ($admin_menu as $key => [$label, $icon, $file, $roles]): ?>
                <?php if (!in_array($role_id, $roles, true)) continue; ?>
                <li>
                    <a href="<?= BASE_URL ?>admin/<?= $file ?>"
                       class="nav-item<?= $key === $admin_page ? ' active' : '' ?>">
                        <i class="fa-solid <?= $icon ?>"></i> <?= e($label) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- ===== MAIN CONTENT AREA (đóng trong admin-footer.php) ===== -->
    <div class="main-content">
