<?php
/**
 * Thanh toán QR - VietQR API
 */
$noiDungCK = 'DH' . $donHang['MaDonHang'];
$soTien = $donHang['TongTien'];

// Tạo URL QR VietQR
$vietQrUrl = "https://img.vietqr.io/image/" . BANK_ID . "-" . BANK_ACCOUNT_NO . "-" . BANK_TEMPLATE . ".png";
$vietQrUrl .= "?amount=" . $soTien;
$vietQrUrl .= "&addInfo=" . urlencode($noiDungCK);
$vietQrUrl .= "&accountName=" . urlencode(BANK_ACCOUNT_NAME);
?>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0"><i class="fas fa-qrcode"></i> Thanh toán chuyển khoản</h4>
                </div>
                <div class="card-body text-center">
                    <div class="alert alert-info">
                        <strong>Đơn hàng #<?= $donHang['MaDonHang'] ?></strong><br>
                        Số tiền: <strong class="text-danger fs-4"><?= number_format($soTien, 0, ',', '.') ?>đ</strong>
                    </div>

                    <p class="text-muted">Quét mã QR bằng app ngân hàng để thanh toán</p>

                    <!-- VietQR Code -->
                    <div class="bg-light p-3 rounded mb-4">
                        <img src="<?= $vietQrUrl ?>" alt="VietQR Code" class="img-fluid" style="max-width: 280px;">
                    </div>

                    <div class="text-start">
                        <h6><i class="fas fa-university text-primary"></i> Thông tin chuyển khoản:</h6>
                        <table class="table table-sm table-bordered">
                            <tr>
                                <td class="bg-light" width="40%">Ngân hàng:</td>
                                <td><strong>MB Bank</strong></td>
                            </tr>
                            <tr>
                                <td class="bg-light">Số tài khoản:</td>
                                <td>
                                    <strong><?= BANK_ACCOUNT_NO ?></strong>
                                    <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="copyText('<?= BANK_ACCOUNT_NO ?>')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td class="bg-light">Chủ tài khoản:</td>
                                <td><strong><?= BANK_ACCOUNT_NAME ?></strong></td>
                            </tr>
                            <tr>
                                <td class="bg-light">Số tiền:</td>
                                <td>
                                    <strong class="text-danger"><?= number_format($soTien, 0, ',', '.') ?>đ</strong>
                                    <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="copyText('<?= $soTien ?>')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td class="bg-light">Nội dung CK:</td>
                                <td>
                                    <strong class="text-success"><?= $noiDungCK ?></strong>
                                    <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="copyText('<?= $noiDungCK ?>')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> 
                        <strong>Lưu ý:</strong> Vui lòng ghi đúng nội dung chuyển khoản <strong class="text-success"><?= $noiDungCK ?></strong> để đơn hàng được xử lý nhanh chóng!
                    </div>

                    <div class="d-flex gap-2 justify-content-center">
                        <a href="<?= BASE_URL ?>/donHang/chiTiet/<?= $donHang['MaDonHang'] ?>" class="btn btn-success">
                            <i class="fas fa-check"></i> Đã thanh toán
                        </a>
                        <a href="<?= BASE_URL ?>/donHang/danhSach" class="btn btn-outline-secondary">
                            <i class="fas fa-list"></i> Xem đơn hàng
                        </a>
                    </div>
                </div>
            </div>

            <!-- Hướng dẫn -->
            <div class="card mt-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-info-circle text-info"></i> Hướng dẫn thanh toán</h6>
                </div>
                <div class="card-body">
                    <ol class="mb-0">
                        <li>Mở app ngân hàng trên điện thoại</li>
                        <li>Chọn chức năng <strong>Quét QR</strong></li>
                        <li>Quét mã QR ở trên</li>
                        <li>Kiểm tra thông tin và xác nhận thanh toán</li>
                        <li>Đơn hàng sẽ được xử lý sau khi nhận được tiền</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyText(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('Đã sao chép: ' + text);
    });
}
</script>
