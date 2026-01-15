<?php
/**
 * Quản lý địa chỉ
 */
?>
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/user/profile">Tài khoản</a></li>
            <li class="breadcrumb-item active">Quản lý địa chỉ</li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Sidebar -->
        <div class="col-lg-3">
            <?php include ROOT . '/app/views/user/_sidebar.php'; ?>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> Quản lý sổ địa chỉ</h5>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                        <i class="fas fa-plus"></i> Thêm địa chỉ mới
                    </button>
                </div>
                <div class="card-body">
                    <?php if (!empty($nguoiDung['DiaChi'])): ?>
                        <div class="address-card p-3 border rounded mb-3">
                            <div class="mb-2">
                                <span class="badge bg-primary">Mặc định</span>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-user text-primary me-2"></i>
                                <strong><?= htmlspecialchars($nguoiDung['HoTen'] ?? $nguoiDung['SoDienThoai']) ?></strong>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-phone text-primary me-2"></i>
                                <?= htmlspecialchars($nguoiDung['SoDienThoai']) ?>
                            </div>
                            <div class="mb-3">
                                <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                <?= htmlspecialchars($nguoiDung['DiaChi']) ?>
                            </div>
                            <a href="<?= BASE_URL ?>/user/profile" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-edit"></i> Sửa
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-map-marker-alt fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">Chưa có địa chỉ nào</h5>
                            <p class="text-muted">Thêm địa chỉ để thuận tiện cho việc giao hàng</p>
                            <a href="<?= BASE_URL ?>/user/profile" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Thêm địa chỉ
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
