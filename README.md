# Nhà Thuốc Thanh Hoàn

Website quản lý nhà thuốc với MVC, RESTful API, PHP, MySQL, Bootstrap.

## 🛠️ Công nghệ sử dụng

- **Backend**: PHP 8.x, MVC Pattern
- **Database**: MySQL
- **Frontend**: HTML, CSS, JavaScript, Bootstrap 5
- **API**: RESTful API với JWT Authentication
- **Deploy**: XAMPP / Docker

---

## 🚀 Cách chạy

### Option 1: Docker (Recommended) ⭐

```bash
# Quick Start - Xem file QUICK_START.md
start-docker.bat

# Hoặc dùng lệnh
docker-compose up -d --build

# Truy cập
Website:    http://localhost:8080/
Login:      http://localhost:8080/user/phoneLogin
Admin:      http://localhost:8080/admin/
API:        http://localhost:8080/api.php
phpMyAdmin: http://localhost:8081/
```

**🔐 Google Login**: Xem hướng dẫn chi tiết trong `GOOGLE_LOGIN_SETUP.md`

### Option 2: XAMPP (Development)

```bash
# 1. Copy thư mục vào htdocs
C:\xampp\htdocs\Ql_NhaThuoc\

# 2. Import database
# Mở phpMyAdmin -> Import -> database/ql_nhathuoc_api.sql
# Sau đó chạy: database/create_test_user.sql

# 3. Cấu hình Google Login (optional)
# Xem file GOOGLE_LOGIN_SETUP.md

# 4. Truy cập
Website: http://localhost/Ql_NhaThuoc/php/
Admin:   http://localhost/Ql_NhaThuoc/php/admin/
API:     http://localhost/Ql_NhaThuoc/php/api.php
```

---

## 🔄 API tích hợp trong MVC

Dự án hỗ trợ **2 cách** gọi API:

### Cách 1: API riêng biệt (folder `/api/`)
```
GET http://localhost/Ql_NhaThuoc/php/api/thuoc
```

### Cách 2: API tích hợp trong MVC (cùng URL với website)
Thêm header `Accept: application/json` hoặc query `?format=json`:

```bash
# Website trả về HTML
GET http://localhost/Ql_NhaThuoc/php/thuoc

# API trả về JSON
GET http://localhost/Ql_NhaThuoc/php/thuoc?format=json
# hoặc
GET http://localhost/Ql_NhaThuoc/php/thuoc
Accept: application/json
```

### RESTful trong MVC
Các controller hỗ trợ cả HTML và JSON response:

| HTTP Method | URL | HTML | JSON (Accept: application/json) |
|-------------|-----|------|--------------------------------|
| GET | `/thuoc` | Trang danh sách | `{"data": [...]}` |
| GET | `/thuoc/1` | Trang chi tiết | `{"data": {...}}` |
| POST | `/thuoc` | - | Tạo mới (Admin) |
| PUT | `/thuoc/1` | - | Cập nhật (Admin) |
| DELETE | `/thuoc/1` | - | Xóa (Admin) |

---

## 📋 API Endpoints (58 URLs)

### Authentication
| Method | Endpoint | Quyền | Mô tả |
|--------|----------|-------|-------|
| POST | `/api/auth/login` | Public | Đăng nhập |
| POST | `/api/auth/register` | Public | Đăng ký |
| POST | `/api/auth/refresh` | User | Refresh token |

### Thuốc
| Method | Endpoint | Quyền | Mô tả |
|--------|----------|-------|-------|
| GET | `/api/thuoc` | Public | Danh sách thuốc |
| GET | `/api/thuoc/{id}` | Public | Chi tiết thuốc |
| POST | `/api/thuoc` | Admin | Thêm thuốc |
| PUT | `/api/thuoc/{id}` | Admin | Sửa thuốc |
| DELETE | `/api/thuoc/{id}` | Admin | Xóa thuốc |

### Đơn Hàng
| Method | Endpoint | Quyền | Mô tả |
|--------|----------|-------|-------|
| GET | `/api/don-hang` | User/Admin | Danh sách (User: của mình, Admin: tất cả) |
| GET | `/api/don-hang/{id}` | Owner/Admin | Chi tiết đơn |
| POST | `/api/don-hang` | User | Tạo đơn hàng |
| PUT | `/api/don-hang/{id}` | Owner/Admin | Cập nhật (User: hủy, Admin: đổi trạng thái) |
| DELETE | `/api/don-hang/{id}` | Admin | Xóa đơn |

### Người Dùng
| Method | Endpoint | Quyền | Mô tả |
|--------|----------|-------|-------|
| GET | `/api/nguoi-dung` | Admin | Danh sách users |
| GET | `/api/nguoi-dung/{id}` | Owner/Admin | Chi tiết user |
| POST | `/api/nguoi-dung` | Admin | Tạo user |
| PUT | `/api/nguoi-dung/{id}` | Owner/Admin | Sửa user |
| DELETE | `/api/nguoi-dung/{id}` | Admin | Xóa user |

### Các API khác (CRUD - Admin only cho CUD)
- `/api/nhom-thuoc` - Nhóm thuốc
- `/api/thuong-hieu` - Thương hiệu
- `/api/nuoc-san-xuat` - Nước sản xuất
- `/api/bai-viet` - Bài viết
- `/api/thanh-phan` - Thành phần
- `/api/tac-dung-phu` - Tác dụng phụ
- `/api/doi-tuong` - Đối tượng sử dụng
- `/api/thong-bao` - Thông báo

---

## 🔐 Authentication

Hệ thống hỗ trợ **3 phương thức đăng nhập**:

### 1. 🌐 Google OAuth (Recommended)
- Đăng nhập nhanh bằng tài khoản Google
- Tự động tạo tài khoản lần đầu
- Xem hướng dẫn: `GOOGLE_LOGIN_SETUP.md`

### 2. 📱 OTP qua SMS
- Gửi mã OTP qua số điện thoại
- Tích hợp eSMS API

### 3. 🔑 Số điện thoại + Mật khẩu
- Đăng nhập truyền thống

### API Authentication

#### Login để lấy token
```bash
POST /api/auth/login
Content-Type: application/json

{
  "phone": "0123456789",
  "password": "123456"
}
```

### Sử dụng token
```bash
Authorization: Bearer <your_token>
```

### Test Account
| Role | Phone | Password |
|------|-------|----------|
| Admin | 0123456789 | 123456 |

---

## 📦 Response Format

### Success
```json
{
  "success": true,
  "message": "Success",
  "data": {...},
  "timestamp": "2026-01-13T10:00:00+07:00"
}
```

### Error
```json
{
  "success": false,
  "message": "Error message",
  "error_code": 404,
  "timestamp": "2026-01-13T10:00:00+07:00"
}
```

### Pagination
```json
{
  "success": true,
  "data": [...],
  "pagination": {
    "total": 100,
    "per_page": 10,
    "current_page": 1,
    "total_pages": 10
  }
}
```

---

## ⚠️ HTTP Status Codes

| Code | Mô tả |
|------|-------|
| 200 | Success |
| 201 | Created |
| 400 | Bad Request |
| 401 | Unauthorized (chưa login) |
| 403 | Forbidden (không có quyền) |
| 404 | Not Found |
| 409 | Conflict |
| 422 | Validation Error |
| 500 | Server Error |

---

## 🧪 Test với Postman

1. Import file `postman/NhaThuoc_API.postman_collection.json`
2. Chọn Environment:
   - XAMPP: `base_url = http://localhost/Ql_NhaThuoc/php/api`
   - Docker: `base_url = http://localhost:8080/api`
3. Chạy request "Login" để lấy token
4. Token tự động lưu vào biến `{{token}}`

---

## 📁 Cấu trúc thư mục

```
Ql_NhaThuoc/
├── php/
│   ├── api/                    # RESTful API
│   │   ├── index.php           # API Entry point
│   │   ├── core/               # ApiController, Router, JWT
│   │   └── controllers/        # API Controllers
│   │
│   ├── app/                    # Website MVC
│   │   ├── controllers/
│   │   ├── models/
│   │   └── views/
│   │
│   ├── admin/                  # Admin Panel
│   ├── assets/                 # CSS, JS, Images
│   ├── config/                 # Config files
│   ├── core/                   # MVC Core
│   └── index.php               # Website Entry point
│
├── database/
│   ├── ql_nhathuoc.sql         # Database schema
│   └── create_test_user.sql    # Test user
│
├── postman/                    # Postman collection
├── docker-compose.yml
├── Dockerfile
└── README.md
```
