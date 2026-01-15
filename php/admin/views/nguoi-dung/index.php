<div class="d-flex justify-content-between align-items-center mb-4">
    <h6 class="mb-0">Danh sách khách hàng</h6>
</div>

<div class="admin-table">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Họ tên</th>
                <th>SĐT</th>
                <th>Địa chỉ</th>
                <th>Vai trò</th>
                <th>Số đơn</th>
                <th>Tổng chi tiêu</th>
                <th>Ngày tạo</th>
                <th width="150">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($danhSach)): ?>
                <?php foreach ($danhSach as $item): ?>
                    <tr>
                        <td><?= $item['MaNguoiDung'] ?></td>
                        <td><strong><?= htmlspecialchars($item['HoTen'] ?? 'N/A') ?></strong></td>
                        <td><?= htmlspecialchars($item['SoDienThoai'] ?? '') ?></td>
                        <td><?= htmlspecialchars($item['DiaChi'] ?? '') ?></td>
                        <td>
                            <?php if (($item['VaiTro'] ?? '') == 'Admin'): ?>
                                <span class="badge bg-danger">Admin</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">User</span>
                            <?php endif; ?>
                        </td>
                        <td><span class="badge bg-info"><?= $item['SoDonHang'] ?? 0 ?></span></td>
                        <td class="text-success fw-bold"><?= number_format($item['TongChiTieu'] ?? 0, 0, ',', '.') ?>đ</td>
                        <td><?= $item['NgayTao'] ? date('d/m/Y', strtotime($item['NgayTao'])) : '' ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/admin/?controller=nguoi-dung&action=details&id=<?= $item['MaNguoiDung'] ?>" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <?php if (($item['VaiTro'] ?? '') != 'Admin'): ?>
                                <form action="<?= BASE_URL ?>/admin/?controller=nguoi-dung&action=delete&id=<?= $item['MaNguoiDung'] ?>" method="post" class="d-inline"
                                      onsubmit="return confirm('Bạn có chắc muốn xóa?')">
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" class="text-center text-muted py-4">Chưa có khách hàng nào</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
