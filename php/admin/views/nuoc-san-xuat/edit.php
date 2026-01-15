<div class="row">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-warning text-dark"><i class="fas fa-edit"></i> Sửa nước sản xuất</div>
            <div class="card-body">
                <form action="<?= BASE_URL ?>/admin/?controller=nuoc-san-xuat&action=edit&id=<?= $nuocSX['MaNuocSX'] ?>" method="post">
                    <div class="mb-3">
                        <label class="form-label">Tên nước sản xuất <span class="text-danger">*</span></label>
                        <input type="text" name="TenNuocSX" class="form-control" required value="<?= htmlspecialchars($nuocSX['TenNuocSX'] ?? '') ?>" />
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Cập nhật</button>
                    <a href="<?= BASE_URL ?>/admin/?controller=nuoc-san-xuat" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
                </form>
            </div>
        </div>
    </div>
</div>
