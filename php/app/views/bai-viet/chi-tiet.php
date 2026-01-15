<?php
/**
 * Chi tiết bài viết
 */
?>
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/baiViet/danhSach">Tin tức</a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($baiViet['TieuDe']) ?></li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <article>
                <h1 class="mb-3"><?= htmlspecialchars($baiViet['TieuDe']) ?></h1>
                
                <div class="text-muted mb-4">
                    <i class="fas fa-calendar"></i> <?= date('d/m/Y H:i', strtotime($baiViet['NgayDang'])) ?>
                    <?php if (!empty($baiViet['TacGia'])): ?>
                        <span class="mx-2">|</span>
                        <i class="fas fa-user"></i> <?= htmlspecialchars($baiViet['TacGia']) ?>
                    <?php endif; ?>
                </div>

                <?php if (!empty($baiViet['HinhAnh'])): ?>
                    <img src="<?= $baiViet['HinhAnh'] ?>" alt="<?= htmlspecialchars($baiViet['TieuDe']) ?>" class="img-fluid rounded mb-4">
                <?php endif; ?>

                <div class="article-content">
                    <?= $baiViet['NoiDung'] ?>
                </div>

                <!-- Share -->
                <div class="mt-4 pt-4 border-top">
                    <strong>Chia sẻ:</strong>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(BASE_URL . '/baiViet/chiTiet/' . $baiViet['MaBaiViet']) ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                        <i class="fab fa-facebook-f"></i> Facebook
                    </a>
                    <a href="https://twitter.com/intent/tweet?url=<?= urlencode(BASE_URL . '/baiViet/chiTiet/' . $baiViet['MaBaiViet']) ?>" target="_blank" class="btn btn-outline-info btn-sm">
                        <i class="fab fa-twitter"></i> Twitter
                    </a>
                </div>
            </article>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-newspaper"></i> Bài viết liên quan</h5>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($baiVietLienQuan)): ?>
                        <?php foreach ($baiVietLienQuan as $bv): ?>
                            <a href="<?= BASE_URL ?>/baiViet/chiTiet/<?= $bv['MaBaiViet'] ?>" class="d-flex p-3 border-bottom text-decoration-none text-dark">
                                <?php if (!empty($bv['HinhAnh'])): ?>
                                    <img src="<?= $bv['HinhAnh'] ?>" alt="" style="width: 80px; height: 60px; object-fit: cover;" class="rounded me-3">
                                <?php else: ?>
                                    <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 60px;">
                                        <i class="fas fa-newspaper text-muted"></i>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <h6 class="mb-1 small"><?= htmlspecialchars($bv['TieuDe']) ?></h6>
                                    <small class="text-muted"><?= date('d/m/Y', strtotime($bv['NgayDang'])) ?></small>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted text-center p-3">Không có bài viết liên quan</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
