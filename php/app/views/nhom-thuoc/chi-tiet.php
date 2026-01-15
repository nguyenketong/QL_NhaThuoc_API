<?php
/**
 * Chi tiết nhóm thuốc - Sản phẩm theo nhóm
 */
?>
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/nhomThuoc/danhSach">Danh mục</a></li>
            <?php if ($danhMucCha): ?>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/nhomThuoc/chiTiet/<?= $danhMucCha['MaNhomThuoc'] ?>"><?= htmlspecialchars($danhMucCha['TenNhomThuoc']) ?></a></li>
            <?php endif; ?>
            <li class="breadcrumb-item active"><?= htmlspecialchars($nhomThuoc['TenNhomThuoc']) ?></li>
        </ol>
    </nav>

    <!-- Category Header -->
    <div class="category-header mb-4 p-4 bg-light rounded" style="border-left: 4px solid #0d6efd;">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="h3 mb-2">
                    <i class="fas fa-pills text-primary me-2"></i><?= htmlspecialchars($nhomThuoc['TenNhomThuoc']) ?>
                </h1>
                <?php if (!empty($nhomThuoc['MoTa'])): ?>
                    <p class="text-muted mb-0"><?= htmlspecialchars($nhomThuoc['MoTa']) ?></p>
                <?php endif; ?>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <span class="badge bg-primary fs-6 px-3 py-2">
                    <i class="fas fa-box me-1"></i> <?= $totalItems ?> sản phẩm
                </span>
            </div>
        </div>
    </div>

    <!-- Danh mục con -->
    <?php if (!empty($danhMucCon)): ?>
        <div class="subcategory-bar mb-4">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <span class="text-muted me-2"><i class="fas fa-folder-open"></i> Danh mục con:</span>
                <?php foreach ($danhMucCon as $dm): ?>
                    <a href="<?= BASE_URL ?>/nhomThuoc/chiTiet/<?= $dm['MaNhomThuoc'] ?>" class="btn btn-outline-primary btn-sm">
                        <?= htmlspecialchars($dm['TenNhomThuoc']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

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
                        <a class="page-link" href="<?= BASE_URL ?>/nhomThuoc/chiTiet/<?= $nhomThuoc['MaNhomThuoc'] ?>?page=<?= $currentPage - 1 ?>&sapXep=<?= $sapXep ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php if ($i == 1 || $i == $totalPages || ($i >= $currentPage - 2 && $i <= $currentPage + 2)): ?>
                            <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                <a class="page-link" href="<?= BASE_URL ?>/nhomThuoc/chiTiet/<?= $nhomThuoc['MaNhomThuoc'] ?>?page=<?= $i ?>&sapXep=<?= $sapXep ?>"><?= $i ?></a>
                            </li>
                        <?php elseif ($i == $currentPage - 3 || $i == $currentPage + 3): ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                    <?php endfor; ?>
                    <li class="page-item <?= $currentPage == $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= BASE_URL ?>/nhomThuoc/chiTiet/<?= $nhomThuoc['MaNhomThuoc'] ?>?page=<?= $currentPage + 1 ?>&sapXep=<?= $sapXep ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    <?php else: ?>
        <div class="text-center py-5 bg-light rounded">
            <i class="fas fa-box-open fa-5x text-muted mb-3"></i>
            <h3 class="text-muted">Chưa có sản phẩm</h3>
            <p class="text-muted">Nhóm thuốc "<?= htmlspecialchars($nhomThuoc['TenNhomThuoc']) ?>" hiện chưa có sản phẩm nào.</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="<?= BASE_URL ?>/nhomThuoc/danhSach" class="btn btn-outline-primary">
                    <i class="fas fa-th-list me-2"></i>Xem danh mục khác
                </a>
                <a href="<?= BASE_URL ?>/thuoc/danhSach" class="btn btn-primary">
                    <i class="fas fa-pills me-2"></i>Xem tất cả sản phẩm
                </a>
            </div>
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
