<?php
/**
 * Footer Component
 */
?>
<footer class="main-footer pt-5 pb-3" style="background: #1a1f2e;">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="footer-brand">
                    <a href="<?= BASE_URL ?>" class="logo mb-3 d-inline-block text-decoration-none">
                        <i class="fas fa-clinic-medical fa-2x" style="color: #1a8ccc;"></i>
                        <span class="logo-text ms-2 fw-bold text-white fs-4">Nhà Thuốc Thanh Hoàn</span>
                    </a>
                    <p style="color: #a0aec0;">Hệ thống nhà thuốc uy tín hàng đầu Việt Nam. Cam kết cung cấp thuốc chính hãng, giá tốt nhất.</p>
                    <div class="social-links mt-3">
                        <a href="https://www.facebook.com" target="_blank" class="me-3" style="color: #a0aec0; font-size: 1.2rem;"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="me-3" style="color: #a0aec0; font-size: 1.2rem;"><i class="fab fa-youtube"></i></a>
                        <a href="#" class="me-3" style="color: #a0aec0; font-size: 1.2rem;"><i class="fas fa-comment-dots"></i></a>
                        <a href="#" style="color: #a0aec0; font-size: 1.2rem;"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <h5 class="text-white mb-3">Về chúng tôi</h5>
                <ul class="list-unstyled footer-links">
                    <li class="mb-2"><a href="<?= BASE_URL ?>/home/gioiThieu" style="color: #a0aec0; text-decoration: none;">Giới thiệu</a></li>
                    <li class="mb-2"><a href="#" style="color: #a0aec0; text-decoration: none;">Hệ thống cửa hàng</a></li>
                    <li class="mb-2"><a href="#" style="color: #a0aec0; text-decoration: none;">Giấy phép kinh doanh</a></li>
                    <li class="mb-2"><a href="#" style="color: #a0aec0; text-decoration: none;">Quy chế hoạt động</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-6">
                <h5 class="text-white mb-3">Danh mục</h5>
                <ul class="list-unstyled footer-links">
                    <li class="mb-2"><a href="<?= BASE_URL ?>/thuoc/danhSach" style="color: #a0aec0; text-decoration: none;">Thuốc giảm đau</a></li>
                    <li class="mb-2"><a href="<?= BASE_URL ?>/thuoc/danhSach" style="color: #a0aec0; text-decoration: none;">Vitamin & TPCN</a></li>
                    <li class="mb-2"><a href="<?= BASE_URL ?>/thuoc/danhSach" style="color: #a0aec0; text-decoration: none;">Thuốc cảm cúm</a></li>
                    <li class="mb-2"><a href="<?= BASE_URL ?>/nhom-thuoc" style="color: #a0aec0; text-decoration: none;">Xem tất cả</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-6">
                <h5 class="text-white mb-3">Hỗ trợ</h5>
                <ul class="list-unstyled footer-links">
                    <li class="mb-2"><a href="#" style="color: #a0aec0; text-decoration: none;">Hướng dẫn mua hàng</a></li>
                    <li class="mb-2"><a href="#" style="color: #a0aec0; text-decoration: none;">Chính sách đổi trả</a></li>
                    <li class="mb-2"><a href="#" style="color: #a0aec0; text-decoration: none;">Chính sách bảo mật</a></li>
                    <li class="mb-2"><a href="<?= BASE_URL ?>/home/lienHe" style="color: #a0aec0; text-decoration: none;">Liên hệ</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-6">
                <h5 class="text-white mb-3">Tổng đài</h5>
                <div class="mb-3">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-phone-alt me-2" style="color: #1a8ccc;"></i>
                        <a href="tel:<?= STORE_PHONE ?>" class="text-white fw-bold" style="text-decoration: none;"><?= STORE_PHONE ?></a>
                    </div>
                    <small style="color: #a0aec0;">Miễn phí 24/7</small>
                </div>
                <div class="d-flex align-items-center">
                    <i class="fas fa-envelope me-2" style="color: #1a8ccc;"></i>
                    <a href="mailto:<?= STORE_EMAIL ?>" style="color: #a0aec0; text-decoration: none;"><?= STORE_EMAIL ?></a>
                </div>
            </div>
        </div>
        <hr class="my-4" style="border-color: #2d3748;" />
        <div class="footer-bottom text-center">
            <p class="mb-0" style="color: #718096;">&copy; 2025 <?= STORE_NAME ?>. All rights reserved.</p>
        </div>
    </div>
</footer>

<style>
.footer-links a:hover {
    color: #1a8ccc !important;
}
.social-links a:hover {
    color: #1a8ccc !important;
}
</style>
