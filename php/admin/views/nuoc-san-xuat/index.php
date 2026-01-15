<div class="d-flex justify-content-between align-items-center mb-4">
    <h6 class="mb-0">Danh sách nước sản xuất</h6>
    <a href="<?= BASE_URL ?>/admin/?controller=nuoc-san-xuat&action=create" class="btn btn-success">
        <i class="fas fa-plus"></i> Thêm nước sản xuất
    </a>
</div>

<div class="admin-table">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên nước sản xuất</th>
                <th>Số thuốc</th>
                <th width="150">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($danhSach)): ?>
                <?php foreach ($danhSach as $item): ?>
                    <tr>
                        <td><?= $item['MaNuocSX'] ?></td>
                        <td><strong><?= htmlspecialchars($item['TenNuocSX']) ?></strong></td>
                        <td><span class="badge bg-info"><?= $item['SoLuongThuoc'] ?? 0 ?></span></td>
                        <td>
                            <a href="<?= BASE_URL ?>/admin/?controller=nuoc-san-xuat&action=edit&id=<?= $item['MaNuocSX'] ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="<?= BASE_URL ?>/admin/?controller=nuoc-san-xuat&action=delete&id=<?= $item['MaNuocSX'] ?>" method="post" class="d-inline"
                                  onsubmit="return confirm('Bạn có chắc muốn xóa?')">
                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4" class="text-center text-muted py-4">Chưa có dữ liệu</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
