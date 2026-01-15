<?php
/**
 * User Sidebar Component
 */
$activeMenu = $activeMenu ?? '';
?>
<div class="user-sidebar card">
    <!-- User Info -->
    <div class="card-header bg-primary text-white text-center py-4">
        <div class="mb-3">
            <i class="fas fa-user-circle fa-4x"></i>
        </div>
        <h5 class="mb-1"><?= htmlspecialchars($nguoiDung['HoTen'] ?? $nguoiDung['SoDienThoai'] ?? 'Khách hàng') ?></h5>
        <small><?= htmlspecialchars($nguoiDung['SoDienThoai'] ?? '') ?></small>
    </div>

    <!-- Menu -->
    <ul class="list-group list-group-flush">
        <li class="list-group-item">
            <a href="<?= BASE_URL ?>/user/profile" class="text-decoration-none d-flex align-items-center <?= $activeMenu === 'profile' ? 'text-primary fw-bold' : 'text-dark' ?>">
                <i class="fas fa-user me-3" style="width: 20px;"></i>
                <span class="flex-grow-1">Thông tin cá nhân</span>
                <i class="fas fa-chevron-right text-muted"></i>
            </a>
        </li>
        <li class="list-group-item">
            <a href="<?= BASE_URL ?>/donHang/danhSach" class="text-decoration-none d-flex align-items-center <?= $activeMenu === 'donhang' ? 'text-primary fw-bold' : 'text-dark' ?>">
                <i class="fas fa-shopping-bag me-3" style="width: 20px;"></i>
                <span class="flex-grow-1">Đơn hàng của tôi</span>
                <i class="fas fa-chevron-right text-muted"></i>
            </a>
        </li>
        <li class="list-group-item">
            <a href="<?= BASE_URL ?>/user/diaChi" class="text-decoration-none d-flex align-items-center <?= $activeMenu === 'diachi' ? 'text-primary fw-bold' : 'text-dark' ?>">
                <i class="fas fa-map-marker-alt me-3" style="width: 20px;"></i>
                <span class="flex-grow-1">Quản lý địa chỉ</span>
                <i class="fas fa-chevron-right text-muted"></i>
            </a>
        </li>
        <li class="list-group-item">
            <a href="<?= BASE_URL ?>/user/logout" class="text-decoration-none d-flex align-items-center text-danger">
                <i class="fas fa-sign-out-alt me-3" style="width: 20px;"></i>
                <span class="flex-grow-1">Đăng xuất</span>
                <i class="fas fa-chevron-right text-muted"></i>
            </a>
        </li>
    </ul>
</div>
