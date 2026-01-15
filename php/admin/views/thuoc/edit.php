<form action="<?= BASE_URL ?>/admin/?controller=thuoc&action=edit&id=<?= $thuoc['MaThuoc'] ?>" method="post" enctype="multipart/form-data">
    <div class="row">
        <!-- Thông tin cơ bản -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-info-circle"></i> Thông tin cơ bản
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Tên thuốc <span class="text-danger">*</span></label>
                            <input type="text" name="TenThuoc" class="form-control" required value="<?= htmlspecialchars($thuoc['TenThuoc'] ?? '') ?>" />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Đơn vị tính</label>
                            <input type="text" name="DonViTinh" class="form-control" value="<?= htmlspecialchars($thuoc['DonViTinh'] ?? '') ?>" />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nhóm thuốc <span class="text-danger">*</span></label>
                            <select name="MaNhomThuoc" class="form-select" required>
                                <option value="">-- Chọn nhóm --</option>
                                <?php foreach ($nhomThuocs ?? [] as $nt): ?>
                                    <option value="<?= $nt['MaNhomThuoc'] ?>" <?= ($thuoc['MaNhomThuoc'] ?? '') == $nt['MaNhomThuoc'] ? 'selected' : '' ?>><?= htmlspecialchars($nt['TenNhomThuoc']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Thương hiệu</label>
                            <select name="MaThuongHieu" class="form-select">
                                <option value="">-- Chọn thương hiệu --</option>
                                <?php foreach ($thuongHieus ?? [] as $th): ?>
                                    <option value="<?= $th['MaThuongHieu'] ?>" <?= ($thuoc['MaThuongHieu'] ?? '') == $th['MaThuongHieu'] ? 'selected' : '' ?>><?= htmlspecialchars($th['TenThuongHieu']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nước sản xuất</label>
                            <select name="MaNuocSX" class="form-select">
                                <option value="">-- Chọn nước --</option>
                                <?php foreach ($nuocSXs ?? [] as $nsx): ?>
                                    <option value="<?= $nsx['MaNuocSX'] ?>" <?= ($thuoc['MaNuocSX'] ?? '') == $nsx['MaNuocSX'] ? 'selected' : '' ?>><?= htmlspecialchars($nsx['TenNuocSX']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Mô tả</label>
                            <textarea name="MoTa" class="form-control" rows="3"><?= htmlspecialchars($thuoc['MoTa'] ?? '') ?></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Hình ảnh</label>
                            <?php if ($thuoc['HinhAnh']): ?>
                                <div class="mb-2">
                                    <img src="<?= htmlspecialchars($thuoc['HinhAnh']) ?>" alt="" style="max-width: 150px; border-radius: 8px;">
                                </div>
                            <?php endif; ?>
                            <input type="file" name="hinhAnhFile" class="form-control" accept="image/*" />
                            <small class="text-muted">Hoặc nhập URL:</small>
                            <input type="text" name="HinhAnh" class="form-control mt-1" value="<?= htmlspecialchars($thuoc['HinhAnh'] ?? '') ?>" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Giá & Kho -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-dollar-sign"></i> Giá & Tồn kho
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Giá bán <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="GiaBan" id="GiaBan" class="form-control" required value="<?= $thuoc['GiaBan'] ?? 0 ?>" />
                                <span class="input-group-text">đ</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Số lượng tồn</label>
                            <input type="number" name="SoLuongTon" class="form-control" value="<?= $thuoc['SoLuongTon'] ?? 0 ?>" />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Số lượng đã bán</label>
                            <input type="number" class="form-control" value="<?= $thuoc['SoLuongDaBan'] ?? 0 ?>" readonly />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Khuyến mãi -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-danger text-white">
                    <i class="fas fa-tags"></i> Khuyến mãi
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Giá gốc (trước KM)</label>
                            <div class="input-group">
                                <input type="number" name="GiaGoc" id="GiaGoc" class="form-control" value="<?= $thuoc['GiaGoc'] ?? '' ?>" />
                                <span class="input-group-text">đ</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Phần trăm giảm</label>
                            <div class="input-group">
                                <input type="number" name="PhanTramGiam" id="PhanTramGiam" class="form-control" min="0" max="100" value="<?= $thuoc['PhanTramGiam'] ?? '' ?>" />
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Ngày bắt đầu KM</label>
                            <input type="date" name="NgayBatDauKM" class="form-control" value="<?= $thuoc['NgayBatDauKM'] ? date('Y-m-d', strtotime($thuoc['NgayBatDauKM'])) : '' ?>" />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Ngày kết thúc KM</label>
                            <input type="date" name="NgayKetThucKM" class="form-control" value="<?= $thuoc['NgayKetThucKM'] ? date('Y-m-d', strtotime($thuoc['NgayKetThucKM'])) : '' ?>" />
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Đánh dấu sản phẩm</label>
                            <div class="d-flex gap-4 mt-2">
                                <div class="form-check">
                                    <input type="checkbox" name="IsHot" value="1" class="form-check-input" id="chkIsHot" <?= ($thuoc['IsHot'] ?? 0) ? 'checked' : '' ?> />
                                    <label class="form-check-label text-danger fw-bold" for="chkIsHot">🔥 Sản phẩm HOT</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" name="IsNew" value="1" class="form-check-input" id="chkIsNew" <?= ($thuoc['IsNew'] ?? 0) ? 'checked' : '' ?> />
                                    <label class="form-check-label text-success fw-bold" for="chkIsNew">🆕 Sản phẩm MỚI</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" name="IsActive" value="1" class="form-check-input" id="chkIsActive" <?= ($thuoc['IsActive'] ?? 1) ? 'checked' : '' ?> />
                                    <label class="form-check-label text-primary fw-bold" for="chkIsActive">✅ Đang kinh doanh</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Thành phần -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-flask"></i> Thành phần
                </div>
                <div class="card-body" id="thanhPhanContainer">
                    <?php if (!empty($selectedThanhPhans)): ?>
                        <?php foreach ($selectedThanhPhans as $stp): ?>
                            <div class="thanh-phan-row mb-2">
                                <div class="row g-2">
                                    <div class="col-7">
                                        <select name="ThanhPhanIds[]" class="form-select form-select-sm">
                                            <option value="">-- Chọn --</option>
                                            <?php foreach ($thanhPhans ?? [] as $tp): ?>
                                                <option value="<?= $tp['MaThanhPhan'] ?>" <?= $stp['MaThanhPhan'] == $tp['MaThanhPhan'] ? 'selected' : '' ?>><?= htmlspecialchars($tp['TenThanhPhan']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-5">
                                        <input type="text" name="HamLuongs[]" class="form-control form-control-sm" placeholder="Hàm lượng" value="<?= htmlspecialchars($stp['HamLuong'] ?? '') ?>" />
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="thanh-phan-row mb-2">
                            <div class="row g-2">
                                <div class="col-7">
                                    <select name="ThanhPhanIds[]" class="form-select form-select-sm">
                                        <option value="">-- Chọn --</option>
                                        <?php foreach ($thanhPhans ?? [] as $tp): ?>
                                            <option value="<?= $tp['MaThanhPhan'] ?>"><?= htmlspecialchars($tp['TenThanhPhan']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-5">
                                    <input type="text" name="HamLuongs[]" class="form-control form-control-sm" placeholder="Hàm lượng" />
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <button type="button" class="btn btn-sm btn-outline-success" onclick="themThanhPhan()">
                        <i class="fas fa-plus"></i> Thêm thành phần
                    </button>
                </div>
            </div>

            <!-- Tác dụng phụ -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <i class="fas fa-exclamation-triangle"></i> Tác dụng phụ
                </div>
                <div class="card-body" id="tacDungPhuContainer">
                    <?php if (!empty($selectedTacDungPhus)): ?>
                        <?php foreach ($selectedTacDungPhus as $stdp): ?>
                            <div class="tac-dung-phu-row mb-2">
                                <div class="row g-2">
                                    <div class="col-7">
                                        <select name="TacDungPhuIds[]" class="form-select form-select-sm">
                                            <option value="">-- Chọn --</option>
                                            <?php foreach ($tacDungPhus ?? [] as $tdp): ?>
                                                <option value="<?= $tdp['MaTacDungPhu'] ?>" <?= $stdp['MaTacDungPhu'] == $tdp['MaTacDungPhu'] ? 'selected' : '' ?>><?= htmlspecialchars($tdp['TenTacDungPhu']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-5">
                                        <select name="MucDos[]" class="form-select form-select-sm">
                                            <option value="">Mức độ</option>
                                            <option value="Nhẹ" <?= ($stdp['MucDo'] ?? '') == 'Nhẹ' ? 'selected' : '' ?>>Nhẹ</option>
                                            <option value="Trung bình" <?= ($stdp['MucDo'] ?? '') == 'Trung bình' ? 'selected' : '' ?>>Trung bình</option>
                                            <option value="Nặng" <?= ($stdp['MucDo'] ?? '') == 'Nặng' ? 'selected' : '' ?>>Nặng</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="tac-dung-phu-row mb-2">
                            <div class="row g-2">
                                <div class="col-7">
                                    <select name="TacDungPhuIds[]" class="form-select form-select-sm">
                                        <option value="">-- Chọn --</option>
                                        <?php foreach ($tacDungPhus ?? [] as $tdp): ?>
                                            <option value="<?= $tdp['MaTacDungPhu'] ?>"><?= htmlspecialchars($tdp['TenTacDungPhu']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-5">
                                    <select name="MucDos[]" class="form-select form-select-sm">
                                        <option value="">Mức độ</option>
                                        <option value="Nhẹ">Nhẹ</option>
                                        <option value="Trung bình">Trung bình</option>
                                        <option value="Nặng">Nặng</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <button type="button" class="btn btn-sm btn-outline-warning" onclick="themTacDungPhu()">
                        <i class="fas fa-plus"></i> Thêm tác dụng phụ
                    </button>
                </div>
            </div>

            <!-- Đối tượng sử dụng -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-users"></i> Đối tượng sử dụng
                </div>
                <div class="card-body">
                    <?php foreach ($doiTuongs ?? [] as $dt): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="DoiTuongIds[]" value="<?= $dt['MaDoiTuong'] ?>" id="dt_<?= $dt['MaDoiTuong'] ?>" <?= in_array($dt['MaDoiTuong'], $selectedDoiTuongs ?? []) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="dt_<?= $dt['MaDoiTuong'] ?>"><?= htmlspecialchars($dt['TenDoiTuong']) ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="fas fa-save"></i> Cập nhật
        </button>
        <a href="<?= BASE_URL ?>/admin/?controller=thuoc" class="btn btn-secondary btn-lg">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>
</form>

<script>
    // Tính giá bán tự động theo phần trăm giảm
    const giaBanInput = document.getElementById('GiaBan');
    const giaGocInput = document.getElementById('GiaGoc');
    const phanTramGiamInput = document.getElementById('PhanTramGiam');

    function tinhGiaBan() {
        const giaGoc = parseFloat(giaGocInput.value) || 0;
        const phanTram = parseFloat(phanTramGiamInput.value) || 0;
        
        if (giaGoc > 0 && phanTram >= 0 && phanTram < 100) {
            giaBanInput.value = Math.round(giaGoc * (100 - phanTram) / 100);
        }
    }

    giaGocInput.addEventListener('input', tinhGiaBan);
    phanTramGiamInput.addEventListener('input', tinhGiaBan);

    function themThanhPhan() {
        var container = document.getElementById('thanhPhanContainer');
        var firstRow = container.querySelector('.thanh-phan-row');
        var newRow = firstRow.cloneNode(true);
        newRow.querySelectorAll('select, input').forEach(el => el.value = '');
        container.appendChild(newRow);
    }

    function themTacDungPhu() {
        var container = document.getElementById('tacDungPhuContainer');
        var firstRow = container.querySelector('.tac-dung-phu-row');
        var newRow = firstRow.cloneNode(true);
        newRow.querySelectorAll('select').forEach(el => el.value = '');
        container.appendChild(newRow);
    }
</script>
