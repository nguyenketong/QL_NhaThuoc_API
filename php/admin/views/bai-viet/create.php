<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0"><i class="fas fa-plus-circle text-primary"></i> Thêm bài viết mới</h5>
    <a href="<?= BASE_URL ?>/admin/?controller=bai-viet" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
</div>

<form action="<?= BASE_URL ?>/admin/?controller=bai-viet&action=create" method="post" enctype="multipart/form-data">
    <div class="row">
        <!-- Cột trái: Nội dung bài viết -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white py-2">
                    <i class="fas fa-edit"></i> Nội dung bài viết
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                        <input type="text" name="TieuDe" class="form-control" placeholder="Nhập tiêu đề bài viết..." required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Mô tả ngắn</label>
                        <textarea name="MoTaNgan" class="form-control" rows="3" placeholder="Mô tả ngắn hiển thị ở trang chủ..." maxlength="500"></textarea>
                        <small class="text-muted">Tối đa 500 ký tự</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Nội dung chi tiết</label>
                        <textarea name="NoiDung" class="form-control" rows="12" placeholder="Nội dung chi tiết bài viết..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cột phải: Hình ảnh & Tùy chọn -->
        <div class="col-lg-4">
            <!-- Card Hình ảnh -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-success text-white py-2">
                    <i class="fas fa-image"></i> Hình ảnh
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Chọn hình ảnh</label>
                        <input type="file" name="hinhAnhFile" class="form-control" accept="image/*" onchange="previewImage(this)">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Hoặc nhập URL</label>
                        <input type="text" name="HinhAnh" class="form-control" placeholder="https://...">
                    </div>
                    <div id="imagePreview" class="text-center" style="display:none;">
                        <img src="" alt="Preview" class="img-fluid rounded" style="max-height: 150px;">
                    </div>
                </div>
            </div>

            <!-- Card Tùy chọn -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-info text-white py-2">
                    <i class="fas fa-cog"></i> Tùy chọn
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="IsNoiBat" value="1" class="form-check-input" id="chkNoiBat">
                            <label class="form-check-label" for="chkNoiBat">
                                <i class="fas fa-star text-warning"></i> Bài viết nổi bật
                            </label>
                            <div class="text-muted small">Hiển thị ở vị trí nổi trên trang chủ</div>
                        </div>
                    </div>
                    <div class="mb-0">
                        <div class="form-check">
                            <input type="checkbox" name="IsActive" value="1" class="form-check-input" id="chkActive" checked>
                            <label class="form-check-label" for="chkActive">
                                <i class="fas fa-check-circle text-success"></i> Hiển thị bài viết
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nút Lưu -->
            <button type="submit" class="btn btn-primary w-100 py-2">
                <i class="fas fa-save"></i> Lưu bài viết
            </button>
        </div>
    </div>
</form>

<script>
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const img = preview.querySelector('img');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
