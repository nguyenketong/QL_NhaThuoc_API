<?php
/**
 * Danh sách bài viết - Góc sức khỏe
 */
?>
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Trang chủ</a></li>
            <li class="breadcrumb-item active">Góc sức khỏe</li>
        </ol>
    </nav>

    <div class="section-header text-center mb-4">
        <h1 class="text-primary fw-bold"><i class="fas fa-heartbeat"></i> GÓC SỨC KHỎE</h1>
        <p class="text-muted">Chia sẻ kiến thức y tế, sức khỏe và chăm sóc bản thân</p>
    </div>

    <div class="row g-4">
        <?php if (!empty($baiViets)): ?>
            <?php foreach ($baiViets as $bv): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 article-card">
                        <?php if (!empty($bv['HinhAnh'])): ?>
                            <img src="<?= $bv['HinhAnh'] ?>" class="card-img-top" alt="<?= htmlspecialchars($bv['TieuDe']) ?>" style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="fas fa-newspaper fa-4x text-muted"></i>
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="<?= BASE_URL ?>/baiViet/chiTiet/<?= $bv['MaBaiViet'] ?>" class="text-decoration-none text-dark stretched-link">
                                    <?= htmlspecialchars($bv['TieuDe']) ?>
                                </a>
                            </h5>
                            <p class="card-text text-muted small">
                                <?= mb_substr(strip_tags($bv['NoiDung'] ?? ''), 0, 120) ?>...
                            </p>
                        </div>
                        <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i> <?= date('d/m/Y', strtotime($bv['NgayDang'])) ?>
                            </small>
                            <small class="text-muted">
                                <i class="fas fa-eye me-1"></i> <?= $bv['LuotXem'] ?? 0 ?> lượt xem
                            </small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle"></i> Chưa có bài viết nào
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if (isset($totalPages) && $totalPages > 1): ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item <?= $currentPage == 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= BASE_URL ?>/baiViet/danhSach?page=<?= $currentPage - 1 ?>">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                        <a class="page-link" href="<?= BASE_URL ?>/baiViet/danhSach?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $currentPage == $totalPages ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= BASE_URL ?>/baiViet/danhSach?page=<?= $currentPage + 1 ?>">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<style>
.article-card {
    transition: all 0.3s ease;
}
.article-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}
</style>
