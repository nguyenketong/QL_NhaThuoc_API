<?php
/**
 * Thanh toán
 */
if (!isset($gioHang) || !is_array($gioHang)) {
    $gioHang = [];
}
if (!isset($nguoiDung) || !is_array($nguoiDung)) {
    $nguoiDung = [];
}

$tongTien = 0;
foreach ($gioHang as $item) {
    $tongTien += ($item['GiaBan'] ?? 0) * ($item['SoLuong'] ?? 0);
}
?>
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/gioHang">Giỏ hàng</a></li>
            <li class="breadcrumb-item active">Thanh toán</li>
        </ol>
    </nav>

    <h3 class="mb-4"><i class="fas fa-credit-card"></i> Thanh toán</h3>

    <form action="<?= BASE_URL ?>/gioHang/datHang" method="POST" id="formThanhToan">
        <div class="row">
            <div class="col-lg-8">
                <!-- Thông tin giao hàng -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-truck"></i> Thông tin giao hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Họ tên người nhận <span class="text-danger">*</span></label>
                                <input type="text" name="hoTen" class="form-control" value="<?= htmlspecialchars($nguoiDung['HoTen'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="text" name="soDienThoai" class="form-control" value="<?= htmlspecialchars($nguoiDung['SoDienThoai'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                                <select name="tinhThanh" id="tinhThanh" class="form-select" required>
                                    <option value="">-- Chọn Tỉnh/Thành phố --</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Quận/Huyện <span class="text-danger">*</span></label>
                                <select name="quanHuyen" id="quanHuyen" class="form-select" required disabled>
                                    <option value="">-- Chọn Quận/Huyện --</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Phường/Xã <span class="text-danger">*</span></label>
                                <select name="phuongXa" id="phuongXa" class="form-select" required disabled>
                                    <option value="">-- Chọn Phường/Xã --</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Địa chỉ cụ thể <span class="text-danger">*</span></label>
                                <input type="text" name="diaChiCuThe" id="diaChiCuThe" class="form-control" placeholder="Số nhà, tên đường, tòa nhà..." required>
                            </div>
                            <!-- Hidden field để lưu địa chỉ đầy đủ -->
                            <input type="hidden" name="diaChiGiaoHang" id="diaChiGiaoHang">
                        </div>
                    </div>
                </div>

                <!-- Phương thức thanh toán -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-wallet"></i> Phương thức thanh toán</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-check payment-option p-3 border rounded">
                                    <input class="form-check-input" type="radio" name="phuongThucThanhToan" value="Tiền mặt" id="tienMat" checked>
                                    <label class="form-check-label d-flex align-items-center" for="tienMat">
                                        <i class="fas fa-money-bill-wave fa-2x text-success me-3"></i>
                                        <div>
                                            <strong>Thanh toán khi nhận hàng</strong>
                                            <small class="d-block text-muted">COD - Trả tiền mặt khi nhận</small>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check payment-option p-3 border rounded">
                                    <input class="form-check-input" type="radio" name="phuongThucThanhToan" value="Chuyển khoản" id="chuyenKhoan">
                                    <label class="form-check-label d-flex align-items-center" for="chuyenKhoan">
                                        <i class="fas fa-university fa-2x text-primary me-3"></i>
                                        <div>
                                            <strong>Chuyển khoản ngân hàng</strong>
                                            <small class="d-block text-muted">Quét mã QR để thanh toán</small>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Danh sách sản phẩm -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-shopping-bag"></i> Sản phẩm (<?= count($gioHang) ?>)</h5>
                    </div>
                    <div class="card-body p-0">
                        <?php foreach ($gioHang as $item): 
                            $hinhAnh = $item['HinhAnh'] ?? '';
                            if (!empty($hinhAnh) && strpos($hinhAnh, 'http') !== 0 && strpos($hinhAnh, BASE_URL) !== 0) {
                                $hinhAnh = BASE_URL . $hinhAnh;
                            }
                            if (empty($hinhAnh)) $hinhAnh = BASE_URL . '/assets/images/no-image.svg';
                        ?>
                            <div class="d-flex align-items-center p-3 border-bottom">
                                <img src="<?= $hinhAnh ?>" alt="" style="width: 60px; height: 60px; object-fit: contain;" class="rounded me-3">
                                <div class="flex-grow-1">
                                    <h6 class="mb-0"><?= htmlspecialchars($item['TenThuoc'] ?? 'Sản phẩm') ?></h6>
                                    <small class="text-muted">Số lượng: <?= $item['SoLuong'] ?? 0 ?></small>
                                </div>
                                <div class="text-danger fw-bold">
                                    <?= number_format(($item['GiaBan'] ?? 0) * ($item['SoLuong'] ?? 0), 0, ',', '.') ?>đ
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Tổng tiền -->
            <div class="col-lg-4">
                <div class="card sticky-top" style="top: 100px;">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="fas fa-receipt"></i> Tổng đơn hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tạm tính:</span>
                            <span><?= number_format($tongTien, 0, ',', '.') ?>đ</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Phí vận chuyển:</span>
                            <span class="text-success">Miễn phí</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Tổng cộng:</strong>
                            <strong class="text-danger h4"><?= number_format($tongTien, 0, ',', '.') ?>đ</strong>
                        </div>
                        <button type="submit" class="btn btn-danger btn-lg w-100">
                            <i class="fas fa-check-circle"></i> Đặt hàng
                        </button>
                        <a href="<?= BASE_URL ?>/gioHang" class="btn btn-outline-secondary w-100 mt-2">
                            <i class="fas fa-arrow-left"></i> Quay lại giỏ hàng
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.payment-option { cursor: pointer; transition: all 0.3s; }
.payment-option:hover { border-color: #0d6efd !important; background: #f8f9fa; }
.payment-option:has(input:checked) { border-color: #0d6efd !important; background: #e7f1ff; }
</style>

<script>
// API tỉnh thành Việt Nam
const API_URL = 'https://provinces.open-api.vn/api';

document.addEventListener('DOMContentLoaded', function() {
    loadTinhThanh();
    
    document.getElementById('tinhThanh').addEventListener('change', function() {
        loadQuanHuyen(this.value);
        document.getElementById('phuongXa').innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
        document.getElementById('phuongXa').disabled = true;
        updateDiaChiDayDu();
    });
    
    document.getElementById('quanHuyen').addEventListener('change', function() {
        loadPhuongXa(this.value);
        updateDiaChiDayDu();
    });
    
    document.getElementById('phuongXa').addEventListener('change', updateDiaChiDayDu);
    document.getElementById('diaChiCuThe').addEventListener('input', updateDiaChiDayDu);
    
    // Validate form trước khi submit
    document.getElementById('formThanhToan').addEventListener('submit', function(e) {
        updateDiaChiDayDu();
        if (!document.getElementById('diaChiGiaoHang').value) {
            e.preventDefault();
            alert('Vui lòng nhập đầy đủ địa chỉ giao hàng!');
        }
    });
});

async function loadTinhThanh() {
    try {
        const res = await fetch(API_URL + '/p/');
        const data = await res.json();
        const select = document.getElementById('tinhThanh');
        data.forEach(tinh => {
            select.innerHTML += `<option value="${tinh.code}" data-name="${tinh.name}">${tinh.name}</option>`;
        });
    } catch(e) {
        console.error('Lỗi load tỉnh thành:', e);
    }
}

async function loadQuanHuyen(tinhCode) {
    const select = document.getElementById('quanHuyen');
    select.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
    
    if (!tinhCode) {
        select.disabled = true;
        return;
    }
    
    try {
        const res = await fetch(API_URL + '/p/' + tinhCode + '?depth=2');
        const data = await res.json();
        data.districts.forEach(quan => {
            select.innerHTML += `<option value="${quan.code}" data-name="${quan.name}">${quan.name}</option>`;
        });
        select.disabled = false;
    } catch(e) {
        console.error('Lỗi load quận huyện:', e);
    }
}

async function loadPhuongXa(quanCode) {
    const select = document.getElementById('phuongXa');
    select.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
    
    if (!quanCode) {
        select.disabled = true;
        return;
    }
    
    try {
        const res = await fetch(API_URL + '/d/' + quanCode + '?depth=2');
        const data = await res.json();
        data.wards.forEach(phuong => {
            select.innerHTML += `<option value="${phuong.code}" data-name="${phuong.name}">${phuong.name}</option>`;
        });
        select.disabled = false;
    } catch(e) {
        console.error('Lỗi load phường xã:', e);
    }
}

function updateDiaChiDayDu() {
    const tinhThanh = document.getElementById('tinhThanh');
    const quanHuyen = document.getElementById('quanHuyen');
    const phuongXa = document.getElementById('phuongXa');
    const diaChiCuThe = document.getElementById('diaChiCuThe').value.trim();
    
    const tinhName = tinhThanh.selectedOptions[0]?.dataset.name || '';
    const quanName = quanHuyen.selectedOptions[0]?.dataset.name || '';
    const phuongName = phuongXa.selectedOptions[0]?.dataset.name || '';
    
    let diaChi = [];
    if (diaChiCuThe) diaChi.push(diaChiCuThe);
    if (phuongName) diaChi.push(phuongName);
    if (quanName) diaChi.push(quanName);
    if (tinhName) diaChi.push(tinhName);
    
    document.getElementById('diaChiGiaoHang').value = diaChi.join(', ');
}
</script>
