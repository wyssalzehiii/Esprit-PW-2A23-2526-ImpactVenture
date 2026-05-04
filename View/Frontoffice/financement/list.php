<?php
// app/views/front/financement/list.php
require_once __DIR__ . '/../../../config/database.php'; // just in case
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Financement - ImpactVenture</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .investor-card { transition: all 0.3s; }
        .investor-card:hover { transform: translateY(-8px); box-shadow: 0 10px 20px rgba(0,0,0,0.15); }
        .green-header { background: linear-gradient(135deg, #28a745, #20c997); color: white; }
    </style>
</head>
<body class="pb-5">

<div class="container mt-5">
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold green-header p-4 rounded-3">
            <i class="fas fa-handshake me-3"></i> Financement & Investisseurs à Impact
        </h1>
        <p class="lead text-muted">Connectez votre projet à des investisseurs tunisiens qui croient en l'IA + Green</p>
    </div>

    <?php if (isset($msg)): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= htmlspecialchars($msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <?php foreach ($investisseurs as $inv): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 investor-card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($inv['nom']) ?></h5>
                        <h6 class="text-primary"><?= htmlspecialchars($inv['organisation']) ?></h6>

                        <span class="badge bg-success mb-2"><?= htmlspecialchars($inv['secteur_focus']) ?></span>

                        <p class="card-text text-muted small">
                            <?= nl2br(htmlspecialchars($inv['description'])) ?>
                        </p>

                        <div class="mb-3">
                            <strong>Montant :</strong>
                            <?= number_format($inv['montant_min'], 0) ?> -
                            <?= number_format($inv['montant_max'], 0) ?> TND
                        </div>

                        <button class="btn btn-success w-100"
                                data-bs-toggle="modal"
                                data-bs-target="#applyModal"
                                data-id="<?= $inv['id'] ?>"
                                data-name="<?= htmlspecialchars($inv['nom']) ?>">
                            <i class="fas fa-paper-plane"></i> Demander un financement
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="applyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Demande de financement</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="?action=financement_submit" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="investisseur_id" id="modal_investisseur_id">
                    <input type="hidden" name="fiche_entreprise_id" value="1"> <!-- Changez plus tard selon l'utilisateur connecté -->

                    <div class="mb-3">
                        <label class="form-label">À :</label>
                        <p id="modal_investisseur_name" class="fw-bold"></p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Votre message / Pitch</label>
                        <textarea name="message" class="form-control" rows="5" placeholder="Présentez brièvement votre projet..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Envoyer la demande</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Fill modal with investor data
    const modal = document.getElementById('applyModal');
    modal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        document.getElementById('modal_investisseur_id').value = button.getAttribute('data-id');
        document.getElementById('modal_investisseur_name').textContent = button.getAttribute('data-name');
    });
</script>

</body>
</html>