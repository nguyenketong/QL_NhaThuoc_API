<div class="d-flex justify-content-between align-items-center mb-4">
    <h6 class="mb-0">Danh sách bài viết</h6>
    <a href="<?= BASE_URL ?>/admin/?controller=bai-viet&action=create" class="btn btn-success">
        <i class="fas fa-plus"></i> Thêm bài viết
    </a>
</div>

<div class="admin-table">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Hình</th>
                <th>Tiêu đề</th>
                <th>Ngày đăng</th>
                <th>Lượt xem</th>
                <th>Nổi bật</th>
                <th>Trạng thái</th>
                <th width="180">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($danhSach)): ?>
                <?php foreach ($danhSach as $item): ?>
                    <tr>
                        <td><?= $item['MaBaiViet'] ?></td>
                        <td>
                            <?php if ($item['HinhAnh']): ?>
                                <img src="<?= htmlspecialchars($item['HinhAnh']) ?>" alt="" style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;">
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td><strong><?= htmlspecialchars($item['TieuDe']) ?></strong></td>
                        <td><?= date('d/m/Y', strtotime($item['NgayDang'])) ?></td>
                        <td><?= $item['LuotXem'] ?? 0 ?></td>
                        <td>
                            <form action="<?= BASE_URL ?>/admin/?controller=bai-viet&action=toggleNoiBat&id=<?= $item['MaBaiViet'] ?>" method="post" class="d-inline">
                                <?php if ($item['IsNoiBat']): ?>
                                    <button type="submit" class="btn btn-sm btn-warning" title="Bỏ nổi bật">
                                        <i class="fas fa-star"></i>
                                    </button>
                                <?php else: ?>
                                    <button type="submit" class="btn btn-sm btn-outline-warning" title="Đánh dấu nổi bật">
                                        <i class="far fa-star"></i>
                                    </button>
                                <?php endif; ?>
                            </form>
                        </td>
                        <td>
                            <form action="<?= BASE_URL ?>/admin/?controller=bai-viet&action=toggleActive&id=<?= $item['MaBaiViet'] ?>" method="post" class="d-inline">
                                <?php if ($item['IsActive']): ?>
                                    <button type="submit" class="btn btn-sm btn-success" title="Đang hiển thị">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                <?php else: ?>
                                    <button type="submit" class="btn btn-sm btn-secondary" title="Đang ẩn">
                                        <i class="fas fa-eye-slash"></i>
                                    </button>
                                <?php endif; ?>
                            </form>
                        </td>
                        <td>
                            <a href="<?= BASE_URL ?>/admin/?controller=bai-viet&action=edit&id=<?= $item['MaBaiViet'] ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="<?= BASE_URL ?>/admin/?controller=bai-viet&action=delete&id=<?= $item['MaBaiViet'] ?>" method="post" class="d-inline"
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
                    <td colspan="8" class="text-center text-muted py-4">Chưa có bài viết nào</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
