<?php require_once __DIR__ . '/../../includes/fo_header.php'; ?>

<div class="hero">
    <div class="container">
        <h1 class="display-4 fw-bold">My Recommended Formations</h1>
        <p class="lead mb-0 mt-3">Formations tailored to your project category.</p>
    </div>
</div>

<div class="container mt-5">

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-5">
        <?php if (!empty($formations)): ?>
            <?php foreach ($formations as $f): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm border-0 card-transition">
                        <!-- Colored Top Border for styling -->
                        <div class="bg-info" style="height: 5px; width: 100%;"></div>
                        <div class="card-body p-4">
                            <h4 class="card-title text-dark fw-bold mb-3"><?= htmlspecialchars($f['title']) ?></h4>
                            <p class="text-info mb-2"><strong>Category:</strong> <?= htmlspecialchars($f['categorie']) ?></p>
                            <?php 
                                $snippet = mb_strimwidth($f['content'], 0, 110, "...");
                            ?>
                            <p class="card-text text-muted"><?= htmlspecialchars($snippet) ?></p>
                        </div>
                        <div class="card-footer bg-transparent border-top-0 p-4 pt-0">
                            <a href="index.php?action=detail_formation_front&id=<?= $f['id'] ?>" class="btn btn-outline-info w-100 fw-bold">Read More</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-light text-center py-5 shadow-sm border" role="alert">
                    <h4 class="text-muted"><i class="bi bi-info-circle"></i> No recommendations available.</h4>
                    <p class="mb-0 mt-2">Create a project to get personalized formation recommendations.</p>
                    <a href="index.php?action=list_projects_front" class="btn btn-primary mt-3">Manage Projects</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/fo_footer.php'; ?>