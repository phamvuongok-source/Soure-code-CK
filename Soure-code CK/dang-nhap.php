<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

// Đã đăng nhập rồi thì không cần vào trang này nữa
if (is_logged_in()) {
    redirect(BASE_URL . 'index.php');
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập - RE:QUIEM</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/dangnhap.css">
</head>
<body>
    <div class="auth-card">
        <h2 class="auth-title">🎼 RE:QUIEM</h2>
        <p class="auth-subtitle">Đăng nhập tài khoản của bạn</p>

        <form class="auth-form" id="loginForm" method="post" action="<?= BASE_URL ?>handlers/xu-ly-dang-nhap.php">
            <div class="form-group">
                <label for="username">Email hoặc Tên đăng nhập</label>
                <input type="text" id="username" name="username"
                       placeholder="Nhập email, tài khoản hoặc SĐT"
                       value="<?= old('username') ?>" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <input type="password" id="password" name="password"
                       placeholder="Nhập mật khẩu" required>
            </div>

            <div class="form-options">
                <label><input type="checkbox" name="nho" checked> Ghi nhớ tài khoản</label>
                <a href="javascript:void(0)" onclick="alert('Vui lòng liên hệ hotline 036x xxx xxx hoặc gửi thư tới admin@nhaccustore.vn để lấy lại mật khẩu.');">Quên mật khẩu?</a>
            </div>

            <button type="submit" name="submit" class="btn-submit">ĐĂNG NHẬP</button>
        </form>

        <div class="auth-footer">
            Chưa có tài khoản? <a href="<?= BASE_URL ?>dang-ky.php">Đăng ký ngay</a>
        </div>
        <div class="auth-footer back-home">
            <a href="<?= BASE_URL ?>index.php">← Quay lại Trang Chủ</a>
        </div>
    </div>

    <!-- Thông báo Toast -->
    <div id="toast" class="toast"></div>

    <script>
        function showToast(message, isDanger) {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = 'toast show' + (isDanger ? ' toast-danger' : '');
            setTimeout(function () {
                toast.className = 'toast';
            }, 3000);
        }

        <?php if (!empty($_SESSION['flash'])): ?>
            showToast(<?= json_encode($_SESSION['flash']['msg'], JSON_UNESCAPED_UNICODE) ?>, <?= !empty($_SESSION['flash']['danger']) ? 'true' : 'false' ?>);
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>
    </script>
</body>
</html>
