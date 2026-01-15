<?php
/**
 * Chi tiết thương hiệu - Sản phẩm theo thương hiệu
 */
?>
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/thuongHieu/danhSach">Thương hiệu</a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($thuongHieu['TenThuongHieu']) ?></li>
        </ol>
    </nav>

    <!-- Brand Header -->
    <div class="brand-header mb-4 p-4 bg-light rounded">
        <div class="row align-items-center">
            <div class="col-auto">
                <?php if (!empty($thuongHieu['HinhAnh'])): ?>
                    <img src="<?= $thuongHieu['HinhAnh'] ?>" alt="<?= htmlspecialchars($thuongHieu['TenThuongHieu']) ?>" 
                         style="max-width: 150px; max-height: 80px; object-fit: contain;">
                <?php else: ?>
                    <div class="bg-white p-3 rounded">
                        <i class="fas fa-building fa-3x text-primary"></i>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col">
                <h1 class="h3 mb-2"><?= htmlspecialchars($thuongHieu['TenThuongHieu']) ?></h1>
                <?php if (!empty($thuongHieu['QuocGia'])): ?>
                    <p class="text-muted mb-1"><i class="fas fa-globe me-2"></i><?= htmlspecialchars($thuongHieu['QuocGia']) ?></p>
                <?php endif; ?>
                <?php if (!empty($thuongHieu['DiaChi'])): ?>
                    <p class="text-muted mb-0"><i class="fas fa-map-marker-alt me-2"></i><?= htmlspecialchars($thuongHieu['DiaChi']) ?></p>
                <?php endif; ?>
            </div>
            <div class="col-auto">
                <span class="badge bg-primary fs-6 px-3 py-2">
                    <i class="fas fa-box me-1"></i> <?= $totalItems ?> sản phẩm
                </span>
            </div>
        </div>
    </div>

    <?php if (!empty($thuocs)): ?>
        <!-- Filter Bar -->
        <div class="filter-bar bg-light rounded p-3 mb-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <span class="text-muted">
                        Hiển thị <?= ($currentPage - 1) * 12 + 1 ?> - <?= min($currentPage * 12, $totalItems) ?> trong <?= $totalItems ?> sản phẩm
                    </span>
                </div>
                <div class="col-md-6 text-md-end mt-2 mt-md-0">
                    <label class="me-2 text-muted">Sắp xếp:</label>
                    <select class="form-select form-select-sm d-inline-block" style="width: auto;" onchange="sortProducts(this.value)">
                        <option value="" <?= empty($sapXep) ? 'selected' : '' ?>>Mặc định</option>
                        <option value="moi-nhat" <?= $sapXep === 'moi-nhat' ? 'selected' : '' ?>>Mới nhất</option>
                        <option value="gia-tang" <?= $sapXep === 'gia-tang' ? 'selected' : '' ?>>Giá tăng dần</option>
                        <option value="gia-giam" <?= $sapXep === 'gia-giam' ? 'selected' : '' ?>>Giá giảm dần</option>
                        <option value="ten-az" <?= $sapXep === 'ten-az' ? 'selected' : '' ?>>Tên A-Z</option>
                        <option value="ten-za" <?= $sapXep === 'ten-za' ? 'selected' : '' ?>>Tên Z-A</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="row g-3">
            <?php foreach ($thuocs as $thuoc): ?>
                <?php include ROOT . '/app/views/components/product-card.php'; ?>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?= $currentPage == 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= BASE_URL ?>/thuongHieu/chiTiet/<?= $thuongHieu['MaThuongHieu'] ?>?page=<?= $currentPage - 1 ?>&sapXep=<?= $sapXep ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php if ($i == 1 || $i == $totalPages || ($i >= $currentPage - 2 && $i <= $currentPage + 2)): ?>
                            <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                <a class="page-link" href="<?= BASE_URL ?>/thuongHieu/chiTiet/<?= $thuongHieu['MaThuongHieu'] ?>?page=<?= $i ?>&sapXep=<?= $sapXep ?>"><?= $i ?></a>
                            </li>
                        <?php elseif ($i == $currentPage - 3 || $i == $currentPage + 3): ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                    <?php endfor; ?>
                    <li class="page-item <?= $currentPage == $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= BASE_URL ?>/thuongHieu/chiTiet/<?= $thuongHieu['MaThuongHieu'] ?>?page=<?= $currentPage + 1 ?>&sapXep=<?= $sapXep ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-box-open fa-5x text-muted mb-3"></i>
            <h3 class="text-muted">Chưa có sản phẩm</h3>
            <p class="text-muted">Thương hiệu "<?= htmlspecialchars($thuongHieu['TenThuongHieu']) ?>" hiện chưa có sản phẩm nào.</p>
            <a href="<?= BASE_URL ?>/thuongHieu/danhSach" class="btn btn-outline-primary">
                <i class="fas fa-building me-2"></i>Xem thương hiệu khác
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
function sortProducts(value) {
    var url = new URL(window.location.href);
    if (value) {
        url.searchParams.set('sapXep', value);
    } else {
        url.searchParams.delete('sapXep');
    }
    url.searchParams.set('page', '1');
    window.location.href = url.toString();
}
</script>
