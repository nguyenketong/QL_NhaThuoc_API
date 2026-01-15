<div class="row">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-warning text-dark"><i class="fas fa-edit"></i> Sửa đối tượng sử dụng</div>
            <div class="card-body">
                <form action="<?= BASE_URL ?>/admin/?controller=doi-tuong&action=edit&id=<?= $doiTuong['MaDoiTuong'] ?>" method="post">
                    <div class="mb-3">
                        <label class="form-label">Tên đối tượng <span class="text-danger">*</span></label>
                        <input type="text" name="TenDoiTuong" class="form-control" required value="<?= htmlspecialchars($doiTuong['TenDoiTuong'] ?? '') ?>" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="MoTa" class="form-control" rows="3"><?= htmlspecialchars($doiTuong['MoTa'] ?? '') ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Cập nhật</button>
                    <a href="<?= BASE_URL ?>/admin/?controller=doi-tuong" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
                </form>
            </div>
        </div>
    </div>
</div>
