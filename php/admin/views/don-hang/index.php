<div class="mb-4">
    <form method="get" class="d-flex gap-2 flex-wrap">
        <input type="hidden" name="controller" value="don-hang">
        <select name="trangThai" class="form-select" style="width: 180px;">
            <option value="">-- Tất cả trạng thái --</option>
            <option value="Cho xu ly" <?= ($trangThaiFilter ?? '') == 'Cho xu ly' ? 'selected' : '' ?>>Chờ xử lý</option>
            <option value="Dang giao" <?= ($trangThaiFilter ?? '') == 'Dang giao' ? 'selected' : '' ?>>Đang giao</option>
            <option value="Hoan thanh" <?= ($trangThaiFilter ?? '') == 'Hoan thanh' ? 'selected' : '' ?>>Hoàn thành</option>
            <option value="Da huy" <?= ($trangThaiFilter ?? '') == 'Da huy' ? 'selected' : '' ?>>Đã hủy</option>
        </select>
        <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Lọc</button>
    </form>
</div>

<div class="admin-table">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Mã ĐH</th>
                <th>Khách hàng</th>
                <th>SĐT</th>
                <th>Tổng tiền</th>
                <th>Trạng thái</th>
                <th>Ngày đặt</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($danhSach)): ?>
                <?php foreach ($danhSach as $item): ?>
                    <tr>
                        <td><strong>#<?= $item['MaDonHang'] ?></strong></td>
                        <td><?= htmlspecialchars($item['HoTen'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($item['SoDienThoai'] ?? '') ?></td>
                        <td class="text-danger fw-bold"><?= number_format($item['TongTien'] ?? 0, 0, ',', '.') ?>đ</td>
                        <td>
                            <?php
                            switch ($item['TrangThai']) {
                                case 'Cho xu ly': echo '<span class="badge bg-warning">Chờ xử lý</span>'; break;
                                case 'Dang giao': echo '<span class="badge bg-info">Đang giao</span>'; break;
                                case 'Hoan thanh': echo '<span class="badge bg-success">Hoàn thành</span>'; break;
                                default: echo '<span class="badge bg-danger">Đã hủy</span>';
                            }
                            ?>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($item['NgayDatHang'])) ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/admin/?controller=don-hang&action=details&id=<?= $item['MaDonHang'] ?>" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> Chi tiết
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="fas fa-info-circle"></i> Chưa có đơn hàng nào
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
