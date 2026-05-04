<?php require_once __DIR__ . '/../../includes/bo_header.php'; ?>

<div class="mb-4">
    <h2 class="text-danger">Delete Formation</h2>
</div>

<div class="card border-danger shadow-sm">
    <div class="card-header bg-danger text-white fw-bold">
        Warning: Irreversible Action
    </div>
    <div class="card-body p-4">
        <h5 class="mb-4">Are you absolutely sure you want to delete this formation?</h5>
        
        <div class="p-3 bg-light rounded mb-4 border">
            <p class="mb-1"><strong class="text-muted">ID:</strong> <?= htmlspecialchars($formation['id']) ?></p>
            <p class="mb-0"><strong class="text-muted">Title:</strong> <span class="fw-bold"><?= htmlspecialchars($formation['title']) ?></span></p>
        </div>
        
        <form action="index.php?action=delete_formation&id=<?= htmlspecialchars($formation['id']) ?>" method="POST">
            <button type="submit" class="btn btn-danger btn-lg me-2">Yes, Delete permanently</button>
            <a href="index.php?action=list_formations" class="btn btn-light btn-lg border">Cancel</a>
        </form>

    </div>
</div>

<?php require_once __DIR__ . '/../../includes/bo_footer.php'; ?>
