<div class="row">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white"><i class="fas fa-plus"></i> Thêm thành phần</div>
            <div class="card-body">
                <form action="<?= BASE_URL ?>/admin/?controller=thanh-phan&action=create" method="post">
                    <div class="mb-3">
                        <label class="form-label">Tên thành phần <span class="text-danger">*</span></label>
                        <input type="text" name="TenThanhPhan" class="form-control" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="MoTa" class="form-control" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Lưu</button>
                    <a href="<?= BASE_URL ?>/admin/?controller=thanh-phan" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
                </form>
            </div>
        </div>
    </div>
</div>
