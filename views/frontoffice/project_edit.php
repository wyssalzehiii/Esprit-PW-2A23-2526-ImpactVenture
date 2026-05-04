<?php require_once __DIR__ . '/../../includes/fo_header.php'; ?>

<div class="hero">
    <div class="container">
        <h1 class="display-4 fw-bold">Edit Project</h1>
        <p class="lead mb-0 mt-3">Update your project details.</p>
    </div>
</div>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-5">
                    <form action="index.php?action=update_project_front&id=<?= $project['id'] ?>" method="post">
                        <div class="mb-3">
                            <label for="title" class="form-label">Project Title</label>
                            <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($project['title']) ?>" required minlength="3" maxlength="255">
                            <div class="form-text">Between 3 and 255 characters.</div>
                        </div>

                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Select a category</option>
                                <option value="FinTech" <?= $project['category'] == 'FinTech' ? 'selected' : '' ?>>FinTech</option>
                                <option value="AgriTech" <?= $project['category'] == 'AgriTech' ? 'selected' : '' ?>>AgriTech</option>
                                <option value="CleanTech" <?= $project['category'] == 'CleanTech' ? 'selected' : '' ?>>CleanTech</option>
                                <option value="AI" <?= $project['category'] == 'AI' ? 'selected' : '' ?>>AI</option>
                                <option value="General" <?= $project['category'] == 'General' ? 'selected' : '' ?>>General</option>
                            </select>
                            <div class="form-text">Choose the category that best fits your project.</div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label">Description (Optional)</label>
                            <textarea class="form-control" id="description" name="description" rows="4" maxlength="1000"><?= htmlspecialchars($project['description'] ?? '') ?></textarea>
                            <div class="form-text">Describe your project briefly (max 1000 characters).</div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Update Project</button>
                            <a href="index.php?action=list_projects_front" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/fo_footer.php'; ?>