<?php
/**
 * Chi tiết thuốc
 */
$phanTramGiam = $thuoc['PhanTramGiam'] ?? 0;
$now = date('Y-m-d H:i:s');
$dangKhuyenMai = $phanTramGiam > 0 
    && (empty($thuoc['NgayBatDauKM']) || $thuoc['NgayBatDauKM'] <= $now)
    && (empty($thuoc['NgayKetThucKM']) || $thuoc['NgayKetThucKM'] >= $now);
$giaGoc = $thuoc['GiaGoc'] ?? $thuoc['GiaBan'] ?? 0;
$giaBan = $dangKhuyenMai ? ($giaGoc * (100 - $phanTramGiam) / 100) : ($thuoc['GiaBan'] ?? 0);
$hetHang = ($thuoc['SoLuongTon'] ?? 0) <= 0;
$ngungKinhDoanh = !($thuoc['IsActive'] ?? true);
$khongTheMua = $ngungKinhDoanh || $hetHang;
?>

<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/thuoc/danhSach">Sản phẩm</a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($thuoc['TenThuoc']) ?></li>
        </ol>
    </nav>

    <div class="row">
        <!-- Hình ảnh -->
        <div class="col-md-5">
            <div class="product-detail-image text-center p-4 bg-light rounded">
                <?php 
                $hinhAnh = $thuoc['HinhAnh'] ?? '';
                if (!empty($hinhAnh)) {
                    if (strpos($hinhAnh, 'http') === 0 || strpos($hinhAnh, BASE_URL) === 0) {
                        $imgSrc = $hinhAnh;
                    } else {
                        $imgSrc = BASE_URL . $hinhAnh;
                    }
                } else {
                    $imgSrc = BASE_URL . '/assets/images/no-image.svg';
                }
                ?>
                <img src="<?= $imgSrc ?>" 
                     alt="<?= htmlspecialchars($thuoc['TenThuoc']) ?>" 
                     class="img-fluid" style="max-height: 400px;"
                     onerror="this.src='<?= BASE_URL ?>/assets/images/no-image.svg'">
            </div>
        </div>

        <!-- Thông tin -->
        <div class="col-md-7">
            <h1 class="h3 mb-3"><?= htmlspecialchars($thuoc['TenThuoc']) ?></h1>
            
            <!-- Badges -->
            <div class="mb-3">
                <?php if ($ngungKinhDoanh): ?>
                    <span class="badge bg-secondary">Ngừng kinh doanh</span>
                <?php elseif ($hetHang): ?>
                    <span class="badge bg-secondary">Hết hàng</span>
                <?php else: ?>
                    <?php if ($phanTramGiam > 0): ?>
                        <span class="badge bg-danger">Giảm <?= $phanTramGiam ?>%</span>
                    <?php endif; ?>
                    <?php if ($thuoc['IsHot'] ?? false): ?>
                        <span class="badge bg-warning text-dark">HOT</span>
                    <?php endif; ?>
                    <?php if ($thuoc['IsNew'] ?? false): ?>
                        <span class="badge bg-success">Mới</span>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <!-- Giá -->
            <div class="product-price mb-3">
                <span class="h3 text-danger fw-bold"><?= number_format($giaBan, 0, ',', '.') ?>đ</span>
                <?php if ($phanTramGiam > 0 && $giaGoc > $giaBan): ?>
                    <span class="text-muted text-decoration-line-through ms-2"><?= number_format($giaGoc, 0, ',', '.') ?>đ</span>
                <?php endif; ?>
            </div>

            <!-- Thông tin chi tiết -->
            <table class="table table-sm">
                <tr>
                    <td class="text-muted" width="150">Thương hiệu:</td>
                    <td><strong><?= htmlspecialchars($thuoc['TenThuongHieu'] ?? 'Đang cập nhật') ?></strong></td>
                </tr>
                <tr>
                    <td class="text-muted">Nhóm thuốc:</td>
                    <td><?= htmlspecialchars($thuoc['TenNhomThuoc'] ?? 'Đang cập nhật') ?></td>
                </tr>
                <tr>
                    <td class="text-muted">Nước sản xuất:</td>
                    <td><?= htmlspecialchars($thuoc['TenNuocSX'] ?? 'Đang cập nhật') ?></td>
                </tr>
                <tr>
                    <td class="text-muted">Đơn vị:</td>
                    <td><?= htmlspecialchars($thuoc['DonViTinh'] ?? 'Hộp') ?></td>
                </tr>
                <tr>
                    <td class="text-muted">Tình trạng:</td>
                    <td>
                        <?php if ($ngungKinhDoanh): ?>
                            <span class="text-secondary">Ngừng kinh doanh</span>
                        <?php elseif ($hetHang): ?>
                            <span class="text-danger">Hết hàng</span>
                        <?php else: ?>
                            <span class="text-success">Còn hàng (<?= $thuoc['SoLuongTon'] ?>)</span>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>

            <!-- Số lượng & Thêm giỏ hàng -->
            <?php if (!$khongTheMua): ?>
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="input-group" style="width: 130px;">
                        <button class="btn btn-outline-secondary" type="button" onclick="giamSoLuong()">-</button>
                        <input type="number" id="soLuong" class="form-control text-center" value="1" min="1" max="<?= $thuoc['SoLuongTon'] ?>">
                        <button class="btn btn-outline-secondary" type="button" onclick="tangSoLuong()">+</button>
                    </div>
                    <button class="btn btn-primary btn-lg" onclick="themVaoGioHang(<?= $thuoc['MaThuoc'] ?>, document.getElementById('soLuong').value)">
                        <i class="fas fa-cart-plus"></i> Thêm vào giỏ
                    </button>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> 
                    <?= $ngungKinhDoanh ? 'Sản phẩm đã ngừng kinh doanh' : 'Sản phẩm tạm hết hàng' ?>
                </div>
            <?php endif; ?>

            <!-- Hotline -->
            <div class="bg-light p-3 rounded">
                <i class="fas fa-phone-alt text-primary"></i> 
                Tư vấn miễn phí: <a href="tel:<?= STORE_PHONE ?>" class="fw-bold"><?= STORE_PHONE ?></a>
            </div>
        </div>
    </div>

    <!-- Mô tả & Thông tin -->
    <div class="row mt-4">
        <div class="col-12">
            <ul class="nav nav-tabs" id="productTab" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#moTa">Mô tả</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#thanhPhan">Thành phần</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tacDungPhu">Tác dụng phụ</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#doiTuong">Đối tượng sử dụng</button>
                </li>
            </ul>
            <div class="tab-content p-4 border border-top-0 rounded-bottom">
                <div class="tab-pane fade show active" id="moTa">
                    <?= nl2br(htmlspecialchars($thuoc['MoTa'] ?? 'Đang cập nhật...')) ?>
                </div>
                <div class="tab-pane fade" id="thanhPhan">
                    <?php if (!empty($thanhPhans)): ?>
                        <ul>
                            <?php foreach ($thanhPhans as $tp): ?>
                                <li><strong><?= htmlspecialchars($tp['TenThanhPhan']) ?></strong>: <?= htmlspecialchars($tp['HamLuong'] ?? '') ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted">Đang cập nhật...</p>
                    <?php endif; ?>
                </div>
                <div class="tab-pane fade" id="tacDungPhu">
                    <?php if (!empty($tacDungPhus)): ?>
                        <ul>
                            <?php foreach ($tacDungPhus as $tdp): ?>
                                <li><?= htmlspecialchars($tdp['TenTacDungPhu']) ?> 
                                    <?php if (!empty($tdp['MucDo'])): ?>
                                        <span class="badge bg-<?= $tdp['MucDo'] == 'Nặng' ? 'danger' : ($tdp['MucDo'] == 'Trung bình' ? 'warning' : 'info') ?>"><?= htmlspecialchars($tdp['MucDo']) ?></span>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted">Đang cập nhật...</p>
                    <?php endif; ?>
                </div>
                <div class="tab-pane fade" id="doiTuong">
                    <?php if (!empty($doiTuongs)): ?>
                        <div class="row">
                            <?php foreach ($doiTuongs as $dt): ?>
                                <div class="col-md-4 col-6 mb-2">
                                    <span class="badge bg-primary p-2">
                                        <i class="fas fa-user me-1"></i> <?= htmlspecialchars($dt['TenDoiTuong']) ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">Phù hợp với mọi đối tượng (theo chỉ định của bác sĩ)</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function tangSoLuong() {
    const input = document.getElementById('soLuong');
    const max = parseInt(input.max);
    if (parseInt(input.value) < max) {
        input.value = parseInt(input.value) + 1;
    }
}
function giamSoLuong() {
    const input = document.getElementById('soLuong');
    if (parseInt(input.value) > 1) {
        input.value = parseInt(input.value) - 1;
    }
}
</script>
