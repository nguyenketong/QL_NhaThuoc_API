-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 14, 2026 at 01:12 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ql_nhathuoc_api`
--

-- --------------------------------------------------------

--
-- Table structure for table `baiviet`
--

CREATE TABLE `baiviet` (
  `MaBaiViet` int(11) NOT NULL,
  `TieuDe` varchar(200) NOT NULL,
  `MoTaNgan` varchar(500) DEFAULT NULL,
  `NoiDung` text DEFAULT NULL,
  `HinhAnh` varchar(500) DEFAULT NULL,
  `NgayDang` datetime DEFAULT current_timestamp(),
  `LuotXem` int(11) DEFAULT 0,
  `IsNoiBat` tinyint(1) DEFAULT 0,
  `IsActive` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `baiviet`
--

INSERT INTO `baiviet` (`MaBaiViet`, `TieuDe`, `MoTaNgan`, `NoiDung`, `HinhAnh`, `NgayDang`, `LuotXem`, `IsNoiBat`, `IsActive`) VALUES
(1, 'Cách sử dụng thuốc giảm đau an toàn', 'Hướng dẫn sử dụng thuốc giảm đau đúng cách', 'Nội dung chi tiết về cách sử dụng thuốc giảm đau...', 'http://localhost/Ql_NhaThuoc/php/assets/images/baiviet/6964b94047a84.jpg', '2025-12-31 14:10:44', 1, 1, 1),
(2, 'Vitamin C và sức khỏe', 'Tầm quan trọng của Vitamin C đối với cơ thể', 'Nội dung chi tiết về Vitamin C...', 'http://localhost/Ql_NhaThuoc/php/assets/images/baiviet/6964b979f3020.png', '2025-12-31 14:10:44', 3, 1, 1),
(3, 'Phòng ngừa cảm cúm mùa đông', 'Các biện pháp phòng ngừa cảm cúm hiệu quả', 'Nội dung chi tiết về phòng ngừa cảm cúm...', 'http://localhost/Ql_NhaThuoc/php/assets/images/baiviet/6964b9a0574e9.jpg', '2025-12-31 14:10:44', 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `chi_tiet_don_hang`
--

CREATE TABLE `chi_tiet_don_hang` (
  `MaChiTiet` int(11) NOT NULL,
  `MaDonHang` int(11) NOT NULL,
  `MaThuoc` int(11) NOT NULL,
  `SoLuong` int(11) NOT NULL,
  `DonGia` decimal(15,2) DEFAULT NULL,
  `ThanhTien` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chi_tiet_don_hang`
--

INSERT INTO `chi_tiet_don_hang` (`MaChiTiet`, `MaDonHang`, `MaThuoc`, `SoLuong`, `DonGia`, `ThanhTien`) VALUES
(1, 1, 10, 1, 65250.00, 65250.00),
(2, 2, 10, 1, 65250.00, 65250.00);

-- --------------------------------------------------------

--
-- Table structure for table `ct_doi_tuong`
--

CREATE TABLE `ct_doi_tuong` (
  `MaThuoc` int(11) NOT NULL,
  `MaDoiTuong` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ct_doi_tuong`
--

INSERT INTO `ct_doi_tuong` (`MaThuoc`, `MaDoiTuong`) VALUES
(3, 1),
(10, 1);

-- --------------------------------------------------------

--
-- Table structure for table `ct_tac_dung_phu`
--

CREATE TABLE `ct_tac_dung_phu` (
  `MaThuoc` int(11) NOT NULL,
  `MaTacDungPhu` int(11) NOT NULL,
  `MucDo` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ct_tac_dung_phu`
--

INSERT INTO `ct_tac_dung_phu` (`MaThuoc`, `MaTacDungPhu`, `MucDo`) VALUES
(10, 1, 'Nhẹ');

-- --------------------------------------------------------

--
-- Table structure for table `ct_thanh_phan`
--

CREATE TABLE `ct_thanh_phan` (
  `MaThuoc` int(11) NOT NULL,
  `MaThanhPhan` int(11) NOT NULL,
  `HamLuong` varchar(100) DEFAULT NULL,
  `GhiChu` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ct_thanh_phan`
--

INSERT INTO `ct_thanh_phan` (`MaThuoc`, `MaThanhPhan`, `HamLuong`, `GhiChu`) VALUES
(10, 1, '300mg', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `doi_tuong_su_dung`
--

CREATE TABLE `doi_tuong_su_dung` (
  `MaDoiTuong` int(11) NOT NULL,
  `TenDoiTuong` varchar(100) NOT NULL,
  `MoTa` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doi_tuong_su_dung`
--

INSERT INTO `doi_tuong_su_dung` (`MaDoiTuong`, `TenDoiTuong`, `MoTa`) VALUES
(1, 'Người lớn', ''),
(2, 'Tất cả mọi người', ''),
(3, 'Người cao tuổi', ''),
(4, 'Trẻ em', ''),
(5, 'Trẻ sơ sinh', ''),
(6, 'Phụ nữ mang thai', '');

-- --------------------------------------------------------

--
-- Table structure for table `don_hang`
--

CREATE TABLE `don_hang` (
  `MaDonHang` int(11) NOT NULL,
  `MaNguoiDung` int(11) NOT NULL,
  `NgayDatHang` datetime DEFAULT current_timestamp(),
  `TongTien` decimal(15,2) DEFAULT NULL,
  `TrangThai` varchar(50) DEFAULT 'Chờ xác nhận',
  `PhuongThucThanhToan` varchar(50) DEFAULT NULL,
  `DiaChiGiaoHang` varchar(500) DEFAULT NULL,
  `DaThanhToan` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `don_hang`
--

INSERT INTO `don_hang` (`MaDonHang`, `MaNguoiDung`, `NgayDatHang`, `TongTien`, `TrangThai`, `PhuongThucThanhToan`, `DiaChiGiaoHang`, `DaThanhToan`) VALUES
(1, 4, '2026-01-11 11:49:36', 65250.00, 'Hoan thanh', 'Tiền mặt', 'ấp tân an chợ, Xã Tân An, Huyện Càng Long, Tỉnh Trà Vinh', 0),
(2, 4, '2026-01-11 12:23:12', 65250.00, 'Hoan thanh', 'Chuyển khoản', 'ấp tân an chợ, Xã Tân An, Huyện Càng Long, Tỉnh Trà Vinh', 1);

-- --------------------------------------------------------

--
-- Table structure for table `nguoi_dung`
--

CREATE TABLE `nguoi_dung` (
  `MaNguoiDung` int(11) NOT NULL,
  `HoTen` varchar(100) DEFAULT NULL,
  `SoDienThoai` varchar(15) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `MatKhau` varchar(255) DEFAULT NULL,
  `GoogleId` varchar(255) DEFAULT NULL,
  `Avatar` varchar(500) DEFAULT NULL,
  `LoaiDangNhap` enum('Phone','Google','Email') DEFAULT 'Phone',
  `DiaChi` varchar(500) DEFAULT NULL,
  `OTP` varchar(6) DEFAULT NULL,
  `OTP_Expire` datetime DEFAULT NULL,
  `NgayTao` datetime DEFAULT current_timestamp(),
  `VaiTro` enum('User','Admin') DEFAULT 'User'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nguoi_dung`
--

INSERT INTO `nguoi_dung` (`MaNguoiDung`, `HoTen`, `SoDienThoai`, `Email`, `MatKhau`, `GoogleId`, `Avatar`, `LoaiDangNhap`, `DiaChi`, `OTP`, `OTP_Expire`, `NgayTao`, `VaiTro`) VALUES
(1, 'Admin', '0795930020', NULL, '$2y$10$4iNf/sMgwULd6rkBb73.kuG8g8JQeNFqz4cwCoQ12QKO9cBYCcjQe', NULL, NULL, 'Phone', '123 Đường ABC, Quận 1, TP.HCM', '513960', '2026-01-14 12:30:49', '2025-12-31 14:10:44', 'Admin'),
(3, 'Kế Tông Nguyễn', NULL, 'nguyenketong1603@gmail.com', NULL, '117433753039301583418', 'https://lh3.googleusercontent.com/a/ACg8ocITtphoKuIBidpi6c-sFX7Geu9wfw_XwC3YO4gHnuIdsLD4u64=s96-c', 'Google', 'trà vinh', NULL, NULL, '2025-12-31 14:57:28', 'User'),
(4, 'Nguyễn Kế Tông', '0939857557', NULL, NULL, NULL, NULL, 'Phone', 'Trà Vinh\r\n', NULL, NULL, '2026-01-07 13:43:56', 'User'),
(5, 'Khách hàng 5120', '0355745120', NULL, NULL, NULL, NULL, 'Phone', NULL, '584871', '2026-01-14 08:28:15', '2026-01-07 14:06:57', 'User'),
(6, 'Hồ Quang Vinh', '0768867054', NULL, NULL, NULL, NULL, 'Phone', '', '654466', '2026-01-14 10:03:15', '2026-01-14 14:24:07', 'User');

-- --------------------------------------------------------

--
-- Table structure for table `nhom_thuoc`
--

CREATE TABLE `nhom_thuoc` (
  `MaNhomThuoc` int(11) NOT NULL,
  `TenNhomThuoc` varchar(100) NOT NULL,
  `MoTa` varchar(500) DEFAULT NULL,
  `MaDanhMucCha` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nhom_thuoc`
--

INSERT INTO `nhom_thuoc` (`MaNhomThuoc`, `TenNhomThuoc`, `MoTa`, `MaDanhMucCha`) VALUES
(1, 'Thuốc giảm đau, hạ sốt', 'Các loại thuốc giảm đau và hạ sốt', NULL),
(2, 'Thuốc kháng sinh', 'Các loại thuốc kháng sinh', NULL),
(3, 'Thuốc ho, cảm cúm', 'Thuốc điều trị ho và cảm cúm', NULL),
(4, 'Vitamin & Thực phẩm chức năng', 'Vitamin và các loại thực phẩm bổ sung', NULL),
(5, 'Thuốc tiêu hóa', 'Thuốc điều trị các bệnh về tiêu hóa', NULL),
(6, 'Thuốc da liễu', 'Thuốc điều trị các bệnh về da', NULL),
(7, 'Thuốc tim mạch', 'Thuốc điều trị các bệnh tim mạch', NULL),
(8, 'Thuốc thần kinh', 'Thuốc điều trị các bệnh thần kinh', NULL),
(9, 'Thuốc trị nấm da', '', 6),
(10, 'Thuốc vitamin', 'Thuốc cung cấp các vitamin', 4);

-- --------------------------------------------------------

--
-- Table structure for table `nuoc_san_xuat`
--

CREATE TABLE `nuoc_san_xuat` (
  `MaNuocSX` int(11) NOT NULL,
  `TenNuocSX` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nuoc_san_xuat`
--

INSERT INTO `nuoc_san_xuat` (`MaNuocSX`, `TenNuocSX`) VALUES
(1, 'Việt Nam'),
(2, 'Pháp'),
(3, 'Mỹ'),
(4, 'Thụy Sĩ'),
(5, 'Đức'),
(6, 'Nhật Bản'),
(7, 'Hàn Quốc'),
(8, 'Ấn Độ'),
(9, 'Balan');

-- --------------------------------------------------------

--
-- Table structure for table `tac_dung_phu`
--

CREATE TABLE `tac_dung_phu` (
  `MaTacDungPhu` int(11) NOT NULL,
  `TenTacDungPhu` varchar(100) NOT NULL,
  `MoTa` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tac_dung_phu`
--

INSERT INTO `tac_dung_phu` (`MaTacDungPhu`, `TenTacDungPhu`, `MoTa`) VALUES
(1, 'Buồn nôn', '');

-- --------------------------------------------------------

--
-- Table structure for table `thanh_phan`
--

CREATE TABLE `thanh_phan` (
  `MaThanhPhan` int(11) NOT NULL,
  `TenThanhPhan` varchar(100) NOT NULL,
  `MoTa` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `thanh_phan`
--

INSERT INTO `thanh_phan` (`MaThanhPhan`, `TenThanhPhan`, `MoTa`) VALUES
(1, 'paracetamol', 'paracetamol');

-- --------------------------------------------------------

--
-- Table structure for table `thong_bao`
--

CREATE TABLE `thong_bao` (
  `MaThongBao` int(11) NOT NULL,
  `MaNguoiDung` int(11) NOT NULL,
  `MaDonHang` int(11) DEFAULT NULL,
  `TieuDe` varchar(200) NOT NULL,
  `NoiDung` varchar(1000) NOT NULL,
  `LoaiThongBao` enum('DonHang','KhuyenMai','HeThong') DEFAULT 'HeThong',
  `DaDoc` tinyint(1) DEFAULT 0,
  `NgayTao` datetime DEFAULT current_timestamp(),
  `DuongDan` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `thong_bao`
--

INSERT INTO `thong_bao` (`MaThongBao`, `MaNguoiDung`, `MaDonHang`, `TieuDe`, `NoiDung`, `LoaiThongBao`, `DaDoc`, `NgayTao`, `DuongDan`) VALUES
(1, 4, 1, 'Đơn hàng #1 đang giao', 'Đơn hàng của bạn đã được giao cho đơn vị vận chuyển. Vui lòng chú ý điện thoại!', 'DonHang', 1, '2026-01-11 12:03:41', '/don-hang/chi-tiet/1'),
(2, 4, 1, 'Đơn hàng #1 hoàn thành', 'Đơn hàng đã giao thành công. Cảm ơn bạn đã mua hàng!', 'DonHang', 1, '2026-01-11 12:15:09', '/don-hang/chi-tiet/1'),
(3, 4, 2, 'Đơn hàng #2 đã xác nhận thanh toán', 'Chúng tôi đã nhận được tiền chuyển khoản của bạn. Đơn hàng sẽ sớm được xử lý!', 'DonHang', 1, '2026-01-11 12:49:42', '/don-hang/chi-tiet/2'),
(4, 4, 2, 'Đơn hàng #2 đang giao', 'Đơn hàng của bạn đã được giao cho đơn vị vận chuyển. Vui lòng chú ý điện thoại!', 'DonHang', 1, '2026-01-11 12:55:46', '/donHang/chiTiet/2'),
(5, 4, 2, 'Đơn hàng #2 hoàn thành', 'Đơn hàng đã giao thành công. Cảm ơn bạn đã mua hàng!', 'DonHang', 1, '2026-01-12 13:30:55', '/donHang/chiTiet/2');

-- --------------------------------------------------------

--
-- Table structure for table `thuoc`
--

CREATE TABLE `thuoc` (
  `MaThuoc` int(11) NOT NULL,
  `TenThuoc` varchar(200) NOT NULL,
  `MaNhomThuoc` int(11) NOT NULL,
  `MaNuocSX` int(11) DEFAULT NULL,
  `MaThuongHieu` int(11) DEFAULT NULL,
  `GiaBan` decimal(15,2) DEFAULT NULL,
  `DonViTinh` varchar(50) DEFAULT NULL,
  `MoTa` varchar(2000) DEFAULT NULL,
  `HinhAnh` varchar(500) DEFAULT NULL,
  `SoLuongTon` int(11) DEFAULT 0,
  `SoLuongDaBan` int(11) DEFAULT 0,
  `NgayTao` datetime DEFAULT current_timestamp(),
  `GiaGoc` decimal(15,2) DEFAULT NULL,
  `PhanTramGiam` int(11) DEFAULT NULL,
  `NgayBatDauKM` datetime DEFAULT NULL,
  `NgayKetThucKM` datetime DEFAULT NULL,
  `IsHot` tinyint(1) DEFAULT 0,
  `IsNew` tinyint(1) DEFAULT 0,
  `IsActive` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `thuoc`
--

INSERT INTO `thuoc` (`MaThuoc`, `TenThuoc`, `MaNhomThuoc`, `MaNuocSX`, `MaThuongHieu`, `GiaBan`, `DonViTinh`, `MoTa`, `HinhAnh`, `SoLuongTon`, `SoLuongDaBan`, `NgayTao`, `GiaGoc`, `PhanTramGiam`, `NgayBatDauKM`, `NgayKetThucKM`, `IsHot`, `IsNew`, `IsActive`) VALUES
(2, 'Efferalgan 500mg', 1, 2, 1, 55000.00, 'Hộp', 'Thuốc giảm đau, hạ sốt dạng sủi. Hộp 4 vỉ x 4 viên.', 'http://localhost/Ql_NhaThuoc/php/assets/images/6964b2a6c998f.jpg', 80, 0, '2025-12-31 14:10:44', NULL, NULL, NULL, NULL, 0, 1, 1),
(3, 'Amoxicillin 500mg', 2, 1, 5, 35000.00, 'Hộp', 'Kháng sinh phổ rộng. Hộp 10 vỉ x 10 viên.', 'http://localhost/Ql_NhaThuoc/php/assets/images/6964b29172973.jpg', 50, 0, '2025-12-31 14:10:44', 40000.00, 12, NULL, NULL, 1, 0, 1),
(4, 'Augmentin 625mg', 2, 2, 1, 120000.00, 'Hộp', 'Kháng sinh kết hợp. Hộp 2 vỉ x 7 viên.', 'http://localhost/Ql_NhaThuoc/php/assets/images/6964b27c1e467.jpg', 30, 0, '2025-12-31 14:10:44', NULL, NULL, NULL, NULL, 0, 1, 1),
(5, 'Tiffy', 3, 1, 6, 15000.00, 'Hộp', 'Thuốc cảm cúm. Hộp 25 gói.', 'http://localhost/Ql_NhaThuoc/php/assets/images/6964b26890758.jpg', 200, 0, '2025-12-31 14:10:44', 18000.00, 17, NULL, NULL, 1, 0, 1),
(6, 'Decolgen', 3, 1, 5, 20000.00, 'Hộp', 'Thuốc cảm cúm, nghẹt mũi. Hộp 25 gói.', 'http://localhost/Ql_NhaThuoc/php/assets/images/6964b25890af6.jpg', 150, 0, '2025-12-31 14:10:44', NULL, NULL, NULL, NULL, 1, 1, 1),
(7, 'Vitamin C 1000mg', 4, 1, 5, 55000.00, 'Hộp', 'Bổ sung vitamin C. Hộp 10 vỉ x 10 viên sủi.', 'http://localhost/Ql_NhaThuoc/php/assets/images/6964b2354bd91.jpg', 100, 0, '2025-12-31 14:10:44', 65000.00, 15, NULL, NULL, 0, 1, 1),
(8, 'Centrum Silver', 4, 3, 2, 350000.00, 'Hộp', 'Vitamin tổng hợp cho người trên 50 tuổi. Hộp 100 viên.', 'http://localhost/Ql_NhaThuoc/php/assets/images/6964afa95e684.jpg', 40, 0, '2025-12-31 14:10:44', NULL, NULL, NULL, NULL, 1, 0, 1),
(9, 'Omeprazole 20mg', 5, 1, 7, 28000.00, 'Hộp', 'Thuốc điều trị viêm loét dạ dày. Hộp 3 vỉ x 10 viên.', 'http://localhost/Ql_NhaThuoc/php/assets/images/6964ad238735a.png', 80, 0, '2025-12-31 14:10:44', NULL, NULL, NULL, NULL, 0, 1, 1),
(10, 'Smecta', 5, 2, 1, 65250.00, 'Hộp', 'Thuốc điều trị tiêu chảy. Hộp 30 gói.', 'http://localhost/Ql_NhaThuoc/php/assets/images/69626f4954495.png', 60, 2, '2025-12-31 14:10:44', 75000.00, 13, '2026-01-10 00:00:00', '2026-01-17 00:00:00', 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `thuong_hieu`
--

CREATE TABLE `thuong_hieu` (
  `MaThuongHieu` int(11) NOT NULL,
  `TenThuongHieu` varchar(100) NOT NULL,
  `DiaChi` varchar(500) DEFAULT NULL,
  `QuocGia` varchar(100) DEFAULT NULL,
  `HinhAnh` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `thuong_hieu`
--

INSERT INTO `thuong_hieu` (`MaThuongHieu`, `TenThuongHieu`, `DiaChi`, `QuocGia`, `HinhAnh`) VALUES
(1, 'Sanofi', '', 'Pháp', 'http://localhost/Ql_NhaThuoc/php/assets/images/brands/6964b341856de.webp'),
(2, 'Pfizer', '', 'Mỹ', 'http://localhost/Ql_NhaThuoc/php/assets/images/brands/6964b318df420.png'),
(3, 'Novartis', '', 'Thụy Sĩ', 'http://localhost/Ql_NhaThuoc/php/assets/images/brands/6964b2eb91b44.png'),
(4, 'Roche', '', 'Thụy Sĩ', 'http://localhost/Ql_NhaThuoc/php/assets/images/brands/6964b3350d8a7.jpg'),
(5, 'DHG Pharma', 'Hà Nội', 'Việt Nam', 'http://localhost/Ql_NhaThuoc/php/assets/images/brands/696272ec47420.jpg'),
(6, 'Traphaco', '', 'Việt Nam', 'http://localhost/Ql_NhaThuoc/php/assets/images/brands/6964b3625a3a4.png'),
(7, 'Imexpharm', 'Quận Bình Thạnh, Tp.Hồ CHÍ MINH', 'Việt Nam', 'http://localhost/Ql_NhaThuoc/php/assets/images/brands/6963287035ed4.jpg'),
(8, 'OPC', '', 'Việt Nam', 'http://localhost/Ql_NhaThuoc/php/assets/images/brands/6964b309eca37.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `baiviet`
--
ALTER TABLE `baiviet`
  ADD PRIMARY KEY (`MaBaiViet`);

--
-- Indexes for table `chi_tiet_don_hang`
--
ALTER TABLE `chi_tiet_don_hang`
  ADD PRIMARY KEY (`MaChiTiet`),
  ADD KEY `MaDonHang` (`MaDonHang`),
  ADD KEY `MaThuoc` (`MaThuoc`);

--
-- Indexes for table `ct_doi_tuong`
--
ALTER TABLE `ct_doi_tuong`
  ADD PRIMARY KEY (`MaThuoc`,`MaDoiTuong`),
  ADD KEY `MaDoiTuong` (`MaDoiTuong`);

--
-- Indexes for table `ct_tac_dung_phu`
--
ALTER TABLE `ct_tac_dung_phu`
  ADD PRIMARY KEY (`MaThuoc`,`MaTacDungPhu`),
  ADD KEY `MaTacDungPhu` (`MaTacDungPhu`);

--
-- Indexes for table `ct_thanh_phan`
--
ALTER TABLE `ct_thanh_phan`
  ADD PRIMARY KEY (`MaThuoc`,`MaThanhPhan`),
  ADD KEY `MaThanhPhan` (`MaThanhPhan`);

--
-- Indexes for table `doi_tuong_su_dung`
--
ALTER TABLE `doi_tuong_su_dung`
  ADD PRIMARY KEY (`MaDoiTuong`);

--
-- Indexes for table `don_hang`
--
ALTER TABLE `don_hang`
  ADD PRIMARY KEY (`MaDonHang`),
  ADD KEY `idx_donhang_nguoidung` (`MaNguoiDung`);

--
-- Indexes for table `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  ADD PRIMARY KEY (`MaNguoiDung`),
  ADD UNIQUE KEY `SoDienThoai` (`SoDienThoai`),
  ADD UNIQUE KEY `idx_email_unique` (`Email`),
  ADD KEY `idx_nguoidung_email` (`Email`),
  ADD KEY `idx_nguoidung_googleid` (`GoogleId`);

--
-- Indexes for table `nhom_thuoc`
--
ALTER TABLE `nhom_thuoc`
  ADD PRIMARY KEY (`MaNhomThuoc`),
  ADD KEY `MaDanhMucCha` (`MaDanhMucCha`);

--
-- Indexes for table `nuoc_san_xuat`
--
ALTER TABLE `nuoc_san_xuat`
  ADD PRIMARY KEY (`MaNuocSX`);

--
-- Indexes for table `tac_dung_phu`
--
ALTER TABLE `tac_dung_phu`
  ADD PRIMARY KEY (`MaTacDungPhu`);

--
-- Indexes for table `thanh_phan`
--
ALTER TABLE `thanh_phan`
  ADD PRIMARY KEY (`MaThanhPhan`);

--
-- Indexes for table `thong_bao`
--
ALTER TABLE `thong_bao`
  ADD PRIMARY KEY (`MaThongBao`),
  ADD KEY `MaDonHang` (`MaDonHang`),
  ADD KEY `idx_thongbao_nguoidung` (`MaNguoiDung`);

--
-- Indexes for table `thuoc`
--
ALTER TABLE `thuoc`
  ADD PRIMARY KEY (`MaThuoc`),
  ADD KEY `MaNuocSX` (`MaNuocSX`),
  ADD KEY `MaThuongHieu` (`MaThuongHieu`),
  ADD KEY `idx_thuoc_nhom` (`MaNhomThuoc`),
  ADD KEY `idx_thuoc_active` (`IsActive`);

--
-- Indexes for table `thuong_hieu`
--
ALTER TABLE `thuong_hieu`
  ADD PRIMARY KEY (`MaThuongHieu`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `baiviet`
--
ALTER TABLE `baiviet`
  MODIFY `MaBaiViet` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `chi_tiet_don_hang`
--
ALTER TABLE `chi_tiet_don_hang`
  MODIFY `MaChiTiet` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `doi_tuong_su_dung`
--
ALTER TABLE `doi_tuong_su_dung`
  MODIFY `MaDoiTuong` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `don_hang`
--
ALTER TABLE `don_hang`
  MODIFY `MaDonHang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  MODIFY `MaNguoiDung` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `nhom_thuoc`
--
ALTER TABLE `nhom_thuoc`
  MODIFY `MaNhomThuoc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `nuoc_san_xuat`
--
ALTER TABLE `nuoc_san_xuat`
  MODIFY `MaNuocSX` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tac_dung_phu`
--
ALTER TABLE `tac_dung_phu`
  MODIFY `MaTacDungPhu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `thanh_phan`
--
ALTER TABLE `thanh_phan`
  MODIFY `MaThanhPhan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `thong_bao`
--
ALTER TABLE `thong_bao`
  MODIFY `MaThongBao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `thuoc`
--
ALTER TABLE `thuoc`
  MODIFY `MaThuoc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `thuong_hieu`
--
ALTER TABLE `thuong_hieu`
  MODIFY `MaThuongHieu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chi_tiet_don_hang`
--
ALTER TABLE `chi_tiet_don_hang`
  ADD CONSTRAINT `chi_tiet_don_hang_ibfk_1` FOREIGN KEY (`MaDonHang`) REFERENCES `don_hang` (`MaDonHang`) ON DELETE CASCADE,
  ADD CONSTRAINT `chi_tiet_don_hang_ibfk_2` FOREIGN KEY (`MaThuoc`) REFERENCES `thuoc` (`MaThuoc`);

--
-- Constraints for table `ct_doi_tuong`
--
ALTER TABLE `ct_doi_tuong`
  ADD CONSTRAINT `ct_doi_tuong_ibfk_1` FOREIGN KEY (`MaThuoc`) REFERENCES `thuoc` (`MaThuoc`) ON DELETE CASCADE,
  ADD CONSTRAINT `ct_doi_tuong_ibfk_2` FOREIGN KEY (`MaDoiTuong`) REFERENCES `doi_tuong_su_dung` (`MaDoiTuong`) ON DELETE CASCADE;

--
-- Constraints for table `ct_tac_dung_phu`
--
ALTER TABLE `ct_tac_dung_phu`
  ADD CONSTRAINT `ct_tac_dung_phu_ibfk_1` FOREIGN KEY (`MaThuoc`) REFERENCES `thuoc` (`MaThuoc`) ON DELETE CASCADE,
  ADD CONSTRAINT `ct_tac_dung_phu_ibfk_2` FOREIGN KEY (`MaTacDungPhu`) REFERENCES `tac_dung_phu` (`MaTacDungPhu`) ON DELETE CASCADE;

--
-- Constraints for table `ct_thanh_phan`
--
ALTER TABLE `ct_thanh_phan`
  ADD CONSTRAINT `ct_thanh_phan_ibfk_1` FOREIGN KEY (`MaThuoc`) REFERENCES `thuoc` (`MaThuoc`) ON DELETE CASCADE,
  ADD CONSTRAINT `ct_thanh_phan_ibfk_2` FOREIGN KEY (`MaThanhPhan`) REFERENCES `thanh_phan` (`MaThanhPhan`) ON DELETE CASCADE;

--
-- Constraints for table `don_hang`
--
ALTER TABLE `don_hang`
  ADD CONSTRAINT `don_hang_ibfk_1` FOREIGN KEY (`MaNguoiDung`) REFERENCES `nguoi_dung` (`MaNguoiDung`);

--
-- Constraints for table `nhom_thuoc`
--
ALTER TABLE `nhom_thuoc`
  ADD CONSTRAINT `nhom_thuoc_ibfk_1` FOREIGN KEY (`MaDanhMucCha`) REFERENCES `nhom_thuoc` (`MaNhomThuoc`) ON DELETE SET NULL;

--
-- Constraints for table `thong_bao`
--
ALTER TABLE `thong_bao`
  ADD CONSTRAINT `thong_bao_ibfk_1` FOREIGN KEY (`MaNguoiDung`) REFERENCES `nguoi_dung` (`MaNguoiDung`) ON DELETE CASCADE,
  ADD CONSTRAINT `thong_bao_ibfk_2` FOREIGN KEY (`MaDonHang`) REFERENCES `don_hang` (`MaDonHang`) ON DELETE SET NULL;

--
-- Constraints for table `thuoc`
--
ALTER TABLE `thuoc`
  ADD CONSTRAINT `thuoc_ibfk_1` FOREIGN KEY (`MaNhomThuoc`) REFERENCES `nhom_thuoc` (`MaNhomThuoc`),
  ADD CONSTRAINT `thuoc_ibfk_2` FOREIGN KEY (`MaNuocSX`) REFERENCES `nuoc_san_xuat` (`MaNuocSX`),
  ADD CONSTRAINT `thuoc_ibfk_3` FOREIGN KEY (`MaThuongHieu`) REFERENCES `thuong_hieu` (`MaThuongHieu`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
