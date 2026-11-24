<?php
// ===== TỰ ĐỘNG XÁC ĐỊNH BASE_URL =====
if (!defined('BASE_URL')) {
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'])) : '';
    $projRoot = str_replace('\\', '/', realpath(dirname(__DIR__)));

    if ($docRoot !== '' && str_starts_with($projRoot, $docRoot)) {
        $rel = substr($projRoot, strlen($docRoot));
        $base = '/' . trim(str_replace('\\', '/', $rel), '/') . '/';
        define('BASE_URL', $base === '//' ? '/' : $base);
    } else {
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        $parts = array_values(array_filter(explode('/', $scriptDir)));
        define('BASE_URL', !empty($parts[0]) ? '/' . $parts[0] . '/' : '/');
    }
}

$DB_HOST = '127.0.0.1';
$DB_NAME = 'new1';        // tên database bạn đã import file .sql
$DB_USER = 'root';
$DB_PASS = '';            // XAMPP mặc định không có mật khẩu

try {
    $pdo = new PDO(
        "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    exit('Không kết nối được cơ sở dữ liệu: ' . htmlspecialchars($e->getMessage()));
}
