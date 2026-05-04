<?php require_once __DIR__ . '/../../includes/fo_header.php'; ?>

<div class="hero">
    <div class="container">
        <h1 class="display-4 fw-bold">Our Badges</h1>
        <p class="lead mb-0 mt-3">Explore the badges you can earn through our formations.</p>
    </div>
</div>

<div class="container mt-5">

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-5">
        <?php if (!empty($badges)): ?>
            <?php foreach ($badges as $b): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm border-0 card-transition">
                        <!-- Colored Top Border for styling -->
                        <div class="bg-success" style="height: 5px; width: 100%;"></div>
                        <div class="card-body p-4">
                            <h4 class="card-title text-dark fw-bold mb-3"><?= htmlspecialchars($b['nom']) ?></h4>
                            <p class="text-muted mb-2"><strong>Formation:</strong> <?= htmlspecialchars($b['formation_title']) ?></p>
                            <p class="text-success fw-bold mb-2"><?= htmlspecialchars($b['points']) ?> points</p>
                            <?php 
                                $snippet = mb_strimwidth($b['description'], 0, 100, "...");
                            ?>
                            <p class="card-text text-muted"><?= htmlspecialchars($snippet) ?></p>
                        </div>
                        <div class="card-footer bg-transparent border-top-0 p-4 pt-0">
                            <a href="index.php?action=detail_badge_front&id=<?= $b['id'] ?>" class="btn btn-outline-success w-100 fw-bold">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-light text-center py-5 shadow-sm border" role="alert">
                    <h4 class="text-muted"><i class="bi bi-info-circle"></i> No badges available at the moment.</h4>
                    <p class="mb-0 mt-2">Please check back later.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/fo_footer.php'; ?>