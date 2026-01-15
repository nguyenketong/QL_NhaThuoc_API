<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-pills"></i></div>
            <div>
                <div class="stat-value"><?= $tongThuoc ?? 0 ?></div>
                <div class="stat-label">Sản phẩm</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-shopping-cart"></i></div>
            <div>
                <div class="stat-value"><?= $tongDonHang ?? 0 ?></div>
                <div class="stat-label">Đơn hàng</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon orange"><i class="fas fa-users"></i></div>
            <div>
                <div class="stat-value"><?= $tongKhachHang ?? 0 ?></div>
                <div class="stat-label">Khách hàng</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon purple"><i class="fas fa-calendar-day"></i></div>
            <div>
                <div class="stat-value"><?= number_format($doanhThuHomNay ?? 0, 0, ',', '.') ?>đ</div>
                <div class="stat-label">Doanh thu hôm nay</div>
            </div>
        </div>
    </div>
</div>

<!-- Doanh thu tháng -->
<div class="row g-4 mb-4">
    <div class="col-md-12">
        <div class="stat-card">
            <div class="stat-icon red"><i class="fas fa-money-bill-wave"></i></div>
            <div>
                <div class="stat-value"><?= number_format($doanhThuThang ?? 0, 0, ',', '.') ?>đ</div>
                <div class="stat-label">Doanh thu tháng <?= date('m/Y') ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <!-- Biểu đồ doanh thu 7 ngày -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-chart-line text-primary"></i> Doanh thu 7 ngày gần nhất</h6>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="120"></canvas>
            </div>
        </div>
    </div>
    <!-- Biểu đồ đơn hàng theo trạng thái -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-chart-pie text-success"></i> Đơn hàng theo trạng thái</h6>
            </div>
            <div class="card-body">
                <canvas id="orderStatusChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Order Status Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <h3 class="text-warning"><?= $choXuLy ?? 0 ?></h3>
                <small class="text-muted">Chờ xử lý</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <h3 class="text-info"><?= $dangGiao ?? 0 ?></h3>
                <small class="text-muted">Đang giao</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <h3 class="text-success"><?= $hoanThanh ?? 0 ?></h3>
                <small class="text-muted">Hoàn thành</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <h3 class="text-danger"><?= $daHuy ?? 0 ?></h3>
                <small class="text-muted">Đã hủy</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Orders -->
    <div class="col-lg-8">
        <div class="admin-table">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-clock text-primary"></i> Đơn hàng gần đây</h6>
                <a href="<?= BASE_URL ?>/admin/?controller=don-hang" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
            </div>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Mã ĐH</th>
                        <th>Khách hàng</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Ngày đặt</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($donHangGanDay)): ?>
                        <?php foreach ($donHangGanDay as $dh): ?>
                            <tr>
                                <td><strong>#<?= $dh['MaDonHang'] ?></strong></td>
                                <td><?= htmlspecialchars($dh['HoTen'] ?? 'N/A') ?></td>
                                <td><?= number_format($dh['TongTien'] ?? 0, 0, ',', '.') ?>đ</td>
                                <td>
                                    <?php
                                    switch ($dh['TrangThai']) {
                                        case 'Cho xu ly': echo '<span class="badge bg-warning">Chờ xử lý</span>'; break;
                                        case 'Dang giao': echo '<span class="badge bg-info">Đang giao</span>'; break;
                                        case 'Hoan thanh': echo '<span class="badge bg-success">Hoàn thành</span>'; break;
                                        default: echo '<span class="badge bg-danger">Đã hủy</span>';
                                    }
                                    ?>
                                </td>
                                <td><?= date('d/m/Y', strtotime($dh['NgayDatHang'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center text-muted">Chưa có đơn hàng</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Top Products -->
    <div class="col-lg-4">
        <div class="admin-table">
            <div class="p-3 border-bottom">
                <h6 class="mb-0"><i class="fas fa-fire text-danger"></i> Top bán chạy</h6>
            </div>
            <div class="p-3">
                <?php if (!empty($topThuocBanChay)): ?>
                    <?php foreach ($topThuocBanChay as $item): ?>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span><?= htmlspecialchars($item['TenThuoc']) ?></span>
                            <span class="badge bg-primary"><?= $item['TongBan'] ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted text-center">Chưa có dữ liệu</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Biểu đồ doanh thu 7 ngày
    var revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode($chartLabels ?? []) ?>,
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: <?= json_encode($chartData ?? []) ?>,
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('vi-VN') + 'đ';
                        }
                    }
                }
            }
        }
    });

    // Biểu đồ đơn hàng theo trạng thái
    var orderCtx = document.getElementById('orderStatusChart').getContext('2d');
    new Chart(orderCtx, {
        type: 'doughnut',
        data: {
            labels: ['Chờ xử lý', 'Đang giao', 'Hoàn thành', 'Đã hủy'],
            datasets: [{
                data: [<?= $choXuLy ?? 0 ?>, <?= $dangGiao ?? 0 ?>, <?= $hoanThanh ?? 0 ?>, <?= $daHuy ?? 0 ?>],
                backgroundColor: ['#ffc107', '#17a2b8', '#28a745', '#dc3545']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
