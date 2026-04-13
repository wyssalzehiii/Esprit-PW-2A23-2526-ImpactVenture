<?php require_once __DIR__ . '/../../includes/bo_header.php'; ?>

<div class="mb-4 d-flex align-items-center">
    <a href="index.php?action=list_formations" class="btn btn-sm btn-outline-secondary me-3">&larr; Back</a>
    <h2 class="mb-0">Create New Formation</h2>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        
        <?php if (!empty($errors) && isset($errors[0])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($errors[0]) ?>
            </div>
        <?php endif; ?>

        <!-- 'novalidate' ensures validation is handled strictly by our PHP code -->
        <form action="index.php?action=store_formation" method="POST" novalidate>
            
            <div class="mb-4">
                <label for="title" class="form-label fw-bold">Title</label>
                <input type="text" 
                       class="form-control form-control-lg <?= isset($errors['title']) ? 'is-invalid' : '' ?>" 
                       id="title" name="title" 
                       value="<?= isset($title) ? htmlspecialchars($title) : '' ?>" 
                       placeholder="Enter formation title">
                
                <?php if (isset($errors['title'])): ?>
                    <div class="invalid-feedback fw-bold"><?= htmlspecialchars($errors['title']) ?></div>
                <?php endif; ?>
                <div class="form-text">Between 5 and 255 characters.</div>
            </div>

            <div class="mb-4">
                <label for="content" class="form-label fw-bold">Content</label>
                <textarea class="form-control <?= isset($errors['content']) ? 'is-invalid' : '' ?>" 
                          id="content" name="content" rows="8" 
                          placeholder="Write the full description of the formation here..."><?= isset($content) ? htmlspecialchars($content) : '' ?></textarea>
                
                <?php if (isset($errors['content'])): ?>
                    <div class="invalid-feedback fw-bold"><?= htmlspecialchars($errors['content']) ?></div>
                <?php endif; ?>
                <div class="form-text">Minimum 20 characters required.</div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg">Save Formation</button>
        </form>

    </div>
</div>

<?php require_once __DIR__ . '/../../includes/bo_footer.php'; ?>
