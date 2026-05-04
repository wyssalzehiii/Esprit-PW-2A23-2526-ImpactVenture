<?php $errors = $errors ?? []; $projet = $projet ?? []; $fiches = $fiches ?? []; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin – Modifier Projet | ImpactVenture</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
  <style>
    :root{--iv-green:#1D9E75;}
    .sidebar-dark-primary{background:#1a1f2e;}
    .brand-logo{font-family:'Space Grotesk',sans-serif;font-weight:700;}
    .nav-sidebar .nav-item .nav-link.active{background:var(--iv-green);}
    .form-control:focus{border-color:var(--iv-green);box-shadow:0 0 0 .2rem rgba(29,158,117,.2);}
    .btn-iv-green{background:var(--iv-green);color:#fff;border:none;}
    .btn-iv-green:hover{background:#15795A;color:#fff;}
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
      <li class="nav-item"><a href="index.php?action=admin_projet" class="nav-link text-muted">Projets</a></li>
      <li class="nav-item"><span class="nav-link font-weight-bold">/ Modifier #<?= $projet['id_projet'] ?? '' ?></span></li>
    </ul>
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
      <li class="nav-item"><a href="index.php?action=admin_fiche" class="nav-link"><i class="nav-icon fas fa-building"></i><p>Fiches Entreprises</p></a></li>
      <li class="nav-item"><a href="index.php?action=admin_projet" class="nav-link active"><i class="nav-icon fas fa-lightbulb"></i><p>Projets</p></a></li>
    </ul></nav></div>
  </aside>
  <div class="content-wrapper" style="background:#F7F8FC">
    <div class="content-header py-3 px-4"><h5 class="mb-0 font-weight-bold">Modifier : <?= htmlspecialchars($projet['titre'] ?? '') ?></h5></div>
    <section class="content px-4 pb-4">
      <div class="row justify-content-center"><div class="col-lg-8">
        <div class="mb-3"><a href="index.php?action=admin_projet" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i>Retour</a></div>
        <?php if(!empty($errors)): ?>
        <div class="alert alert-danger"><ul class="mb-0"><?php foreach($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>
        <div class="card border-0 shadow-sm rounded-lg">
          <div class="card-header bg-white"><h6 class="font-weight-bold mb-0">Modifier le Projet</h6></div>
          <div class="card-body">
            <form method="POST" action="index.php?action=admin_projet_update&id=<?= $projet['id_projet'] ?>">
              <div class="form-group">
                <label class="font-weight-bold">Titre <span class="text-danger">*</span></label>
                <input type="text" name="titre" class="form-control" value="<?= htmlspecialchars($_POST['titre']??$projet['titre']) ?>">
              </div>
              <div class="form-group">
                <label class="font-weight-bold">Description <span class="text-danger">*</span></label>
                <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($_POST['description']??$projet['description']) ?></textarea>
              </div>
              <div class="row">
                <div class="col-md-7">
                  <div class="form-group">
                    <label class="font-weight-bold">Entreprise Associée <span class="text-danger">*</span></label>
                    <select name="id_fiche_entreprise" class="form-control">
                      <?php foreach($fiches as $f): ?>
                        <option value="<?= $f['id'] ?>" <?= (($_POST['id_fiche_entreprise']??$projet['id_fiche_entreprise'])==$f['id'])?'selected':'' ?>><?= htmlspecialchars($f['nom']) ?></option>
                      <?php endforeach; ?>
                    </select>
                    <small class="text-muted">Jointure Projet ↔ FicheEntreprise</small>
                  </div>
                </div>
                <div class="col-md-5">
                  <div class="form-group">
                    <label class="font-weight-bold">Statut</label>
                    <select name="statut" class="form-control">
                      <?php $statuts=['soumis','en_evaluation','accepté','rejeté']; $cur=$_POST['statut']??$projet['statut']; ?>
                      <?php foreach($statuts as $s): ?><option value="<?=$s?>" <?=$cur===$s?'selected':''?>><?=$s?></option><?php endforeach; ?>
                    </select>
                  </div>
                </div>
              </div>
              <div class="d-flex">
                <a href="index.php?action=admin_projet" class="btn btn-outline-secondary mr-2"><i class="fas fa-times mr-1"></i>Annuler</a>
                <button type="submit" class="btn btn-iv-green"><i class="fas fa-save mr-1"></i>Enregistrer</button>
              </div>
            </form>
          </div>
        </div>
      </div></div>
    </section>
  </div>
  <footer class="main-footer py-2 px-4"><small class="text-muted">ImpactVenture — Esprit 2025–2026</small></footer>
</div>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
