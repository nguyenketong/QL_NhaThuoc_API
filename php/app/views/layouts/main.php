<?php
/**
 * Layout chính - Nhà Thuốc Thanh Hoàn
 */
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $title ?? STORE_NAME ?></title>
    <meta name="description" content="Nhà thuốc online uy tín - Thuốc chính hãng, giá tốt, giao hàng nhanh toàn quốc">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/custom.css?v=<?= time() ?>" />
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        
        /* Header Styles */
        .nav-menu-new { display: flex; gap: 5px; }
        .nav-item-new { position: relative; }
        .nav-link-new { display: block; padding: 12px 15px; color: rgba(255,255,255,0.9); text-decoration: none; font-size: 14px; font-weight: 500; transition: all 0.3s; }
        .nav-link-new:hover, .nav-link-new.active { color: #fff; background: rgba(255,255,255,0.1); }

        /* Nav Dropdown - Thương hiệu */
        .nav-dropdown { position: relative; }
        .nav-dropdown-menu { 
            position: absolute; 
            top: 100%; 
            left: 0; 
            min-width: 250px; 
            background: #fff; 
            border-radius: 8px; 
            box-shadow: 0 5px 25px rgba(0,0,0,0.15); 
            padding: 10px 0; 
            opacity: 0; 
            visibility: hidden; 
            transform: translateY(10px); 
            transition: all 0.3s ease; 
            z-index: 1000;
            padding-top: 15px; /* Thêm padding để tạo vùng hover */
            margin-top: 0; /* Không có khoảng cách */
        }
        .nav-dropdown-menu::before {
            content: '';
            position: absolute;
            top: -10px;
            left: 0;
            right: 0;
            height: 15px;
            background: transparent;
        }
        .nav-dropdown:hover .nav-dropdown-menu { opacity: 1; visibility: visible; transform: translateY(0); }
        .nav-dropdown-menu a { display: flex; align-items: center; padding: 10px 15px; color: #333; text-decoration: none; font-size: 13px; transition: all 0.2s; }
        .nav-dropdown-menu a:hover { background: #f0f7ff; color: #1a8ccc; }
        .nav-dropdown-menu .brand-icon { width: 30px; height: 20px; object-fit: contain; margin-right: 10px; }
        .nav-dropdown-menu .brand-icon-placeholder { width: 30px; margin-right: 10px; color: #1a8ccc; text-align: center; }
        .nav-dropdown-menu .view-all-brands { border-top: 1px solid #eee; margin-top: 5px; padding-top: 12px; color: #1a8ccc; font-weight: 600; justify-content: center; }
        .nav-dropdown-menu .view-all-brands:hover { background: #1a8ccc; color: #fff; }
        
        .cart-btn-new { display: inline-flex; align-items: center; padding: 8px 20px; background: #28a745; color: #fff; text-decoration: none; border-radius: 20px; font-size: 13px; font-weight: 600; position: relative; }
        .cart-btn-new:hover { background: #218838; color: #fff; }
        .cart-badge-new { position: absolute; top: -5px; right: -5px; background: #dc3545; color: #fff; font-size: 10px; padding: 2px 6px; border-radius: 10px; min-width: 18px; text-align: center; }
        
        .category-btn-new { background: #f8f9fa; border: 1px solid #dee2e6; padding: 8px 15px; font-size: 14px; font-weight: 500; color: #333; }
        .category-btn-new:hover { background: #e9ecef; }
        
        .cat-dropdown { position: relative; }
        .category-link-new { color: #333; text-decoration: none; font-size: 13px; font-weight: 600; padding: 8px 10px; transition: all 0.3s; display: inline-block; white-space: nowrap; }
        .category-link-new:hover { color: #1a8ccc; }
        .cat-dropdown-menu { 
            position: absolute; 
            top: 100%; 
            left: 0; 
            min-width: 220px; 
            background: #fff; 
            border-radius: 8px; 
            box-shadow: 0 5px 25px rgba(0,0,0,0.15); 
            padding: 10px 0; 
            padding-top: 15px;
            opacity: 0; 
            visibility: hidden; 
            transform: translateY(10px); 
            transition: all 0.3s ease; 
            z-index: 1000; 
        }
        .cat-dropdown-menu::before {
            content: '';
            position: absolute;
            top: -10px;
            left: 0;
            right: 0;
            height: 15px;
            background: transparent;
        }
        .cat-dropdown-menu-right { left: auto; right: 0; }
        .cat-dropdown:hover .cat-dropdown-menu { opacity: 1; visibility: visible; transform: translateY(0); }
        .cat-dropdown-menu a { display: block; padding: 10px 20px; color: #333; text-decoration: none; font-size: 13px; transition: all 0.2s; }
        .cat-dropdown-menu a:hover { background: #f0f7ff; color: #1a8ccc; padding-left: 25px; }
        .cat-dropdown-menu a.view-all { border-top: 1px solid #eee; margin-top: 5px; padding-top: 12px; color: #1a8ccc; font-weight: 600; }
        .cat-dropdown-menu a.view-all:hover { background: #1a8ccc; color: #fff; }
        
        .mega-menu-new { min-width: 700px; padding: 0; border: none; box-shadow: 0 5px 20px rgba(0,0,0,0.15); }
        
        .notification-badge { position: absolute; top: -8px; right: -8px; background: #dc3545; color: #fff; font-size: 10px; padding: 2px 6px; border-radius: 10px; min-width: 18px; text-align: center; }
        .notification-dropdown .notification-item { display: block; padding: 12px 15px; border-bottom: 1px solid #eee; text-decoration: none; color: #333; transition: background 0.2s; }
        .notification-dropdown .notification-item:hover { background: #f8f9fa; }
        .notification-dropdown .notification-item.unread { background: #f0f7ff; }
        .notification-dropdown .notification-item .notif-title { font-weight: 600; font-size: 13px; margin-bottom: 3px; }
        .notification-dropdown .notification-item .notif-content { font-size: 12px; color: #666; margin-bottom: 3px; }
        .notification-dropdown .notification-item .notif-time { font-size: 11px; color: #999; }
        
        .header-wrapper { position: fixed; top: 0; left: 0; right: 0; z-index: 1000; transition: all 0.3s ease; }
        .header-scrolled { box-shadow: 0 2px 20px rgba(0,0,0,0.15); }
        .main-header-hidden { display: none !important; height: 0 !important; padding: 0 !important; overflow: hidden; }
        .header-spacer { height: 0; transition: height 0.3s ease; }
        
        /* Đảm bảo main content không bị che */
        .main-content { min-height: calc(100vh - 300px); }
        
        /* Hero slider full width - không cần spacer */
        .hero-slider-full { margin-top: 0; }
        .hero-slide-full { min-height: 450px; display: flex; align-items: center; }
        .min-vh-50 { min-height: 400px; }
        
        /* Các trang khác cần padding-top */
        .page-content { padding-top: 20px; }
        
        /* Product card styles */
        .product-card { background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.08); transition: all 0.3s; height: 100%; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 5px 20px rgba(0,0,0,0.15); }
        .product-card .product-image { height: 180px; display: flex; align-items: center; justify-content: center; background: #f8f9fa; padding: 15px; }
        .product-card .product-image img { max-height: 100%; max-width: 100%; object-fit: contain; }
        .product-card .product-name { font-size: 14px; font-weight: 600; color: #333; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 42px; }
        .product-card .product-price .price-new { font-size: 16px; }
        .product-card .product-price .price-old { font-size: 13px; }
        .product-unavailable { opacity: 0.7; }
        
        /* Section title */
        .section-title-main { font-size: 1.8rem; position: relative; display: inline-block; }
        .section-title-main::after { content: ''; position: absolute; bottom: -10px; left: 50%; transform: translateX(-50%); width: 60px; height: 3px; background: #1a8ccc; }
        
        /* Brand section */
        .brand-card { transition: all 0.3s ease; border: 1px solid #eee; }
        .brand-card:hover { transform: translateY(-5px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); border-color: #1a8ccc; }
        .brand-logo { max-height: 60px; max-width: 100%; object-fit: contain; }
        .brand-placeholder { height: 60px; display: flex; align-items: center; justify-content: center; }
        .brand-name { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        
        /* Brand Marquee - Cuộn ngang */
        .brand-slider-container { position: relative; }
        .brand-nav-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 45px;
            height: 45px;
            border: none;
            border-radius: 50%;
            background: #fff;
            box-shadow: 0 3px 15px rgba(0,0,0,0.15);
            color: #1a8ccc;
            font-size: 18px;
            cursor: pointer;
            z-index: 10;
            transition: all 0.3s ease;
        }
        .brand-nav-btn:hover {
            background: #1a8ccc;
            color: #fff;
            transform: translateY(-50%) scale(1.1);
        }
        .brand-nav-prev { left: -20px; }
        .brand-nav-next { right: -20px; }
        @media (max-width: 768px) {
            .brand-nav-prev { left: 5px; }
            .brand-nav-next { right: 5px; }
            .brand-nav-btn { width: 35px; height: 35px; font-size: 14px; }
        }
        
        .brand-marquee-wrapper { overflow: hidden; padding: 20px 0; }
        .brand-marquee { display: flex; width: max-content; animation: marquee 30s linear infinite; }
        .brand-marquee:hover { animation-play-state: paused; }
        .brand-marquee-content { display: flex; gap: 30px; padding: 0 15px; }
        .brand-item { 
            display: flex; 
            align-items: center; 
            justify-content: center;
            min-width: 150px; 
            height: 80px; 
            padding: 15px 25px;
            background: #f8f9fa; 
            border-radius: 10px;
            border: 1px solid #eee;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        .brand-item:hover { 
            transform: scale(1.05); 
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            border-color: #1a8ccc;
            background: #fff;
        }
        .brand-item img { 
            max-height: 50px; 
            max-width: 120px; 
            object-fit: contain;
            filter: grayscale(30%);
            transition: filter 0.3s;
        }
        .brand-item:hover img { filter: grayscale(0%); }
        .brand-fallback {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
            color: #666;
            font-size: 12px;
            font-weight: 500;
        }
        .brand-fallback i { font-size: 24px; color: #1a8ccc; }
        
        @keyframes marquee {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .brand-item { min-width: 120px; height: 60px; padding: 10px 15px; }
            .brand-item img { max-height: 35px; max-width: 80px; }
            .brand-marquee { animation-duration: 20s; }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include ROOT . '/app/views/layouts/header.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <?php if (isset($_SESSION['flash']['success'])): ?>
            <div class="container mt-3">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> <?= $_SESSION['flash']['success'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
            <?php unset($_SESSION['flash']['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['flash']['error'])): ?>
            <div class="container mt-3">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> <?= $_SESSION['flash']['error'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
            <?php unset($_SESSION['flash']['error']); ?>
        <?php endif; ?>
        
        <?= $content ?>
    </main>

    <!-- Footer -->
    <?php include ROOT . '/app/views/layouts/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Back to top
        const backToTop = document.getElementById('backToTop');
        backToTop?.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // Auto hide alerts
        setTimeout(() => {
            document.querySelectorAll('.alert-dismissible').forEach(alert => {
                bootstrap.Alert.getOrCreateInstance(alert).close();
            });
        }, 5000);

        // Cập nhật số lượng giỏ hàng
        function capNhatSoLuongGioHang() {
            fetch('<?= BASE_URL ?>/gioHang/laySoLuong')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('cart-count').textContent = data.soLuong;
                })
                .catch(err => console.log('Lỗi:', err));
        }

        // Thêm vào giỏ hàng AJAX
        function themVaoGioHang(maThuoc, soLuong = 1) {
            fetch('<?= BASE_URL ?>/gioHang/themAjax', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `maThuoc=${maThuoc}&soLuong=${soLuong}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('cart-count').textContent = data.soLuong;
                    hienThiThongBao(data.message, 'success');
                } else {
                    hienThiThongBao(data.message, 'danger');
                }
            })
            .catch(err => hienThiThongBao('Có lỗi xảy ra', 'danger'));
        }

        // Hiển thị thông báo toast
        function hienThiThongBao(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            alertDiv.style.cssText = 'top: 180px; right: 20px; z-index: 9999; min-width: 300px;';
            alertDiv.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(alertDiv);
            setTimeout(() => alertDiv.remove(), 3000);
        }
        
        document.addEventListener('DOMContentLoaded', capNhatSoLuongGioHang);

        // Floating Buttons Toggle (Click)
        const floatBtnMain = document.querySelector('.float-btn-main');
        const floatingButtons = document.querySelector('.floating-buttons');
        
        floatBtnMain?.addEventListener('click', function(e) {
            e.stopPropagation();
            this.classList.toggle('active');
            floatingButtons?.classList.toggle('show');
        });

        // Đóng khi click ra ngoài
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.floating-buttons-wrapper')) {
                floatBtnMain?.classList.remove('active');
                floatingButtons?.classList.remove('show');
            }
        });
    </script>
</body>
</html>
