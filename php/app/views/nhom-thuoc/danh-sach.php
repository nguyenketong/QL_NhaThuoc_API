<?php
/**
 * Danh sách nhóm thuốc
 */
?>
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Trang chủ</a></li>
            <li class="breadcrumb-item active">Danh mục thuốc</li>
        </ol>
    </nav>

    <div class="section-header text-center mb-4">
        <h1 class="text-primary fw-bold"><i class="fas fa-th-large"></i> DANH MỤC SẢN PHẨM</h1>
        <p class="text-muted">Khám phá các danh mục thuốc và thực phẩm chức năng</p>
    </div>

    <div class="row g-4">
        <?php if (!empty($nhomThuocs)): ?>
            <?php foreach ($nhomThuocs as $nhom): ?>
                <div class="col-md-4 col-lg-3">
                    <a href="<?= BASE_URL ?>/nhomThuoc/chiTiet/<?= $nhom['MaNhomThuoc'] ?>" class="card h-100 text-decoration-none category-card">
                        <div class="card-body text-center">
                            <?php if (!empty($nhom['HinhAnh'])): ?>
                                <img src="<?= $nhom['HinhAnh'] ?>" alt="<?= htmlspecialchars($nhom['TenNhomThuoc']) ?>" 
                                     style="max-height: 80px; max-width: 100%; object-fit: contain;" class="mb-3">
                            <?php else: ?>
                                <div class="mb-3">
                                    <i class="fas fa-pills fa-3x text-primary"></i>
                                </div>
                            <?php endif; ?>
                            <h5 class="card-title"><?= htmlspecialchars($nhom['TenNhomThuoc']) ?></h5>
                            <?php if (!empty($nhom['MoTa'])): ?>
                                <p class="card-text text-muted small"><?= htmlspecialchars(mb_substr($nhom['MoTa'], 0, 50)) ?>...</p>
                            <?php endif; ?>
                            <span class="badge bg-primary"><?= $nhom['SoLuongSanPham'] ?? 0 ?> sản phẩm</span>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle"></i> Chưa có danh mục nào
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.category-card {
    transition: all 0.3s ease;
    border: 1px solid #eee;
}
.category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    border-color: #0d6efd;
}
</style>
