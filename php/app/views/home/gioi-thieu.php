<?php
/**
 * Giới thiệu
 */
?>
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Trang chủ</a></li>
            <li class="breadcrumb-item active">Giới thiệu</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1 class="text-center text-primary mb-4">Giới thiệu <?= STORE_NAME ?></h1>
            
            <div class="text-center mb-4">
                <i class="fas fa-clinic-medical fa-5x text-primary"></i>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h4 class="text-primary">Về chúng tôi</h4>
                    <p><?= STORE_NAME ?> là hệ thống nhà thuốc uy tín, chuyên cung cấp các sản phẩm thuốc, thực phẩm chức năng, dụng cụ y tế chính hãng với giá cả hợp lý.</p>
                    <p>Với đội ngũ dược sĩ giàu kinh nghiệm, chúng tôi cam kết mang đến cho khách hàng dịch vụ tư vấn chuyên nghiệp và sản phẩm chất lượng cao.</p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h4 class="text-primary">Tầm nhìn & Sứ mệnh</h4>
                    <ul>
                        <li>Trở thành hệ thống nhà thuốc hàng đầu Việt Nam</li>
                        <li>Mang đến sức khỏe tốt nhất cho cộng đồng</li>
                        <li>Cung cấp thuốc chính hãng, giá tốt nhất</li>
                        <li>Dịch vụ tư vấn chuyên nghiệp, tận tâm</li>
                    </ul>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h4 class="text-primary">Thông tin liên hệ</h4>
                    <p><i class="fas fa-map-marker-alt text-danger"></i> <strong>Địa chỉ:</strong> <?= STORE_ADDRESS ?></p>
                    <p><i class="fas fa-phone text-success"></i> <strong>Hotline:</strong> <?= STORE_PHONE ?></p>
                    <p><i class="fas fa-envelope text-primary"></i> <strong>Email:</strong> <?= STORE_EMAIL ?></p>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-4">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <i class="fas fa-certificate fa-3x text-warning mb-3"></i>
                            <h5>Chính hãng 100%</h5>
                            <p class="text-muted small">Cam kết thuốc chính hãng, nguồn gốc rõ ràng</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <i class="fas fa-truck fa-3x text-primary mb-3"></i>
                            <h5>Giao hàng nhanh</h5>
                            <p class="text-muted small">Giao hàng toàn quốc trong 24-48h</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <i class="fas fa-headset fa-3x text-success mb-3"></i>
                            <h5>Hỗ trợ 24/7</h5>
                            <p class="text-muted small">Tư vấn miễn phí mọi lúc mọi nơi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
