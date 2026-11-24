<?php
// Giỏ hàng lưu trong session: $_SESSION['cart'][id_san_pham] = so_luong
//
// POST action = add     : id, so_luong (mặc định 1)
//             = update  : qty[id_san_pham] = so_luong   (form ở gio-hang.php)
//             = remove  : id
//             = clear
require_once __DIR__ . '/../includes/bootstrap.php';

$cart_page = BASE_URL . 'gio-hang.php';
require_post($cart_page);

$action = $_POST['action'] ?? '';
$cart   = $_SESSION['cart'] ?? [];
$back   = back_url($cart_page);

switch ($action) {
    case 'add':
        $id  = (int)($_POST['id'] ?? 0);
        $qty = max(1, (int)($_POST['so_luong'] ?? 1));

        $st = $pdo->prepare('SELECT ten_san_pham, so_luong_ton FROM san_pham WHERE id_san_pham = ?');
        $st->execute([$id]);
        $sp = $st->fetch();

        if (!$sp) {
            flash('Sản phẩm không tồn tại.', true);
            break;
        }
        $ton = (int)$sp['so_luong_ton'];
        if ($ton <= 0) {
            flash('"' . $sp['ten_san_pham'] . '" hiện đã hết hàng.', true);
            break;
        }
        $new = ($cart[$id] ?? 0) + $qty;
        if ($new > $ton) {
            $cart[$id] = $ton;
            flash('Kho chỉ còn ' . $ton . ' sản phẩm "' . $sp['ten_san_pham'] . '", đã điều chỉnh số lượng trong giỏ.', true);
        } else {
            $cart[$id] = $new;
            flash('Đã thêm ' . $qty . ' × "' . $sp['ten_san_pham'] . '" vào giỏ hàng!');
        }
        break;

    case 'update':
        $qtys = $_POST['qty'] ?? [];
        if (!is_array($qtys) || !$cart) {
            break;
        }
        // Lấy tồn kho của các sản phẩm đang có trong giỏ bằng 1 truy vấn
        $ids = array_map('intval', array_keys($cart));
        $in  = implode(',', array_fill(0, count($ids), '?'));
        $st  = $pdo->prepare("SELECT id_san_pham, so_luong_ton FROM san_pham WHERE id_san_pham IN ($in)");
        $st->execute($ids);
        $stock = array_column($st->fetchAll(), 'so_luong_ton', 'id_san_pham');

        foreach ($qtys as $id => $q) {
            $id = (int)$id;
            $q  = (int)$q;
            if (!isset($cart[$id])) {
                continue; // chỉ sửa món đang có trong giỏ
            }
            if ($q <= 0 || !isset($stock[$id])) {
                unset($cart[$id]);
            } else {
                $cart[$id] = min($q, (int)$stock[$id]);
            }
        }
        flash('Đã cập nhật số lượng giỏ hàng!');
        break;

    case 'remove':
        $id = (int)($_POST['id'] ?? 0);
        unset($cart[$id]);
        flash('Đã xóa sản phẩm khỏi giỏ!');
        break;

    case 'clear':
        $cart = [];
        flash('Đã làm trống giỏ hàng!');
        break;

    default:
        flash('Thao tác không hợp lệ.', true);
}

$_SESSION['cart'] = $cart;
redirect($back);
