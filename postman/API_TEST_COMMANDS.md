# API Test Commands - Nhà Thuốc Tây Thanh Hoàn

## Base URL
- **XAMPP**: `http://localhost/Ql_NhaThuoc/php/api.php`
- **Docker**: `http://localhost:8080/api.php`

---

## 1. AUTHENTICATION (Xác thực)

### POST - Đăng nhập User
```
POST {{base_url}}/auth/login
Content-Type: application/json

{
    "phone": "0939857557",
    "password": "123456"
}
```

### POST - Đăng ký User
```
POST {{base_url}}/auth/register
Content-Type: application/json

{
    "name": "Nguyễn Văn Test",
    "phone": "0912345678",
    "password": "123456"
}
```

### POST - Đăng nhập Admin
```
POST {{base_url}}/auth/admin-login
Content-Type: application/json

{
    "phone": "0795930020",
    "password": "admin123"
}
```

### POST - Gửi OTP
```
POST {{base_url}}/auth/send-otp
Content-Type: application/json

{
    "phone": "0939857557"
}
```

### POST - Xác nhận OTP
```
POST {{base_url}}/auth/verify-otp
Content-Type: application/json

{
    "phone": "0939857557",
    "otp": "123456"
}
```

### POST - Đăng xuất
```
POST {{base_url}}/auth/logout
```

### GET - Thông tin User hiện tại
```
GET {{base_url}}/auth/me
```

---

## 2. THUỐC (Products)

### GET - Danh sách thuốc
```
GET {{base_url}}/thuoc
GET {{base_url}}/thuoc?page=1&limit=10
GET {{base_url}}/thuoc?search=paracetamol
GET {{base_url}}/thuoc?nhom=1
```

### GET - Chi tiết thuốc
```
GET {{base_url}}/thuoc/10
```

### POST - Tạo thuốc mới (Admin)
```
POST {{base_url}}/thuoc
Content-Type: application/json

{
    "TenThuoc": "Thuốc Test API",
    "MoTa": "Mô tả thuốc test",
    "GiaBan": 50000,
    "GiaGoc": 60000,
    "PhanTramGiam": 15,
    "DonViTinh": "Hộp",
    "MaNhomThuoc": 1,
    "MaThuongHieu": 1,
    "MaNuocSX": 1,
    "SoLuongTon": 100,
    "IsHot": 1,
    "IsNew": 1
}
```

### PUT - Cập nhật thuốc (Admin)
```
PUT {{base_url}}/thuoc/10
Content-Type: application/json

{
    "TenThuoc": "Smecta - Đã cập nhật",
    "GiaBan": 70000,
    "PhanTramGiam": 10
}
```

### DELETE - Xóa thuốc (Admin)
```
DELETE {{base_url}}/thuoc/10
```

---

## 3. NHÓM THUỐC (Categories)

### GET - Danh sách nhóm thuốc
```
GET {{base_url}}/nhom-thuoc
```

### GET - Chi tiết nhóm thuốc
```
GET {{base_url}}/nhom-thuoc/1
```

### POST - Tạo nhóm thuốc
```
POST {{base_url}}/nhom-thuoc
Content-Type: application/json

{
    "TenNhomThuoc": "Nhóm thuốc test",
    "MoTa": "Mô tả nhóm thuốc test",
    "MaDanhMucCha": null
}
```

### PUT - Cập nhật nhóm thuốc
```
PUT {{base_url}}/nhom-thuoc/1
Content-Type: application/json

{
    "TenNhomThuoc": "Thuốc giảm đau - Updated",
    "MoTa": "Mô tả đã cập nhật"
}
```

### DELETE - Xóa nhóm thuốc
```
DELETE {{base_url}}/nhom-thuoc/10
```

---

## 4. THƯƠNG HIỆU (Brands)

### GET - Danh sách thương hiệu
```
GET {{base_url}}/thuong-hieu
```

### GET - Chi tiết thương hiệu
```
GET {{base_url}}/thuong-hieu/1
```

### POST - Tạo thương hiệu
```
POST {{base_url}}/thuong-hieu
Content-Type: application/json

{
    "TenThuongHieu": "Thương hiệu Test",
    "QuocGia": "Việt Nam",
    "DiaChi": "Hà Nội"
}
```

### PUT - Cập nhật thương hiệu
```
PUT {{base_url}}/thuong-hieu/1
Content-Type: application/json

{
    "TenThuongHieu": "Sanofi - Updated"
}
```

### DELETE - Xóa thương hiệu
```
DELETE {{base_url}}/thuong-hieu/8
```

---

## 5. NƯỚC SẢN XUẤT

### GET - Danh sách nước sản xuất
```
GET {{base_url}}/nuoc-san-xuat
```

### GET - Chi tiết
```
GET {{base_url}}/nuoc-san-xuat/1
```

### POST - Tạo mới
```
POST {{base_url}}/nuoc-san-xuat
Content-Type: application/json

{
    "TenNuocSX": "Nhật Bản"
}
```

### PUT - Cập nhật
```
PUT {{base_url}}/nuoc-san-xuat/1
Content-Type: application/json

{
    "TenNuocSX": "Việt Nam - Updated"
}
```

### DELETE - Xóa
```
DELETE {{base_url}}/nuoc-san-xuat/9
```

---

## 6. THÀNH PHẦN

### GET - Danh sách thành phần
```
GET {{base_url}}/thanh-phan
```

### GET - Chi tiết
```
GET {{base_url}}/thanh-phan/1
```

### POST - Tạo mới (Admin)
```
POST {{base_url}}/thanh-phan
Content-Type: application/json

{
    "TenThanhPhan": "Vitamin D",
    "MoTa": "Vitamin D3"
}
```

### PUT - Cập nhật (Admin)
```
PUT {{base_url}}/thanh-phan/1
Content-Type: application/json

{
    "TenThanhPhan": "Paracetamol - Updated"
}
```

### DELETE - Xóa (Admin)
```
DELETE {{base_url}}/thanh-phan/5
```

---

## 7. TÁC DỤNG PHỤ

### GET - Danh sách tác dụng phụ
```
GET {{base_url}}/tac-dung-phu
```

### GET - Chi tiết
```
GET {{base_url}}/tac-dung-phu/1
```

### POST - Tạo mới (Admin)
```
POST {{base_url}}/tac-dung-phu
Content-Type: application/json

{
    "TenTacDungPhu": "Chóng mặt",
    "MoTa": "Cảm giác quay cuồng"
}
```

### PUT - Cập nhật (Admin)
```
PUT {{base_url}}/tac-dung-phu/1
Content-Type: application/json

{
    "TenTacDungPhu": "Buồn nôn - Updated"
}
```

### DELETE - Xóa (Admin)
```
DELETE {{base_url}}/tac-dung-phu/5
```

---

## 8. ĐỐI TƯỢNG SỬ DỤNG

### GET - Danh sách đối tượng
```
GET {{base_url}}/doi-tuong
```

### GET - Chi tiết
```
GET {{base_url}}/doi-tuong/1
```

### POST - Tạo mới (Admin)
```
POST {{base_url}}/doi-tuong
Content-Type: application/json

{
    "TenDoiTuong": "Người cao tuổi",
    "MoTa": "Trên 60 tuổi"
}
```

### PUT - Cập nhật (Admin)
```
PUT {{base_url}}/doi-tuong/1
Content-Type: application/json

{
    "TenDoiTuong": "Người lớn - Updated"
}
```

### DELETE - Xóa (Admin)
```
DELETE {{base_url}}/doi-tuong/6
```

---

## 9. ĐƠN HÀNG (Cần đăng nhập)

### GET - Danh sách đơn hàng
```
GET {{base_url}}/don-hang
GET {{base_url}}/don-hang?page=1&limit=10
```

### GET - Chi tiết đơn hàng
```
GET {{base_url}}/don-hang/1
```

### POST - Tạo đơn hàng
```
POST {{base_url}}/don-hang
Content-Type: application/json

{
    "DiaChiGiao": "123 Đường ABC, Quận 1, TP.HCM",
    "GhiChu": "Giao giờ hành chính",
    "items": [
        {"MaThuoc": 10, "SoLuong": 2},
        {"MaThuoc": 7, "SoLuong": 1}
    ]
}
```

### PUT - Cập nhật trạng thái (Admin)
```
PUT {{base_url}}/don-hang/1
Content-Type: application/json

{
    "TrangThai": "Đang giao"
}
```

---

## 10. NGƯỜI DÙNG (Admin only)

### GET - Danh sách người dùng
```
GET {{base_url}}/nguoi-dung
GET {{base_url}}/users
```

### GET - Chi tiết người dùng
```
GET {{base_url}}/nguoi-dung/1
```

### PUT - Cập nhật người dùng
```
PUT {{base_url}}/nguoi-dung/1
Content-Type: application/json

{
    "HoTen": "Admin Updated",
    "VaiTro": "Admin"
}
```

### DELETE - Xóa người dùng
```
DELETE {{base_url}}/nguoi-dung/5
```

---

## 11. BÀI VIẾT

### GET - Danh sách bài viết
```
GET {{base_url}}/bai-viet
GET {{base_url}}/bai-viet?page=1&limit=10
```

### GET - Chi tiết bài viết
```
GET {{base_url}}/bai-viet/1
```

### POST - Tạo bài viết (Admin)
```
POST {{base_url}}/bai-viet
Content-Type: application/json

{
    "TieuDe": "Bài viết test API",
    "NoiDung": "Nội dung chi tiết bài viết...",
    "TacGia": "Admin"
}
```

### PUT - Cập nhật bài viết (Admin)
```
PUT {{base_url}}/bai-viet/1
Content-Type: application/json

{
    "TieuDe": "Bài viết đã cập nhật"
}
```

### DELETE - Xóa bài viết (Admin)
```
DELETE {{base_url}}/bai-viet/3
```

---

## 12. HOME - Trang chủ API

### GET - Dữ liệu trang chủ
```
GET {{base_url}}/home
GET {{base_url}}/
```

---

## CURL Commands (Windows CMD)

### GET
```cmd
curl -X GET "http://localhost:8080/api.php/thuoc"
curl -X GET "http://localhost:8080/api.php/thuoc/10"
curl -X GET "http://localhost:8080/api.php/nhom-thuoc"
```

### POST - Đăng nhập Admin
```cmd
curl -X POST "http://localhost:8080/api.php/auth/admin-login" -H "Content-Type: application/json" -d "{\"phone\":\"0795930020\",\"password\":\"admin123\"}"
```

### POST - Tạo thuốc
```cmd
curl -X POST "http://localhost:8080/api.php/thuoc" -H "Content-Type: application/json" -d "{\"TenThuoc\":\"Test\",\"GiaBan\":50000,\"MaNhomThuoc\":1}"
```

### PUT - Cập nhật
```cmd
curl -X PUT "http://localhost:8080/api.php/thuoc/10" -H "Content-Type: application/json" -d "{\"TenThuoc\":\"Updated\"}"
```

### DELETE - Xóa
```cmd
curl -X DELETE "http://localhost:8080/api.php/thuoc/10"
```

---

## Postman Environment Variables

```json
{
    "base_url": "http://localhost:8080/api.php"
}
```

## Notes
- API trả về JSON với format: `{"success": true/false, "message": "...", "data": {...}}`
- Các endpoint POST/PUT/DELETE cần đăng nhập Admin trước
- Đơn hàng cần đăng nhập User
- Pagination: `?page=1&limit=10`
