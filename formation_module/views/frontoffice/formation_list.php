<?php require_once __DIR__ . '/../../includes/fo_header.php'; ?>

<div class="hero">
    <div class="container">
        <h1 class="display-4 fw-bold">Our Formations</h1>
        <p class="lead mb-0 mt-3">Discover our latest formations and boost your skills with ImpactVenture.</p>
    </div>
</div>

<div class="container mt-5">
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger shadow-sm" role="alert">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-5">
        <?php if (!empty($formations)): ?>
            <?php foreach ($formations as $f): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm border-0 card-transition">
                        <!-- Colored Top Border for styling -->
                        <div class="bg-primary" style="height: 5px; width: 100%;"></div>
                        <div class="card-body p-4">
                            <h4 class="card-title text-dark fw-bold mb-3"><?= htmlspecialchars($f['title']) ?></h4>
                            <?php 
                                $snippet = mb_strimwidth($f['content'], 0, 110, "...");
                            ?>
                            <p class="card-text text-muted"><?= htmlspecialchars($snippet) ?></p>
                        </div>
                        <div class="card-footer bg-transparent border-top-0 p-4 pt-0">
                            <a href="index.php?action=detail_formation_front&id=<?= $f['id'] ?>" class="btn btn-outline-primary w-100 fw-bold">Lire plus (Read more)</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-light text-center py-5 shadow-sm border" role="alert">
                    <h4 class="text-muted"><i class="bi bi-info-circle"></i> No formations available at the moment.</h4>
                    <p class="mb-0 mt-2">Please check back later or contact administrators.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/fo_footer.php'; ?>
