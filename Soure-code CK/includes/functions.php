<?php
// Escape HTML ngắn gọn, dùng khi in dữ liệu ra trang (chống XSS)
function e($s): string {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

// 3450000 -> "3.450.000 đ"
function format_price($n): string {
    return number_format((float)$n, 0, ',', '.') . ' đ';
}

// Số lượng sản phẩm trong giỏ (giỏ lưu: $_SESSION['cart'][id_san_pham] = so_luong)
function cart_count(): int {
    return array_sum($_SESSION['cart'] ?? []);
}

// Emoji dự phòng theo danh mục
function category_icon(int $id_danh_muc): string {
    return [1 => '🎸', 2 => '🎹', 3 => '🥁', 4 => '🎷', 5 => '📻'][$id_danh_muc] ?? '🎼';
}

// Ảnh sản phẩm: có file thì hiện ảnh, không có thì hiện emoji
function product_image(array $sp): string {
    $file = $sp['hinh_anh'] ?? '';
    $path = __DIR__ . '/../uploads/products/' . $file;
    if ($file !== '' && is_file($path)) {
        return '<img src="' . BASE_URL . 'uploads/products/' . e($file) . '" alt="' . e($sp['ten_san_pham']) .
               '" style="max-width:100%;max-height:100%;object-fit:contain">';
    }
    return '<span>' . category_icon((int)$sp['id_danh_muc']) . '</span>';
}

// ==========================================================================
// HẰNG SỐ & HELPER DÙNG CHUNG CHO HANDLERS
// ==========================================================================

const SHIPPING_FEE = 50000;

// Trạng thái đơn hàng: giá trị lưu trong DB => nhãn hiển thị
const ORDER_STATUSES = [
    'ChoXuLy'   => 'Chờ xử lý',
    'DangGiao'  => 'Đang giao',
    'HoanThanh' => 'Hoàn thành',
    'DaHuy'     => 'Đã hủy',
];

// Chuyển hướng rồi dừng chương trình
function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}

// Thông báo hiện 1 lần (toast ở footer.php / admin-footer.php)
function flash(string $msg, bool $danger = false): void {
    $_SESSION['flash'] = ['msg' => $msg, 'danger' => $danger];
}

// Giữ lại dữ liệu form khi có lỗi để người dùng không phải nhập lại
function keep_old(array $data, array $except = []): void {
    foreach ($except as $k) {
        unset($data[$k]);
    }
    $_SESSION['old'] = array_filter($data, 'is_scalar');
}

function old(string $key, string $default = ''): string {
    return e($_SESSION['old'][$key] ?? $default);
}

function clear_old(): void {
    unset($_SESSION['old']);
}

// Handler chỉ nhận POST; truy cập bằng cách gõ URL sẽ bị đưa về trang $fallback
function require_post(string $fallback): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect($fallback);
    }
}

// Quay lại trang trước đó, chỉ chấp nhận cùng website (tránh bị chuyển sang trang lạ)
function back_url(string $fallback): string {
    $ref  = $_SERVER['HTTP_REFERER'] ?? '';
    $host = strtok($_SERVER['HTTP_HOST'] ?? '', ':');
    if ($ref !== '' && parse_url($ref, PHP_URL_HOST) === $host) {
        return $ref;
    }
    return $fallback;
}

// Số điện thoại Việt Nam 10 số bắt đầu bằng 0 (cột so_dien_thoai là varchar(10))
function valid_phone(string $phone): bool {
    return (bool)preg_match('/^0\d{9}$/', $phone);
}

// Kiểm tra mật khẩu. Hỗ trợ cả mật khẩu cũ lưu thô trong DB: đăng nhập đúng thì báo
// $needs_rehash = true để handler băm lại và lưu (tự nâng cấp dần, không cần chạy script).
function password_matches(string $plain, string $stored, ?bool &$needs_rehash = null): bool {
    $needs_rehash = false;
    if (password_get_info($stored)['algo'] !== null) {
        if (!password_verify($plain, $stored)) {
            return false;
        }
        $needs_rehash = password_needs_rehash($stored, PASSWORD_DEFAULT);
        return true;
    }
    if (hash_equals($stored, $plain)) {
        $needs_rehash = true;
        return true;
    }
    return false;
}

// Lưu ảnh sản phẩm vào uploads/products/, đặt tên theo $custom_name (ví dụ 'sp-6') nếu truyền vào.
// Trả về tên file mới (hoặc null nếu không chọn ảnh).
// Ném RuntimeException với thông báo tiếng Việt khi ảnh không hợp lệ.
function upload_product_image(array $file, ?string $custom_name = null): ?string {
    $err = $file['error'] ?? UPLOAD_ERR_NO_FILE;
    if ($err === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($err === UPLOAD_ERR_INI_SIZE || $err === UPLOAD_ERR_FORM_SIZE) {
        throw new RuntimeException('Ảnh quá lớn (tối đa 2MB).');
    }
    if ($err !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Lỗi khi tải ảnh lên.');
    }
    if ($file['size'] > 2 * 1024 * 1024) {
        throw new RuntimeException('Ảnh quá lớn (tối đa 2MB).');
    }
    $mime    = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if (!isset($allowed[$mime])) {
        throw new RuntimeException('Chỉ nhận ảnh JPG, PNG hoặc WEBP.');
    }
    $dir = __DIR__ . '/../uploads/products/';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $ext = $allowed[$mime];
    if ($custom_name !== null && $custom_name !== '') {
        $name = $custom_name . '.' . $ext;
    } else {
        $name = bin2hex(random_bytes(8)) . '.' . $ext;
    }

    if (!move_uploaded_file($file['tmp_name'], $dir . $name)) {
        throw new RuntimeException('Không lưu được ảnh lên máy chủ.');
    }
    return $name;
}

// Xóa file ảnh trong uploads/products/ (DB không tự xóa file)
function delete_product_image(?string $name): void {
    if ($name === null || $name === '') {
        return;
    }
    $path = __DIR__ . '/../uploads/products/' . basename($name);
    if (is_file($path)) {
        unlink($path);
    }
}
