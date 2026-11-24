<?php
// Partial form dùng chung cho modal thêm và sửa người dùng
// Biến $roles_db phải được định nghĩa ở trang cha (admin/nguoi-dung.php)
?>
<div class="form-group">
    <label>Họ và tên <span class="req">*</span></label>
    <input type="text" name="ho_ten" maxlength="150" required>
</div>
<div class="form-group">
    <label>Email <span class="req">*</span></label>
    <input type="email" name="email" maxlength="150" required>
</div>
<div class="form-group">
    <label>Số điện thoại</label>
    <input type="tel" name="so_dien_thoai" maxlength="10" placeholder="0xxxxxxxxx">
</div>
<div class="form-group">
    <label>Địa chỉ</label>
    <input type="text" name="dia_chi" maxlength="255">
</div>
<div class="form-group">
    <label>Tên tài khoản (đăng nhập)</label>
    <input type="text" name="tai_khoan" maxlength="100" placeholder="Để trống nếu dùng email">
</div>
<div class="form-row">
    <div class="form-group">
        <label>Vai trò <span class="req">*</span></label>
        <select name="id_vai_tro" required>
            <option value="">-- Chọn vai trò --</option>
            <?php foreach ($roles_db as $r): ?>
                <option value="<?= (int)$r['id_vai_tro'] ?>"><?= e($r['ten_vai_tro']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label>Trạng thái</label>
        <select name="trang_thai">
            <option value="1">✅ Hoạt động</option>
            <option value="0">🔒 Khóa</option>
        </select>
    </div>
</div>
<div class="form-group">
    <label>Mật khẩu <small>(để trống = không đổi)</small></label>
    <input type="password" name="mat_khau" minlength="6" placeholder="Tối thiểu 6 ký tự">
</div>
