<div class="row">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-warning text-dark"><i class="fas fa-edit"></i> Sửa tác dụng phụ</div>
            <div class="card-body">
                <form action="<?= BASE_URL ?>/admin/?controller=tac-dung-phu&action=edit&id=<?= $tacDungPhu['MaTacDungPhu'] ?>" method="post">
                    <div class="mb-3">
                        <label class="form-label">Tên tác dụng phụ <span class="text-danger">*</span></label>
                        <input type="text" name="TenTacDungPhu" class="form-control" required value="<?= htmlspecialchars($tacDungPhu['TenTacDungPhu'] ?? '') ?>" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="MoTa" class="form-control" rows="3"><?= htmlspecialchars($tacDungPhu['MoTa'] ?? '') ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Cập nhật</button>
                    <a href="<?= BASE_URL ?>/admin/?controller=tac-dung-phu" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
                </form>
            </div>
        </div>
    </div>
</div>
