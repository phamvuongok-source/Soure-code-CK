<?php
$admin_page = 'san-pham';
$page_title  = 'Quản Lý Sản Phẩm Nhạc Cụ';
$extra_css   = ['(admin) quản lý sản phẩm.css'];
require __DIR__ . '/../includes/admin-header.php';

// Lấy danh mục
$categories_db = $pdo->query('SELECT id_danh_muc, ten_danh_muc FROM danh_muc ORDER BY id_danh_muc')->fetchAll();

// Phân trang + tìm kiếm
$keyword  = trim($_GET['keyword'] ?? '');
$cat_f    = (int)($_GET['cat'] ?? 0);
$page     = max(1, (int)($_GET['page'] ?? 1));
$per_page = 15;
$where    = ['1=1'];
$params   = [];
if ($keyword !== '') {
    $where[]  = '(sp.ten_san_pham LIKE ? OR sp.thuong_hieu LIKE ?)';
    $kw = '%' . $keyword . '%';
    $params = array_merge($params, [$kw, $kw]);
}
if ($cat_f > 0) {
    $where[]  = 'sp.id_danh_muc = ?';
    $params[] = $cat_f;
}
$where_sql = implode(' AND ', $where);

$cnt_st = $pdo->prepare("SELECT COUNT(*) FROM san_pham sp WHERE $where_sql");
$cnt_st->execute($params);
$total      = (int)$cnt_st->fetchColumn();
$total_page = max(1, (int)ceil($total / $per_page));
$page       = min($page, $total_page);
$offset     = ($page - 1) * $per_page;

$st = $pdo->prepare(
    "SELECT sp.*, dm.ten_danh_muc
     FROM san_pham sp
     LEFT JOIN danh_muc dm ON dm.id_danh_muc = sp.id_danh_muc
     WHERE $where_sql
     ORDER BY sp.id_san_pham DESC
     LIMIT $per_page OFFSET $offset"
);
$st->execute($params);
$products = $st->fetchAll();
?>

<h1 class="section-title">Quản lý sản phẩm nhạc cụ</h1>

<!-- Action Bar (Nút thêm sản phẩm & Ô tìm kiếm) -->
<div class="action-bar">
    <button class="btn-add" onclick="openAddModal()"><i class="fa-solid fa-plus"></i> Thêm sản phẩm</button>
    <div class="search-box">
        <i class="fa-solid fa-magnifying-glass"></i>
        <form method="get" style="display:inline; margin:0;" id="searchForm">
            <input type="text" name="keyword" value="<?= e($keyword) ?>" placeholder="Tìm kiếm sản phẩm...">
            <?php if ($cat_f > 0): ?>
                <input type="hidden" name="cat" value="<?= $cat_f ?>">
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Bảng danh sách sản phẩm -->
<div class="table-responsive">
    <table id="productTable">
        <thead>
            <tr>
                <th style="width:60px;">ID</th>
                <th>Tên sản phẩm</th>
                <th>Thương hiệu</th>
                <th>Danh mục</th>
                <th>Giá</th>
                <th>Tồn kho</th>
                <th>Trạng thái</th>
                <th style="width:160px;">Hành động</th>
            </tr>
        </thead>
        <tbody id="productTableBody">
            <?php foreach ($products as $sp):
                $isOut = (int)$sp['so_luong_ton'] <= 0;
            ?>
                <tr>
                    <td><?= (int)$sp['id_san_pham'] ?></td>
                    <td><strong><?= e($sp['ten_san_pham']) ?></strong></td>
                    <td><?= e($sp['thuong_hieu'] ?? '—') ?></td>
                    <td><?= e($sp['ten_danh_muc'] ?? '—') ?></td>
                    <td style="color:#e67e22; font-weight:bold;"><?= format_price($sp['gia_ban']) ?></td>
                    <td><?= (int)$sp['so_luong_ton'] ?></td>
                    <td>
                        <span class="badge <?= $isOut ? 'badge-danger' : ((int)$sp['so_luong_ton'] <= 5 ? 'badge-warning' : 'badge-success') ?>">
                            <?= $isOut ? 'Hết hàng' : ((int)$sp['so_luong_ton'] <= 5 ? 'Sắp hết' : 'Còn hàng') ?>
                        </span>
                    </td>
                    <td>
                        <button class="btn-action btn-edit" onclick="editProduct(<?= htmlspecialchars(json_encode($sp, JSON_UNESCAPED_UNICODE), ENT_QUOTES) ?>)">
                            <i class="fa-solid fa-pen-to-square"></i> Sửa
                        </button>
                        <form method="post" action="<?= BASE_URL ?>handlers/san-pham.php" style="display:inline;" onsubmit="return confirm('Xóa sản phẩm này?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= (int)$sp['id_san_pham'] ?>">
                            <button type="submit" class="btn-action btn-delete">
                                <i class="fa-solid fa-trash"></i> Xóa
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$products): ?>
                <tr><td colspan="8" style="text-align:center; padding:30px; color:#94a3b8;">Không có sản phẩm nào.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Phân trang -->
<?php if ($total_page > 1): ?>
    <div style="display:flex; justify-content:center; gap:8px; margin-top:20px;">
        <?php for ($i = 1; $i <= $total_page; $i++): ?>
            <a href="?keyword=<?= urlencode($keyword) ?>&cat=<?= $cat_f ?>&page=<?= $i ?>"
               style="padding:6px 12px; border:1px solid #cbd5e1; border-radius:4px; text-decoration:none; color:<?= $i === $page ? '#fff' : '#1e293b' ?>; background:<?= $i === $page ? '#0088cc' : '#fff' ?>; font-weight:bold;"><?= $i ?></a>
        <?php endfor; ?>
    </div>
<?php endif; ?>

<!-- MODAL THÊM / SỬA SẢN PHẨM -->
<div id="productModal" class="modal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5); justify-content:center; align-items:center;">
    <div class="modal-content" style="background:#fff; border-radius:8px; width:100%; max-width:500px; max-height:90vh; overflow-y:auto; box-shadow:0 8px 25px rgba(0,0,0,0.3);">
        <div class="modal-header" style="padding:15px 20px; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
            <h2 id="modalTitle" style="font-size:18px; margin:0; color:#1e293b;">Thêm sản phẩm mới</h2>
            <span class="close-btn" onclick="closeModal()" style="font-size:24px; cursor:pointer; color:#64748b;">&times;</span>
        </div>
        <form id="productForm" method="post" action="<?= BASE_URL ?>handlers/san-pham.php" enctype="multipart/form-data">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" id="prodId" value="0">
            <div class="modal-body" style="padding:20px;">
                <div class="form-group" style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:5px; font-weight:bold; font-size:14px;">Tên sản phẩm *</label>
                    <input type="text" name="ten_san_pham" id="prodName" required maxlength="200" style="width:100%; padding:8px 10px; border:1px solid #cbd5e1; border-radius:4px;">
                </div>
                <div class="form-group" style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:5px; font-weight:bold; font-size:14px;">Thương hiệu</label>
                    <input type="text" name="thuong_hieu" id="prodBrand" maxlength="100" style="width:100%; padding:8px 10px; border:1px solid #cbd5e1; border-radius:4px;">
                </div>
                <div class="form-group" style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:5px; font-weight:bold; font-size:14px;">Danh mục *</label>
                    <select name="id_danh_muc" id="prodCat" required style="width:100%; padding:8px 10px; border:1px solid #cbd5e1; border-radius:4px;">
                        <option value="">-- Chọn danh mục --</option>
                        <?php foreach ($categories_db as $c): ?>
                            <option value="<?= (int)$c['id_danh_muc'] ?>"><?= e($c['ten_danh_muc']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:15px;">
                    <div class="form-group">
                        <label style="display:block; margin-bottom:5px; font-weight:bold; font-size:14px;">Giá bán (đ) *</label>
                        <input type="number" name="gia_ban" id="prodPrice" required min="1" style="width:100%; padding:8px 10px; border:1px solid #cbd5e1; border-radius:4px;">
                    </div>
                    <div class="form-group">
                        <label style="display:block; margin-bottom:5px; font-weight:bold; font-size:14px;">Số lượng tồn *</label>
                        <input type="number" name="so_luong_ton" id="prodStock" required min="0" style="width:100%; padding:8px 10px; border:1px solid #cbd5e1; border-radius:4px;">
                    </div>
                </div>
                <div class="form-group" style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:5px; font-weight:bold; font-size:14px;">Mô tả</label>
                    <textarea name="mo_ta" id="prodDesc" rows="3" style="width:100%; padding:8px 10px; border:1px solid #cbd5e1; border-radius:4px;"></textarea>
                </div>
                <div class="form-group">
                    <label style="display:block; margin-bottom:5px; font-weight:bold; font-size:14px;">Ảnh sản phẩm</label>
                    <input type="file" name="hinh_anh" id="prodFileInput" accept=".jpg,.jpeg,.png,.webp">
                    <small style="color:#64748b; font-size:12px; display:block; margin-top:4px;">
                        Tên ảnh sẽ tự động chuẩn hóa dạng: <code>sp-[ID].[đuôi]</code> (ví dụ: sp-6.png)
                    </small>
                    <div id="currentImageContainer" style="display:none; margin-top:10px; padding:10px; background:#f8fafc; border:1px dashed #cbd5e1; border-radius:6px; align-items:center; gap:12px;">
                        <img id="currentImageThumb" src="" alt="Ảnh hiện tại" style="width:60px; height:60px; object-fit:contain; border:1px solid #e2e8f0; border-radius:4px; background:#fff;">
                        <div>
                            <div style="font-size:12px; color:#64748b;">Ảnh hiện tại trong hệ thống:</div>
                            <strong id="currentImageName" style="font-size:13px; color:#1e293b;"></strong>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="padding:15px 20px; border-top:1px solid #e2e8f0; display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn-cancel" onclick="closeModal()" style="padding:8px 16px; border:1px solid #cbd5e1; background:#f1f5f9; border-radius:4px; cursor:pointer;">Hủy</button>
                <button type="submit" class="btn-save" style="padding:8px 16px; background:#0088cc; color:#fff; border:none; border-radius:4px; font-weight:bold; cursor:pointer;">Lưu sản phẩm</button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('modalTitle').innerText = 'Thêm sản phẩm mới';
    document.getElementById('prodId').value = 0;
    document.getElementById('prodName').value = '';
    document.getElementById('prodBrand').value = '';
    document.getElementById('prodCat').value = '';
    document.getElementById('prodPrice').value = '';
    document.getElementById('prodStock').value = '';
    document.getElementById('prodDesc').value = '';
    document.getElementById('prodFileInput').value = '';
    document.getElementById('currentImageContainer').style.display = 'none';
    document.getElementById('productModal').style.display = 'flex';
}
function editProduct(sp) {
    document.getElementById('modalTitle').innerText = 'Chỉnh sửa sản phẩm #' + sp.id_san_pham;
    document.getElementById('prodId').value = sp.id_san_pham;
    document.getElementById('prodName').value = sp.ten_san_pham || '';
    document.getElementById('prodBrand').value = sp.thuong_hieu || '';
    document.getElementById('prodCat').value = sp.id_danh_muc || '';
    document.getElementById('prodPrice').value = sp.gia_ban || '';
    document.getElementById('prodStock').value = sp.so_luong_ton || '';
    document.getElementById('prodDesc').value = sp.mo_ta || '';
    document.getElementById('prodFileInput').value = '';

    const imgContainer = document.getElementById('currentImageContainer');
    if (sp.hinh_anh) {
        imgContainer.style.display = 'flex';
        document.getElementById('currentImageThumb').src = '<?= BASE_URL ?>uploads/products/' + encodeURIComponent(sp.hinh_anh);
        document.getElementById('currentImageName').innerText = sp.hinh_anh;
    } else {
        imgContainer.style.display = 'none';
    }

    document.getElementById('productModal').style.display = 'flex';
}
function closeModal() {
    document.getElementById('productModal').style.display = 'none';
}
document.getElementById('productModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>

<?php require __DIR__ . '/../includes/admin-footer.php'; ?>
