    </div><!-- /.main-content -->

    <!-- Thông báo Toast (CSS .toast nằm trong file CSS riêng của từng trang admin) -->
    <div id="toast" class="toast"></div>

    <script>
        function showToast(message, isDanger = false) {
            const toast = document.getElementById('toast');
            toast.innerText = message;
            toast.className = 'toast show' + (isDanger ? ' toast-danger' : '');
            setTimeout(() => { toast.className = 'toast'; }, 2500);
        }
        <?php if (!empty($_SESSION['flash'])): ?>
        showToast(<?= json_encode($_SESSION['flash']['msg'], JSON_UNESCAPED_UNICODE) ?>, <?= !empty($_SESSION['flash']['danger']) ? 'true' : 'false' ?>);
        <?php unset($_SESSION['flash']); endif; ?>
    </script>
</body>
</html>
