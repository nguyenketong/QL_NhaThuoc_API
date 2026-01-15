<?php
/**
 * Danh sách thương hiệu
 */
?>
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Trang chủ</a></li>
            <li class="breadcrumb-item active">Thương hiệu</li>
        </ol>
    </nav>

    <div class="section-header text-center mb-4">
        <h1 class="text-primary fw-bold">THƯƠNG HIỆU ĐỐI TÁC</h1>
        <p class="text-muted">Các thương hiệu uy tín hàng đầu mà chúng tôi hợp tác</p>
    </div>

    <div class="row g-4">
        <?php if (!empty($danhSach)): ?>
            <?php foreach ($danhSach as $th): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="<?= BASE_URL ?>/thuongHieu/chiTiet/<?= $th['MaThuongHieu'] ?>" class="card h-100 text-decoration-none brand-card">
                        <div class="card-body text-center">
                            <?php if (!empty($th['HinhAnh'])): ?>
                                <img src="<?= $th['HinhAnh'] ?>" alt="<?= htmlspecialchars($th['TenThuongHieu']) ?>" 
                                     style="max-height: 80px; max-width: 100%; object-fit: contain;" class="mb-3">
                            <?php else: ?>
                                <div class="mb-3">
                                    <i class="fas fa-building fa-3x text-primary"></i>
                                </div>
                            <?php endif; ?>
                            <h5 class="card-title"><?= htmlspecialchars($th['TenThuongHieu']) ?></h5>
                            <?php if (!empty($th['QuocGia'])): ?>
                                <span class="text-muted small"><i class="fas fa-globe me-1"></i><?= htmlspecialchars($th['QuocGia']) ?></span>
                            <?php endif; ?>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="fas fa-building fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">Chưa có thương hiệu nào</h4>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.brand-card {
    transition: all 0.3s ease;
}
.brand-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}
</style>
