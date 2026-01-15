<?php
/**
 * Xác nhận OTP - Có đếm ngược thời gian
 */
$otpTime = $_SESSION['otp_time'] ?? time();
$expireTime = $otpTime + 300; // 5 phút
$remainingSeconds = max(0, $expireTime - time());
?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0"><i class="bi bi-shield-check"></i> Xác nhận OTP</h4>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted text-center mb-3">
                        Mã OTP đã được gửi đến số <strong><?= htmlspecialchars($soDienThoai) ?></strong>
                    </p>

                    <!-- Đếm ngược thời gian -->
                    <div class="text-center mb-3">
                        <span class="badge bg-warning text-dark fs-6" id="countdown">
                            <i class="bi bi-clock"></i> Còn lại: <span id="timer"><?= gmdate("i:s", $remainingSeconds) ?></span>
                        </span>
                    </div>

                    <?php if (!empty($devOtp)): ?>
                        <div class="alert alert-info text-center py-2">
                            <small><i class="bi bi-info-circle"></i> <strong>DEV:</strong> Mã OTP là <code class="fs-5"><?= $devOtp ?></code></small>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($_SESSION['flash']['error'])): ?>
                        <div class="alert alert-danger py-2">
                            <?= $_SESSION['flash']['error']; unset($_SESSION['flash']['error']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form action="<?= BASE_URL ?>/user/confirmOtp" method="POST" id="otpForm">
                        <div class="mb-4">
                            <label class="form-label">Mã OTP</label>
                            <input type="text" name="otp" class="form-control form-control-lg text-center" 
                                   placeholder="000000" required maxlength="6" pattern="[0-9]{6}"
                                   style="letter-spacing: 10px; font-size: 24px;" id="otpInput" autofocus>
                            <small class="text-muted">Nhập mã 6 số đã gửi đến điện thoại</small>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100" id="submitBtn">
                            <i class="bi bi-check-lg"></i> Xác nhận
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <p class="text-muted mb-2">Không nhận được mã?</p>
                        <a href="<?= BASE_URL ?>/user/phoneLogin" class="btn btn-outline-secondary btn-sm" id="resendBtn">
                            <i class="bi bi-arrow-repeat"></i> Gửi lại mã mới
                        </a>
                    </div>

                    <div class="text-center mt-3">
                        <a href="<?= BASE_URL ?>/user/phoneLogin" class="text-muted small">
                            <i class="bi bi-arrow-left"></i> Đổi số điện thoại
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal hết hạn OTP -->
<div class="modal fade" id="expiredModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <i class="bi bi-clock-history text-danger" style="font-size: 4rem;"></i>
                <h4 class="mt-3">Mã OTP đã hết hạn!</h4>
                <p class="text-muted">Vui lòng yêu cầu gửi lại mã OTP mới.</p>
                <a href="<?= BASE_URL ?>/user/phoneLogin" class="btn btn-primary">
                    <i class="bi bi-arrow-repeat"></i> Gửi lại mã OTP
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Đếm ngược thời gian
let remainingSeconds = <?= $remainingSeconds ?>;
const timerEl = document.getElementById('timer');
const countdownEl = document.getElementById('countdown');
const otpForm = document.getElementById('otpForm');
const submitBtn = document.getElementById('submitBtn');
const otpInput = document.getElementById('otpInput');

function updateTimer() {
    if (remainingSeconds <= 0) {
        // Hết hạn
        countdownEl.classList.remove('bg-warning');
        countdownEl.classList.add('bg-danger', 'text-white');
        timerEl.textContent = 'Hết hạn!';
        
        // Disable form
        otpInput.disabled = true;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-x-circle"></i> Mã đã hết hạn';
        submitBtn.classList.remove('btn-primary');
        submitBtn.classList.add('btn-secondary');
        
        // Hiện modal
        const modal = new bootstrap.Modal(document.getElementById('expiredModal'));
        modal.show();
        return;
    }
    
    const minutes = Math.floor(remainingSeconds / 60);
    const seconds = remainingSeconds % 60;
    timerEl.textContent = String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
    
    // Đổi màu khi còn ít thời gian
    if (remainingSeconds <= 60) {
        countdownEl.classList.remove('bg-warning');
        countdownEl.classList.add('bg-danger', 'text-white');
    }
    
    remainingSeconds--;
    setTimeout(updateTimer, 1000);
}

// Bắt đầu đếm ngược
updateTimer();

// Auto focus và chỉ cho nhập số
otpInput.addEventListener('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '');
});
</script>
