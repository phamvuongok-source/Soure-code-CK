<?php
// Handler CRUD sản phẩm (đặt tại handlers/ để admin/san-pham.php POST về đây)
// Alias trỏ tới main/san-pham.php gốc (đã có logic đầy đủ), nhưng vì file gốc dùng path tương đối,
// ta tạo file wrapper để đảm bảo đúng include path.
require_once __DIR__ . '/../includes/bootstrap.php';

$back = BASE_URL . 'admin/san-pham.php';
require_post($back);
require_role([ROLE_ADMIN, ROLE_WAREHOUSE]);

$action = $_POST['action'] ?? '';
$id     = (int)($_POST['id'] ?? 0);

if ($action === 'save') {
    $ten   = trim($_POST['ten_san_pham'] ?? '');
    $hang  = trim($_POST['thuong_hieu'] ?? '');
    $dm    = (int)($_POST['id_danh_muc'] ?? 0);
    $gia   = (int)preg_replace('/\D/', '', (string)($_POST['gia_ban'] ?? ''));
    $ton   = (int)($_POST['so_luong_ton'] ?? -1);
    $mo_ta = trim($_POST['mo_ta'] ?? '');

    $loi = null;
    if ($ten === '' || mb_strlen($ten) > 200) {
        $loi = 'Tên sản phẩm bắt buộc và tối đa 200 ký tự.';
    } elseif (mb_strlen($hang) > 100) {
        $loi = 'Thương hiệu tối đa 100 ký tự.';
    } elseif ($gia <= 0) {
        $loi = 'Giá bán phải lớn hơn 0.';
    } elseif ($ton < 0) {
        $loi = 'Số lượng tồn không được âm.';
    } else {
        $st = $pdo->prepare('SELECT 1 FROM danh_muc WHERE id_danh_muc = ?');
        $st->execute([$dm]);
        if (!$st->fetchColumn()) {
            $loi = 'Danh mục không hợp lệ.';
        }
    }
    if ($loi) {
        flash($loi, true);
        redirect($back);
    }

    $anh_cu = null;
    if ($id > 0) {
        $st = $pdo->prepare('SELECT hinh_anh FROM san_pham WHERE id_san_pham = ?');
        $st->execute([$id]);
        $row = $st->fetch();
        if (!$row) { flash('Sản phẩm không tồn tại.', true); redirect($back); }
        $anh_cu = $row['hinh_anh'];
    }

    $has_file = isset($_FILES['hinh_anh']) && ($_FILES['hinh_anh']['error'] !== UPLOAD_ERR_NO_FILE);

    try {
        if ($id > 0) {
            // Sửa sản phẩm: nếu có chọn ảnh mới thì đặt tên chuẩn sp-{id}
            $anh_moi = $has_file ? upload_product_image($_FILES['hinh_anh'], 'sp-' . $id) : null;

            if ($anh_moi) {
                $sql    = 'UPDATE san_pham SET ten_san_pham=?, thuong_hieu=?, id_danh_muc=?, gia_ban=?, so_luong_ton=?, mo_ta=?, hinh_anh=? WHERE id_san_pham=?';
                $params = [$ten, $hang !== '' ? $hang : null, $dm, $gia, $ton, $mo_ta !== '' ? $mo_ta : null, $anh_moi, $id];
                $pdo->prepare($sql)->execute($params);
                if ($anh_cu && $anh_cu !== $anh_moi) {
                    delete_product_image($anh_cu);
                }
            } else {
                $sql    = 'UPDATE san_pham SET ten_san_pham=?, thuong_hieu=?, id_danh_muc=?, gia_ban=?, so_luong_ton=?, mo_ta=? WHERE id_san_pham=?';
                $params = [$ten, $hang !== '' ? $hang : null, $dm, $gia, $ton, $mo_ta !== '' ? $mo_ta : null, $id];
                $pdo->prepare($sql)->execute($params);
            }
            flash('Đã cập nhật sản phẩm "' . $ten . '".');
        } else {
            // Thêm mới: INSERT trước để lấy ID tự tăng
            $pdo->prepare(
                'INSERT INTO san_pham (id_danh_muc, ten_san_pham, thuong_hieu, gia_ban, so_luong_ton, mo_ta, hinh_anh)
                 VALUES (?, ?, ?, ?, ?, ?, NULL)'
            )->execute([$dm, $ten, $hang !== '' ? $hang : null, $gia, $ton, $mo_ta !== '' ? $mo_ta : null]);
            $new_id = (int)$pdo->lastInsertId();

            // Nếu có upload ảnh thì đặt tên chuẩn sp-{new_id} và cập nhật lại
            if ($has_file) {
                $anh_moi = upload_product_image($_FILES['hinh_anh'], 'sp-' . $new_id);
                $pdo->prepare('UPDATE san_pham SET hinh_anh = ? WHERE id_san_pham = ?')->execute([$anh_moi, $new_id]);
            }
            flash('Đã thêm sản phẩm "' . $ten . '".');
        }
    } catch (RuntimeException $e) {
        flash($e->getMessage(), true);
    } catch (PDOException $e) {
        error_log('Lưu sản phẩm lỗi: ' . $e->getMessage());
        flash('Không lưu được sản phẩm, vui lòng thử lại.', true);
    }
    redirect($back);
}

if ($action === 'delete') {
    $st = $pdo->prepare('SELECT ten_san_pham, hinh_anh FROM san_pham WHERE id_san_pham = ?');
    $st->execute([$id]);
    $sp = $st->fetch();
    if (!$sp) { flash('Sản phẩm không tồn tại.', true); redirect($back); }
    try {
        $pdo->prepare('DELETE FROM san_pham WHERE id_san_pham = ?')->execute([$id]);
        delete_product_image($sp['hinh_anh']);
        flash('Đã xóa sản phẩm "' . $sp['ten_san_pham'] . '".', true);
    } catch (PDOException $e) {
        flash('Không thể xóa "' . $sp['ten_san_pham'] . '" vì đã có trong đơn hàng. Hãy đặt tồn kho về 0.', true);
    }
    redirect($back);
}

flash('Thao tác không hợp lệ.', true);
redirect($back);
