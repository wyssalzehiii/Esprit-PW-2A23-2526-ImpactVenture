<?php
$projets = $projets ?? [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - Projets | ImpactVenture</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="hold-transition sidebar-mini">

<div class="wrapper">
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
    <span class="nav-link font-weight-bold">Back Office - Projets</span>
  </nav>

  <aside class="main-sidebar sidebar-dark-primary elevation-2">
    <a href="index.php?action=admin" class="brand-link">
      <span class="brand-logo text-[#1D9E75]">Impact</span>
      <span class="brand-logo text-white">Venture</span>
    </a>
    <div class="sidebar">
      <nav class="mt-3">
        <ul class="nav nav-pills nav-sidebar flex-column">
          <li class="nav-item"><a href="index.php?action=admin" class="nav-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
          <li class="nav-item"><a href="index.php?action=admin_fiche" class="nav-link"><i class="fas fa-building"></i> Fiches Entreprises</a></li>
          <li class="nav-item"><a href="index.php?action=admin_projet" class="nav-link active"><i class="fas fa-lightbulb"></i> Projets</a></li>
        </ul>
      </nav>
    </div>
  </aside>

  <div class="content-wrapper">
    <div class="content-header">
      <h1 class="m-0">Gestion des Projets</h1>
    </div>

    <section class="content">
      <div class="container-fluid">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Liste des Projets</h3>
          </div>
          <div class="card-body table-responsive p-0">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Titre</th>
                  <th>Entreprise</th>
                  <th>Statut</th>
                  <th>Score IA</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($projets as $p): ?>
                <tr>
                  <td><?= $p['id_projet'] ?></td>
                  <td><strong><?= htmlspecialchars($p['titre']) ?></strong></td>
                  <td><?= htmlspecialchars($p['entreprise_nom'] ?? '—') ?></td>
                  <td>
                    <span class="badge badge-<?= $p['statut'] === 'accepté' ? 'success' : 'warning' ?>">
                      <?= htmlspecialchars($p['statut']) ?>
                    </span>
                  </td>
                  <td><?= $p['score_ia'] ? round($p['score_ia']) : '—' ?></td>
                  <td>
                    <button class="btn btn-sm btn-info">Évaluer</button>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>