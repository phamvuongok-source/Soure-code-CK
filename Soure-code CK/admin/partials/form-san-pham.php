<?php
// Partial form dùng chung cho modal thêm và modal sửa sản phẩm
// Biến $categories_db phải được định nghĩa ở trang cha (admin/san-pham.php)
?>
<div class="form-group">
    <label>Tên sản phẩm <span class="req">*</span></label>
    <input type="text" name="ten_san_pham" maxlength="200" required>
</div>
<div class="form-group">
    <label>Thương hiệu</label>
    <input type="text" name="thuong_hieu" maxlength="100">
</div>
<div class="form-group">
    <label>Danh mục <span class="req">*</span></label>
    <select name="id_danh_muc" required>
        <option value="">-- Chọn danh mục --</option>
        <?php foreach ($categories_db as $c): ?>
            <option value="<?= (int)$c['id_danh_muc'] ?>"><?= e($c['ten_danh_muc']) ?></option>
        <?php endforeach; ?>
    </select>
</div>
<div class="form-row">
    <div class="form-group">
        <label>Giá bán (đ) <span class="req">*</span></label>
        <input type="number" name="gia_ban" min="1" required>
    </div>
    <div class="form-group">
        <label>Số lượng tồn <span class="req">*</span></label>
        <input type="number" name="so_luong_ton" min="0" required>
    </div>
</div>
<div class="form-group">
    <label>Mô tả</label>
    <textarea name="mo_ta" rows="4" maxlength="2000"></textarea>
</div>
<div class="form-group">
    <label>Hình ảnh (JPG, PNG, WEBP – tối đa 2MB)</label>
    <input type="file" name="hinh_anh" accept=".jpg,.jpeg,.png,.webp">
</div>
