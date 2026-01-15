<?php
/**
 * Chi tiết đơn hàng
 */
$badgeClass = match($donHang['TrangThai']) {
    'Cho xu ly' => 'bg-warning text-dark',
    'Dang giao' => 'bg-primary',
    'Hoan thanh' => 'bg-success',
    'Da huy' => 'bg-danger',
    default => 'bg-secondary'
};

$trangThaiText = match($donHang['TrangThai']) {
    'Cho xu ly' => 'Chờ xử lý',
    'Dang giao' => 'Đang giao',
    'Hoan thanh' => 'Hoàn thành',
    'Da huy' => 'Đã hủy',
    default => $donHang['TrangThai']
};

// Kiểm tra đơn hàng chuyển khoản chưa thanh toán
$laChuyenKhoan = ($donHang['PhuongThucThanhToan'] ?? '') === 'Chuyển khoản';
$chuaThanhToan = empty($donHang['DaThanhToan']);
$canThanhToan = $laChuyenKhoan && $chuaThanhToan && $donHang['TrangThai'] === 'Cho xu ly';
?>
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/donHang/danhSach">Đơn hàng</a></li>
            <li class="breadcrumb-item active">Chi tiết #<?= $donHang['MaDonHang'] ?></li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0"><i class="fas fa-file-invoice"></i> Đơn hàng #<?= $donHang['MaDonHang'] ?></h3>
        <span class="badge <?= $badgeClass ?> fs-6"><?= $trangThaiText ?></span>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Thông tin đơn hàng -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Thông tin đơn hàng</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Ngày đặt:</strong> <?= date('d/m/Y H:i', strtotime($donHang['NgayDatHang'] ?? '')) ?></p>
                            <p>
                                <strong>Phương thức thanh toán:</strong> <?= $donHang['PhuongThucThanhToan'] ?>
                                <?php if ($laChuyenKhoan): ?>
                                    <?php if ($chuaThanhToan): ?>
                                        <span class="badge bg-warning text-dark ms-2"><i class="fas fa-clock"></i> Chờ thanh toán</span>
                                    <?php else: ?>
                                        <span class="badge bg-success ms-2"><i class="fas fa-check"></i> Đã thanh toán</span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Địa chỉ giao hàng:</strong></p>
                            <p class="text-muted"><?= nl2br(htmlspecialchars($donHang['DiaChiGiaoHang'])) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sản phẩm -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-shopping-bag"></i> Sản phẩm</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Sản phẩm</th>
                                <th class="text-center">Số lượng</th>
                                <th class="text-end">Đơn giá</th>
                                <th class="text-end">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($chiTiet as $item): 
                                $hinhAnh = $item['HinhAnh'] ?? '';
                                if (!empty($hinhAnh) && strpos($hinhAnh, 'http') !== 0 && strpos($hinhAnh, BASE_URL) !== 0) {
                                    $hinhAnh = BASE_URL . $hinhAnh;
                                }
                                if (empty($hinhAnh)) $hinhAnh = BASE_URL . '/assets/images/no-image.svg';
                            ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="<?= $hinhAnh ?>" 
                                                 alt="" style="width: 50px; height: 50px; object-fit: contain;" class="rounded me-2">
                                            <span><?= htmlspecialchars($item['TenThuoc']) ?></span>
                                        </div>
                                    </td>
                                    <td class="text-center"><?= $item['SoLuong'] ?></td>
                                    <td class="text-end"><?= number_format($item['DonGia'], 0, ',', '.') ?>đ</td>
                                    <td class="text-end text-danger fw-bold"><?= number_format($item['ThanhTien'], 0, ',', '.') ?>đ</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Tổng tiền -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Tổng đơn hàng</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tạm tính:</span>
                        <span><?= number_format($donHang['TongTien'], 0, ',', '.') ?>đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Phí vận chuyển:</span>
                        <span class="text-success">Miễn phí</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Tổng cộng:</strong>
                        <strong class="text-danger h4"><?= number_format($donHang['TongTien'], 0, ',', '.') ?>đ</strong>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-3">
                <?php if ($canThanhToan): ?>
                    <a href="<?= BASE_URL ?>/donHang/thanhToanQR/<?= $donHang['MaDonHang'] ?>" class="btn btn-success w-100 mb-2">
                        <i class="fas fa-qrcode"></i> Thanh toán ngay
                    </a>
                <?php endif; ?>
                <?php if ($donHang['TrangThai'] === 'Cho xu ly'): ?>
                    <a href="<?= BASE_URL ?>/donHang/huy/<?= $donHang['MaDonHang'] ?>" class="btn btn-danger w-100 mb-2" onclick="return confirm('Bạn có chắc muốn hủy đơn hàng này?')">
                        <i class="fas fa-times"></i> Hủy đơn hàng
                    </a>
                <?php endif; ?>
                <a href="<?= BASE_URL ?>/donHang/danhSach" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>
    </div>
</div>
