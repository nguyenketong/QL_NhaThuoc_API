<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($title ?? 'Admin Panel') ?> - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 260px;
            --primary: #1E88E5;
            --primary-dark: #1565C0;
            --dark: #1a1a2e;
            --dark-light: #16213e;
        }
        body { font-family: 'Inter', sans-serif; background: #f4f6f9; }
        
        /* Sidebar */
        .admin-sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--dark) 0%, var(--dark-light) 100%);
            color: #fff;
            z-index: 1000;
            overflow-y: auto;
        }
        .sidebar-brand {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-brand h4 { margin: 0; font-weight: 700; }
        .sidebar-brand i { font-size: 2rem; color: var(--primary); }
        
        .sidebar-menu { padding: 15px 0; }
        .menu-header {
            padding: 10px 20px;
            font-size: 0.75rem;
            text-transform: uppercase;
            color: rgba(255,255,255,0.4);
            font-weight: 600;
        }
        .menu-item {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        .menu-item:hover, .menu-item.active {
            background: rgba(255,255,255,0.1);
            color: #fff;
            border-left-color: var(--primary);
        }
        .menu-item i { width: 25px; margin-right: 10px; }
        .menu-item .badge { margin-left: auto; }
        
        /* Main Content */
        .admin-main {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }
        .admin-header {
            background: #fff;
            padding: 15px 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .admin-content { padding: 25px; }
        
        /* Cards */
        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #fff;
        }
        .stat-icon.blue { background: linear-gradient(135deg, #1E88E5, #1565C0); }
        .stat-icon.green { background: linear-gradient(135deg, #4CAF50, #2E7D32); }
        .stat-icon.orange { background: linear-gradient(135deg, #FF9800, #F57C00); }
        .stat-icon.red { background: linear-gradient(135deg, #F44336, #D32F2F); }
        .stat-icon.purple { background: linear-gradient(135deg, #9C27B0, #7B1FA2); }
        .stat-value { font-size: 1.8rem; font-weight: 700; color: #333; }
        .stat-label { color: #666; font-size: 0.9rem; }
        
        /* Table */
        .admin-table { background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .admin-table .table { margin: 0; }
        .admin-table thead { background: var(--dark); color: #fff; }
        .admin-table th { font-weight: 600; padding: 15px; border: none; }
        .admin-table td { padding: 15px; vertical-align: middle; }
        
        /* Buttons */
        .btn-admin { border-radius: 8px; padding: 8px 16px; font-weight: 500; }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-clinic-medical"></i>
            <h4>Admin Panel</h4>
        </div>
        <nav class="sidebar-menu">
            <div class="menu-header">Tổng quan</div>
            <a href="<?= BASE_URL ?>/admin/" class="menu-item">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            
            <div class="menu-header">Quản lý sản phẩm</div>
            <a href="<?= BASE_URL ?>/admin/?controller=thuoc" class="menu-item">
                <i class="fas fa-pills"></i> Thuốc
            </a>
            <a href="<?= BASE_URL ?>/admin/?controller=nhom-thuoc" class="menu-item">
                <i class="fas fa-th-list"></i> Nhóm thuốc
            </a>
            <a href="<?= BASE_URL ?>/admin/?controller=thuong-hieu" class="menu-item">
                <i class="fas fa-building"></i> Thương hiệu
            </a>
            <a href="<?= BASE_URL ?>/admin/?controller=nuoc-san-xuat" class="menu-item">
                <i class="fas fa-globe"></i> Nước sản xuất
            </a>
            <a href="<?= BASE_URL ?>/admin/?controller=thanh-phan" class="menu-item">
                <i class="fas fa-flask"></i> Thành phần
            </a>
            <a href="<?= BASE_URL ?>/admin/?controller=tac-dung-phu" class="menu-item">
                <i class="fas fa-exclamation-triangle"></i> Tác dụng phụ
            </a>
            <a href="<?= BASE_URL ?>/admin/?controller=doi-tuong" class="menu-item">
                <i class="fas fa-user-tag"></i> Đối tượng sử dụng
            </a>
            
            <div class="menu-header">Quản lý bán hàng</div>
            <a href="<?= BASE_URL ?>/admin/?controller=don-hang" class="menu-item">
                <i class="fas fa-shopping-cart"></i> Đơn hàng
                <?php if (($soDonChoXuLy ?? 0) > 0): ?>
                    <span class="badge bg-danger"><?= $soDonChoXuLy ?></span>
                <?php endif; ?>
            </a>
            <a href="<?= BASE_URL ?>/admin/?controller=nguoi-dung" class="menu-item">
                <i class="fas fa-users"></i> Khách hàng
            </a>
            
            <div class="menu-header">Nội dung</div>
            <a href="<?= BASE_URL ?>/admin/?controller=bai-viet" class="menu-item">
                <i class="fas fa-newspaper"></i> Bài viết
            </a>
            
            <div class="menu-header">Hệ thống</div>
            <a href="<?= BASE_URL ?>/" class="menu-item">
                <i class="fas fa-globe"></i> Xem trang web
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
        <header class="admin-header">
            <h5 class="mb-0"><?= htmlspecialchars($title ?? 'Dashboard') ?></h5>
            <div class="d-flex align-items-center gap-3">
                <!-- Thông báo đơn hàng mới -->
                <div class="dropdown">
                    <a href="#" class="position-relative text-decoration-none" data-bs-toggle="dropdown" id="notificationBell" title="Thông báo">
                        <i class="fas fa-bell fs-5 <?= ($soDonChoXuLy ?? 0) > 0 ? 'text-warning' : 'text-muted' ?>"></i>
                        <?php if (($soDonChoXuLy ?? 0) > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 10px;" id="notificationCount">
                                <?= $soDonChoXuLy > 99 ? '99+' : $soDonChoXuLy ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end shadow" style="width: 320px; max-height: 400px; overflow-y: auto;">
                        <div class="dropdown-header d-flex justify-content-between align-items-center">
                            <strong><i class="fas fa-bell me-1"></i> Thông báo</strong>
                            <?php if (($soDonChoXuLy ?? 0) > 0): ?>
                                <span class="badge bg-danger"><?= $soDonChoXuLy ?> đơn mới</span>
                            <?php endif; ?>
                        </div>
                        <div class="dropdown-divider"></div>
                        <?php if (($soDonChoXuLy ?? 0) > 0): ?>
                            <a class="dropdown-item py-3" href="<?= BASE_URL ?>/admin/?controller=don-hang&trangThai=Cho xu ly">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <span class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fas fa-shopping-cart"></i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <strong class="text-dark">Đơn hàng chờ xử lý</strong>
                                        <p class="text-muted small mb-0">Có <?= $soDonChoXuLy ?> đơn hàng cần xử lý</p>
                                    </div>
                                </div>
                            </a>
                        <?php else: ?>
                            <div class="dropdown-item text-center text-muted py-4">
                                <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                                <p class="mb-0">Không có thông báo mới</p>
                            </div>
                        <?php endif; ?>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-center small" href="<?= BASE_URL ?>/admin/?controller=don-hang">
                            <i class="fas fa-list me-1"></i> Xem tất cả đơn hàng
                        </a>
                    </div>
                </div>
                <span class="text-muted"><?= date('d/m/Y') ?></span>
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle fs-4 text-primary me-2"></i>
                        <span><?= htmlspecialchars($adminName ?? 'Admin') ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><span class="dropdown-item-text text-muted small"><?= htmlspecialchars($adminPhone ?? '') ?></span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/?controller=auth&action=changePassword">
                            <i class="fas fa-key me-2"></i>Đổi mật khẩu</a></li>
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/"><i class="fas fa-globe me-2"></i>Xem trang web</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/admin/?controller=auth&action=logout">
                            <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</a></li>
                    </ul>
                </div>
            </div>
        </header>
        
        <div class="admin-content">
            <?php if (isset($_SESSION['flash']['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle"></i> <?= $_SESSION['flash']['success'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['flash']['success']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['flash']['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle"></i> <?= $_SESSION['flash']['error'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['flash']['error']); ?>
            <?php endif; ?>
            
            <?= $content ?>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Auto-refresh notification count every 30 seconds
    function refreshNotificationCount() {
        fetch('<?= BASE_URL ?>/admin/?controller=don-hang&action=countPending&ajax=1')
            .then(response => response.json())
            .then(data => {
                const count = data.count || 0;
                const badge = document.getElementById('notificationCount');
                const bell = document.getElementById('notificationBell');
                
                if (count > 0) {
                    if (badge) {
                        badge.textContent = count > 99 ? '99+' : count;
                    } else {
                        // Create badge if not exists
                        const newBadge = document.createElement('span');
                        newBadge.id = 'notificationCount';
                        newBadge.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger';
                        newBadge.style.fontSize = '10px';
                        newBadge.textContent = count > 99 ? '99+' : count;
                        bell.appendChild(newBadge);
                    }
                    bell.querySelector('i').classList.remove('text-muted');
                    bell.querySelector('i').classList.add('text-warning');
                } else {
                    if (badge) badge.remove();
                    bell.querySelector('i').classList.remove('text-warning');
                    bell.querySelector('i').classList.add('text-muted');
                }
            })
            .catch(err => console.log('Notification check failed'));
    }
    
    // Check every 30 seconds
    setInterval(refreshNotificationCount, 30000);
    </script>
</body>
</html>
