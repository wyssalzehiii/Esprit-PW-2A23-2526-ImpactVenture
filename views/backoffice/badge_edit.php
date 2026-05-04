<?php require_once __DIR__ . '/../../includes/bo_header.php'; ?>

<div class="mb-4 d-flex align-items-center">
    <a href="index.php?action=list_badges" class="btn btn-sm btn-outline-secondary me-3">&larr; Back</a>
    <h2 class="mb-0">Edit Badge</h2>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        
        <?php if (!empty($errors) && isset($errors[0])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($errors[0]) ?>
            </div>
        <?php endif; ?>

        <?php $formId = $badge_id; ?>

        <!-- 'novalidate' ensures validation is handled strictly by our PHP code -->
        <form action="index.php?action=update_badge&id=<?= htmlspecialchars($formId) ?>" method="POST" novalidate>
            
            <div class="mb-4">
                <label for="nom" class="form-label fw-bold">Name</label>
                <input type="text" 
                       class="form-control form-control-lg <?= isset($errors['nom']) ? 'is-invalid' : '' ?>" 
                       id="nom" name="nom" 
                       value="<?= isset($nom) ? htmlspecialchars($nom) : '' ?>">
                
                <?php if (isset($errors['nom'])): ?>
                    <div class="invalid-feedback fw-bold"><?= htmlspecialchars($errors['nom']) ?></div>
                <?php endif; ?>
                <div class="form-text">Between 3 and 255 characters.</div>
            </div>

            <div class="mb-4">
                <label for="description" class="form-label fw-bold">Description</label>
                <textarea class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>" 
                          id="description" name="description" rows="4"><?= isset($description) ? htmlspecialchars($description) : '' ?></textarea>
                
                <?php if (isset($errors['description'])): ?>
                    <div class="invalid-feedback fw-bold"><?= htmlspecialchars($errors['description']) ?></div>
                <?php endif; ?>
                <div class="form-text">Minimum 10 characters required.</div>
            </div>

            <div class="mb-4">
                <label for="image" class="form-label fw-bold">Image URL (Optional)</label>
                <input type="text" 
                       class="form-control form-control-lg" 
                       id="image" name="image" 
                       value="<?= isset($image) ? htmlspecialchars($image) : '' ?>">
                <div class="form-text">Optional image URL for the badge.</div>
            </div>

            <div class="mb-4">
                <label for="points" class="form-label fw-bold">Points</label>
                <input type="text" 
                       class="form-control form-control-lg <?= isset($errors['points']) ? 'is-invalid' : '' ?>" 
                       id="points" name="points" 
                       value="<?= isset($points) ? htmlspecialchars($points) : '' ?>">
                
                <?php if (isset($errors['points'])): ?>
                    <div class="invalid-feedback fw-bold"><?= htmlspecialchars($errors['points']) ?></div>
                <?php endif; ?>
                <div class="form-text">Points awarded for this badge (0-1000).</div>
            </div>

            <div class="mb-4">
                <label for="formation_id" class="form-label fw-bold">Formation</label>
                <select class="form-select form-select-lg <?= isset($errors['formation_id']) ? 'is-invalid' : '' ?>" 
                        id="formation_id" name="formation_id">
                    <option value="">Select a formation</option>
                    <?php foreach ($formations as $f): ?>
                        <option value="<?= $f['id'] ?>" <?= (isset($formation_id) && $formation_id == $f['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($f['title']) ?> (<?= htmlspecialchars($f['categorie']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <?php if (isset($errors['formation_id'])): ?>
                    <div class="invalid-feedback fw-bold"><?= htmlspecialchars($errors['formation_id']) ?></div>
                <?php endif; ?>
                <div class="form-text">Choose the formation this badge is linked to.</div>
            </div>

            <button type="submit" class="btn btn-warning btn-lg text-dark fw-bold">Update Badge</button>
        </form>

    </div>
</div>

<?php require_once __DIR__ . '/../../includes/bo_footer.php'; ?>