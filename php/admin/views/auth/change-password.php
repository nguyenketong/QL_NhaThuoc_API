<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đổi mật khẩu - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1a1f2e 0%, #2d3748 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .change-password-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
        }
        .change-password-header {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #fff;
            padding: 30px;
            text-align: center;
        }
        .change-password-header i {
            font-size: 50px;
            margin-bottom: 15px;
        }
        .change-password-body {
            padding: 30px;
        }
        .form-control {
            border-radius: 8px;
            padding: 12px 15px;
            border: 1px solid #e2e8f0;
        }
        .form-control:focus {
            border-color: #f59e0b;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
        }
        .btn-change {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 600;
            width: 100%;
        }
        .btn-change:hover {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
        }
        .input-group-text {
            background: #f8fafc;
            border-right: none;
        }
        .form-control.with-icon {
            border-left: none;
        }
        .alert-warning-custom {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            color: #92400e;
            border-radius: 8px;
        }
        .password-requirements {
            font-size: 12px;
            color: #64748b;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="change-password-card">
        <div class="change-password-header">
            <i class="fas fa-key"></i>
            <h4 class="mb-0">Đổi mật khẩu</h4>
        </div>
        <div class="change-password-body">
            <?php if ($requireChange): ?>
            <div class="alert alert-warning-custom mb-4">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Bảo mật!</strong> Bạn đang sử dụng mật khẩu mặc định. Vui lòng đổi mật khẩu để bảo vệ tài khoản.
            </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-times-circle"></i> <?= $error ?>
            </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Mật khẩu hiện tại</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="matKhauCu" class="form-control with-icon" placeholder="Nhập mật khẩu hiện tại" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mật khẩu mới</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                        <input type="password" name="matKhauMoi" class="form-control with-icon" placeholder="Nhập mật khẩu mới" required minlength="6">
                    </div>
                    <div class="password-requirements">
                        <i class="fas fa-info-circle"></i> Mật khẩu phải có ít nhất 6 ký tự
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Xác nhận mật khẩu mới</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-check-double"></i></span>
                        <input type="password" name="xacNhanMatKhau" class="form-control with-icon" placeholder="Nhập lại mật khẩu mới" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-warning btn-change text-white">
                    <i class="fas fa-save"></i> Đổi mật khẩu
                </button>

                <?php if (!$requireChange): ?>
                <a href="<?= BASE_URL ?>/admin/" class="btn btn-outline-secondary w-100 mt-3">
                    <i class="fas fa-arrow-left"></i> Quay lại Dashboard
                </a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
