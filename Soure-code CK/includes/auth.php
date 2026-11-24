<?php
// Vai trò (khớp bảng vai_tro)
const ROLE_ADMIN    = 1;
const ROLE_CUSTOMER = 2;
const ROLE_SALES    = 3;
const ROLE_WAREHOUSE = 4;
const ROLE_TECH     = 5;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_logged_in(): bool {
    return !empty($_SESSION['user']);
}

function current_user(): ?array {
    return $_SESSION['user'] ?? null;
}

// Nhân sự = mọi vai trò khác khách hàng
function is_staff(): bool {
    return is_logged_in() && in_array($_SESSION['user']['id_vai_tro'], [ROLE_ADMIN, ROLE_SALES, ROLE_WAREHOUSE, ROLE_TECH], true);
}

function require_login(): void {
    if (!is_logged_in()) {
        header('Location: ' . BASE_URL . 'dang-nhap.php');
        exit;
    }
}

function require_role(array $roles): void {
    require_login();
    if (!in_array((int)$_SESSION['user']['id_vai_tro'], $roles, true)) {
        http_response_code(403);
        exit('Bạn không có quyền truy cập trang này.');
    }
}
