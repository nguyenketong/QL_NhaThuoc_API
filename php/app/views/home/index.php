<?php
/**
 * Trang chủ - Home/Index
 */
// Tách bài viết nổi bật và bài viết khác
$baiVietNoiBat = null;
$baiVietKhac = [];
if (!empty($baiViets)) {
    foreach ($baiViets as $bv) {
        if (($bv['IsNoiBat'] ?? 0) && !$baiVietNoiBat) {
            $baiVietNoiBat = $bv;
        } else {
            $baiVietKhac[] = $bv;
        }
    }
    // Nếu không có bài nổi bật, lấy bài đầu tiên
    if (!$baiVietNoiBat && !empty($baiViets)) {
        $baiVietNoiBat = $baiViets[0];
        $baiVietKhac = array_slice($baiViets, 1);
    }
}
?>

<!-- Hero Slider -->
<section class="hero-slider-full">
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="hero-slide-full" style="background-image: url('https://images.unsplash.com/photo-1631549916768-4119b2e5f926?w=1920&q=80'); background-size: cover; background-position: center;">
                    <div class="container position-relative">
                        <div class="row align-items-center min-vh-50">
                            <div class="col-lg-5">
                                <div class="hero-content-box bg-white bg-opacity-90 p-4 rounded">
                                    <span class="badge bg-primary mb-3 px-3 py-2">🏥 Nhà Thuốc Uy Tín</span>
                                    <h1 class="display-5 fw-bold mb-3 text-primary">Chăm sóc sức khỏe toàn diện</h1>
                                    <p class="lead mb-4 text-muted">Thuốc chính hãng - Giá tốt nhất - Giao hàng nhanh chóng</p>
                                    <div class="d-flex gap-3 flex-wrap">
                                        <a href="<?= BASE_URL ?>/thuoc/danhSach" class="btn btn-primary btn-lg px-4">
                                            <i class="fas fa-shopping-bag me-2"></i> Mua ngay
                                        </a>
                                        <a href="tel:<?= STORE_PHONE ?>" class="btn btn-outline-primary btn-lg px-4">
                                            <i class="fas fa-phone me-2"></i> Tư vấn
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <div class="hero-slide-full" style="background-image: url('https://images.unsplash.com/photo-1587854692152-cbe660dbde88?w=1920&q=80'); background-size: cover; background-position: center;">
                    <div class="container position-relative">
                        <div class="row align-items-center min-vh-50">
                            <div class="col-lg-5">
                                <div class="hero-content-box bg-white bg-opacity-90 p-4 rounded">
                                    <span class="badge bg-success mb-3 px-3 py-2">🚚 Free Ship</span>
                                    <h1 class="display-5 fw-bold mb-3 text-success">Miễn phí giao hàng</h1>
                                    <p class="lead mb-4 text-muted">Giao hàng toàn quốc trong 24h</p>
                                    <a href="<?= BASE_URL ?>/thuoc/danhSach" class="btn btn-success btn-lg px-4">
                                        <i class="fas fa-truck me-2"></i> Khám phá ngay
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <div class="hero-slide-full" style="background-image: url('https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=1920&q=80'); background-size: cover; background-position: center;">
                    <div class="container position-relative">
                        <div class="row align-items-center min-vh-50">
                            <div class="col-lg-5">
                                <div class="hero-content-box bg-white bg-opacity-90 p-4 rounded">
                                    <span class="badge bg-warning text-dark mb-3 px-3 py-2">👨‍⚕️ Hỗ trợ 24/7</span>
                                    <h1 class="display-5 fw-bold mb-3 text-warning">Tư vấn dược sĩ miễn phí</h1>
                                    <p class="lead mb-4 text-muted">Hotline: <?= STORE_PHONE ?></p>
                                    <a href="tel:<?= STORE_PHONE ?>" class="btn btn-warning btn-lg px-4">
                                        <i class="fas fa-phone me-2"></i> Gọi ngay
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
</section>

<!-- SẢN PHẨM BÁN CHẠY -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title-main text-primary fw-bold mb-3">SẢN PHẨM BÁN CHẠY</h2>
            <p class="text-muted mt-3">Một số dòng sản phẩm bán chạy của công ty chúng tôi</p>
        </div>
        
        <div class="row g-3">
            <?php if (!empty($sanPhamBanChay)): ?>
                <?php foreach (array_slice($sanPhamBanChay, 0, 8) as $thuoc): ?>
                    <?php include ROOT . '/app/views/components/product-card.php'; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-4">
                    <p class="text-muted">Chưa có sản phẩm</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- SẢN PHẨM KHUYẾN MÃI -->
<section class="py-5">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title-main text-primary fw-bold mb-3">SẢN PHẨM KHUYẾN MÃI</h2>
            <p class="text-muted mt-3">Săn deal hot - Giá siêu hời</p>
        </div>
        
        <div class="row g-3">
            <?php if (!empty($sanPhamKhuyenMai)): ?>
                <?php foreach (array_slice($sanPhamKhuyenMai, 0, 10) as $thuoc): ?>
                    <?php include ROOT . '/app/views/components/product-card.php'; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-4">
                    <p class="text-muted">Chưa có sản phẩm khuyến mãi</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="<?= BASE_URL ?>/thuoc/khuyenMai" class="btn btn-outline-danger btn-lg px-5">
                <i class="fas fa-tags me-2"></i> Xem tất cả khuyến mãi
            </a>
        </div>
    </div>
</section>

<!-- SẢN PHẨM MỚI -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title-main text-primary fw-bold mb-3">SẢN PHẨM MỚI</h2>
            <p class="text-muted mt-3">Một số dòng sản phẩm mới nhất</p>
        </div>
        
        <div class="row g-3">
            <?php if (!empty($sanPhamMoi)): ?>
                <?php foreach (array_slice($sanPhamMoi, 0, 10) as $thuoc): ?>
                    <?php include ROOT . '/app/views/components/product-card.php'; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-4">
                    <p class="text-muted">Chưa có sản phẩm mới</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- HÃY CHỌN NHÀ THUỐC -->
<section class="py-5" style="background: linear-gradient(135deg, #1a8ccc 0%, #0d6efd 100%);">
    <div class="container">
        <div class="text-center text-white mb-5">
            <h2 class="fw-bold">HÃY CHỌN <?= mb_strtoupper(STORE_NAME, 'UTF-8') ?></h2>
            <p>Cam kết mang đến dịch vụ tốt nhất cho khách hàng</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="text-center text-white">
                    <div class="mb-3"><i class="fas fa-headset fa-3x"></i></div>
                    <h5>TƯ VẤN MIỄN PHÍ</h5>
                    <p class="small opacity-75">Tư vấn tận tâm 24/7</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center text-white">
                    <div class="mb-3"><i class="fas fa-truck fa-3x"></i></div>
                    <h5>VẬN CHUYỂN NHANH</h5>
                    <p class="small opacity-75">Giao hàng tận nơi</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center text-white">
                    <div class="mb-3"><i class="fas fa-heart fa-3x"></i></div>
                    <h5>PHỤC VỤ TẬN TÂM</h5>
                    <p class="small opacity-75">Khách hàng là trên hết</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- THƯƠNG HIỆU NỔI BẬT -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title-main text-primary fw-bold mb-3">THƯƠNG HIỆU NỔI BẬT</h2>
            <p class="text-muted mt-3">Các thương hiệu uy tín hàng đầu</p>
        </div>
        
        <?php if (!empty($thuongHieus)): ?>
            <div class="brand-slider-container position-relative">
                <!-- Nút trái -->
                <button class="brand-nav-btn brand-nav-prev" onclick="scrollBrandLeft()">
                    <i class="fas fa-chevron-left"></i>
                </button>
                
                <div class="brand-marquee-wrapper" id="brandSlider">
                    <div class="brand-marquee" id="brandMarquee">
                        <div class="brand-marquee-content">
                            <?php foreach ($thuongHieus as $th): ?>
                                <?php 
                                $logoSrc = '';
                                if (!empty($th['HinhAnh'])) {
                                    if (strpos($th['HinhAnh'], 'http') === 0 || strpos($th['HinhAnh'], BASE_URL) === 0) {
                                        $logoSrc = $th['HinhAnh'];
                                    } else {
                                        $logoSrc = BASE_URL . $th['HinhAnh'];
                                    }
                                }
                                ?>
                                <a href="<?= BASE_URL ?>/thuongHieu/chiTiet/<?= $th['MaThuongHieu'] ?>" class="brand-item">
                                    <?php if (!empty($logoSrc)): ?>
                                        <img src="<?= $logoSrc ?>" 
                                             alt="<?= htmlspecialchars($th['TenThuongHieu']) ?>"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <span class="brand-fallback" style="display:none;">
                                            <i class="fas fa-building"></i>
                                            <?= htmlspecialchars($th['TenThuongHieu']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="brand-fallback">
                                            <i class="fas fa-building"></i>
                                            <?= htmlspecialchars($th['TenThuongHieu']) ?>
                                        </span>
                                    <?php endif; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                        <!-- Duplicate for seamless loop -->
                        <div class="brand-marquee-content">
                            <?php foreach ($thuongHieus as $th): ?>
                                <?php 
                                $logoSrc = '';
                                if (!empty($th['HinhAnh'])) {
                                    if (strpos($th['HinhAnh'], 'http') === 0 || strpos($th['HinhAnh'], BASE_URL) === 0) {
                                        $logoSrc = $th['HinhAnh'];
                                    } else {
                                        $logoSrc = BASE_URL . $th['HinhAnh'];
                                    }
                                }
                                ?>
                                <a href="<?= BASE_URL ?>/thuongHieu/chiTiet/<?= $th['MaThuongHieu'] ?>" class="brand-item">
                                    <?php if (!empty($logoSrc)): ?>
                                        <img src="<?= $logoSrc ?>" 
                                             alt="<?= htmlspecialchars($th['TenThuongHieu']) ?>"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <span class="brand-fallback" style="display:none;">
                                            <i class="fas fa-building"></i>
                                            <?= htmlspecialchars($th['TenThuongHieu']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="brand-fallback">
                                            <i class="fas fa-building"></i>
                                            <?= htmlspecialchars($th['TenThuongHieu']) ?>
                                        </span>
                                    <?php endif; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Nút phải -->
                <button class="brand-nav-btn brand-nav-next" onclick="scrollBrandRight()">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            
            <script>
            let brandScrollPos = 0;
            const brandMarquee = document.getElementById('brandMarquee');
            let isAutoScroll = true;
            
            function scrollBrandLeft() {
                brandScrollPos -= 200;
                if (brandScrollPos < 0) brandScrollPos = 0;
                brandMarquee.style.animation = 'none';
                brandMarquee.style.transform = `translateX(-${brandScrollPos}px)`;
                isAutoScroll = false;
                // Resume auto scroll after 3 seconds
                setTimeout(() => {
                    if (!isAutoScroll) {
                        brandMarquee.style.animation = '';
                        brandMarquee.style.transform = '';
                        isAutoScroll = true;
                    }
                }, 3000);
            }
            
            function scrollBrandRight() {
                const maxScroll = brandMarquee.scrollWidth / 2;
                brandScrollPos += 200;
                if (brandScrollPos > maxScroll) brandScrollPos = maxScroll;
                brandMarquee.style.animation = 'none';
                brandMarquee.style.transform = `translateX(-${brandScrollPos}px)`;
                isAutoScroll = false;
                // Resume auto scroll after 3 seconds
                setTimeout(() => {
                    if (!isAutoScroll) {
                        brandMarquee.style.animation = '';
                        brandMarquee.style.transform = '';
                        isAutoScroll = true;
                    }
                }, 3000);
            }
            </script>
            
            <div class="text-center mt-4">
                <a href="<?= BASE_URL ?>/thuongHieu/danhSach" class="btn btn-outline-primary px-4">
                    <i class="fas fa-building me-2"></i> Xem tất cả thương hiệu
                </a>
            </div>
        <?php else: ?>
            <div class="text-center py-4">
                <p class="text-muted">Chưa có thương hiệu</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- GÓC SỨC KHỎE - BÀI VIẾT NỔI BẬT -->
<?php if (!empty($baiViets)): ?>
<section class="py-5 bg-light">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title-main text-primary fw-bold mb-3">GÓC SỨC KHỎE</h2>
            <p class="text-muted mt-3">Chia sẻ kiến thức y tế, sức khỏe và chăm sóc bản thân</p>
        </div>
        
        <div class="row g-4">
            <!-- Bài viết nổi bật (bên trái) -->
            <div class="col-lg-6">
                <?php if ($baiVietNoiBat): ?>
                    <?php 
                    $hinhAnhBV = $baiVietNoiBat['HinhAnh'] ?? '';
                    if (!empty($hinhAnhBV) && strpos($hinhAnhBV, 'http') !== 0 && strpos($hinhAnhBV, BASE_URL) !== 0) {
                        $hinhAnhBV = BASE_URL . $hinhAnhBV;
                    }
                    ?>
                    <div class="card border-0 shadow-sm h-100 health-featured-card">
                        <a href="<?= BASE_URL ?>/baiViet/chiTiet/<?= $baiVietNoiBat['MaBaiViet'] ?>" class="text-decoration-none">
                            <div class="health-featured-img position-relative" style="height: 280px; overflow: hidden;">
                                <?php if (!empty($hinhAnhBV)): ?>
                                    <img src="<?= htmlspecialchars($hinhAnhBV) ?>" 
                                         alt="<?= htmlspecialchars($baiVietNoiBat['TieuDe']) ?>"
                                         class="w-100 h-100" style="object-fit: cover;"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="health-placeholder" style="display:none; height:100%; background:#e9ecef; align-items:center; justify-content:center;">
                                        <i class="fas fa-heartbeat fa-4x text-primary"></i>
                                    </div>
                                <?php else: ?>
                                    <div class="health-placeholder" style="height:100%; background:#e9ecef; display:flex; align-items:center; justify-content:center;">
                                        <i class="fas fa-heartbeat fa-4x text-primary"></i>
                                    </div>
                                <?php endif; ?>
                                <?php if ($baiVietNoiBat['IsNoiBat'] ?? 0): ?>
                                    <span class="badge bg-danger position-absolute" style="top:15px; left:15px;">
                                        <i class="fas fa-star"></i> Nổi bật
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title text-dark fw-bold"><?= htmlspecialchars($baiVietNoiBat['TieuDe']) ?></h5>
                                <p class="card-text text-muted small">
                                    <?= htmlspecialchars(mb_substr($baiVietNoiBat['MoTaNgan'] ?? '', 0, 150)) ?>...
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        <?= date('d/m/Y', strtotime($baiVietNoiBat['NgayDang'])) ?>
                                    </small>
                                    <small class="text-muted">
                                        <i class="fas fa-eye me-1"></i>
                                        <?= number_format($baiVietNoiBat['LuotXem'] ?? 0) ?> lượt xem
                                    </small>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Danh sách bài viết khác (bên phải) -->
            <div class="col-lg-6">
                <div class="health-article-list">
                    <?php foreach (array_slice($baiVietKhac, 0, 4) as $bv): ?>
                        <?php 
                        $hinhAnhBV2 = $bv['HinhAnh'] ?? '';
                        if (!empty($hinhAnhBV2) && strpos($hinhAnhBV2, 'http') !== 0 && strpos($hinhAnhBV2, BASE_URL) !== 0) {
                            $hinhAnhBV2 = BASE_URL . $hinhAnhBV2;
                        }
                        ?>
                        <a href="<?= BASE_URL ?>/baiViet/chiTiet/<?= $bv['MaBaiViet'] ?>" class="health-article-item d-flex mb-3 text-decoration-none">
                            <div class="health-article-thumb me-3" style="width:100px; height:80px; flex-shrink:0; overflow:hidden; border-radius:8px;">
                                <?php if (!empty($hinhAnhBV2)): ?>
                                    <img src="<?= htmlspecialchars($hinhAnhBV2) ?>" 
                                         alt="<?= htmlspecialchars($bv['TieuDe']) ?>"
                                         class="w-100 h-100" style="object-fit: cover;"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="health-thumb-placeholder" style="display:none; width:100%; height:100%; background:#e9ecef; align-items:center; justify-content:center;">
                                        <i class="fas fa-heartbeat text-primary"></i>
                                    </div>
                                <?php else: ?>
                                    <div class="health-thumb-placeholder" style="width:100%; height:100%; background:#e9ecef; display:flex; align-items:center; justify-content:center;">
                                        <i class="fas fa-heartbeat text-primary"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="health-article-info flex-grow-1">
                                <h6 class="mb-1 text-dark fw-semibold" style="line-height:1.4;">
                                    <?= htmlspecialchars(mb_substr($bv['TieuDe'], 0, 60)) ?><?= mb_strlen($bv['TieuDe']) > 60 ? '...' : '' ?>
                                </h6>
                                <small class="text-muted">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    <?= date('d/m/Y', strtotime($bv['NgayDang'])) ?>
                                    <span class="ms-2">
                                        <i class="fas fa-eye me-1"></i>
                                        <?= number_format($bv['LuotXem'] ?? 0) ?>
                                    </span>
                                </small>
                            </div>
                        </a>
                    <?php endforeach; ?>
                    
                    <?php if (empty($baiVietKhac)): ?>
                        <p class="text-muted text-center py-3">Chưa có bài viết khác</p>
                    <?php endif; ?>
                </div>
                
                <div class="text-center mt-3">
                    <a href="<?= BASE_URL ?>/baiViet/danhSach" class="btn btn-outline-primary">
                        <i class="fas fa-newspaper me-2"></i> Xem tất cả bài viết
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>
