<?php
/**
 * Theo dõi đơn hàng
 */
$trangThai = $donHang['TrangThai'] ?? '';
$choXuLy = in_array($trangThai, ['Cho xu ly', 'Dang giao', 'Hoan thanh']);
$dangGiao = in_array($trangThai, ['Dang giao', 'Hoan thanh']);
$hoanThanh = $trangThai === 'Hoan thanh';
$daHuy = $trangThai === 'Da huy';
?>
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/donHang/danhSach">Đơn hàng</a></li>
            <li class="breadcrumb-item active">Theo dõi #<?= $donHang['MaDonHang'] ?></li>
        </ol>
    </nav>

    <h3 class="mb-4">Theo dõi đơn hàng #<?= $donHang['MaDonHang'] ?></h3>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Ngày đặt hàng:</strong><br>
                            <?= date('d/m/Y H:i', strtotime($donHang['NgayDatHang'] ?? '')) ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Tổng tiền:</strong><br>
                            <span class="text-danger fs-5"><?= number_format($donHang['TongTien'], 0, ',', '.') ?>đ</span>
                        </div>
                    </div>
                    <div>
                        <strong>Địa chỉ giao hàng:</strong><br>
                        <?= htmlspecialchars($donHang['DiaChiGiaoHang']) ?>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Trạng thái đơn hàng</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="step <?= $choXuLy ? 'active' : '' ?>">
                            <div class="step-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="step-content">
                                <h6>Đơn hàng đã đặt</h6>
                                <p class="text-muted small"><?= date('d/m/Y H:i', strtotime($donHang['NgayDatHang'] ?? '')) ?></p>
                            </div>
                        </div>

                        <?php if (!$daHuy): ?>
                            <div class="step <?= $dangGiao ? 'active' : '' ?>">
                                <div class="step-icon">
                                    <i class="fas fa-truck"></i>
                                </div>
                                <div class="step-content">
                                    <h6>Đang giao hàng</h6>
                                    <p class="text-muted small"><?= $dangGiao ? 'Đơn hàng đang được vận chuyển' : 'Chờ xử lý' ?></p>
                                </div>
                            </div>

                            <div class="step <?= $hoanThanh ? 'active' : '' ?>">
                                <div class="step-icon">
                                    <i class="fas fa-box"></i>
                                </div>
                                <div class="step-content">
                                    <h6>Giao hàng thành công</h6>
                                    <p class="text-muted small"><?= $hoanThanh ? 'Đơn hàng đã được giao' : 'Chưa hoàn thành' ?></p>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="step active">
                                <div class="step-icon bg-danger">
                                    <i class="fas fa-times-circle"></i>
                                </div>
                                <div class="step-content">
                                    <h6 class="text-danger">Đơn hàng đã bị hủy</h6>
                                    <p class="text-muted small">Đơn hàng không được xử lý</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="mt-4 text-center">
                <a href="<?= BASE_URL ?>/donHang/chiTiet/<?= $donHang['MaDonHang'] ?>" class="btn btn-primary">
                    <i class="fas fa-eye"></i> Xem chi tiết đơn hàng
                </a>
                <a href="<?= BASE_URL ?>/donHang/danhSach" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại danh sách
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.timeline { position: relative; padding: 20px 0; }
.step { display: flex; margin-bottom: 30px; position: relative; opacity: 0.5; }
.step.active { opacity: 1; }
.step-icon { width: 50px; height: 50px; border-radius: 50%; background: #e9ecef; display: flex; align-items: center; justify-content: center; font-size: 20px; margin-right: 20px; flex-shrink: 0; }
.step.active .step-icon { background: #0d6efd; color: white; }
.step-icon.bg-danger { background: #dc3545 !important; color: white; }
.step-content h6 { margin-bottom: 5px; }
.step::before { content: ''; position: absolute; left: 25px; top: 50px; width: 2px; height: calc(100% + 30px); background: #e9ecef; }
.step:last-child::before { display: none; }
.step.active::before { background: #0d6efd; }
</style>
