<?php
/**
 * Header Component
 */

// Đọc giỏ hàng từ Session hoặc Cookie
$cartCount = 0;
$gioHang = [];

if (isset($_SESSION['GioHang']) && is_array($_SESSION['GioHang'])) {
    $gioHang = $_SESSION['GioHang'];
} elseif (isset($_COOKIE['GioHang'])) {
    $decoded = json_decode($_COOKIE['GioHang'], true);
    if (is_array($decoded)) {
        $gioHang = $decoded;
        $_SESSION['GioHang'] = $gioHang;
    }
}
$cartCount = count($gioHang);

$userId = $_SESSION['user_id'] ?? ($_COOKIE['UserId'] ?? null);
$hoTen = $_SESSION['user_name'] ?? '';
$soDienThoai = $_SESSION['user_phone'] ?? '';

// Load nhóm thuốc
$db = Database::getInstance()->getConnection();
$nhomThuocs = $db->query("SELECT * FROM nhom_thuoc ORDER BY TenNhomThuoc")->fetchAll(PDO::FETCH_ASSOC);
$danhMucCha = array_filter($nhomThuocs, fn($n) => empty($n['MaDanhMucCha']));
if (empty($danhMucCha)) $danhMucCha = $nhomThuocs;

// Load thương hiệu
$thuongHieus = $db->query("SELECT * FROM thuong_hieu ORDER BY TenThuongHieu LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Fixed Header Wrapper -->
<div class="header-wrapper" id="headerWrapper">
    <!-- Top Bar -->
    <div class="top-bar" style="background: #1a8ccc; padding: 6px 0;">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-white small">
                    <marquee behavior="scroll" direction="left" scrollamount="3">
                        <?= STORE_NAME ?> - Chuyên cung cấp thuốc chính hãng, giá tốt nhất thị trường. Hotline: <?= STORE_PHONE ?>
                    </marquee>
                </div>
                <div class="d-none d-md-block">
                    <select class="form-select form-select-sm bg-transparent text-white border-0" style="width: auto; font-size: 12px;">
                        <option>🌐 Select Language</option>
                        <option>Tiếng Việt</option>
                        <option>English</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header class="main-header bg-white py-3">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-3 col-md-3 col-6">
                    <a href="<?= BASE_URL ?>" class="d-flex align-items-center text-decoration-none">
                        <div class="logo-icon me-2">
                            <div style="width: 70px; height: 70px; background: linear-gradient(135deg, #1a8ccc, #28a745); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-clinic-medical text-white fa-2x"></i>
                            </div>
                        </div>
                        <div class="logo-text">
                            <div style="font-size: 1.5rem; font-weight: bold; color: #1a8ccc; line-height: 1.2;">NHÀ THUỐC TÂY</div>
                            <div style="font-size: 1.8rem; font-weight: bold; color: #d63384; line-height: 1;">THANH HOÀN</div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-5 col-md-5 d-none d-md-block">
                    <form action="<?= BASE_URL ?>/thuoc/timKiem" method="get">
                        <div class="input-group" style="border: 2px solid #1a8ccc; border-radius: 25px; overflow: hidden;">
                            <input type="text" name="tuKhoa" class="form-control border-0 py-2 px-4" placeholder="Nhập từ khóa cần tìm..." style="box-shadow: none;">
                            <button class="btn px-4" type="submit" style="background: #1a8ccc; color: white;"><i class="fas fa-search"></i></button>
                        </div>
                    </form>
                </div>
                <div class="col-lg-4 col-md-4 col-6 text-end">
                    <div class="d-flex align-items-center justify-content-end">
                        <div class="hotline-box d-flex align-items-center">
                            <div class="hotline-icon me-2"><i class="fas fa-phone-alt fa-lg" style="color: #28a745;"></i></div>
                            <div class="hotline-text">
                                <small class="text-muted d-block" style="font-size: 11px;">Hotline hỗ trợ</small>
                                <strong style="color: #1a8ccc; font-size: 1.3rem;"><?= STORE_PHONE ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row d-md-none mt-2">
                <div class="col-12">
                    <form action="<?= BASE_URL ?>/thuoc/timKiem" method="get">
                        <div class="input-group input-group-sm">
                            <input type="text" name="tuKhoa" class="form-control" placeholder="Tìm kiếm...">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Primary Navigation -->
    <nav class="primary-nav" style="background: #1a8ccc;">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between">
                <ul class="nav-menu-new d-none d-lg-flex list-unstyled mb-0">
                    <li class="nav-item-new">
                        <a href="<?= BASE_URL ?>" class="nav-link-new"><i class="fas fa-home me-1"></i> Trang chủ</a>
                    </li>
                    <li class="nav-item-new">
                        <a href="<?= BASE_URL ?>/home/gioiThieu" class="nav-link-new"><i class="fas fa-info-circle me-1"></i> Giới thiệu</a>
                    </li>
                    <li class="nav-item-new">
                        <a href="<?= BASE_URL ?>/thuoc/khuyenMai" class="nav-link-new"><i class="fas fa-tags me-1"></i> Khuyến mãi</a>
                    </li>
                    <li class="nav-item-new">
                        <a href="<?= BASE_URL ?>/baiViet/danhSach" class="nav-link-new"><i class="fas fa-share-alt me-1"></i> Góc chia sẻ</a>
                    </li>
                    <li class="nav-item-new nav-dropdown">
                        <a href="<?= BASE_URL ?>/thuongHieu/danhSach" class="nav-link-new">
                            <i class="fas fa-building me-1"></i> Thương hiệu <i class="fas fa-chevron-down ms-1 small"></i>
                        </a>
                        <div class="nav-dropdown-menu">
                            <?php foreach ($thuongHieus as $th): 
                                $logoSrc = '';
                                if (!empty($th['HinhAnh'])) {
                                    if (strpos($th['HinhAnh'], 'http') === 0 || strpos($th['HinhAnh'], BASE_URL) === 0) {
                                        $logoSrc = $th['HinhAnh'];
                                    } else {
                                        $logoSrc = BASE_URL . $th['HinhAnh'];
                                    }
                                }
                            ?>
                                <a href="<?= BASE_URL ?>/thuongHieu/chiTiet/<?= $th['MaThuongHieu'] ?>">
                                    <?php if (!empty($logoSrc)): ?>
                                        <img src="<?= $logoSrc ?>" alt="" class="brand-icon">
                                    <?php else: ?>
                                        <i class="fas fa-building brand-icon-placeholder"></i>
                                    <?php endif; ?>
                                    <?= htmlspecialchars($th['TenThuongHieu']) ?>
                                </a>
                            <?php endforeach; ?>
                            <a href="<?= BASE_URL ?>/thuongHieu/danhSach" class="view-all-brands">
                                Xem tất cả <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </li>
                    <li class="nav-item-new">
                        <a href="<?= BASE_URL ?>/home/lienHe" class="nav-link-new"><i class="fas fa-phone-alt me-1"></i> Liên hệ</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-3">
                    <?php if ($userId): ?>
                        <!-- Thông báo -->
                        <div class="dropdown notification-wrapper">
                            <a href="#" class="text-white position-relative notification-bell" data-bs-toggle="dropdown" id="notificationDropdown" aria-expanded="false">
                                <i class="fas fa-bell fa-lg"></i>
                                <span class="notification-badge" id="notification-count">0</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end notification-dropdown">
                                <div class="notification-header">
                                    <span class="notification-title">Thông báo</span>
                                    <a href="#" onclick="danhDauTatCaDaDoc(); return false;" class="mark-all-read">Đánh dấu đã đọc</a>
                                </div>
                                <div id="notification-list" class="notification-body">
                                    <div class="notification-loading">
                                        <i class="fas fa-spinner fa-spin"></i>
                                        <span>Đang tải...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown">
                            <a href="#" class="text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i> <?= htmlspecialchars($hoTen ?: $soDienThoai) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/user/profile"><i class="fas fa-user me-2"></i>Thông tin cá nhân</a></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/don-hang"><i class="fas fa-box me-2"></i>Đơn hàng của tôi</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/user/logout"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>/user/phoneLogin" class="text-white text-decoration-none"><i class="fas fa-sign-in-alt me-1"></i> Đăng nhập</a>
                    <?php endif; ?>
                    
                    <!-- Giỏ hàng với badge số lượng -->
                    <div style="position:relative;display:inline-block;margin-right:15px;">
                        <a href="<?= BASE_URL ?>/gio-hang" class="cart-btn-new" style="display:inline-flex;align-items:center;gap:8px;background:#28a745;color:#fff;padding:10px 20px;border-radius:25px;text-decoration:none;font-weight:600;">
                            <i class="fas fa-shopping-cart"></i> 
                            <span>GIỎ HÀNG</span>
                        </a>
                        <span id="cart-count" style="position:absolute;top:-10px;right:-10px;background:#dc3545;color:#fff;font-size:14px;font-weight:bold;min-width:26px;height:26px;line-height:22px;text-align:center;border-radius:50%;border:2px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,0.5);z-index:999;padding:0 4px;"><?= (int)$cartCount ?></span>
                    </div>
                </div>
                <button class="btn text-white d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#mobileMenu">
                    <i class="fas fa-bars fa-lg"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Secondary Navigation - Danh mục -->
    <nav class="secondary-nav bg-white border-bottom py-2">
        <div class="container">
            <div class="d-flex align-items-center">
                <!-- Category Button with Mega Menu -->
                <div class="dropdown me-4">
                    <button class="btn category-btn-new dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-bars me-2"></i> Danh mục
                    </button>
                    <div class="dropdown-menu mega-menu-new">
                        <div class="row g-0">
                            <?php 
                            $chunks = array_chunk(array_slice($danhMucCha, 0, 12), 4);
                            foreach ($chunks as $chunk): 
                            ?>
                                <div class="col-md-3 border-end">
                                    <div class="p-3">
                                        <?php foreach ($chunk as $nhom): ?>
                                            <div class="mb-3">
                                                <a href="<?= BASE_URL ?>/nhom-thuoc/chi-tiet/<?= $nhom['MaNhomThuoc'] ?>" class="fw-bold text-primary text-decoration-none d-block mb-1">
                                                    <?= htmlspecialchars($nhom['TenNhomThuoc']) ?>
                                                </a>
                                                <?php 
                                                $danhMucCon = array_filter($nhomThuocs, fn($n) => $n['MaDanhMucCha'] == $nhom['MaNhomThuoc']);
                                                if (!empty($danhMucCon)): 
                                                ?>
                                                    <ul class="list-unstyled ms-2 small">
                                                        <?php foreach (array_slice($danhMucCon, 0, 4) as $con): ?>
                                                            <li><a href="<?= BASE_URL ?>/nhom-thuoc/chi-tiet/<?= $con['MaNhomThuoc'] ?>" class="text-muted text-decoration-none"><?= htmlspecialchars($con['TenNhomThuoc']) ?></a></li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <div class="col-md-3">
                                <div class="p-3">
                                    <a href="<?= BASE_URL ?>/nhom-thuoc" class="btn btn-primary btn-sm w-100">
                                        <i class="fas fa-th-large me-1"></i> Xem tất cả (<?= count($nhomThuocs) ?>)
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category Links -->
                <div class="category-links d-none d-lg-flex flex-wrap gap-1">
                    <?php foreach (array_slice($danhMucCha, 0, 6) as $nhomCha): ?>
                        <?php $danhMucCon = array_filter($nhomThuocs, fn($n) => $n['MaDanhMucCha'] == $nhomCha['MaNhomThuoc']); ?>
                        <?php if (!empty($danhMucCon)): ?>
                            <div class="cat-dropdown">
                                <a href="<?= BASE_URL ?>/nhom-thuoc/chi-tiet/<?= $nhomCha['MaNhomThuoc'] ?>" class="category-link-new">
                                    <?= htmlspecialchars($nhomCha['TenNhomThuoc']) ?> <i class="fas fa-chevron-down ms-1 small"></i>
                                </a>
                                <div class="cat-dropdown-menu">
                                    <?php foreach ($danhMucCon as $con): ?>
                                        <a href="<?= BASE_URL ?>/nhom-thuoc/chi-tiet/<?= $con['MaNhomThuoc'] ?>"><?= htmlspecialchars($con['TenNhomThuoc']) ?></a>
                                    <?php endforeach; ?>
                                    <a href="<?= BASE_URL ?>/nhom-thuoc/chi-tiet/<?= $nhomCha['MaNhomThuoc'] ?>" class="view-all">Xem tất cả <i class="fas fa-arrow-right"></i></a>
                                </div>
                            </div>
                        <?php else: ?>
                            <a href="<?= BASE_URL ?>/nhom-thuoc/chi-tiet/<?= $nhomCha['MaNhomThuoc'] ?>" class="category-link-new">
                                <?= htmlspecialchars($nhomCha['TenNhomThuoc']) ?>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <?php if (count($danhMucCha) > 6): ?>
                        <div class="cat-dropdown">
                            <a href="#" class="category-link-new">XEM THÊM <i class="fas fa-chevron-down ms-1 small"></i></a>
                            <div class="cat-dropdown-menu cat-dropdown-menu-right">
                                <?php foreach (array_slice($danhMucCha, 6) as $nhom): ?>
                                    <a href="<?= BASE_URL ?>/nhom-thuoc/chi-tiet/<?= $nhom['MaNhomThuoc'] ?>"><?= htmlspecialchars($nhom['TenNhomThuoc']) ?></a>
                                <?php endforeach; ?>
                                <a href="<?= BASE_URL ?>/nhom-thuoc" class="view-all">Xem tất cả <i class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div class="collapse d-lg-none bg-white border-bottom" id="mobileMenu">
        <div class="container py-3">
            <ul class="list-unstyled mb-0">
                <li class="py-2 border-bottom"><a href="<?= BASE_URL ?>" class="text-dark text-decoration-none"><i class="fas fa-home me-2 text-primary"></i> Trang chủ</a></li>
                <li class="py-2 border-bottom"><a href="<?= BASE_URL ?>/thuoc/danhSach" class="text-dark text-decoration-none"><i class="fas fa-capsules me-2 text-primary"></i> Sản phẩm</a></li>
                <li class="py-2 border-bottom"><a href="<?= BASE_URL ?>/nhom-thuoc" class="text-dark text-decoration-none"><i class="fas fa-th-list me-2 text-primary"></i> Danh mục</a></li>
                <li class="py-2 border-bottom"><a href="<?= BASE_URL ?>/home/gioiThieu" class="text-dark text-decoration-none"><i class="fas fa-info-circle me-2 text-primary"></i> Giới thiệu</a></li>
                <li class="py-2 border-bottom"><a href="<?= BASE_URL ?>/home/lienHe" class="text-dark text-decoration-none"><i class="fas fa-phone-alt me-2 text-primary"></i> Liên hệ</a></li>
                <?php if ($userId): ?>
                    <li class="py-2 border-bottom"><a href="<?= BASE_URL ?>/user/profile" class="text-dark text-decoration-none"><i class="fas fa-user me-2 text-primary"></i> Tài khoản</a></li>
                    <li class="py-2 border-bottom"><a href="<?= BASE_URL ?>/don-hang" class="text-dark text-decoration-none"><i class="fas fa-box me-2 text-primary"></i> Đơn hàng</a></li>
                    <li class="py-2"><a href="<?= BASE_URL ?>/user/logout" class="text-danger text-decoration-none"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a></li>
                <?php else: ?>
                    <li class="py-2"><a href="<?= BASE_URL ?>/user/phoneLogin" class="text-primary text-decoration-none"><i class="fas fa-sign-in-alt me-2"></i> Đăng nhập</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<div class="header-spacer" id="headerSpacer"></div>

<!-- Toast thông báo giỏ hàng -->
<div class="position-fixed top-0 end-0 p-3" style="z-index: 9999; margin-top: 80px;">
    <div id="cartToast" class="toast align-items-center text-white bg-success border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-check-circle me-2"></i>
                <span id="cartToastMessage">Đã thêm vào giỏ hàng!</span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tự động điều chỉnh header spacer
    function adjustHeaderSpacer() {
        const header = document.getElementById('headerWrapper');
        const spacer = document.getElementById('headerSpacer');
        if (header && spacer) {
            spacer.style.height = header.offsetHeight + 'px';
        }
    }
    
    setTimeout(adjustHeaderSpacer, 100);
    window.addEventListener('resize', adjustHeaderSpacer);
    
    // Header scroll effect - Giữ top-bar + navigation + danh mục, chỉ ẩn logo/search
    const headerWrapper = document.getElementById('headerWrapper');
    const topBar = document.querySelector('.top-bar');
    const primaryNav = document.querySelector('.primary-nav');
    const secondaryNav = document.querySelector('.secondary-nav');
    let isScrolled = false;
    
    window.addEventListener('scroll', function() {
        let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        if (scrollTop > 100 && !isScrolled) {
            isScrolled = true;
            headerWrapper.classList.add('header-scrolled');
            // Cập nhật spacer = top-bar + nav xanh + danh mục
            let newHeight = 0;
            if (topBar) newHeight += topBar.offsetHeight;
            if (primaryNav) newHeight += primaryNav.offsetHeight;
            if (secondaryNav) newHeight += secondaryNav.offsetHeight;
            spacer.style.height = newHeight + 'px';
        } else if (scrollTop <= 100 && isScrolled) {
            isScrolled = false;
            headerWrapper.classList.remove('header-scrolled');
            setTimeout(adjustHeaderSpacer, 50);
        }
    });

    <?php if ($userId): ?>
    loadThongBao();
    setInterval(loadThongBao, 30000);
    <?php endif; ?>
    
    // Load cart count từ server để đảm bảo chính xác
    loadCartCount();
});

// Cập nhật số lượng giỏ hàng
function updateCartCount(count) {
    const badge = document.getElementById('cart-count');
    if (badge) {
        badge.textContent = count;
        badge.style.animation = 'pulse 0.3s ease';
        setTimeout(() => badge.style.animation = '', 300);
    }
}

// Hiển thị thông báo toast
function showCartToast(message, type = 'success') {
    const toast = document.getElementById('cartToast');
    const toastMessage = document.getElementById('cartToastMessage');
    if (toast && toastMessage) {
        toastMessage.textContent = message || 'Đã thêm vào giỏ hàng!';
        toast.className = 'toast align-items-center text-white border-0 bg-' + type;
        const bsToast = new bootstrap.Toast(toast, { delay: 3000 });
        bsToast.show();
    }
}

// Thêm sản phẩm vào giỏ hàng (AJAX)
async function themVaoGio(maThuoc, soLuong = 1) {
    try {
        const res = await fetch('<?= BASE_URL ?>/gioHang/themAjax', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'maThuoc=' + maThuoc + '&soLuong=' + soLuong
        });
        const data = await res.json();
        
        if (data.success) {
            updateCartCount(data.soLuong);
            showCartToast(data.message || 'Đã thêm vào giỏ hàng!', 'success');
        } else {
            showCartToast(data.message || 'Không thể thêm vào giỏ hàng!', 'danger');
        }
        return data;
    } catch(e) {
        console.error('Add to cart error:', e);
        showCartToast('Có lỗi xảy ra!', 'danger');
        return { success: false };
    }
}

// Alias cho tương thích với product-card
function themVaoGioHang(maThuoc, soLuong = 1) {
    return themVaoGio(maThuoc, soLuong);
}

// Load số lượng giỏ hàng từ server
async function loadCartCount() {
    try {
        const res = await fetch('<?= BASE_URL ?>/gioHang/laySoLuong');
        const data = await res.json();
        updateCartCount(data.soLuong || 0);
    } catch(e) { console.error('Load cart error:', e); }
}

// Thông báo - Load danh sách và số lượng
async function loadThongBao() {
    try {
        // Load số lượng chưa đọc
        const countRes = await fetch('<?= BASE_URL ?>/thongBao/laySoLuongChuaDoc');
        const countData = await countRes.json();
        const badge = document.getElementById('notification-count');
        
        if (badge) {
            if (countData.soLuong > 0) {
                badge.textContent = countData.soLuong > 99 ? '99+' : countData.soLuong;
                badge.classList.add('has-notification');
            } else {
                badge.textContent = '0';
                badge.classList.remove('has-notification');
            }
        }

        // Load danh sách thông báo
        const listRes = await fetch('<?= BASE_URL ?>/thongBao/layDanhSach');
        const listData = await listRes.json();
        const listEl = document.getElementById('notification-list');
        
        if (!listEl) return;
        
        if (listData.thongBaos && listData.thongBaos.length > 0) {
            listEl.innerHTML = listData.thongBaos.map(tb => {
                // Icon theo loại thông báo
                let icon = 'fa-bell';
                let iconColor = '#1a8ccc';
                if (tb.loaiThongBao === 'DonHang') {
                    if (tb.tieuDe.includes('đang giao')) { icon = 'fa-truck'; iconColor = '#17a2b8'; }
                    else if (tb.tieuDe.includes('hoàn thành')) { icon = 'fa-check-circle'; iconColor = '#28a745'; }
                    else if (tb.tieuDe.includes('hủy')) { icon = 'fa-times-circle'; iconColor = '#dc3545'; }
                    else if (tb.tieuDe.includes('thanh toán')) { icon = 'fa-credit-card'; iconColor = '#6f42c1'; }
                    else { icon = 'fa-box'; iconColor = '#fd7e14'; }
                }
                
                return `
                <a href="<?= BASE_URL ?>${tb.duongDan || '#'}" class="notification-item ${tb.daDoc ? 'read' : 'unread'}" onclick="danhDauDaDoc(${tb.maThongBao})">
                    <div class="notification-icon" style="background: ${iconColor}20; color: ${iconColor};">
                        <i class="fas ${icon}"></i>
                    </div>
                    <div class="notification-content">
                        <div class="notification-title">${tb.tieuDe || 'Thông báo'}</div>
                        <div class="notification-text">${tb.noiDung || ''}</div>
                        <div class="notification-time">
                            <i class="far fa-clock"></i> ${tb.ngayTao || ''}
                        </div>
                    </div>
                    ${!tb.daDoc ? '<span class="notification-dot"></span>' : ''}
                </a>
            `}).join('');
        } else {
            listEl.innerHTML = `
                <div class="notification-empty">
                    <i class="fas fa-bell-slash"></i>
                    <p>Không có thông báo mới</p>
                </div>
            `;
        }
    } catch(e) { 
        console.error('Load notification error:', e);
        const listEl = document.getElementById('notification-list');
        if (listEl) {
            listEl.innerHTML = `
                <div class="notification-empty">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>Không thể tải thông báo</p>
                </div>
            `;
        }
    }
}

async function danhDauDaDoc(id) {
    await fetch('<?= BASE_URL ?>/thongBao/danhDauDaDoc', { method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded'}, body: 'id=' + id });
}

async function danhDauTatCaDaDoc() {
    await fetch('<?= BASE_URL ?>/thongBao/danhDauTatCaDaDoc', { method: 'POST' });
    loadThongBao();
}
</script>
