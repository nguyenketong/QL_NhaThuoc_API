<?php
/**
 * Product Card Component
 * Sử dụng biến $thuoc từ parent view
 */
$phanTramGiam = $thuoc['PhanTramGiam'] ?? 0;
$isHot = $thuoc['IsHot'] ?? false;
$isNew = $thuoc['IsNew'] ?? false;
$isActive = $thuoc['IsActive'] ?? true;
$hetHang = ($thuoc['SoLuongTon'] ?? 0) <= 0;
$ngungKinhDoanh = !$isActive;
$khongTheMua = $ngungKinhDoanh || $hetHang;

$now = date('Y-m-d H:i:s');
$dangKhuyenMai = $phanTramGiam > 0 
    && (empty($thuoc['NgayBatDauKM']) || $thuoc['NgayBatDauKM'] <= $now)
    && (empty($thuoc['NgayKetThucKM']) || $thuoc['NgayKetThucKM'] >= $now);

// GiaGoc là giá gốc trước khuyến mãi, GiaBan là giá đang bán (có thể đã giảm)
$giaGoc = $thuoc['GiaGoc'] ?? $thuoc['GiaBan'] ?? 0;
// Nếu đang KM: tính giá giảm từ GiaGoc. Nếu hết KM: trả về GiaGoc (giá gốc trước KM)
$giaBan = $dangKhuyenMai ? ($giaGoc * (100 - $phanTramGiam) / 100) : $giaGoc;
?>
<div class="col-md-3 col-6">
    <div class="product-card position-relative <?= $khongTheMua ? 'product-unavailable' : '' ?>">
        <!-- Badges -->
        <div class="product-badges position-absolute" style="top: 10px; left: 10px; z-index: 2;">
            <?php if ($ngungKinhDoanh): ?>
                <span class="badge bg-secondary">Ngừng kinh doanh</span>
            <?php elseif ($hetHang): ?>
                <span class="badge bg-secondary">Hết hàng</span>
            <?php else: ?>
                <?php if ($dangKhuyenMai): ?>
                    <span class="badge bg-danger">-<?= $phanTramGiam ?>%</span>
                <?php endif; ?>
                <?php if ($isHot): ?>
                    <span class="badge bg-warning text-dark">HOT</span>
                <?php endif; ?>
                <?php if ($isNew): ?>
                    <span class="badge bg-success">Mới</span>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Image -->
        <div class="product-image">
            <?php 
            $hinhAnh = $thuoc['HinhAnh'] ?? '';
            $imgSrc = !empty($hinhAnh) ? $hinhAnh : BASE_URL . '/assets/images/no-image.svg';
            ?>
            <img src="<?= htmlspecialchars($imgSrc) ?>" 
                 alt="<?= htmlspecialchars($thuoc['TenThuoc']) ?>" 
                 onerror="this.src='<?= BASE_URL ?>/assets/images/no-image.svg'" />
        </div>

        <!-- Info -->
        <div class="product-info p-3">
            <h6 class="product-name mb-1"><?= htmlspecialchars($thuoc['TenThuoc']) ?></h6>
            <div class="product-category">
                <small class="text-muted"><?= htmlspecialchars($thuoc['TenNhomThuoc'] ?? '') ?></small>
            </div>
            <div class="product-price my-2">
                <span class="price-new text-danger fw-bold"><?= number_format($giaBan, 0, ',', '.') ?>đ</span>
                <?php if ($dangKhuyenMai && $giaGoc > $giaBan): ?>
                    <span class="price-old text-muted text-decoration-line-through ms-2"><?= number_format($giaGoc, 0, ',', '.') ?>đ</span>
                <?php endif; ?>
            </div>
            <div class="product-brand mb-2">
                <small><i class="fas fa-building"></i> <?= htmlspecialchars($thuoc['TenThuongHieu'] ?? 'Đang cập nhật') ?></small>
            </div>
            <div class="product-actions">
                <a href="<?= BASE_URL ?>/thuoc/chiTiet/<?= $thuoc['MaThuoc'] ?>" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-info-circle"></i> Chi tiết
                </a>
                <?php if ($khongTheMua): ?>
                    <button type="button" class="btn btn-secondary btn-sm" disabled>
                        <i class="fas fa-ban"></i> <?= $ngungKinhDoanh ? 'Ngừng KD' : 'Hết hàng' ?>
                    </button>
                <?php else: ?>
                    <button type="button" class="btn btn-primary btn-sm" onclick="themVaoGioHang(<?= $thuoc['MaThuoc'] ?>, 1)">
                        <i class="fas fa-cart-plus"></i> Thêm
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
