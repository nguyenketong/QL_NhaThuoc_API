<?php
/**
 * Thông tin tài khoản
 */
?>
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Trang chủ</a></li>
            <li class="breadcrumb-item active">Tài khoản</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <div class="avatar mb-3">
                        <?php if (!empty($nguoiDung['Avatar'])): ?>
                            <img src="<?= htmlspecialchars($nguoiDung['Avatar']) ?>" alt="Avatar" class="rounded-circle" style="width:80px; height:80px; object-fit:cover;">
                        <?php else: ?>
                            <i class="fas fa-user-circle fa-5x text-primary"></i>
                        <?php endif; ?>
                    </div>
                    <h5><?= htmlspecialchars($nguoiDung['HoTen'] ?? 'Khách hàng') ?></h5>
                    <p class="text-muted">
                        <?php if (!empty($nguoiDung['SoDienThoai'])): ?>
                            <?= htmlspecialchars($nguoiDung['SoDienThoai']) ?>
                        <?php elseif (!empty($nguoiDung['Email'])): ?>
                            <?= htmlspecialchars($nguoiDung['Email']) ?>
                        <?php else: ?>
                            <span class="text-muted">Chưa cập nhật</span>
                        <?php endif; ?>
                    </p>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item active">
                        <i class="fas fa-user me-2"></i> Thông tin tài khoản
                    </li>
                    <li class="list-group-item">
                        <a href="<?= BASE_URL ?>/donHang/danhSach" class="text-decoration-none text-dark">
                            <i class="fas fa-box me-2"></i> Đơn hàng của tôi
                        </a>
                    </li>
                    <li class="list-group-item">
                        <a href="<?= BASE_URL ?>/user/logout" class="text-decoration-none text-danger">
                            <i class="fas fa-sign-out-alt me-2"></i> Đăng xuất
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <!-- Thống kê -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Tổng đơn hàng</h6>
                                    <h3 class="mb-0"><?= $tongDonHang ?></h3>
                                </div>
                                <i class="fas fa-shopping-bag fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Tổng chi tiêu</h6>
                                    <h3 class="mb-0"><?= number_format($tongChiTieu, 0, ',', '.') ?>đ</h3>
                                </div>
                                <i class="fas fa-wallet fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form cập nhật -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-edit"></i> Cập nhật thông tin</h5>
                </div>
                <div class="card-body">
                    <form action="<?= BASE_URL ?>/user/updateProfile" method="POST">
                        <?php if (!empty($nguoiDung['Email'])): ?>
                        <!-- Đăng nhập bằng Google -->
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="<?= htmlspecialchars($nguoiDung['Email']) ?>" disabled>
                            <small class="text-muted">Đăng nhập bằng Google</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="tel" name="soDienThoai" class="form-control" 
                                   value="<?= htmlspecialchars($nguoiDung['SoDienThoai'] ?? '') ?>" 
                                   placeholder="Nhập số điện thoại để nhận hàng..."
                                   pattern="[0-9]{10,11}" required>
                            <small class="text-muted">Số điện thoại dùng để liên hệ khi giao hàng</small>
                        </div>
                        <?php else: ?>
                        <!-- Đăng nhập bằng SĐT -->
                        <div class="mb-3">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($nguoiDung['SoDienThoai'] ?? '') ?>" disabled>
                            <small class="text-muted">Không thể thay đổi số điện thoại</small>
                        </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label class="form-label">Họ tên</label>
                            <input type="text" name="hoTen" class="form-control" value="<?= htmlspecialchars($nguoiDung['HoTen'] ?? '') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Địa chỉ</label>
                            <textarea name="diaChi" class="form-control" rows="3"><?= htmlspecialchars($nguoiDung['DiaChi'] ?? '') ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Lưu thay đổi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
