<?php
$fiches = $fiches ?? [];
$total = $total ?? count($fiches);
$avgGreen = $avgGreen ?? 0;
$totalP = $totalP ?? 0;
$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - Fiches Entreprises | ImpactVenture</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    :root { --iv-green: #1D9E75; }
    .brand-logo { font-family: 'Space Grotesk', sans-serif; font-weight: 700; }
  </style>
</head>
<body class="hold-transition sidebar-mini">

<div class="wrapper">

  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
    <span class="nav-link font-weight-bold">Back Office - ImpactVenture</span>
  </nav>

  <aside class="main-sidebar sidebar-dark-primary elevation-2">
    <a href="index.php?action=admin" class="brand-link">
      <span class="brand-logo text-[#1D9E75]">Impact</span>
      <span class="brand-logo text-white">Venture</span>
    </a>
    <div class="sidebar">
      <nav class="mt-3">
        <ul class="nav nav-pills nav-sidebar flex-column">
          <li class="nav-item"><a href="index.php?action=admin" class="nav-link active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
          <li class="nav-item"><a href="index.php?action=admin_fiche" class="nav-link"><i class="fas fa-building"></i> Fiches Entreprises</a></li>
          <li class="nav-item"><a href="index.php?action=admin_projet" class="nav-link"><i class="fas fa-lightbulb"></i> Projets</a></li>
        </ul>
      </nav>
    </div>
  </aside>

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <h1>Gestion des Fiches Entreprises</h1>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">
        <?php if($msg): ?>
          <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <div class="row">
          <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
              <div class="inner"><h3><?= $total ?></h3><p>Fiches Entreprises</p></div>
              <div class="icon"><i class="fas fa-building"></i></div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
              <div class="inner"><h3><?= $avgGreen ?>%</h3><p>Score Green Moyen</p></div>
              <div class="icon"><i class="fas fa-leaf"></i></div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
              <div class="inner"><h3><?= $totalP ?></h3><p>Projets Total</p></div>
              <div class="icon"><i class="fas fa-lightbulb"></i></div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Liste des Fiches Entreprises</h3>
          </div>
          <div class="card-body table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nom</th>
                  <th>Catégorie</th>
                  <th>Score Green</th>
                  <th>Projets associés</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($fiches as $f): ?>
                <tr>
                  <td><?= $f['id'] ?></td>
                  <td><strong><?= htmlspecialchars($f['nom']) ?></strong></td>
                  <td><?= htmlspecialchars($f['categorie']) ?></td>
                  <td><span class="badge bg-success"><?= $f['score_green'] ?>%</span></td>
                  <td><?= $f['nb_projets'] ?? 0 ?></td>
                  <td>
                    <a href="#" class="btn btn-sm btn-warning">Modifier</a>
                    <a href="#" onclick="return confirm('Supprimer cette fiche ?')" class="btn btn-sm btn-danger">Supprimer</a>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
            <nav class="bg-white shadow-sm border-b">
  <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
    <a href="index.php" class="flex items-center gap-3">
      <img src="https://i.ibb.co.com/0jKzK7Z/impact-venture-logo.png" 
           alt="ImpactVenture" 
           class="h-10 w-10">
      <div class="brand text-2xl font-bold">
        <span class="text-[#1D9E75]">Impact</span>
        <span class="text-[#534AB7]">Venture</span>
      </div>
    </a>
    <!-- Le reste de ta navbar -->
  </div>
</nav>
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