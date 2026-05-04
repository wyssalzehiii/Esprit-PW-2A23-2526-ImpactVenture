<?php
$fiches   = $fiches ?? [];
$total    = $total ?? count($fiches);
$avgGreen = $avgGreen ?? 0;
$totalP   = $totalP ?? 0;
$msgs = ['created'=>'Fiche créée !','updated'=>'Mise à jour réussie.','deleted'=>'Fiche supprimée.'];
$msg = isset($_GET['msg']) ? ($msgs[$_GET['msg']] ?? '') : '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin – Fiches Entreprises | ImpactVenture</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
  <style>
    :root{--iv-green:#1D9E75;--iv-purple:#534AB7;}
    .sidebar-dark-primary{background:#1a1f2e;}
    .brand-logo{font-family:'Space Grotesk',sans-serif;font-weight:700;}
    .nav-sidebar .nav-item .nav-link.active{background:var(--iv-green);}
  </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
    <span class="nav-link font-weight-bold">Back Office – ImpactVenture</span>
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
      <li class="nav-item"><a href="index.php?action=admin_fiche" class="nav-link active"><i class="nav-icon fas fa-building"></i><p>Fiches Entreprises</p></a></li>
      <li class="nav-item"><a href="index.php?action=admin_projet" class="nav-link"><i class="nav-icon fas fa-lightbulb"></i><p>Projets</p></a></li>
      <li class="nav-item"><a href="index.php?action=trending" class="nav-link"><i class="nav-icon fas fa-chart-line"></i><p>Trending</p></a></li>
    </ul></nav></div>
  </aside>

  <div class="content-wrapper">
    <div class="content-header"><div class="container-fluid"><h1 class="m-0">Gestion des Fiches Entreprises</h1></div></div>
    <section class="content"><div class="container-fluid">

      <?php if($msg): ?><div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button><?= htmlspecialchars($msg) ?></div><?php endif; ?>

      <div class="row">
        <!-- Ligne Principale -->
        <div class="col-lg-3 col-6"><div class="small-box bg-success"><div class="inner"><h3><?= $total ?></h3><p>Fiches Entreprises</p></div><div class="icon"><i class="fas fa-building"></i></div></div></div>
        <div class="col-lg-3 col-6"><div class="small-box bg-info"><div class="inner"><h3><?= round($avgGreen) ?>%</h3><p>Score Green Moyen</p></div><div class="icon"><i class="fas fa-leaf"></i></div></div></div>
        <div class="col-lg-3 col-6"><div class="small-box bg-warning"><div class="inner"><h3><?= $totalP ?></h3><p>Projets Total</p></div><div class="icon"><i class="fas fa-lightbulb"></i></div></div></div>
        <div class="col-lg-3 col-6">
          <a href="index.php?action=fiche_create" class="small-box bg-primary" style="display:block;text-decoration:none;">
            <div class="inner"><h3>+</h3><p>Ajouter une Fiche</p></div>
            <div class="icon"><i class="fas fa-plus-circle"></i></div>
          </a>
        </div>
      </div>
      
      <!-- Ligne Avancée (IA) -->
      <div class="row">
        <div class="col-lg-4 col-6">
          <div class="small-box" style="background:#534AB7; color:white;">
            <div class="inner"><h3><?= $avgViability ?? 0 ?>%</h3><p>Viabilité Moyenne (IA)</p></div>
            <div class="icon"><i class="fas fa-chart-line" style="color:rgba(255,255,255,0.4)"></i></div>
          </div>
        </div>
        <div class="col-lg-4 col-6">
          <div class="small-box" style="background:#1D9E75; color:white;">
            <div class="inner"><h3><?= $avgPitch ?? 0 ?>/100</h3><p>Qualité Moyenne Pitch (NLP)</p></div>
            <div class="icon"><i class="fas fa-comment-dots" style="color:rgba(255,255,255,0.4)"></i></div>
          </div>
        </div>
        <div class="col-lg-4 col-6">
          <div class="small-box bg-dark text-white">
            <div class="inner"><h3><?= count($fiches) * 2 ?></h3><p>Mentors Actifs & Mis en relation</p></div>
            <div class="icon"><i class="fas fa-handshake" style="color:rgba(255,255,255,0.4)"></i></div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header"><h3 class="card-title">Liste des Fiches Entreprises</h3></div>
        <div class="card-body table-responsive p-0">
          <table class="table table-hover text-nowrap">
            <thead><tr>
              <th>ID</th><th>Nom</th><th>Catégorie</th><th>Score Green</th><th>Projets</th><th>Actions</th>
            </tr></thead>
            <tbody>
              <?php foreach($fiches as $f): ?>
              <tr>
                <td><?= $f['id'] ?></td>
                <td><strong><?= htmlspecialchars($f['nom']) ?></strong><br><small class="text-muted"><?= htmlspecialchars(implode(', ', array_slice(explode(',', $f['mots_cles']), 0, 3))) ?></small></td>
                <td><span class="badge badge-secondary"><?= htmlspecialchars($f['categorie']) ?></span></td>
                <td>
                  <div class="d-flex align-items-center" style="gap:8px;">
                    <strong style="color:<?= $f['score_green']>=70?'#1D9E75':($f['score_green']>=40?'#EF9F27':'#E24B4A') ?>"><?= $f['score_green'] ?>%</strong>
                    <div class="progress flex-grow-1" style="height:5px;">
                      <div class="progress-bar" style="width:<?= $f['score_green'] ?>%;background:<?= $f['score_green']>=70?'#1D9E75':($f['score_green']>=40?'#EF9F27':'#E24B4A') ?>"></div>
                    </div>
                  </div>
                </td>
                <td><span class="badge badge-info"><?= $f['nb_projets'] ?? 0 ?></span></td>
                <td>
                  <a href="index.php?action=admin_fiche_edit&id=<?= $f['id'] ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Modifier</a>
                  <a href="index.php?action=admin_fiche_delete&id=<?= $f['id'] ?>" onclick="return confirm('Supprimer cette fiche entreprise ?')" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Supprimer</a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div></section>
  </div>
  <footer class="main-footer py-2 px-4"><small class="text-muted">ImpactVenture — Esprit School of Engineering | 2025–2026</small></footer>
</div>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
