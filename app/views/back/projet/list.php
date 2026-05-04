<?php
$projets = $projets ?? [];
$stats   = $stats ?? [];
$msgs = ['created'=>'Projet créé !','updated'=>'Projet modifié.','deleted'=>'Projet supprimé.'];
$msg = isset($_GET['msg']) ? ($msgs[$_GET['msg']] ?? '') : '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin – Projets | ImpactVenture</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
  <style>
    :root{--iv-green:#1D9E75;}
    .sidebar-dark-primary{background:#1a1f2e;}
    .brand-logo{font-family:'Space Grotesk',sans-serif;font-weight:700;}
    .nav-sidebar .nav-item .nav-link.active{background:var(--iv-green);}
  </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
    <span class="nav-link font-weight-bold">Back Office – Projets</span>
  </nav>

  <aside class="main-sidebar sidebar-dark-primary elevation-2">
    <a href="index.php?action=admin" class="brand-link px-4 py-3 d-flex align-items-center" style="gap:10px;">
      <img src="logo.png" alt="ImpactVenture Logo" style="height: 35px; width: auto; object-fit: contain;">
      <span class="brand-logo brand-text">
        <span style="color:#1D9E75">Impact</span><span style="color:#534AB7">Venture</span>
      </span>
    </a>
    <div class="sidebar"><nav class="mt-3"><ul class="nav nav-pills nav-sidebar flex-column">
      <li class="nav-item"><a href="index.php?action=admin" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>
      <li class="nav-header" style="color:rgba(255,255,255,.3);font-size:.7rem">MODULE 02</li>
      <li class="nav-item"><a href="index.php?action=admin_fiche" class="nav-link"><i class="nav-icon fas fa-building"></i><p>Fiches Entreprises</p></a></li>
      <li class="nav-item"><a href="index.php?action=admin_projet" class="nav-link active"><i class="nav-icon fas fa-lightbulb"></i><p>Projets</p></a></li>
      <li class="nav-item"><a href="index.php?action=trending" class="nav-link"><i class="nav-icon fas fa-chart-line"></i><p>Trending</p></a></li>
    </ul></nav></div>
  </aside>

  <div class="content-wrapper">
    <div class="content-header"><h1 class="m-0 px-4 pt-3">Gestion des Projets</h1></div>
    <section class="content"><div class="container-fluid">

      <?php if($msg): ?><div class="alert alert-success mx-0"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

      <div class="row mb-3">
        <div class="col-lg-3 col-6"><div class="small-box bg-primary"><div class="inner"><h3><?= $stats['total'] ?? 0 ?></h3><p>Total Projets</p></div><div class="icon"><i class="fas fa-lightbulb"></i></div></div></div>
        <div class="col-lg-3 col-6"><div class="small-box bg-success"><div class="inner"><h3><?= $stats['acceptes'] ?? 0 ?></h3><p>Acceptés</p></div><div class="icon"><i class="fas fa-check"></i></div></div></div>
        <div class="col-lg-3 col-6"><div class="small-box bg-warning"><div class="inner"><h3><?= $stats['en_evaluation'] ?? 0 ?></h3><p>En Évaluation</p></div><div class="icon"><i class="fas fa-hourglass"></i></div></div></div>
        <div class="col-lg-3 col-6"><div class="small-box bg-info"><div class="inner"><h3><?= $stats['soumis'] ?? 0 ?></h3><p>Soumis</p></div><div class="icon"><i class="fas fa-inbox"></i></div></div></div>
      </div>

      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h3 class="card-title">Liste des Projets</h3>
          <a href="index.php?action=projet_create" class="btn btn-sm" style="background:#1D9E75;color:#fff;"><i class="fas fa-plus mr-1"></i>Nouveau Projet</a>
        </div>
        <div class="card-body table-responsive p-0">
          <table class="table table-hover">
            <thead><tr>
              <th>ID</th><th>Titre</th><th>Entreprise (Jointure)</th><th>Statut</th><th>Score IA</th><th>Date</th><th>Actions</th>
            </tr></thead>
            <tbody>
              <?php foreach($projets as $p): ?>
              <tr>
                <td><?= $p['id_projet'] ?></td>
                <td><strong><?= htmlspecialchars($p['titre']) ?></strong></td>
                <td>
                  <span class="badge badge-info"><?= htmlspecialchars($p['entreprise_nom'] ?? '—') ?></span>
                  <br><small class="text-muted"><?= htmlspecialchars($p['entreprise_categorie'] ?? '') ?></small>
                </td>
                <td>
                  <span class="badge badge-<?= $p['statut']==='accepté'?'success':($p['statut']==='en_evaluation'?'warning':'secondary') ?>">
                    <?= htmlspecialchars($p['statut']) ?>
                  </span>
                </td>
                <td><?= $p['score_ia'] ? '<strong>'.round($p['score_ia']).'</strong>' : '—' ?></td>
                <td><small><?= date('d/m/Y', strtotime($p['date_soumission'])) ?></small></td>
                <td>
                  <a href="index.php?action=admin_projet_edit&id=<?= $p['id_projet'] ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                  <a href="index.php?action=admin_projet_delete&id=<?= $p['id_projet'] ?>" onclick="return confirm('Supprimer ce projet ?')" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div></section>
  </div>
  <footer class="main-footer py-2 px-4"><small class="text-muted">ImpactVenture — Esprit 2025–2026</small></footer>
</div>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
