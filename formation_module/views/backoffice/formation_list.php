<?php require_once __DIR__ . '/../../includes/bo_header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Manage Formations</h2>
    <a href="index.php?action=create_formation" class="btn btn-primary shadow-sm">+ Add New Formation</a>
</div>

<div class="card shadow-sm border-0 rounded">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Title</th>
                        <th>Content Preview</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($formations)): ?>
                        <?php foreach ($formations as $f): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-secondary">#<?= htmlspecialchars($f['id']) ?></td>
                                <td class="fw-semibold text-primary"><?= htmlspecialchars($f['title']) ?></td>
                                <td class="text-muted small">
                                    <?= htmlspecialchars(mb_strimwidth($f['content'], 0, 60, "...")) ?>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="index.php?action=edit_formation&id=<?= $f['id'] ?>" class="btn btn-sm btn-outline-warning mx-1">Edit</a>
                                    <a href="index.php?action=delete_formation&id=<?= $f['id'] ?>" class="btn btn-sm btn-outline-danger">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <h5>No formations found.</h5>
                                <p>Click on "Add New Formation" to create one.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/bo_footer.php'; ?>
