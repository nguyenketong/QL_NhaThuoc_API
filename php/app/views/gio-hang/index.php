<?php
/**
 * Giỏ hàng 
 */
// Đảm bảo $gioHang là mảng
if (!isset($gioHang) || !is_array($gioHang)) {
    $gioHang = [];
}

$sanPhamKhaDung = [];
$sanPhamKhongKhaDung = [];
$tongTienChon = 0;
$soSanPhamChon = 0;
$tatCaDuocChon = false;

if (!empty($gioHang)) {
    $sanPhamKhaDung = array_filter($gioHang, function($item) {
        return empty($item['KhongKhaDung']);
    });
    $sanPhamKhongKhaDung = array_filter($gioHang, function($item) {
        return !empty($item['KhongKhaDung']);
    });

    foreach ($gioHang as $item) {
        if (!empty($item['DuocChon']) && empty($item['KhongKhaDung'])) {
            $tongTienChon += ($item['GiaBan'] ?? 0) * ($item['SoLuong'] ?? 0);
            $soSanPhamChon++;
        }
    }

    $tatCaDuocChon = count($sanPhamKhaDung) > 0 && count(array_filter($sanPhamKhaDung, function($item) {
        return !empty($item['DuocChon']);
    })) == count($sanPhamKhaDung);
}
?>

<div class="container my-4">
    <h1 class="mb-4"><i class="bi bi-cart"></i> Giỏ hàng của bạn</h1>

    <?php if (!empty($_SESSION['flash']['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle"></i> <?= $_SESSION['flash']['success']; unset($_SESSION['flash']['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($_SESSION['flash']['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-circle"></i> <?= $_SESSION['flash']['error']; unset($_SESSION['flash']['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($gioHang)): ?>
        <?php if (!empty($sanPhamKhongKhaDung)): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-circle-fill"></i> 
                <strong>Lưu ý:</strong> Một số sản phẩm đã hết hàng hoặc ngừng kinh doanh.
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-8">
                <?php if (!empty($sanPhamKhaDung)): ?>
                    <div class="card mb-4">
                        <div class="card-header bg-white d-flex align-items-center">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="chonTatCa" 
                                       <?= $tatCaDuocChon ? 'checked' : '' ?>
                                       onchange="chonTatCaSanPham(this.checked)">
                                <label class="form-check-label fw-bold" for="chonTatCa">
                                    Chọn tất cả (<?= count($sanPhamKhaDung) ?> sản phẩm)
                                </label>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:40px;"></th>
                                        <th>Sản phẩm</th>
                                        <th class="text-end">Đơn giá</th>
                                        <th class="text-center">Số lượng</th>
                                        <th class="text-end">Thành tiền</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($sanPhamKhaDung as $item): ?>
                                        <?php 
                                        $thanhTien = $item['GiaBan'] * $item['SoLuong'];
                                        $sapHetHang = isset($item['SoLuongTon']) && $item['SoLuongTon'] > 0 && $item['SoLuongTon'] <= 5;
                                        ?>
                                        <tr class="<?= $sapHetHang ? 'table-warning' : '' ?>">
                                            <td class="align-middle">
                                                <input class="form-check-input item-checkbox" type="checkbox" 
                                                       data-mathuoc="<?= $item['MaThuoc'] ?>" 
                                                       data-thanhtien="<?= $thanhTien ?>"
                                                       <?= !empty($item['DuocChon']) ? 'checked' : '' ?>
                                                       onchange="capNhatChonSanPham(<?= $item['MaThuoc'] ?>, this.checked)">
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <?php 
                                                    $hinhAnh = $item['HinhAnh'] ?? '';
                                                    if (!empty($hinhAnh)) {
                                                        if (strpos($hinhAnh, 'http') === 0 || strpos($hinhAnh, BASE_URL) === 0) {
                                                            $imgSrc = $hinhAnh;
                                                        } else {
                                                            $imgSrc = BASE_URL . $hinhAnh;
                                                        }
                                                    } else {
                                                        $imgSrc = BASE_URL . '/assets/images/no-image.svg';
                                                    }
                                                    ?>
                                                    <img src="<?= $imgSrc ?>" 
                                                         alt="<?= htmlspecialchars($item['TenThuoc']) ?>" 
                                                         style="width:60px;height:60px;object-fit:contain;"
                                                         onerror="this.src='<?= BASE_URL ?>/assets/images/no-image.svg'">
                                                    <div>
                                                        <strong><?= htmlspecialchars($item['TenThuoc']) ?></strong>
                                                        <?php if ($sapHetHang): ?>
                                                            <div class="text-warning small">
                                                                <i class="bi bi-exclamation-triangle"></i> Còn <?= $item['SoLuongTon'] ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end align-middle"><?= number_format($item['GiaBan'], 0, ',', '.') ?>đ</td>
                                            <td class="text-center align-middle">
                                                <div class="input-group input-group-sm" style="width:100px;margin:0 auto;">
                                                    <button type="button" class="btn btn-outline-secondary" 
                                                            onclick="giamSoLuong(<?= $item['MaThuoc'] ?>, <?= $item['SoLuong'] ?>)">-</button>
                                                    <input type="number" value="<?= $item['SoLuong'] ?>" min="1" 
                                                           max="<?= $item['SoLuongTon'] ?? 999 ?>" 
                                                           class="form-control text-center" 
                                                           onchange="capNhatSoLuong(<?= $item['MaThuoc'] ?>, this.value)"
                                                           id="soLuong_<?= $item['MaThuoc'] ?>">
                                                    <button type="button" class="btn btn-outline-secondary" 
                                                            onclick="tangSoLuong(<?= $item['MaThuoc'] ?>, <?= $item['SoLuongTon'] ?? 999 ?>)">+</button>
                                                </div>
                                            </td>
                                            <td class="text-end align-middle">
                                                <strong class="text-danger"><?= number_format($thanhTien, 0, ',', '.') ?>đ</strong>
                                            </td>
                                            <td class="align-middle">
                                                <a href="<?= BASE_URL ?>/gioHang/xoa/<?= $item['MaThuoc'] ?>" 
                                                   class="btn btn-sm btn-outline-danger"
                                                   onclick="return confirm('Xóa sản phẩm này?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($sanPhamKhongKhaDung)): ?>
                    <div class="card mb-4 border-danger">
                        <div class="card-header bg-danger text-white">
                            <i class="bi bi-exclamation-triangle"></i> Sản phẩm không khả dụng (<?= count($sanPhamKhongKhaDung) ?>)
                        </div>
                        <div class="card-body p-0">
                            <table class="table mb-0">
                                <tbody>
                                    <?php foreach ($sanPhamKhongKhaDung as $item): ?>
                                        <?php 
                                        $giaBan = $item['GiaBan'] ?? 0;
                                        $soLuong = $item['SoLuong'] ?? 0;
                                        $thanhTien = $giaBan * $soLuong; 
                                        ?>
                                        <tr style="opacity:0.5;">
                                            <td style="width:40px;"><input type="checkbox" disabled></td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <?php 
                                                    $hinhAnh2 = $item['HinhAnh'] ?? '';
                                                    if (!empty($hinhAnh2)) {
                                                        if (strpos($hinhAnh2, 'http') === 0 || strpos($hinhAnh2, BASE_URL) === 0) {
                                                            $imgSrc2 = $hinhAnh2;
                                                        } else {
                                                            $imgSrc2 = BASE_URL . $hinhAnh2;
                                                        }
                                                    } else {
                                                        $imgSrc2 = BASE_URL . '/assets/images/no-image.svg';
                                                    }
                                                    ?>
                                                    <img src="<?= $imgSrc2 ?>" 
                                                         style="width:60px;height:60px;object-fit:contain;filter:grayscale(100%);">
                                                    <div>
                                                        <del><?= htmlspecialchars($item['TenThuoc'] ?? 'Sản phẩm') ?></del>
                                                        <div class="text-danger small">
                                                            <i class="bi bi-x-circle"></i> 
                                                            <?= !empty($item['NgungKinhDoanh']) ? 'Ngừng kinh doanh' : 'Hết hàng' ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end align-middle text-muted"><?= number_format($giaBan, 0, ',', '.') ?>đ</td>
                                            <td class="text-center align-middle">
                                                <span class="badge bg-danger">
                                                    <?= !empty($item['NgungKinhDoanh']) ? 'Ngừng KD' : 'Hết hàng' ?>
                                                </span>
                                            </td>
                                            <td class="text-end align-middle text-muted">
                                                <del><?= number_format($thanhTien, 0, ',', '.') ?>đ</del>
                                            </td>
                                            <td class="align-middle">
                                                <a href="<?= BASE_URL ?>/gioHang/xoa/<?= $item['MaThuoc'] ?? 0 ?>" class="btn btn-sm btn-danger">
                                                    <i class="bi bi-trash"></i> Xóa
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="d-flex justify-content-between">
                    <a href="<?= BASE_URL ?>/thuoc/danhSach" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left"></i> Tiếp tục mua
                    </a>
                    <a href="<?= BASE_URL ?>/gioHang/xoaTatCa" class="btn btn-outline-danger" 
                       onclick="return confirm('Xóa toàn bộ giỏ hàng?')">
                        <i class="bi bi-trash"></i> Xóa tất cả
                    </a>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card position-sticky" style="top:100px;">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Tổng đơn hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Đã chọn:</span>
                            <strong id="soSanPhamChon"><?= $soSanPhamChon ?> sản phẩm</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tạm tính:</span>
                            <strong id="tongTienTamTinh"><?= number_format($tongTienChon, 0, ',', '.') ?>đ</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Phí ship:</span>
                            <strong>Miễn phí</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Tổng:</strong>
                            <strong class="text-danger fs-5" id="tongTienCong"><?= number_format($tongTienChon, 0, ',', '.') ?>đ</strong>
                        </div>
                        <div class="d-grid" id="btnContainer">
                            <?php if ($soSanPhamChon > 0): ?>
                                <a href="<?= BASE_URL ?>/gioHang/thanhToan" class="btn btn-primary btn-lg" id="btnThanhToan">
                                    <i class="bi bi-credit-card"></i> Thanh toán (<span id="soLuongTT"><?= $soSanPhamChon ?></span>)
                                </a>
                            <?php else: ?>
                                <button class="btn btn-secondary btn-lg" disabled id="btnDisabled">
                                    Chọn sản phẩm để thanh toán
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <i class="bi bi-cart-x" style="font-size:5rem;color:#ccc;"></i>
            <h3 class="mt-3">Giỏ hàng trống</h3>
            <a href="<?= BASE_URL ?>/thuoc/danhSach" class="btn btn-primary mt-2">Mua sắm ngay</a>
        </div>
    <?php endif; ?>
</div>

<script>
function tangSoLuong(maThuoc, max) {
    const input = document.getElementById('soLuong_' + maThuoc);
    if (parseInt(input.value) < max) {
        input.value = parseInt(input.value) + 1;
        capNhatSoLuong(maThuoc, input.value);
    } else {
        alert('Chỉ còn ' + max + ' sản phẩm!');
    }
}

function giamSoLuong(maThuoc, currentQty) {
    if (currentQty > 1) {
        const input = document.getElementById('soLuong_' + maThuoc);
        input.value = parseInt(input.value) - 1;
        capNhatSoLuong(maThuoc, input.value);
    } else {
        if (confirm('Xóa sản phẩm này khỏi giỏ hàng?')) {
            window.location.href = '<?= BASE_URL ?>/gioHang/xoa/' + maThuoc;
        }
    }
}

function capNhatSoLuong(maThuoc, soLuong) {
    fetch('<?= BASE_URL ?>/gioHang/capNhatSoLuong', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `maThuoc=${maThuoc}&soLuong=${soLuong}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    });
}

function capNhatChonSanPham(maThuoc, chon) {
    fetch('<?= BASE_URL ?>/gioHang/capNhatChon', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `maThuoc=${maThuoc}&duocChon=${chon}`
    })
    .then(r => r.json())
    .then(() => capNhatUI());
}

function chonTatCaSanPham(chon) {
    fetch('<?= BASE_URL ?>/gioHang/chonTatCa', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `chon=${chon}`
    })
    .then(r => r.json())
    .then(() => {
        document.querySelectorAll('.item-checkbox').forEach(c => c.checked = chon);
        capNhatUI();
    });
}

function capNhatUI() {
    let tong = 0, sl = 0;
    document.querySelectorAll('.item-checkbox:checked').forEach(c => {
        tong += parseFloat(c.dataset.thanhtien);
        sl++;
    });
    
    document.getElementById('tongTienTamTinh').textContent = tong.toLocaleString('vi-VN') + 'đ';
    document.getElementById('tongTienCong').textContent = tong.toLocaleString('vi-VN') + 'đ';
    document.getElementById('soSanPhamChon').textContent = sl + ' sản phẩm';
    
    const container = document.getElementById('btnContainer');
    if (sl > 0) {
        container.innerHTML = `<a href="<?= BASE_URL ?>/gioHang/thanhToan" class="btn btn-primary btn-lg">
            <i class="bi bi-credit-card"></i> Thanh toán (${sl})
        </a>`;
    } else {
        container.innerHTML = `<button class="btn btn-secondary btn-lg" disabled>Chọn sản phẩm để thanh toán</button>`;
    }
    
    // Cập nhật checkbox "Chọn tất cả"
    const all = document.querySelectorAll('.item-checkbox');
    const checked = document.querySelectorAll('.item-checkbox:checked');
    document.getElementById('chonTatCa').checked = all.length > 0 && all.length === checked.length;
}
</script>
