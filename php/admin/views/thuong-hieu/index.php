<div class="d-flex justify-content-between align-items-center mb-4">
    <h6 class="mb-0">Danh sách thương hiệu</h6>
    <a href="<?= BASE_URL ?>/admin/?controller=thuong-hieu&action=create" class="btn btn-success">
        <i class="fas fa-plus"></i> Thêm thương hiệu
    </a>
</div>

<div class="admin-table">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Logo</th>
                <th>Tên thương hiệu</th>
                <th>Quốc gia</th>
                <th>Địa chỉ</th>
                <th>Số thuốc</th>
                <th width="150">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($danhSach)): ?>
                <?php foreach ($danhSach as $item): ?>
                    <tr>
                        <td><?= $item['MaThuongHieu'] ?></td>
                        <td>
                            <?php if (!empty($item['HinhAnh'])): ?>
                                <img src="<?= htmlspecialchars($item['HinhAnh']) ?>" alt="" style="width: 50px; height: 50px; object-fit: contain;">
                            <?php else: ?>
                                <span class="text-muted"><i class="fas fa-building"></i></span>
                            <?php endif; ?>
                        </td>
                        <td><strong><?= htmlspecialchars($item['TenThuongHieu']) ?></strong></td>
                        <td><?= htmlspecialchars($item['QuocGia'] ?? '') ?></td>
                        <td><?= htmlspecialchars($item['DiaChi'] ?? '') ?></td>
                        <td><span class="badge bg-info"><?= $item['SoLuongThuoc'] ?? 0 ?></span></td>
                        <td>
                            <a href="<?= BASE_URL ?>/admin/?controller=thuong-hieu&action=edit&id=<?= $item['MaThuongHieu'] ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="<?= BASE_URL ?>/admin/?controller=thuong-hieu&action=delete&id=<?= $item['MaThuongHieu'] ?>" method="post" class="d-inline"
                                  onsubmit="return confirm('Bạn có chắc muốn xóa?')">
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">Chưa có thương hiệu nào</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
