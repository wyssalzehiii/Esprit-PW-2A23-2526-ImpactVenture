<?php require_once __DIR__ . '/../../includes/fo_header.php'; ?>

<div class="container mt-5 mb-5">
    <div class="mb-4">
        <a href="index.php?action=list_formations_front" class="btn btn-outline-primary fw-bold px-4">
            &larr; Back to all Formations
        </a>
    </div>

    <div class="card shadow-lg border-0 overflow-hidden">
        <div class="card-header bg-primary text-white py-4 px-5 border-0">
            <h1 class="mb-1 fw-bold"><?= htmlspecialchars($formation['title']) ?></h1>
            <div class="opacity-75 small">
                <!-- If timestamp fields exist, this is where they usually appear -->
                <span>ImpactVenture Global Curriculum</span>
            </div>
        </div>
        <div class="card-body p-5 bg-white">
            <div class="content-text text-dark" style="white-space: pre-wrap; line-height: 1.8; font-size: 1.15rem;">
<?= htmlspecialchars($formation['content']) ?>
            </div>
            <div class="mt-4">
                <a href="index.php?action=complete_formation&id=<?= $formation['id'] ?>" class="btn btn-success btn-lg fw-bold">Mark as Complete</a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/fo_footer.php'; ?>
