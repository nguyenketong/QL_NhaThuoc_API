<div class="d-flex justify-content-between align-items-center mb-4">
    <h6 class="mb-0">Danh sách đối tượng sử dụng</h6>
    <a href="<?= BASE_URL ?>/admin/?controller=doi-tuong&action=create" class="btn btn-success">
        <i class="fas fa-plus"></i> Thêm đối tượng
    </a>
</div>

<div class="admin-table">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên đối tượng</th>
                <th>Mô tả</th>
                <th width="150">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($danhSach)): ?>
                <?php foreach ($danhSach as $item): ?>
                    <tr>
                        <td><?= $item['MaDoiTuong'] ?></td>
                        <td><strong><?= htmlspecialchars($item['TenDoiTuong']) ?></strong></td>
                        <td><?= htmlspecialchars($item['MoTa'] ?? '') ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/admin/?controller=doi-tuong&action=edit&id=<?= $item['MaDoiTuong'] ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="<?= BASE_URL ?>/admin/?controller=doi-tuong&action=delete&id=<?= $item['MaDoiTuong'] ?>" method="post" class="d-inline"
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
