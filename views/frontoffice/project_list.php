<?php require_once __DIR__ . '/../../includes/fo_header.php'; ?>

<div class="hero">
    <div class="container">
        <h1 class="display-4 fw-bold">My Projects</h1>
        <p class="lead mb-0 mt-3">Manage your projects to get personalized formation recommendations.</p>
    </div>
</div>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Projects</h2>
        <a href="index.php?action=create_project_front" class="btn btn-primary">+ Create New Project</a>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php if (!empty($projects)): ?>
            <?php foreach ($projects as $project): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm border-0 card-transition">
                        <div class="bg-success" style="height: 5px; width: 100%;"></div>
                        <div class="card-body p-4">
                            <h4 class="card-title text-dark fw-bold mb-3"><?= htmlspecialchars($project['title']) ?></h4>
                            <p class="card-text text-muted mb-2"><strong>Category:</strong> <?= htmlspecialchars($project['category']) ?></p>
                            <?php if (!empty($project['description'])): ?>
                                <p class="card-text text-muted"><?= htmlspecialchars(substr($project['description'], 0, 100)) ?>...</p>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer bg-transparent border-top-0 p-4 pt-0">
                            <div class="d-flex gap-2">
                                <a href="index.php?action=edit_project_front&id=<?= $project['id'] ?>" class="btn btn-outline-secondary btn-sm">Edit</a>
                                <a href="index.php?action=delete_project_front&id=<?= $project['id'] ?>" class="btn btn-outline-danger btn-sm">Delete</a>
                                <a href="index.php?action=toggle_project_link&id=<?= $project['id'] ?>" class="btn btn-outline-<?= $project['linked_to_path'] ? 'success' : 'warning' ?> btn-sm">
                                    <?= $project['linked_to_path'] ? 'Linked to Path' : 'Link to Path' ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-light text-center py-5 shadow-sm border" role="alert">
                    <h4 class="text-muted"><i class="bi bi-folder"></i> No projects yet.</h4>
                    <p class="mb-0 mt-2">Create your first project to get started with personalized recommendations.</p>
                    <a href="index.php?action=create_project_front" class="btn btn-primary mt-3">Create Project</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/fo_footer.php'; ?>