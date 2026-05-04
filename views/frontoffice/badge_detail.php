<?php require_once __DIR__ . '/../../includes/fo_header.php'; ?>

<div class="container mt-5 mb-5">
    <div class="mb-4">
        <a href="index.php?action=list_badges_front" class="btn btn-outline-success fw-bold px-4">
            &larr; Back to all Badges
        </a>
    </div>

    <div class="card shadow-lg border-0 overflow-hidden">
        <div class="card-header bg-success text-white py-4 px-5 border-0">
            <h1 class="mb-1 fw-bold"><?= htmlspecialchars($badge['nom']) ?></h1>
            <div class="opacity-75 small">
                <span>Formation: <?= htmlspecialchars($badge['formation_title']) ?> | Points: <?= htmlspecialchars($badge['points']) ?></span>
            </div>
        </div>
        <div class="card-body p-5 bg-white">
            <?php if (!empty($badge['image'])): ?>
                <div class="text-center mb-4">
                    <img src="<?= htmlspecialchars($badge['image']) ?>" alt="<?= htmlspecialchars($badge['nom']) ?>" class="img-fluid rounded shadow-sm" style="max-width: 200px;">
                </div>
            <?php endif; ?>
            <div class="content-text text-dark" style="white-space: pre-wrap; line-height: 1.8; font-size: 1.15rem;">
<?= htmlspecialchars($badge['description']) ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/fo_footer.php'; ?>