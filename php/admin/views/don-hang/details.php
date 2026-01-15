<?php
$laChuyenKhoan = ($donHang['PhuongThucThanhToan'] ?? '') == 'Chuyển khoản';
$noiDungCK = 'DH' . $donHang['MaDonHang'];
$daHoanThanh = ($donHang['TrangThai'] ?? '') == 'Hoan thanh';
$daHuy = ($donHang['TrangThai'] ?? '') == 'Da huy';
$dangGiao = ($donHang['TrangThai'] ?? '') == 'Dang giao';
$khongTheDoiTrangThai = $daHoanThanh || $daHuy;
$khongTheHuy = $dangGiao || ($donHang['DaThanhToan'] ?? false);
?>

<div class="row g-4">
    <div class="col-lg-8">
        <!-- Thông tin đơn hàng -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white"><h6 class="mb-0">Thông tin đơn hàng</h6></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Mã đơn:</strong> #<?= $donHang['MaDonHang'] ?></p>
                        <p><strong>Ngày đặt:</strong> <?= date('d/m/Y H:i', strtotime($donHang['NgayDatHang'])) ?></p>
                        <p><strong>Thanh toán:</strong> <?= htmlspecialchars($donHang['PhuongThucThanhToan'] ?? 'COD') ?>
                            <?php if ($laChuyenKhoan): ?>
                                <?php if ($donHang['DaThanhToan']): ?>
                                    <span class="badge bg-success ms-2"><i class="fas fa-check"></i> Đã thanh toán</span>
                                <?php else: ?>
                                    <span class="badge bg-warning ms-2"><i class="fas fa-clock"></i> Chờ thanh toán</span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Khách hàng:</strong> <?= htmlspecialchars($donHang['HoTen'] ?? 'N/A') ?></p>
                        <p><strong>SĐT:</strong> <?= htmlspecialchars($donHang['SoDienThoai'] ?? '') ?></p>
                        <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($donHang['DiaChiGiaoHang'] ?? '') ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thông tin chuyển khoản -->
        <?php if ($laChuyenKhoan): ?>
            <div class="card border-0 shadow-sm mb-4 <?= $donHang['DaThanhToan'] ? 'border-success' : 'border-warning' ?>">
                <div class="card-header <?= $donHang['DaThanhToan'] ? 'bg-success text-white' : 'bg-warning text-dark' ?>">
                    <h6 class="mb-0"><i class="fas fa-university"></i> Thông tin chuyển khoản cần kiểm tra</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <p class="mb-2"><strong>Số tiền:</strong></p>
                            <h4 class="text-danger"><?= number_format($donHang['TongTien'] ?? 0, 0, ',', '.') ?>đ</h4>
                        </div>
                        <div class="col-6">
                            <p class="mb-2"><strong>Nội dung CK:</strong></p>
                            <h4 class="text-success"><?= $noiDungCK ?></h4>
                        </div>
                    </div>
                    <hr>
                    <p class="small text-muted mb-0">
                        <i class="fas fa-info-circle"></i> Kiểm tra lịch sử giao dịch ngân hàng với nội dung chứa "<strong><?= $noiDungCK ?></strong>" và số tiền <strong><?= number_format($donHang['TongTien'] ?? 0, 0, ',', '.') ?>đ</strong>
                    </p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Chi tiết sản phẩm -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><h6 class="mb-0">Sản phẩm</h6></div>
            <table class="table mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Sản phẩm</th>
                        <th class="text-end">Đơn giá</th>
                        <th class="text-center">SL</th>
                        <th class="text-end">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($chiTiet ?? [] as $ct): ?>
                        <tr>
                            <td><?= htmlspecialchars($ct['TenThuoc'] ?? '') ?></td>
                            <td class="text-end"><?= number_format($ct['DonGia'] ?? 0, 0, ',', '.') ?>đ</td>
                            <td class="text-center"><?= $ct['SoLuong'] ?></td>
                            <td class="text-end fw-bold"><?= number_format($ct['ThanhTien'] ?? 0, 0, ',', '.') ?>đ</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="3" class="text-end"><strong>Tổng cộng:</strong></td>
                        <td class="text-end text-danger fw-bold fs-5"><?= number_format($donHang['TongTien'] ?? 0, 0, ',', '.') ?>đ</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Xác nhận thanh toán -->
        <?php if ($laChuyenKhoan && !$donHang['DaThanhToan'] && $donHang['TrangThai'] == 'Cho xu ly'): ?>
            <div class="card border-0 shadow-sm mb-3 border-warning">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0"><i class="fas fa-money-check-alt"></i> Xác nhận thanh toán</h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-3">
                        Kiểm tra tài khoản ngân hàng đã nhận được <strong class="text-danger"><?= number_format($donHang['TongTien'] ?? 0, 0, ',', '.') ?>đ</strong> 
                        với nội dung <strong class="text-success"><?= $noiDungCK ?></strong> chưa?
                    </p>
                    <div class="alert alert-info small mb-3">
                        <i class="fas fa-info-circle"></i> Khi xác nhận, đơn hàng sẽ tự động chuyển sang trạng thái <strong>"Đang giao"</strong>
                    </div>
                    <form action="<?= BASE_URL ?>/admin/?controller=don-hang&action=xacNhanThanhToan" method="post">
                        <input type="hidden" name="id" value="<?= $donHang['MaDonHang'] ?>" />
                        <button type="submit" class="btn btn-success w-100" onclick="return confirm('Xác nhận đã nhận tiền và chuyển sang Đang giao?')">
                            <i class="fas fa-check-circle"></i> Đã nhận tiền - Giao hàng
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <!-- Cập nhật trạng thái -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><h6 class="mb-0">Trạng thái đơn hàng</h6></div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Hiện tại:</strong>
                    <?php
                    switch ($donHang['TrangThai']) {
                        case 'Cho xu ly': echo '<span class="badge bg-warning fs-6">Chờ xử lý</span>'; break;
                        case 'Dang giao': echo '<span class="badge bg-info fs-6">Đang giao</span>'; break;
                        case 'Hoan thanh': echo '<span class="badge bg-success fs-6">Hoàn thành</span>'; break;
                        default: echo '<span class="badge bg-danger fs-6">Đã hủy</span>';
                    }
                    ?>
                </div>

                <?php if ($laChuyenKhoan && !$donHang['DaThanhToan'] && $donHang['TrangThai'] == 'Cho xu ly'): ?>
                    <div class="alert alert-warning small">
                        <i class="fas fa-exclamation-triangle"></i> Cần xác nhận thanh toán trước khi chuyển trạng thái "Đang giao"
                    </div>
                <?php endif; ?>

                <?php if ($khongTheDoiTrangThai): ?>
                    <div class="alert <?= $daHoanThanh ? 'alert-success' : 'alert-secondary' ?> small">
                        <i class="fas fa-lock"></i> Đơn hàng <?= $daHoanThanh ? 'đã hoàn thành' : 'đã hủy' ?> - không thể thay đổi trạng thái
                    </div>
                <?php else: ?>
                    <form action="<?= BASE_URL ?>/admin/?controller=don-hang&action=capNhatTrangThai" method="post" id="formCapNhat">
                        <input type="hidden" name="id" value="<?= $donHang['MaDonHang'] ?>" />
                        <select name="trangThai" class="form-select mb-3" id="selectTrangThai">
                            <?php if (!$dangGiao): ?>
                                <option value="Cho xu ly" <?= $donHang['TrangThai'] == 'Cho xu ly' ? 'selected' : '' ?>>Chờ xử lý</option>
                            <?php endif; ?>
                            <option value="Dang giao" <?= $donHang['TrangThai'] == 'Dang giao' ? 'selected' : '' ?>>Đang giao</option>
                            <option value="Hoan thanh">Hoàn thành</option>
                            <?php if (!$khongTheHuy): ?>
                                <option value="Da huy">Đã hủy</option>
                            <?php endif; ?>
                        </select>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save"></i> Cập nhật
                        </button>
                    </form>
                    
                    <?php if ($khongTheHuy): ?>
                        <div class="alert alert-info small mt-2 mb-0">
                            <i class="fas fa-info-circle"></i> 
                            <?php if ($donHang['DaThanhToan']): ?>
                                <span>Đã thanh toán - không thể hủy đơn</span>
                            <?php elseif ($dangGiao): ?>
                                <span>Đang giao - không thể hủy đơn</span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        <a href="<?= BASE_URL ?>/admin/?controller=don-hang" class="btn btn-secondary w-100 mt-3">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>
</div>

<?php if ($laChuyenKhoan && !$donHang['DaThanhToan'] && $donHang['TrangThai'] == 'Cho xu ly'): ?>
<script>
document.getElementById('formCapNhat')?.addEventListener('submit', function(e) {
    var trangThai = document.getElementById('selectTrangThai').value;
    if (trangThai === 'Dang giao' || trangThai === 'Hoan thanh') {
        alert('Vui lòng xác nhận đã nhận tiền chuyển khoản trước khi chuyển trạng thái!');
        e.preventDefault();
        return false;
    }
});
</script>
<?php endif; ?>
