<?php require_once __DIR__ . '/../../includes/fo_header.php'; ?>

<div class="hero">
    <div class="container">
        <h1 class="display-4 fw-bold">Create New Project</h1>
        <p class="lead mb-0 mt-3">Define your project to unlock tailored formation recommendations.</p>
    </div>
</div>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-5">
                    <form action="index.php?action=store_project_front" method="post">
                        <div class="mb-3">
                            <label for="title" class="form-label">Project Title</label>
                            <input type="text" class="form-control" id="title" name="title" required minlength="3" maxlength="255">
                            <div class="form-text">Between 3 and 255 characters.</div>
                        </div>

                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Select a category</option>
                                <option value="FinTech">FinTech</option>
                                <option value="AgriTech">AgriTech</option>
                                <option value="CleanTech">CleanTech</option>
                                <option value="AI">AI</option>
                                <option value="General">General</option>
                            </select>
                            <div class="form-text">Choose the category that best fits your project.</div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label">Description (Optional)</label>
                            <textarea class="form-control" id="description" name="description" rows="4" maxlength="1000"></textarea>
                            <div class="form-text">Describe your project briefly (max 1000 characters).</div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Create Project</button>
                            <a href="index.php?action=list_projects_front" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/fo_footer.php'; ?>