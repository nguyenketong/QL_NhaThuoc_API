<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-user"></i> Thông tin khách hàng
            </div>
            <div class="card-body">
                <p><strong>Họ tên:</strong> <?= htmlspecialchars($nguoiDung['HoTen'] ?? 'N/A') ?></p>
                <p><strong>SĐT:</strong> <?= htmlspecialchars($nguoiDung['SoDienThoai'] ?? '') ?></p>
                <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($nguoiDung['DiaChi'] ?? '') ?></p>
                <p><strong>Ngày tạo:</strong> <?= $nguoiDung['NgayTao'] ? date('d/m/Y H:i', strtotime($nguoiDung['NgayTao'])) : '' ?></p>
                <hr>
                <p><strong>Vai trò hiện tại:</strong>
                    <?php if (($nguoiDung['VaiTro'] ?? '') == 'Admin'): ?>
                        <span class="badge bg-danger">Admin</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">User</span>
                    <?php endif; ?>
                </p>
                
                <form action="<?= BASE_URL ?>/admin/?controller=nguoi-dung&action=capNhatVaiTro" method="post" class="mt-3">
                    <input type="hidden" name="id" value="<?= $nguoiDung['MaNguoiDung'] ?>">
                    <div class="input-group">
                        <select name="vaiTro" class="form-select">
                            <option value="User" <?= ($nguoiDung['VaiTro'] ?? '') != 'Admin' ? 'selected' : '' ?>>User</option>
                            <option value="Admin" <?= ($nguoiDung['VaiTro'] ?? '') == 'Admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
        
        <a href="<?= BASE_URL ?>/admin/?controller=nguoi-dung" class="btn btn-secondary w-100 mt-3">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>
    
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-shopping-cart"></i> Lịch sử đơn hàng</h6>
            </div>
            <table class="table mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Mã ĐH</th>
                        <th>Ngày đặt</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($donHangs)): ?>
                        <?php foreach ($donHangs as $dh): ?>
                            <tr>
                                <td><strong>#<?= $dh['MaDonHang'] ?></strong></td>
                                <td><?= date('d/m/Y', strtotime($dh['NgayDatHang'])) ?></td>
                                <td class="text-danger fw-bold"><?= number_format($dh['TongTien'] ?? 0, 0, ',', '.') ?>đ</td>
                                <td>
                                    <?php
                                    switch ($dh['TrangThai']) {
                                        case 'Cho xu ly': echo '<span class="badge bg-warning">Chờ xử lý</span>'; break;
                                        case 'Dang giao': echo '<span class="badge bg-info">Đang giao</span>'; break;
                                        case 'Hoan thanh': echo '<span class="badge bg-success">Hoàn thành</span>'; break;
                                        default: echo '<span class="badge bg-danger">Đã hủy</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a href="<?= BASE_URL ?>/admin/?controller=don-hang&action=details&id=<?= $dh['MaDonHang'] ?>" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Chưa có đơn hàng nào</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
