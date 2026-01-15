<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <form method="get" class="d-flex gap-2">
            <input type="hidden" name="controller" value="thuoc">
            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm..." value="<?= htmlspecialchars($search ?? '') ?>" />
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
        </form>
    </div>
    <a href="<?= BASE_URL ?>/admin/?controller=thuoc&action=create" class="btn btn-success">
        <i class="fas fa-plus"></i> Thêm thuốc
    </a>
</div>

<div class="admin-table">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Hình</th>
                <th>Tên thuốc</th>
                <th>Nhóm</th>
                <th>Thương hiệu</th>
                <th>Giá bán</th>
                <th>Tồn kho</th>
                <th width="150">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($danhSach)): ?>
                <?php foreach ($danhSach as $item): ?>
                    <tr>
                        <td><?= $item['MaThuoc'] ?></td>
                        <td>
                            <?php 
                            $imgSrc = $item['HinhAnh'] ?? '';
                            if (empty($imgSrc)) {
                                $imgSrc = BASE_URL . '/assets/images/no-image.svg';
                            }
                            ?>
                            <img src="<?= htmlspecialchars($imgSrc) ?>" alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($item['TenThuoc']) ?></strong>
                            <?php if ($item['IsHot']): ?><span class="badge bg-danger ms-1">HOT</span><?php endif; ?>
                            <?php if ($item['IsNew']): ?><span class="badge bg-success ms-1">NEW</span><?php endif; ?>
                        </td>
                        <td><span class="badge bg-info"><?= htmlspecialchars($item['TenNhomThuoc'] ?? 'N/A') ?></span></td>
                        <td><?= htmlspecialchars($item['TenThuongHieu'] ?? 'N/A') ?></td>
                        <td class="text-danger fw-bold"><?= number_format($item['GiaBan'] ?? 0, 0, ',', '.') ?>đ</td>
                        <td>
                            <?php if (($item['SoLuongTon'] ?? 0) <= 10): ?>
                                <span class="text-danger"><?= $item['SoLuongTon'] ?? 0 ?></span>
                            <?php else: ?>
                                <?= $item['SoLuongTon'] ?? 0 ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= BASE_URL ?>/admin/?controller=thuoc&action=edit&id=<?= $item['MaThuoc'] ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="<?= BASE_URL ?>/admin/?controller=thuoc&action=delete&id=<?= $item['MaThuoc'] ?>" method="post" class="d-inline"
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
                    <td colspan="8" class="text-center text-muted py-4">
                        <i class="fas fa-info-circle"></i> Chưa có thuốc nào
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
