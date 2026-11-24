
<footer class="footer">
    <p>© <?= date('Y') ?> Cửa Hàng Nhạc Cụ RE:QUIEM. Tất cả quyền được bảo lưu.</p>
    <p>Địa chỉ: TP. Hồ Chí Minh | Hotline: 036x xxx xxx | Email: support@nhaccustore.vn</p>
</footer>

<div id="toast" class="toast"></div>
<script src="<?= BASE_URL ?>assets/js/product-slider.js?v=<?= filemtime(__DIR__ . '/../assets/js/product-slider.js') ?>"></script>
<script>
function showToast(message, isDanger) {
    const t = document.getElementById('toast');
    t.textContent = message;
    t.className = 'toast show' + (isDanger ? ' toast-danger' : '');
    setTimeout(() => { t.className = 'toast'; }, 3000);
}
<?php if (!empty($_SESSION['flash'])): ?>
showToast(<?= json_encode($_SESSION['flash']['msg'], JSON_UNESCAPED_UNICODE) ?>, <?= !empty($_SESSION['flash']['danger']) ? 'true' : 'false' ?>);
<?php unset($_SESSION['flash']); endif; ?>
</script>
</body>
</html>
