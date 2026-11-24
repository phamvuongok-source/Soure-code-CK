<?php
$admin_page = 'nguoi-dung';
$page_title  = 'Quản Lý Người Dùng';
$extra_css   = ['(admin) QUẢN LÝ NGƯỜI DÙNG.css'];
require __DIR__ . '/../includes/admin-header.php';

// Lấy danh sách vai trò
$roles_db = $pdo->query('SELECT id_vai_tro, ten_vai_tro FROM vai_tro ORDER BY id_vai_tro')->fetchAll();

// Phân trang + tìm kiếm
$keyword    = trim($_GET['keyword'] ?? '');
$role_f     = (int)($_GET['role'] ?? 0);
$status_f   = $_GET['status'] ?? '';
$page       = max(1, (int)($_GET['page'] ?? 1));
$per_page   = 15;

$where  = ['1=1'];
$params = [];
if ($keyword !== '') {
    $where[]  = '(u.ho_ten LIKE ? OR u.email LIKE ? OR u.so_dien_thoai LIKE ?)';
    $kw = '%' . $keyword . '%';
    array_push($params, $kw, $kw, $kw);
}
if ($role_f > 0) {
    $where[]  = 'u.id_vai_tro = ?';
    $params[] = $role_f;
}
if ($status_f === '1' || $status_f === '0') {
    $where[]  = 'u.trang_thai = ?';
    $params[] = (int)$status_f;
}
$where_sql = implode(' AND ', $where);

$cnt_st = $pdo->prepare("SELECT COUNT(*) FROM nguoi_dung u WHERE $where_sql");
$cnt_st->execute($params);
$total      = (int)$cnt_st->fetchColumn();
$total_page = max(1, (int)ceil($total / $per_page));
$page       = min($page, $total_page);
$offset     = ($page - 1) * $per_page;

$st = $pdo->prepare(
    "SELECT u.*, v.ten_vai_tro
     FROM nguoi_dung u
     LEFT JOIN vai_tro v ON v.id_vai_tro = u.id_vai_tro
     WHERE $where_sql
     ORDER BY u.id_nguoi_dung DESC
     LIMIT $per_page OFFSET $offset"
);
$st->execute($params);
$users = $st->fetchAll();
$me    = (int)current_user()['id'];
?>

<h1 class="section-title">Quản lý người dùng</h1>

<div class="filter-box">
    <form method="get" id="filterForm">
        <div class="search-line">
            🔍 <input type="text" name="keyword" value="<?= e($keyword) ?>" placeholder="Tìm kiếm người dùng (họ tên, email, SĐT)...">
            <button type="submit" class="btn-search">Search</button>
        </div>

        <div class="filter-line" style="margin-top:12px;">
            <div>
                <label for="roleFilter">Role: </label>
                <select name="role" id="roleFilter" onchange="this.form.submit()">
                    <option value="">[Tất cả ▼]</option>
                    <?php foreach ($roles_db as $r): ?>
                        <option value="<?= (int)$r['id_vai_tro'] ?>" <?= $role_f === (int)$r['id_vai_tro'] ? 'selected' : '' ?>>
                            <?= e($r['ten_vai_tro']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="statusFilter" style="margin-left:15px;">Status: </label>
                <select name="status" id="statusFilter" onchange="this.form.submit()">
                    <option value="">[Tất cả ▼]</option>
                    <option value="1" <?= $status_f === '1' ? 'selected' : '' ?>>Hoạt động</option>
                    <option value="0" <?= $status_f === '0' ? 'selected' : '' ?>>Khóa</option>
                </select>
            </div>
            <button type="button" class="btn-add" onclick="openAddModal()">+ Thêm người dùng</button>
        </div>
    </form>
</div>

<table class="user-table">
    <thead>
        <tr>
            <th width="60">ID</th>
            <th>Họ tên</th>
            <th>Email</th>
            <th>SĐT</th>
            <th>Role</th>
            <th>Trạng thái</th>
            <th width="150">Hành động</th>
        </tr>
    </thead>
    <tbody id="userTableBody">
        <?php foreach ($users as $u):
            $isLocked = (int)$u['trang_thai'] === 0;
        ?>
            <tr>
                <td class="col-id"><?= sprintf('%02d', (int)$u['id_nguoi_dung']) ?></td>
                <td class="col-name">
                    <?= e($u['ho_ten']) ?>
                    <?php if ((int)$u['id_nguoi_dung'] === $me): ?>
                        <span style="font-size:11px; background:#e0f2fe; color:#0284c7; padding:2px 6px; border-radius:10px; margin-left:4px;">Bạn</span>
                    <?php endif; ?>
                </td>
                <td><?= e($u['email']) ?></td>
                <td><?= e($u['so_dien_thoai'] ?? '—') ?></td>
                <td><span class="role-badge"><?= e($u['ten_vai_tro'] ?? 'Khách') ?></span></td>
                <td>
                    <span class="badge <?= $isLocked ? 'badge-locked' : 'badge-active' ?>">
                        <?= $isLocked ? 'Khóa' : 'Hoạt động' ?>
                    </span>
                </td>
                <td>
                    <button class="btn-edit" onclick="editUser(<?= htmlspecialchars(json_encode($u, JSON_UNESCAPED_UNICODE), ENT_QUOTES) ?>)">
                        Sửa
                    </button>
                    <?php if ((int)$u['id_nguoi_dung'] !== $me): ?>
                        <form method="post" action="<?= BASE_URL ?>handlers/nguoi-dung.php" style="display:inline;" onsubmit="return confirm('Xóa người dùng này?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= (int)$u['id_nguoi_dung'] ?>">
                            <button type="submit" class="btn-delete">Xóa</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$users): ?>
            <tr><td colspan="7" style="text-align:center; padding:30px; color:#888;">Không tìm thấy người dùng.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<!-- Phân trang -->
<?php if ($total_page > 1): ?>
    <div style="display:flex; justify-content:center; gap:8px; margin-top:20px;">
        <?php for ($i = 1; $i <= $total_page; $i++): ?>
            <a href="?keyword=<?= urlencode($keyword) ?>&role=<?= $role_f ?>&status=<?= e($status_f) ?>&page=<?= $i ?>"
               style="padding:6px 12px; border:1px solid #cbd5e1; border-radius:4px; text-decoration:none; color:<?= $i === $page ? '#fff' : '#1e293b' ?>; background:<?= $i === $page ? '#0088cc' : '#fff' ?>; font-weight:bold;"><?= $i ?></a>
        <?php endfor; ?>
    </div>
<?php endif; ?>

<!-- MODAL THÊM / SỬA USER -->
<div id="userModal" class="modal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5); justify-content:center; align-items:center;">
    <div class="modal-content" style="background:#fff; border-radius:8px; width:100%; max-width:480px; padding:25px; box-shadow:0 8px 25px rgba(0,0,0,0.3);">
        <div class="modal-header">
            <h3 id="modalTitle">Thêm người dùng mới</h3>
            <span class="close-btn" onclick="closeModal()">&times;</span>
        </div>
        <form id="userForm" method="post" action="<?= BASE_URL ?>handlers/nguoi-dung.php">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" id="userId" value="0">
            <div class="modal-body">
                <div class="form-group">
                    <label>Họ và tên *</label>
                    <input type="text" name="ho_ten" id="userFullName" required maxlength="150">
                </div>
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" id="userEmail" required maxlength="150">
                </div>
                <div class="form-group">
                    <label>Số điện thoại</label>
                    <input type="tel" name="so_dien_thoai" id="userPhone" maxlength="10" placeholder="0xxxxxxxxx">
                </div>
                <div class="form-group">
                    <label>Địa chỉ</label>
                    <input type="text" name="dia_chi" id="userAddress" maxlength="255">
                </div>
                <div class="form-group">
                    <label>Tên tài khoản (đăng nhập)</label>
                    <input type="text" name="tai_khoan" id="userAcc" maxlength="100">
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div class="form-group">
                        <label>Vai trò *</label>
                        <select name="id_vai_tro" id="userRole" required>
                            <?php foreach ($roles_db as $r): ?>
                                <option value="<?= (int)$r['id_vai_tro'] ?>"><?= e($r['ten_vai_tro']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Trạng thái</label>
                        <select name="trang_thai" id="userStatus">
                            <option value="1">Hoạt động</option>
                            <option value="0">Khóa</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Mật khẩu <small id="passHint">(để trống = không đổi)</small></label>
                    <input type="password" name="mat_khau" id="userPass" minlength="6" placeholder="Tối thiểu 6 ký tự">
                </div>
            </div>
            <div class="modal-footer" style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                <button type="button" class="btn-cancel" onclick="closeModal()" style="padding:8px 16px; border:1px solid #cbd5e1; background:#f1f5f9; border-radius:4px; cursor:pointer;">Hủy</button>
                <button type="submit" class="btn-save" style="padding:8px 16px; background:#0088cc; color:#fff; border:none; border-radius:4px; font-weight:bold; cursor:pointer;">Lưu</button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('modalTitle').innerText = 'Thêm người dùng mới';
    document.getElementById('userId').value = 0;
    document.getElementById('userFullName').value = '';
    document.getElementById('userEmail').value = '';
    document.getElementById('userPhone').value = '';
    document.getElementById('userAddress').value = '';
    document.getElementById('userAcc').value = '';
    document.getElementById('userPass').value = '';
    document.getElementById('userPass').required = true;
    document.getElementById('passHint').innerText = '(bắt buộc)';
    document.getElementById('userModal').style.display = 'flex';
}
function editUser(u) {
    document.getElementById('modalTitle').innerText = 'Chỉnh sửa người dùng #' + u.id_nguoi_dung;
    document.getElementById('userId').value = u.id_nguoi_dung;
    document.getElementById('userFullName').value = u.ho_ten || '';
    document.getElementById('userEmail').value = u.email || '';
    document.getElementById('userPhone').value = u.so_dien_thoai || '';
    document.getElementById('userAddress').value = u.dia_chi || '';
    document.getElementById('userAcc').value = u.tai_khoan || '';
    document.getElementById('userRole').value = u.id_vai_tro || 2;
    document.getElementById('userStatus').value = u.trang_thai;
    document.getElementById('userPass').value = '';
    document.getElementById('userPass').required = false;
    document.getElementById('passHint').innerText = '(để trống = không đổi)';
    document.getElementById('userModal').style.display = 'flex';
}
function closeModal() {
    document.getElementById('userModal').style.display = 'none';
}
document.getElementById('userModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>

<?php require __DIR__ . '/../includes/admin-footer.php'; ?>
