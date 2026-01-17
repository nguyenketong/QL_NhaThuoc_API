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

<!-- Back to Top Button -->
<button id="backToTop" class="back-to-top" title="Quay lại đầu trang">
    <i class="fas fa-chevron-up"></i>
</button>

<style>
.footer-links a:hover {
    color: #1a8ccc !important;
}
.social-links a:hover {
    color: #1a8ccc !important;
}

/* Back to Top Button */
.back-to-top {
    position: fixed;
    bottom: 100px;
    right: 30px;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #1a8ccc, #0d6efd);
    color: #fff;
    border: none;
    cursor: pointer;
    opacity: 0;
    visibility: hidden;
    transform: translateY(20px);
    transition: all 0.3s ease;
    z-index: 997;
    box-shadow: 0 4px 15px rgba(26, 140, 204, 0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

.back-to-top.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.back-to-top:hover {
    background: linear-gradient(135deg, #0d6efd, #0b5ed7);
    transform: translateY(-5px);
    box-shadow: 0 6px 25px rgba(13, 110, 253, 0.5);
}

@media (max-width: 768px) {
    .back-to-top {
        bottom: 90px;
        right: 15px;
        width: 45px;
        height: 45px;
        font-size: 16px;
    }
}
</style>

<script>
// Back to Top Button
(function() {
    const backToTop = document.getElementById('backToTop');
    
    // Show/hide button on scroll
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTop.classList.add('show');
        } else {
            backToTop.classList.remove('show');
        }
    });
    
    // Scroll to top on click
    backToTop.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
})();
</script>
