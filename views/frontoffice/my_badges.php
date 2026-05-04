<?php require_once __DIR__ . '/../../includes/fo_header.php'; ?>

<div class="hero">
    <div class="container">
        <h1 class="display-4 fw-bold">My Earned Badges</h1>
        <p class="lead mb-0 mt-3">Badges you've earned by completing formations.</p>
    </div>
</div>

<div class="container mt-5">
    <?php endif; ?>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-5">
        <?php if (!empty($badges)): ?>
            <?php foreach ($badges as $b): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm border-0 card-transition">
                        <!-- Colored Top Border for styling -->
                        <div class="bg-warning" style="height: 5px; width: 100%;"></div>
                        <div class="card-body p-4 text-center">
                            <?php if (!empty($b['image'])): ?>
                                <img src="<?= htmlspecialchars($b['image']) ?>" alt="<?= htmlspecialchars($b['nom']) ?>" class="img-fluid rounded-circle mb-3 shadow-sm" style="max-width: 100px; height: 100px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px; font-size: 2rem; font-weight: bold;">
                                    🏆
                                </div>
                            <?php endif; ?>
                            <h4 class="card-title text-dark fw-bold mb-2"><?= htmlspecialchars($b['nom']) ?></h4>
                            <p class="text-muted mb-2"><strong>Formation:</strong> <?= htmlspecialchars($b['formation_title']) ?></p>
                            <p class="text-success fw-bold mb-2"><?= htmlspecialchars($b['points']) ?> points</p>
                            <small class="text-muted">Earned on: <?= htmlspecialchars(date('M j, Y', strtotime($b['awarded_at']))) ?></small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-light text-center py-5 shadow-sm border" role="alert">
                    <h4 class="text-muted"><i class="bi bi-trophy"></i> No badges earned yet.</h4>
                    <p class="mb-0 mt-2">Complete formations to earn badges!</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/fo_footer.php'; ?>