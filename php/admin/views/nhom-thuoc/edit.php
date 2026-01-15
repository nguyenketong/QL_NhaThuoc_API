<div class="row">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-warning text-dark">
                <i class="fas fa-edit"></i> Sửa nhóm thuốc
            </div>
            <div class="card-body">
                <form action="<?= BASE_URL ?>/admin/?controller=nhom-thuoc&action=edit&id=<?= $nhomThuoc['MaNhomThuoc'] ?>" method="post">
                    <div class="mb-3">
                        <label class="form-label">Tên nhóm thuốc <span class="text-danger">*</span></label>
                        <input type="text" name="TenNhomThuoc" class="form-control" required value="<?= htmlspecialchars($nhomThuoc['TenNhomThuoc'] ?? '') ?>" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Danh mục cha</label>
                        <select name="MaDanhMucCha" class="form-select">
                            <option value="">-- Không có (Danh mục gốc) --</option>
                            <?php foreach ($danhMucChaList ?? [] as $dm): ?>
                                <option value="<?= $dm['MaNhomThuoc'] ?>" <?= ($nhomThuoc['MaDanhMucCha'] ?? '') == $dm['MaNhomThuoc'] ? 'selected' : '' ?>><?= htmlspecialchars($dm['TenNhomThuoc']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="MoTa" class="form-control" rows="3"><?= htmlspecialchars($nhomThuoc['MoTa'] ?? '') ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Cập nhật
                    </button>
                    <a href="<?= BASE_URL ?>/admin/?controller=nhom-thuoc" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>
