<?php
/**
 * Danh sách thuốc
 */
?>
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Trang chủ</a></li>
            <li class="breadcrumb-item active"><?= $tenNhom ?? ($isKhuyenMai ?? false ? 'Khuyến mãi' : 'Sản phẩm') ?></li>
        </ol>
    </nav>

    <div class="row">
        <!-- Sidebar Filter -->
        <div class="col-lg-3">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-filter"></i> Bộ lọc
                </div>
                <div class="card-body">
                    <form action="<?= BASE_URL ?>/thuoc/danhSach" method="GET">
                        <!-- Tìm kiếm -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tìm kiếm</label>
                            <input type="text" name="search" class="form-control" value="<?= htmlspecialchars($filters['search'] ?? '') ?>" placeholder="Tên thuốc...">
                        </div>

                        <!-- Nhóm thuốc -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nhóm thuốc</label>
                            <select name="nhom" class="form-select">
                                <option value="">Tất cả</option>
                                <?php foreach ($nhomThuocs ?? [] as $nhom): ?>
                                    <option value="<?= $nhom['MaNhomThuoc'] ?>" <?= ($filters['MaNhomThuoc'] ?? '') == $nhom['MaNhomThuoc'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($nhom['TenNhomThuoc']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Thương hiệu -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Thương hiệu</label>
                            <select name="thuong_hieu" class="form-select">
                                <option value="">Tất cả</option>
                                <?php foreach ($thuongHieus ?? [] as $th): ?>
                                    <option value="<?= $th['MaThuongHieu'] ?>" <?= ($filters['MaThuongHieu'] ?? '') == $th['MaThuongHieu'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($th['TenThuongHieu']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Đối tượng sử dụng -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-users text-primary me-1"></i> Đối tượng sử dụng
                            </label>
                            <select name="doi_tuong" class="form-select">
                                <option value="">Tất cả</option>
                                <?php foreach ($doiTuongs ?? [] as $dt): ?>
                                    <option value="<?= $dt['MaDoiTuong'] ?>" <?= ($filters['doi_tuong'] ?? '') == $dt['MaDoiTuong'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($dt['TenDoiTuong']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> Lọc
                        </button>
                        
                        <?php if (!empty($filters['MaNhomThuoc']) || !empty($filters['MaThuongHieu']) || !empty($filters['doi_tuong']) || !empty($filters['search'])): ?>
                            <a href="<?= BASE_URL ?>/thuoc/danhSach" class="btn btn-outline-secondary w-100 mt-2">
                                <i class="fas fa-times"></i> Xóa bộ lọc
                            </a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>

        <!-- Product List -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">
                    <?= $tenNhom ?? ($isKhuyenMai ?? false ? 'Sản phẩm khuyến mãi' : 'Tất cả sản phẩm') ?>
                    <?php if (!empty($tuKhoa)): ?>
                        <small class="text-muted">- Kết quả cho "<?= htmlspecialchars($tuKhoa) ?>"</small>
                    <?php endif; ?>
                </h4>
                <span class="badge bg-primary"><?= count($danhSachThuoc ?? []) ?> sản phẩm</span>
            </div>

            <div class="row g-3">
                <?php if (!empty($danhSachThuoc)): ?>
                    <?php foreach ($danhSachThuoc as $thuoc): ?>
                        <?php include ROOT . '/app/views/components/product-card.php'; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle"></i> Không tìm thấy sản phẩm nào
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
