-- Tạo user test Admin
INSERT INTO nguoi_dung (HoTen, SoDienThoai, MatKhau, VaiTro, NgayTao) 
VALUES ('Admin Test', '0795930020', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', NOW())
ON DUPLICATE KEY UPDATE HoTen = VALUES(HoTen);

-- Password: password
