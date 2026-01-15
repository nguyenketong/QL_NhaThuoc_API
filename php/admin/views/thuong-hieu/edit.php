<div class="row">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-warning text-dark">
                <i class="fas fa-edit"></i> Sửa thương hiệu
            </div>
            <div class="card-body">
                <form action="<?= BASE_URL ?>/admin/?controller=thuong-hieu&action=edit&id=<?= $thuongHieu['MaThuongHieu'] ?>" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Tên thương hiệu <span class="text-danger">*</span></label>
                        <input type="text" name="TenThuongHieu" class="form-control" required value="<?= htmlspecialchars($thuongHieu['TenThuongHieu'] ?? '') ?>" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quốc gia</label>
                        <input type="text" name="QuocGia" class="form-control" value="<?= htmlspecialchars($thuongHieu['QuocGia'] ?? '') ?>" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Địa chỉ</label>
                        <input type="text" name="DiaChi" class="form-control" value="<?= htmlspecialchars($thuongHieu['DiaChi'] ?? '') ?>" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Logo</label>
                        <?php if (!empty($thuongHieu['HinhAnh'])): ?>
                            <div class="mb-2">
                                <img src="<?= htmlspecialchars($thuongHieu['HinhAnh']) ?>" alt="" style="max-width: 100px;">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="LogoFile" class="form-control" accept="image/*" />
                        <small class="text-muted">Hoặc nhập URL:</small>
                        <input type="text" name="HinhAnh" class="form-control mt-1" value="<?= htmlspecialchars($thuongHieu['HinhAnh'] ?? '') ?>" placeholder="https://..." />
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Cập nhật
                    </button>
                    <a href="<?= BASE_URL ?>/admin/?controller=thuong-hieu" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>
