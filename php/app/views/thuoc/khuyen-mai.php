<?php
/**
 * Sản phẩm khuyến mãi
 */
?>
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Trang chủ</a></li>
            <li class="breadcrumb-item active">Khuyến mãi</li>
        </ol>
    </nav>

    <div class="section-header text-center mb-4">
        <h2 class="section-title-main text-danger fw-bold">
            <i class="fas fa-tags"></i> SẢN PHẨM KHUYẾN MÃI
        </h2>
        <p class="text-muted">Săn deal hot - Giá siêu hời</p>
    </div>

    <div class="row g-3">
        <?php if (!empty($danhSachThuoc)): ?>
            <?php foreach ($danhSachThuoc as $thuoc): ?>
                <?php include ROOT . '/app/views/components/product-card.php'; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle"></i> Hiện chưa có sản phẩm khuyến mãi
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
