<?php require_once __DIR__ . '/../../includes/fo_header.php'; ?>

<div class="hero">
    <div class="container">
        <h1 class="display-4 fw-bold">Delete Project</h1>
        <p class="lead mb-0 mt-3">Are you sure you want to delete this project?</p>
    </div>
</div>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-5">
                    <div class="alert alert-warning" role="alert">
                        <h4 class="alert-heading">Warning!</h4>
                        <p>This action cannot be undone. Deleting this project will remove it from your recommendations.</p>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($project['title']) ?></h5>
                            <p class="card-text"><strong>Category:</strong> <?= htmlspecialchars($project['category']) ?></p>
                            <?php if (!empty($project['description'])): ?>
                                <p class="card-text"><?= htmlspecialchars($project['description']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <form action="index.php?action=delete_project_front&id=<?= $project['id'] ?>" method="post">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-danger">Yes, Delete Project</button>
                            <a href="index.php?action=list_projects_front" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/fo_footer.php'; ?>