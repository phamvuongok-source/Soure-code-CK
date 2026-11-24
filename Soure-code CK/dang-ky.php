<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

if (is_logged_in()) {
    redirect(BASE_URL . 'index.php');
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký - RE:QUIEM</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/dangky.css">
</head>
<body>
    <div class="auth-card">
        <h2 class="auth-title">🎼 RE:QUIEM</h2>
        <p class="auth-subtitle">Tạo tài khoản thành viên mới</p>

        <form class="auth-form" id="registerForm" method="post" action="<?= BASE_URL ?>handlers/xu-ly-dang-ky.php">
            <div class="form-row">
                <div class="form-group">
                    <label for="fullname">Họ và tên *</label>
                    <input type="text" id="fullname" name="fullname" placeholder="Nguyễn Văn A"
                           value="<?= old('fullname') ?>" required autofocus maxlength="150">
                </div>
                <div class="form-group">
                    <label for="username">Tên tài khoản *</label>
                    <input type="text" id="username" name="username" placeholder="Ví dụ: nguyenvana"
                           value="<?= old('username') ?>" required maxlength="100">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="email">Địa chỉ Email *</label>
                    <input type="email" id="email" name="email" placeholder="example@email.com"
                           value="<?= old('email') ?>" required maxlength="150">
                </div>
                <div class="form-group">
                    <label for="phone">Số điện thoại *</label>
                    <input type="tel" id="phone" name="phone" placeholder="0901234567"
                           value="<?= old('phone') ?>" required maxlength="10">
                </div>
            </div>

            <div class="form-group">
                <label for="address">Địa chỉ liên hệ</label>
                <input type="text" id="address" name="address" placeholder="Số nhà, đường, quận/huyện, TP..."
                       value="<?= old('address') ?>" maxlength="255">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password">Mật khẩu *</label>
                    <input type="password" id="password" name="password" placeholder="Tối thiểu 6 ký tự"
                           minlength="6" required>
                </div>
                <div class="form-group">
                    <label for="confirm-password">Xác nhận mật khẩu *</label>
                    <input type="password" id="confirm-password" name="confirm-password" placeholder="Nhập lại mật khẩu"
                           minlength="6" required>
                </div>
            </div>

            <button type="submit" class="btn-submit">TẠO TÀI KHOẢN</button>
        </form>

        <div class="auth-footer">
            Đã có tài khoản? <a href="<?= BASE_URL ?>dang-nhap.php">Đăng nhập ngay</a>
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
