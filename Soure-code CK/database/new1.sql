-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 14, 2026 lúc 03:37 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `new1`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chi_tiet_don_hang`
--

CREATE TABLE `chi_tiet_don_hang` (
  `id_chi_tiet_don_hang` int(11) NOT NULL,
  `id_don_hang` int(11) NOT NULL,
  `id_san_pham` int(11) NOT NULL,
  `so_luong` int(11) NOT NULL DEFAULT 1,
  `don_gia` decimal(12,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `chi_tiet_don_hang`
--

INSERT INTO `chi_tiet_don_hang` (`id_chi_tiet_don_hang`, `id_don_hang`, `id_san_pham`, `so_luong`, `don_gia`) VALUES
(1, 1, 1, 1, 3450000.00),
(2, 2, 3, 1, 21500000.00),
(3, 3, 2, 1, 19800000.00),
(4, 4, 5, 1, 2300000.00),
(5, 5, 4, 1, 14500000.00),
(6, 6, 5, 1, 2300000.00),
(7, 7, 2, 1, 1950000.00),
(8, 8, 5, 1, 2300000.00),
(9, 8, 3, 1, 20000000.00),
(10, 8, 4, 1, 14500000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chi_tiet_nhap_kho`
--

CREATE TABLE `chi_tiet_nhap_kho` (
  `id_chi_tiet` int(11) NOT NULL,
  `id_phieu_nhap` int(11) NOT NULL,
  `id_san_pham` int(11) NOT NULL,
  `so_luong` int(11) NOT NULL DEFAULT 1,
  `gia_nhap` decimal(12,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `chi_tiet_nhap_kho`
--

INSERT INTO `chi_tiet_nhap_kho` (`id_chi_tiet`, `id_phieu_nhap`, `id_san_pham`, `so_luong`, `gia_nhap`) VALUES
(1, 1, 1, 15, 2500000.00),
(2, 2, 2, 5, 15500000.00),
(3, 3, 3, 6, 17500000.00),
(4, 4, 4, 5, 11600000.00),
(5, 5, 5, 10, 1700000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danh_gia`
--

CREATE TABLE `danh_gia` (
  `id_danh_gia` int(11) NOT NULL,
  `id_san_pham` int(11) NOT NULL,
  `id_khach_hang` int(11) NOT NULL,
  `so_sao` tinyint(4) NOT NULL,
  `binh_luan` text DEFAULT NULL,
  `ngay_danh_gia` datetime NOT NULL DEFAULT current_timestamp()
) ;

--
-- Đang đổ dữ liệu cho bảng `danh_gia`
--

INSERT INTO `danh_gia` (`id_danh_gia`, `id_san_pham`, `id_khach_hang`, `so_sao`, `binh_luan`, `ngay_danh_gia`) VALUES
(1, 1, 2, 5, 'Âm vang sáng, bấm êm tay. Đóng gói cẩn thận!', '2026-11-08 15:09:09'),
(2, 3, 3, 4, 'Phím có trọng lượng thật, giá hợp lý.', '2026-11-08 15:09:09'),
(3, 5, 1, 5, 'sản phẩm tốt và chất lượng', '2026-11-10 13:58:42'),
(5, 5, 1, 5, 'sản phẩm tốt và chất lượng', '2026-11-10 14:05:23'),
(6, 5, 14, 1, 'sản phẩm tệ', '2026-11-10 14:41:11');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danh_muc`
--

CREATE TABLE `danh_muc` (
  `id_danh_muc` int(11) NOT NULL,
  `ten_danh_muc` varchar(150) NOT NULL,
  `mo_ta` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `danh_muc`
--

INSERT INTO `danh_muc` (`id_danh_muc`, `ten_danh_muc`, `mo_ta`) VALUES
(1, 'Đàn Guitar', 'Đàn guitar classic, guitar acoustic, guitar điện và guitar bass'),
(2, 'Đàn Piano & Keyboard', 'Đàn piano cơ acoustic, piano điện tử kỹ thuật số và organ'),
(3, 'Trống & Bộ gõ', 'Bộ trống cơ chuyên nghiệp, trống điện tử, cajon và cymbal'),
(4, 'Kèn & Nhạc cụ hơi', 'Kèn Saxophone, Trumpet, sáo Flute và kèn Harmonica'),
(5, 'Phụ kiện âm nhạc', 'Amplifier, bao da, chân để đàn, capo, dây đàn và tuner');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `don_hang`
--

CREATE TABLE `don_hang` (
  `id_don_hang` int(11) NOT NULL,
  `id_khach_hang` int(11) NOT NULL,
  `id_nhan_vien` int(11) DEFAULT NULL,
  `ngay_dat` datetime DEFAULT current_timestamp(),
  `phi_van_chuyen` decimal(12,2) DEFAULT 0.00,
  `tong_thanh_toan` decimal(12,2) NOT NULL DEFAULT 0.00,
  `trang_thai_don_hang` varchar(50) DEFAULT 'ChoXuLy',
  `ten_nguoi_nhan` varchar(150) DEFAULT NULL,
  `sdt_nguoi_nhan` varchar(15) DEFAULT NULL,
  `dia_chi_giao` varchar(255) DEFAULT NULL,
  `ghi_chu` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `don_hang`
--

INSERT INTO `don_hang` (`id_don_hang`, `id_khach_hang`, `id_nhan_vien`, `ngay_dat`, `phi_van_chuyen`, `tong_thanh_toan`, `trang_thai_don_hang`, `ten_nguoi_nhan`, `sdt_nguoi_nhan`, `dia_chi_giao`, `ghi_chu`) VALUES
(1, 2, 4, '2026-09-01 09:00:00', 50000.00, 3500000.00, 'HoanThanh', 'Trần Minh Khang', '0912345678', '45 Lê Duẩn, P. Bến Nghé, Q.1, TP.HCM', NULL),
(2, 3, 4, '2026-09-03 14:30:00', 150000.00, 21650000.00, 'HoanThanh', 'Lê Thị Mỹ Duyên', '0987654321', '89 Nguyễn Trãi, Phường 3, Q.5, TP.HCM', NULL),
(3, 7, 4, '2026-09-05 10:15:00', 100000.00, 19900000.00, 'DangGiao', 'Vũ Hải Đăng', '0966778899', '15 Quang Trung, Phường 10, Q. Gò Vấp, TP.HCM', NULL),
(4, 2, 4, '2026-09-10 16:20:00', 40000.00, 2340000.00, 'ChoXuLy', 'Trần Minh Khang', '0912345678', '45 Lê Duẩn, P. Bến Nghé, Q.1, TP.HCM', NULL),
(5, 3, 4, '2026-09-12 11:00:00', 200000.00, 14700000.00, 'ChoXuLy', 'Lê Thị Mỹ Duyên', '0987654321', '89 Nguyễn Trãi, Phường 3, Q.5, TP.HCM', NULL),
(6, 2, 1, '2026-11-10 13:27:11', 50000.00, 2350000.00, 'DaHuy', 'Trần Minh Khang', '0123456789', 'asdasd', 'dsada'),
(7, 14, 1, '2026-11-10 14:42:29', 50000.00, 2000000.00, 'ChoXuLy', 'toibingu', '0123456789', 'qawfdwasfe', 'khách hàng đẹp trai vô cùng, không gì có thể so sánh'),
(8, 14, 1, '2026-11-10 14:48:47', 50000.00, 36850000.00, 'HoanThanh', 'toibingu', '0123456789', '212eqwe', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `don_vi_van_chuyen`
--

CREATE TABLE `don_vi_van_chuyen` (
  `id_don_vi` int(11) NOT NULL,
  `ten_don_vi` varchar(150) NOT NULL,
  `lien_he` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `don_vi_van_chuyen`
--

INSERT INTO `don_vi_van_chuyen` (`id_don_vi`, `ten_don_vi`, `lien_he`) VALUES
(1, 'Giao Hàng Tiết Kiệm (GHTK)', '18006092'),
(2, 'Giao Hàng Nhanh (GHN)', '1900636677'),
(3, 'Viettel Post', '19008095'),
(4, 'J&T Express', '19001088'),
(5, 'Shopee Xpress Instant', '19001221');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nguoi_dung`
--

CREATE TABLE `nguoi_dung` (
  `id_nguoi_dung` int(11) NOT NULL,
  `id_vai_tro` int(11) NOT NULL,
  `tai_khoan` varchar(100) DEFAULT NULL,
  `mat_khau` varchar(255) DEFAULT NULL,
  `ho_ten` varchar(150) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `so_dien_thoai` varchar(10) DEFAULT NULL,
  `dia_chi` varchar(255) DEFAULT NULL,
  `trang_thai` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nguoi_dung`
--

INSERT INTO `nguoi_dung` (`id_nguoi_dung`, `id_vai_tro`, `tai_khoan`, `mat_khau`, `ho_ten`, `email`, `so_dien_thoai`, `dia_chi`, `trang_thai`) VALUES
(1, 1, 'admin', '$2y$10$0hza7LYLbzuK5m9MFGR.deuZ6bUpqM3M7K7vCra79Vd9fD6WVYbLO', 'Nguyễn Văn Quản Trị', 'admin@nhaccustore.vn', '0901112233', 'Số 1 Hai Bà Trưng, P. Bến Nghé, Q.1, TP.HCM', 1),
(2, 2, 'minhkhang', '$2y$10$1WhUA44m2HgOq1hA8xXcFugXB2iLXOc0HfCIA/HXWS.3eMT6RXQae', 'Trần Minh Khang', 'minhkhang.tran@gmail.com', '0912345678', '45 Lê Duẩn, P. Bến Nghé, Q.1, TP.HCM', 1),
(3, 2, 'myduyen', '123456', 'Lê Thị Mỹ Duyên', 'myduyen.le@gmail.com', '0987654321', '89 Nguyễn Trãi, Phường 3, Q.5, TP.HCM', 1),
(4, 2, 'quocbao', '123456', 'Phạm Quốc Bảo', 'baonv.sales@nhaccustore.vn', '0933445566', '120 Điện Biên Phủ, P. 15, Q. Bình Thạnh, TP.HCM', 1),
(5, 1, 'tantai', '123456', 'Đỗ Tấn Tài', 'taidt.kho@nhaccustore.vn', '0944556677', '55 Kha Vạn Cân, P. Hiệp Bình Chánh, TP. Thủ Đức', 1),
(6, 2, 'trongphuc', '123456', 'Hoàng Trọng Phúc', 'phucht.tech@nhaccustore.vn', '0977889900', '210 Cách Mạng Tháng 8, Q.3, TP.HCM', 1),
(7, 2, 'haidang', '123456', 'Vũ Hải Đăng', 'dangvu.music@gmail.com', '0966778899', '15 Quang Trung, Phường 10, Q. Gò Vấp, TP.HCM', 1),
(14, 2, 'toibingu', '$2y$10$4S95fzOOz99OlCwH2SM.7.6z4rp66OSUu6JKd6oCp5CIn8OFP2b0y', 'toibingu', '1231237@gmail.com', '0123456789', NULL, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nha_cung_cap`
--

CREATE TABLE `nha_cung_cap` (
  `id_nha_cung_cap` int(11) NOT NULL,
  `ten_nha_cung_cap` varchar(200) NOT NULL,
  `dia_chi` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nha_cung_cap`
--

INSERT INTO `nha_cung_cap` (`id_nha_cung_cap`, `ten_nha_cung_cap`, `dia_chi`) VALUES
(1, 'Công ty TNHH Âm Nhạc Yamaha Việt Nam', 'Tầng 15, MB Sunny Tower, 259 Trần Hưng Đạo, Q.1, TP.HCM'),
(2, 'Công ty Phân Phối Nhạc Cụ Fender Melody', '246 Nguyễn Đình Chiểu, Phường Võ Thị Sáu, Q.3, TP.HCM'),
(3, 'Công ty TNHH Thiết Bị Âm Thanh Roland Á Châu', '36 Võ Văn Tần, Phường 6, Q.3, TP.HCM'),
(4, 'Tổng Kho Bộ Gõ Pearl Drum Việt Nam', '128 Bạch Đằng, Phường 24, Q. Bình Thạnh, TP.HCM'),
(5, 'Đại Lý Phụ Kiện DAddario & Dây Đàn Quốc Tế', '56 Hoàng Hoa Thám, Phường 7, Q. Bình Thạnh, TP.HCM');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phieu_nhap_kho`
--

CREATE TABLE `phieu_nhap_kho` (
  `id_phieu_nhap` int(11) NOT NULL,
  `id_nhan_vien` int(11) NOT NULL,
  `id_nha_cung_cap` int(11) NOT NULL,
  `ngay_nhap` datetime DEFAULT current_timestamp(),
  `tong_tien` decimal(12,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `phieu_nhap_kho`
--

INSERT INTO `phieu_nhap_kho` (`id_phieu_nhap`, `id_nhan_vien`, `id_nha_cung_cap`, `ngay_nhap`, `tong_tien`) VALUES
(1, 5, 1, '2026-08-01 08:30:00', 37500000.00),
(2, 5, 2, '2026-08-05 09:15:00', 77500000.00),
(3, 5, 3, '2026-08-10 10:00:00', 105000000.00),
(4, 5, 4, '2026-08-15 14:20:00', 58000000.00),
(5, 5, 2, '2026-08-20 15:45:00', 17000000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `san_pham`
--

CREATE TABLE `san_pham` (
  `id_san_pham` int(11) NOT NULL,
  `id_danh_muc` int(11) NOT NULL,
  `ten_san_pham` varchar(200) NOT NULL,
  `thuong_hieu` varchar(100) DEFAULT NULL,
  `gia_ban` decimal(12,2) NOT NULL DEFAULT 0.00,
  `so_luong_ton` int(11) NOT NULL DEFAULT 0,
  `mo_ta` text DEFAULT NULL,
  `hinh_anh` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `san_pham`
--

INSERT INTO `san_pham` (`id_san_pham`, `id_danh_muc`, `ten_san_pham`, `thuong_hieu`, `gia_ban`, `so_luong_ton`, `mo_ta`, `hinh_anh`) VALUES
(1, 1, 'Đàn Guitar Acoustic Yamaha F310', 'Yamaha', 3450000.00, 25, 'Đàn guitar acoustic Yamaha F310 mang lại âm thanh mộc ấm áp, thùng đàn gỗ vân sam chọn lọc, cần đàn gỗ nato dễ bấm. Lựa chọn số 1 cho người mới bắt đầu.', 'sp-1.png'),
(2, 1, 'Đàn Guitar Điện Fender Player Stratocaster', 'Fender', 1950000.00, 7, 'Huyền thoại Stratocaster với 3 pickup Single-Coil Player Series, cần đàn Modern C êm ái, thân gỗ Alder cho chất âm sắc bén, linh hoạt.', 'sp-2.png'),
(3, 2, 'Đàn Piano Điện Roland FP-30X', 'Roland', 20000000.00, 11, 'Công nghệ SuperNATURAL Piano kết hợp bàn phím PHA-4 tái hiện cảm giác phím piano cơ. Hỗ trợ Bluetooth Audio/MIDI.', 'sp-3.png'),
(4, 3, 'Bộ Trống Jazz Pearl Roadshow RS525SC', 'Pearl', 14500000.00, 4, 'Bộ trống cơ 5 món của Pearl, vỏ gỗ Poplar 9 lớp, gồm chân cymbal kép, pedal bass và ghế ngồi.', 'sp-4.png'),
(5, 5, 'Amplifier Guitar Fender Frontman 10G', 'Fender', 2300000.00, 17, 'Amply 10W có kênh Clean và Overdrive, loa Fender Special Design 6 inch, có ngõ cắm tai nghe.', 'sp-5.png'),
(6, 1, 'Đàn guitar bass Squier Classic Vibe ’70s Jazz Bass', NULL, 3500000.00, 10, NULL, 'sp-6.png');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thanh_toan`
--

CREATE TABLE `thanh_toan` (
  `id_giao_dich` int(11) NOT NULL,
  `id_don_hang` int(11) NOT NULL,
  `so_tien` decimal(12,2) NOT NULL,
  `loai_thanh_toan` varchar(50) DEFAULT NULL,
  `trang_thai` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `thanh_toan`
--

INSERT INTO `thanh_toan` (`id_giao_dich`, `id_don_hang`, `so_tien`, `loai_thanh_toan`, `trang_thai`) VALUES
(1, 1, 3500000.00, 'COD', 'DaThanhToan'),
(2, 2, 21650000.00, 'ChuyenKhoan', 'DaThanhToan'),
(3, 3, 19900000.00, 'ChuyenKhoan', 'DaThanhToan'),
(4, 4, 2340000.00, 'COD', 'ChuaThanhToan'),
(5, 5, 14700000.00, 'ChuyenKhoan', 'ChuaThanhToan'),
(6, 6, 2350000.00, 'COD', 'ChuaThanhToan'),
(7, 7, 2000000.00, 'COD', 'ChuaThanhToan'),
(8, 8, 36850000.00, 'COD', 'DaThanhToan');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `vai_tro`
--

CREATE TABLE `vai_tro` (
  `id_vai_tro` int(11) NOT NULL,
  `ten_vai_tro` varchar(100) NOT NULL,
  `mo_ta` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `vai_tro`
--

INSERT INTO `vai_tro` (`id_vai_tro`, `ten_vai_tro`, `mo_ta`) VALUES
(1, 'Quản trị viên', 'Toàn quyền cấu hình, vận hành và quản lý hệ thống'),
(2, 'Khách hàng', 'Người dùng cá nhân xem thông tin và mua sắm nhạc cụ');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `van_chuyen`
--

CREATE TABLE `van_chuyen` (
  `id_van_don` int(11) NOT NULL,
  `id_don_hang` int(11) NOT NULL,
  `id_don_vi` int(11) NOT NULL,
  `trang_thai_giao_hang` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `van_chuyen`
--

INSERT INTO `van_chuyen` (`id_van_don`, `id_don_hang`, `id_don_vi`, `trang_thai_giao_hang`) VALUES
(1, 1, 1, 'GiaoThanhCong'),
(2, 2, 2, 'GiaoThanhCong'),
(3, 3, 3, 'DangVanChuyen'),
(4, 4, 1, 'ChoLayHang'),
(5, 5, 4, 'ChoLayHang');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `yeu_cau_tu_van`
--

CREATE TABLE `yeu_cau_tu_van` (
  `id_tu_van` int(11) NOT NULL,
  `id_khach_hang` int(11) DEFAULT NULL,
  `id_nhan_vien_ho_tro` int(11) DEFAULT NULL,
  `ten_khach` varchar(150) DEFAULT NULL,
  `tieu_de` varchar(255) NOT NULL,
  `trang_thai` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `yeu_cau_tu_van`
--

INSERT INTO `yeu_cau_tu_van` (`id_tu_van`, `id_khach_hang`, `id_nhan_vien_ho_tro`, `ten_khach`, `tieu_de`, `trang_thai`) VALUES
(1, 2, 4, 'Trần Minh Khang', 'Tư vấn chọn mua đàn Guitar cho người mới bắt đầu dưới 4 triệu', 'DaGiaiQuyet'),
(2, 3, 4, 'Lê Thị Mỹ Duyên', 'So sánh phím đàn Piano điện Roland FP-30X và Yamaha P-125', 'DaGiaiQuyet'),
(3, 7, 4, 'Vũ Hải Đăng', 'Hỏi chính sách bảo hành và phụ kiện đi kèm bộ trống Pearl', 'DangTuVan'),
(4, NULL, 4, 'Đỗ Khắc Nam', 'Hỏi thủ tục mua nhạc cụ trả góp qua thẻ tín dụng', 'ChoPhanHoi'),
(5, NULL, 4, 'Bùi Tuyết Mai', 'Cửa hàng có sẵn Amply Fender Frontman 10G để test tại chỗ không?', 'ChoPhanHoi');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `chi_tiet_don_hang`
--
ALTER TABLE `chi_tiet_don_hang`
  ADD PRIMARY KEY (`id_chi_tiet_don_hang`),
  ADD KEY `fk_ctdh_donhang` (`id_don_hang`),
  ADD KEY `fk_ctdh_sanpham` (`id_san_pham`);

--
-- Chỉ mục cho bảng `chi_tiet_nhap_kho`
--
ALTER TABLE `chi_tiet_nhap_kho`
  ADD PRIMARY KEY (`id_chi_tiet`),
  ADD KEY `fk_ctnk_phieunhap` (`id_phieu_nhap`),
  ADD KEY `fk_ctnk_sanpham` (`id_san_pham`);

--
-- Chỉ mục cho bảng `danh_gia`
--
ALTER TABLE `danh_gia`
  ADD PRIMARY KEY (`id_danh_gia`),
  ADD KEY `fk_danhgia_khachhang` (`id_khach_hang`),
  ADD KEY `idx_san_pham` (`id_san_pham`);

--
-- Chỉ mục cho bảng `danh_muc`
--
ALTER TABLE `danh_muc`
  ADD PRIMARY KEY (`id_danh_muc`);

--
-- Chỉ mục cho bảng `don_hang`
--
ALTER TABLE `don_hang`
  ADD PRIMARY KEY (`id_don_hang`),
  ADD KEY `fk_donhang_khachhang` (`id_khach_hang`),
  ADD KEY `fk_donhang_nhanvien` (`id_nhan_vien`);

--
-- Chỉ mục cho bảng `don_vi_van_chuyen`
--
ALTER TABLE `don_vi_van_chuyen`
  ADD PRIMARY KEY (`id_don_vi`);

--
-- Chỉ mục cho bảng `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  ADD PRIMARY KEY (`id_nguoi_dung`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `tai_khoan` (`tai_khoan`),
  ADD KEY `fk_nguoidung_vaitro` (`id_vai_tro`);

--
-- Chỉ mục cho bảng `nha_cung_cap`
--
ALTER TABLE `nha_cung_cap`
  ADD PRIMARY KEY (`id_nha_cung_cap`);

--
-- Chỉ mục cho bảng `phieu_nhap_kho`
--
ALTER TABLE `phieu_nhap_kho`
  ADD PRIMARY KEY (`id_phieu_nhap`),
  ADD KEY `fk_phieunhap_nhanvien` (`id_nhan_vien`),
  ADD KEY `fk_phieunhap_nhacungcap` (`id_nha_cung_cap`);

--
-- Chỉ mục cho bảng `san_pham`
--
ALTER TABLE `san_pham`
  ADD PRIMARY KEY (`id_san_pham`),
  ADD KEY `fk_sanpham_danhmuc` (`id_danh_muc`);

--
-- Chỉ mục cho bảng `thanh_toan`
--
ALTER TABLE `thanh_toan`
  ADD PRIMARY KEY (`id_giao_dich`),
  ADD KEY `fk_thanhtoan_donhang` (`id_don_hang`);

--
-- Chỉ mục cho bảng `vai_tro`
--
ALTER TABLE `vai_tro`
  ADD PRIMARY KEY (`id_vai_tro`);

--
-- Chỉ mục cho bảng `van_chuyen`
--
ALTER TABLE `van_chuyen`
  ADD PRIMARY KEY (`id_van_don`),
  ADD KEY `fk_vanchuyen_donhang` (`id_don_hang`),
  ADD KEY `fk_vanchuyen_donvi` (`id_don_vi`);

--
-- Chỉ mục cho bảng `yeu_cau_tu_van`
--
ALTER TABLE `yeu_cau_tu_van`
  ADD PRIMARY KEY (`id_tu_van`),
  ADD KEY `fk_tuvan_khachhang` (`id_khach_hang`),
  ADD KEY `fk_tuvan_nhanvien` (`id_nhan_vien_ho_tro`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `chi_tiet_don_hang`
--
ALTER TABLE `chi_tiet_don_hang`
  MODIFY `id_chi_tiet_don_hang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `chi_tiet_nhap_kho`
--
ALTER TABLE `chi_tiet_nhap_kho`
  MODIFY `id_chi_tiet` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `danh_gia`
--
ALTER TABLE `danh_gia`
  MODIFY `id_danh_gia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `danh_muc`
--
ALTER TABLE `danh_muc`
  MODIFY `id_danh_muc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `don_hang`
--
ALTER TABLE `don_hang`
  MODIFY `id_don_hang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `don_vi_van_chuyen`
--
ALTER TABLE `don_vi_van_chuyen`
  MODIFY `id_don_vi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  MODIFY `id_nguoi_dung` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT cho bảng `nha_cung_cap`
--
ALTER TABLE `nha_cung_cap`
  MODIFY `id_nha_cung_cap` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `phieu_nhap_kho`
--
ALTER TABLE `phieu_nhap_kho`
  MODIFY `id_phieu_nhap` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `san_pham`
--
ALTER TABLE `san_pham`
  MODIFY `id_san_pham` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `thanh_toan`
--
ALTER TABLE `thanh_toan`
  MODIFY `id_giao_dich` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `vai_tro`
--
ALTER TABLE `vai_tro`
  MODIFY `id_vai_tro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `van_chuyen`
--
ALTER TABLE `van_chuyen`
  MODIFY `id_van_don` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `yeu_cau_tu_van`
--
ALTER TABLE `yeu_cau_tu_van`
  MODIFY `id_tu_van` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `chi_tiet_don_hang`
--
ALTER TABLE `chi_tiet_don_hang`
  ADD CONSTRAINT `fk_ctdh_donhang` FOREIGN KEY (`id_don_hang`) REFERENCES `don_hang` (`id_don_hang`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ctdh_sanpham` FOREIGN KEY (`id_san_pham`) REFERENCES `san_pham` (`id_san_pham`) ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `chi_tiet_nhap_kho`
--
ALTER TABLE `chi_tiet_nhap_kho`
  ADD CONSTRAINT `fk_ctnk_phieunhap` FOREIGN KEY (`id_phieu_nhap`) REFERENCES `phieu_nhap_kho` (`id_phieu_nhap`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ctnk_sanpham` FOREIGN KEY (`id_san_pham`) REFERENCES `san_pham` (`id_san_pham`) ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `danh_gia`
--
ALTER TABLE `danh_gia`
  ADD CONSTRAINT `fk_danhgia_khachhang` FOREIGN KEY (`id_khach_hang`) REFERENCES `nguoi_dung` (`id_nguoi_dung`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_danhgia_sanpham` FOREIGN KEY (`id_san_pham`) REFERENCES `san_pham` (`id_san_pham`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `don_hang`
--
ALTER TABLE `don_hang`
  ADD CONSTRAINT `fk_donhang_khachhang` FOREIGN KEY (`id_khach_hang`) REFERENCES `nguoi_dung` (`id_nguoi_dung`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_donhang_nhanvien` FOREIGN KEY (`id_nhan_vien`) REFERENCES `nguoi_dung` (`id_nguoi_dung`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  ADD CONSTRAINT `fk_nguoidung_vaitro` FOREIGN KEY (`id_vai_tro`) REFERENCES `vai_tro` (`id_vai_tro`) ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `phieu_nhap_kho`
--
ALTER TABLE `phieu_nhap_kho`
  ADD CONSTRAINT `fk_phieunhap_nhacungcap` FOREIGN KEY (`id_nha_cung_cap`) REFERENCES `nha_cung_cap` (`id_nha_cung_cap`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_phieunhap_nhanvien` FOREIGN KEY (`id_nhan_vien`) REFERENCES `nguoi_dung` (`id_nguoi_dung`) ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `san_pham`
--
ALTER TABLE `san_pham`
  ADD CONSTRAINT `fk_sanpham_danhmuc` FOREIGN KEY (`id_danh_muc`) REFERENCES `danh_muc` (`id_danh_muc`) ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `thanh_toan`
--
ALTER TABLE `thanh_toan`
  ADD CONSTRAINT `fk_thanhtoan_donhang` FOREIGN KEY (`id_don_hang`) REFERENCES `don_hang` (`id_don_hang`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `van_chuyen`
--
ALTER TABLE `van_chuyen`
  ADD CONSTRAINT `fk_vanchuyen_donhang` FOREIGN KEY (`id_don_hang`) REFERENCES `don_hang` (`id_don_hang`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_vanchuyen_donvi` FOREIGN KEY (`id_don_vi`) REFERENCES `don_vi_van_chuyen` (`id_don_vi`) ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `yeu_cau_tu_van`
--
ALTER TABLE `yeu_cau_tu_van`
  ADD CONSTRAINT `fk_tuvan_khachhang` FOREIGN KEY (`id_khach_hang`) REFERENCES `nguoi_dung` (`id_nguoi_dung`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tuvan_nhanvien` FOREIGN KEY (`id_nhan_vien_ho_tro`) REFERENCES `nguoi_dung` (`id_nguoi_dung`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
