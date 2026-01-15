<div class="d-flex justify-content-between align-items-center mb-4">
    <h6 class="mb-0">Danh sách nhóm thuốc</h6>
    <a href="<?= BASE_URL ?>/admin/?controller=nhom-thuoc&action=create" class="btn btn-success">
        <i class="fas fa-plus"></i> Thêm nhóm
    </a>
</div>

<div class="admin-table">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên nhóm thuốc</th>
                <th>Danh mục cha</th>
                <th>Mô tả</th>
                <th>Số thuốc</th>
                <th width="120">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($danhSach)): ?>
                <?php foreach ($danhSach as $item): ?>
                    <?php $isParent = $item['isParent'] ?? empty($item['MaDanhMucCha']); ?>
                    <tr class="<?= $isParent ? 'table-light' : '' ?>">
                        <td><?= $item['MaNhomThuoc'] ?></td>
                        <td>
                            <?php if ($isParent): ?>
                                <strong><?= htmlspecialchars($item['TenNhomThuoc']) ?></strong>
                                <span class="badge bg-warning text-dark ms-2">Danh mục cha</span>
                            <?php else: ?>
                                <span class="text-muted ms-3">└─</span> <?= htmlspecialchars($item['TenNhomThuoc']) ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($item['TenDanhMucCha'])): ?>
                                <span class="badge bg-secondary"><?= htmlspecialchars($item['TenDanhMucCha']) ?></span>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($item['MoTa'] ?? '') ?></td>
                        <td><span class="badge bg-info"><?= $item['SoLuongThuoc'] ?? 0 ?></span></td>
                        <td>
                            <a href="<?= BASE_URL ?>/admin/?controller=nhom-thuoc&action=edit&id=<?= $item['MaNhomThuoc'] ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="<?= BASE_URL ?>/admin/?controller=nhom-thuoc&action=delete&id=<?= $item['MaNhomThuoc'] ?>" method="post" class="d-inline"
                                  onsubmit="return confirm('Bạn có chắc muốn xóa nhóm thuốc này?')">
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">Chưa có nhóm thuốc nào</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
