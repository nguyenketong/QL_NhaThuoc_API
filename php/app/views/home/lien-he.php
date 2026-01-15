<?php
/**
 * Liên hệ
 */
?>
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Trang chủ</a></li>
            <li class="breadcrumb-item active">Liên hệ</li>
        </ol>
    </nav>

    <h1 class="text-center text-primary mb-4">Liên hệ với chúng tôi</h1>

    <div class="row justify-content-center">
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Thông tin liên hệ</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <i class="fas fa-map-marker-alt fa-2x text-danger"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Địa chỉ</h6>
                                    <p class="text-muted mb-0"><?= STORE_ADDRESS ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <i class="fas fa-phone fa-2x text-success"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Hotline</h6>
                                    <p class="mb-0"><a href="tel:<?= STORE_PHONE ?>" class="text-decoration-none h5 text-primary"><?= STORE_PHONE ?></a></p>
                                    <small class="text-muted">Miễn phí - Hoạt động 24/7</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <i class="fas fa-envelope fa-2x text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Email</h6>
                                    <p class="mb-0"><a href="mailto:<?= STORE_EMAIL ?>" class="text-decoration-none"><?= STORE_EMAIL ?></a></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <i class="fas fa-clock fa-2x text-warning"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Giờ làm việc</h6>
                                    <p class="text-muted mb-0">Thứ 2 - Chủ nhật: 7:00 - 22:00</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Nút liên hệ nhanh -->
                    <hr>
                    <div class="text-center">
                        <h6 class="mb-3">Liên hệ nhanh</h6>
                        <a href="tel:<?= STORE_PHONE ?>" class="btn btn-success me-2">
                            <i class="fas fa-phone"></i> Gọi ngay
                        </a>
                        <a href="https://zalo.me/<?= STORE_PHONE ?>" target="_blank" class="btn btn-primary me-2">
                            <i class="fas fa-comment-dots"></i> Chat Zalo
                        </a>
                        <a href="mailto:<?= STORE_EMAIL ?>" class="btn btn-outline-primary">
                            <i class="fas fa-envelope"></i> Gửi Email
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Map -->
    <div class="card">
        <div class="card-body p-0">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d503170.307906194!2d105.17266943826036!3d9.848852231731945!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x310a7648e6d8526f%3A0xb5d56e5f2b715884!2zVGh14buRYyBUw6J5IFRoYW5oIEhvw6BuZw!5e0!3m2!1svi!2s!4v1768199879488!5m2!1svi!2s" 
                    width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>
</div>
