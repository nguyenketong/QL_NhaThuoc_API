# 📋 Hướng dẫn Test API - Nhà Thuốc Thanh Hoàn

## 🔧 Cài đặt Postman

1. Import Collection: `NhaThuoc_API.postman_collection.json`
2. Import Environment: 
   - Docker: `NhaThuoc_Docker.postman_environment.json`
   - XAMPP: `NhaThuoc_XAMPP.postman_environment.json`
3. Chọn Environment phù hợp

## 🔑 Tài khoản Test

| Vai trò | Số điện thoại | Mật khẩu |
|---------|---------------|----------|
| Admin | 0795930020 | admin123 |
| User | 0123456789 | 123456 |

---

## 🔐 AUTH - Xác thực

### Đăng nhập Admin
```bash
curl -X POST http://localhost:8080/api.php/auth/admin-login \
  -H "Content-Type: application/json" \
  -d '{"phone": "0795930020", "password": "admin123"}'
```

### Đăng nhập User
```bash
curl -X POST http://localhost:8080/api.php/auth/login \
  -H "Content-Type: application/json" \
  -d '{"phone": "0123456789", "password": "123456"}'
```

### Đăng ký User mới
```bash
curl -X POST http://localhost:8080/api.php/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name": "Nguyen Van Test", "phone": "0987654321", "password": "123456"}'
```

### Thông tin tài khoản
```bash
curl http://localhost:8080/api.php/auth/me
```

### Đăng xuất
```bash
curl -X POST http://localhost:8080/api.php/auth/logout
```

---

## 💊 THUỐC

### Danh sách (Public)
```bash
curl http://localhost:8080/api.php/thuoc
curl http://localhost:8080/api.php/thuoc?page=1&limit=5
curl http://localhost:8080/api.php/thuoc?search=para
curl http://localhost:8080/api.php/thuoc?nhom=1
```

### Chi tiết (Public)
```bash
curl http://localhost:8080/api.php/thuoc/1
```

### Tạo mới (Admin)
```bash
curl -X POST http://localhost:8080/api.php/thuoc \
  -H "Content-Type: application/json" \
  -d '{
    "TenThuoc": "Thuốc Test API",
    "MoTa": "Mô tả thuốc test",
    "DonViTinh": "Hộp",
    "GiaBan": 50000,
    "GiaGoc": 60000,
    "PhanTramGiam": 20,
    "NgayBatDauKM": "2024-01-01",
    "NgayKetThucKM": "2024-12-31",
    "SoLuongTon": 100,
    "MaNhomThuoc": 1,
    "IsNew": 1,
    "IsHot": 0
  }'
```

### Cập nhật (Admin)
```bash
curl -X PUT http://localhost:8080/api.php/thuoc/1 \
  -H "Content-Type: application/json" \
  -d '{
    "TenThuoc": "Thuốc Đã Cập Nhật",
    "GiaBan": 55000,
    "PhanTramGiam": 25,
    "NgayKetThucKM": "2024-06-30"
  }'
```

### Xóa khuyến mãi (Admin)
```bash
curl -X PUT http://localhost:8080/api.php/thuoc/1 \
  -H "Content-Type: application/json" \
  -d '{"PhanTramGiam": 0, "NgayBatDauKM": null, "NgayKetThucKM": null}'
```

### Xóa thuốc (Admin - soft delete)
```bash
curl -X DELETE http://localhost:8080/api.php/thuoc/99
```

---

## 🛒 ĐƠN HÀNG

### User - Xem đơn hàng của mình
```bash
curl http://localhost:8080/api.php/don-hang
curl http://localhost:8080/api.php/don-hang/1
```

### User - Đặt hàng
```bash
curl -X POST http://localhost:8080/api.php/don-hang \
  -H "Content-Type: application/json" \
  -d '{
    "DiaChiGiao": "123 Nguyen Van Linh, Q7, HCM",
    "GhiChu": "Giao buổi sáng",
    "items": [
      {"MaThuoc": 1, "SoLuong": 2},
      {"MaThuoc": 2, "SoLuong": 1}
    ]
  }'
```

### Admin - Xem tất cả đơn hàng
```bash
curl http://localhost:8080/api.php/don-hang
```

### Admin - Cập nhật trạng thái
```bash
curl -X PUT http://localhost:8080/api.php/don-hang/1 \
  -H "Content-Type: application/json" \
  -d '{"TrangThai": "Đang giao"}'
```

Các trạng thái: `Chờ xác nhận`, `Đang xử lý`, `Đang giao`, `Đã giao`, `Đã hủy`

---

## 👥 NGƯỜI DÙNG (Admin)

```bash
# Danh sách
curl http://localhost:8080/api.php/nguoi-dung

# Chi tiết
curl http://localhost:8080/api.php/nguoi-dung/1

# Cập nhật
curl -X PUT http://localhost:8080/api.php/nguoi-dung/2 \
  -H "Content-Type: application/json" \
  -d '{"HoTen": "Nguyen Van Updated", "Email": "test@email.com"}'

# Xóa (soft)
curl -X DELETE http://localhost:8080/api.php/nguoi-dung/99
```

---

## 📁 NHÓM THUỐC

```bash
# GET
curl http://localhost:8080/api.php/nhom-thuoc
curl http://localhost:8080/api.php/nhom-thuoc/1

# POST (Admin)
curl -X POST http://localhost:8080/api.php/nhom-thuoc \
  -H "Content-Type: application/json" \
  -d '{"TenNhomThuoc": "Nhóm Test", "MoTa": "Mô tả"}'

# PUT (Admin)
curl -X PUT http://localhost:8080/api.php/nhom-thuoc/1 \
  -H "Content-Type: application/json" \
  -d '{"TenNhomThuoc": "Nhóm Updated"}'

# DELETE (Admin)
curl -X DELETE http://localhost:8080/api.php/nhom-thuoc/99
```

---

## 🏷️ THƯƠNG HIỆU

```bash
# GET
curl http://localhost:8080/api.php/thuong-hieu
curl http://localhost:8080/api.php/thuong-hieu/1

# POST (Admin)
curl -X POST http://localhost:8080/api.php/thuong-hieu \
  -H "Content-Type: application/json" \
  -d '{"TenThuongHieu": "Brand Test", "QuocGia": "Vietnam"}'

# PUT (Admin)
curl -X PUT http://localhost:8080/api.php/thuong-hieu/1 \
  -H "Content-Type: application/json" \
  -d '{"TenThuongHieu": "Brand Updated"}'

# DELETE (Admin)
curl -X DELETE http://localhost:8080/api.php/thuong-hieu/99
```

---

## 🌍 NƯỚC SẢN XUẤT

```bash
curl http://localhost:8080/api.php/nuoc-san-xuat
curl -X POST http://localhost:8080/api.php/nuoc-san-xuat \
  -H "Content-Type: application/json" -d '{"TenNuocSX": "Japan"}'
curl -X PUT http://localhost:8080/api.php/nuoc-san-xuat/1 \
  -H "Content-Type: application/json" -d '{"TenNuocSX": "Korea"}'
curl -X DELETE http://localhost:8080/api.php/nuoc-san-xuat/99
```

---

## 🧪 THÀNH PHẦN

```bash
curl http://localhost:8080/api.php/thanh-phan
curl -X POST http://localhost:8080/api.php/thanh-phan \
  -H "Content-Type: application/json" \
  -d '{"TenThanhPhan": "Paracetamol", "MoTa": "Giảm đau, hạ sốt"}'
```

---

## ⚠️ TÁC DỤNG PHỤ

```bash
curl http://localhost:8080/api.php/tac-dung-phu
curl -X POST http://localhost:8080/api.php/tac-dung-phu \
  -H "Content-Type: application/json" \
  -d '{"TenTacDungPhu": "Buồn nôn", "MoTa": "Có thể gây buồn nôn"}'
```

---

## 👶 ĐỐI TƯỢNG SỬ DỤNG

```bash
curl http://localhost:8080/api.php/doi-tuong
curl -X POST http://localhost:8080/api.php/doi-tuong \
  -H "Content-Type: application/json" \
  -d '{"TenDoiTuong": "Trẻ em 6-12 tuổi", "MoTa": "Dành cho trẻ em"}'
```

---

## 📰 BÀI VIẾT

```bash
curl http://localhost:8080/api.php/bai-viet
curl http://localhost:8080/api.php/bai-viet/1
curl -X POST http://localhost:8080/api.php/bai-viet \
  -H "Content-Type: application/json" \
  -d '{"TieuDe": "Bài viết Test", "NoiDung": "<p>Nội dung</p>", "TacGia": "Admin"}'
```

---

## 🏠 TRANG CHỦ

```bash
curl http://localhost:8080/api.php/home
```

Trả về: `san_pham_moi`, `san_pham_khuyen_mai`, `nhom_thuoc`

---

## ⚠️ Lưu ý quan trọng

1. **Session/Cookie**: API sử dụng session để xác thực. Trong Postman, bật "Cookies" để lưu session.

2. **Thứ tự test**:
   - Đăng nhập Admin trước khi test các API cần quyền Admin
   - Đăng nhập User trước khi test đơn hàng của User

3. **Khuyến mãi**: Sản phẩm chỉ hiển thị khuyến mãi khi:
   - `PhanTramGiam > 0`
   - `NgayBatDauKM <= NOW()` (hoặc NULL)
   - `NgayKetThucKM >= NOW()` (hoặc NULL)

4. **Soft Delete**: Thuốc và Người dùng sử dụng soft delete (IsActive = 0)
