<?php
// Xử lý form của admin/nguoi-dung.php (chỉ Quản trị viên)
// POST action = save   : id (0 hoặc rỗng = thêm mới), ho_ten, email, so_dien_thoai, dia_chi,
//                        tai_khoan (tùy chọn), mat_khau, id_vai_tro, trang_thai (1 = hoạt động, 0 = khóa)
//             = delete : id
require_once __DIR__ . '/../../includes/bootstrap.php';

$back = BASE_URL . 'admin/nguoi-dung.php';
require_post($back);
require_role([ROLE_ADMIN]);

$me     = (int)current_user()['id'];
$action = $_POST['action'] ?? '';
$id     = (int)($_POST['id'] ?? 0);

if ($action === 'save') {
    $ho_ten  = trim($_POST['ho_ten'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $sdt     = trim($_POST['so_dien_thoai'] ?? '');
    $dia_chi = trim($_POST['dia_chi'] ?? '');
    $tk      = trim($_POST['tai_khoan'] ?? '');
    $mk      = (string)($_POST['mat_khau'] ?? '');
    $vai_tro = (int)($_POST['id_vai_tro'] ?? 0);
    $hoat_dong = ((int)($_POST['trang_thai'] ?? 1)) === 1 ? 1 : 0;

    $loi = null;
    if ($ho_ten === '' || mb_strlen($ho_ten) > 150) {
        $loi = 'Họ tên bắt buộc và tối đa 150 ký tự.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 150) {
        $loi = 'Email không hợp lệ.';
    } elseif ($sdt !== '' && !valid_phone($sdt)) {
        $loi = 'Số điện thoại phải gồm 10 chữ số và bắt đầu bằng 0.';
    } elseif (mb_strlen($dia_chi) > 255 || mb_strlen($tk) > 100) {
        $loi = 'Địa chỉ hoặc tài khoản quá dài.';
    } elseif ($id === 0 && strlen($mk) < 6) {
        $loi = 'Thêm người dùng mới cần mật khẩu tối thiểu 6 ký tự.';
    } elseif ($mk !== '' && strlen($mk) < 6) {
        $loi = 'Mật khẩu mới phải có tối thiểu 6 ký tự.';
    }
    if (!$loi) {
        $st = $pdo->prepare('SELECT 1 FROM vai_tro WHERE id_vai_tro = ?');
        $st->execute([$vai_tro]);
        if (!$st->fetchColumn()) {
            $loi = 'Vai trò không hợp lệ.';
        }
    }
    // Không cho admin tự khóa mình hoặc tự hạ quyền mình (tránh mất quyền quản trị)
    if (!$loi && $id === $me && ($vai_tro !== ROLE_ADMIN || $hoat_dong === 0)) {
        $loi = 'Bạn không thể tự khóa hoặc tự đổi vai trò của chính mình.';
    }
    // Email / tài khoản không được trùng người khác
    if (!$loi) {
        $st = $pdo->prepare('SELECT 1 FROM nguoi_dung WHERE id_nguoi_dung <> ? AND (email = ? OR (? <> \'\' AND tai_khoan = ?))');
        $st->execute([$id, $email, $tk, $tk]);
        if ($st->fetchColumn()) {
            $loi = 'Email hoặc tên tài khoản đã được người khác sử dụng.';
        }
    }
    if ($loi) {
        flash($loi, true);
        redirect($back);
    }

    $sdt_db = $sdt !== '' ? $sdt : null;
    $dc_db  = $dia_chi !== '' ? $dia_chi : null;
    $tk_db  = $tk !== '' ? $tk : null;

    try {
        if ($id > 0) {
            $st = $pdo->prepare('SELECT 1 FROM nguoi_dung WHERE id_nguoi_dung = ?');
            $st->execute([$id]);
            if (!$st->fetchColumn()) {
                flash('Người dùng không tồn tại.', true);
                redirect($back);
            }
            $sql    = 'UPDATE nguoi_dung SET ho_ten=?, email=?, so_dien_thoai=?, dia_chi=?, tai_khoan=?, id_vai_tro=?, trang_thai=?'
                    . ($mk !== '' ? ', mat_khau=?' : '') . ' WHERE id_nguoi_dung=?';
            $params = [$ho_ten, $email, $sdt_db, $dc_db, $tk_db, $vai_tro, $hoat_dong];
            if ($mk !== '') {
                $params[] = password_hash($mk, PASSWORD_DEFAULT);
            }
            $params[] = $id;
            $pdo->prepare($sql)->execute($params);
            if ($id === $me) {
                $_SESSION['user']['ho_ten'] = $ho_ten; // cập nhật tên hiển thị ở header
                $_SESSION['user']['email']  = $email;
            }
            flash('Đã cập nhật người dùng "' . $ho_ten . '".');
        } else {
            $pdo->prepare(
                'INSERT INTO nguoi_dung (id_vai_tro, tai_khoan, mat_khau, ho_ten, email, so_dien_thoai, dia_chi, trang_thai)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
            )->execute([$vai_tro, $tk_db, password_hash($mk, PASSWORD_DEFAULT), $ho_ten, $email, $sdt_db, $dc_db, $hoat_dong]);
            flash('Đã thêm người dùng "' . $ho_ten . '".');
        }
    } catch (PDOException $e) {
        error_log('Lưu người dùng lỗi: ' . $e->getMessage());
        flash('Không lưu được người dùng, vui lòng thử lại.', true);
    }
    redirect($back);
}

if ($action === 'delete') {
    if ($id === $me) {
        flash('Bạn không thể tự xóa tài khoản của chính mình.', true);
        redirect($back);
    }
    $st = $pdo->prepare('SELECT ho_ten FROM nguoi_dung WHERE id_nguoi_dung = ?');
    $st->execute([$id]);
    $ten = $st->fetchColumn();
    if ($ten === false) {
        flash('Người dùng không tồn tại.', true);
        redirect($back);
    }
    try {
        $pdo->prepare('DELETE FROM nguoi_dung WHERE id_nguoi_dung = ?')->execute([$id]);
        flash('Đã xóa người dùng "' . $ten . '".', true);
    } catch (PDOException $e) {
        // Vướng khóa ngoại: người này đã có đơn hàng, phiếu nhập, yêu cầu bảo dưỡng...
        flash('Không thể xóa "' . $ten . '" vì đã có dữ liệu liên quan (đơn hàng, phiếu nhập...). Hãy khóa tài khoản thay vì xóa.', true);
    }
    redirect($back);
}

flash('Thao tác không hợp lệ.', true);
redirect($back);
