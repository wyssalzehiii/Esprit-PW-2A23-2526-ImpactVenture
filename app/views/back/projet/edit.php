<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Back Office – Modifier project | ImpactVenture</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet"/>
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
<body class="hold-transition sidebar-mini layout-fixed" style="font-size:.9rem;">
<div class="wrapper">
  <nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
      <li class="nav-item"><a href="index.php?action=admin" class="nav-link text-muted">Dashboard</a></li>
      <li class="nav-item"><span class="nav-link text-muted">/</span></li>
      <li class="nav-item"><span class="nav-link font-weight-bold">Modifier #<?= $theme['id'] ?></span></li>
    </ul>
  </nav>

  <aside class="main-sidebar sidebar-dark-primary elevation-2">
    <a href="index.php?action=admin" class="brand-link px-4 py-3" style="border-bottom:1px solid rgba(255,255,255,.1);">
      <span class="brand-logo brand-text" style="color:#1D9E75;">Impact</span><span class="brand-logo brand-text" style="color:#534AB7;">Venture</span>
    </a>
    <div class="sidebar"><nav class="mt-3"><ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview">
      <li class="nav-item"><a href="index.php?action=admin" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>
      <li class="nav-header" style="color:rgba(255,255,255,.3);font-size:.7rem;">MODULE 02</li>
      <li class="nav-item"><a href="index.php?action=admin" class="nav-link active"><i class="nav-icon fas fa-tags"></i><p>Gestion projects</p></a></li>
      <li class="nav-item"><a href="index.php?action=trending" class="nav-link"><i class="nav-icon fas fa-chart-line"></i><p>Trending</p></a></li>
    </ul></nav></div>
  </aside>

  <div class="content-wrapper" style="background:#F7F8FC;">
    <div class="content-header py-3 px-4">
      <h5 class="mb-0 font-weight-bold" style="font-family:'Space Grotesk',sans-serif;">Modifier : <?= htmlspecialchars($theme['nom']) ?></h5>
    </div>
    <section class="content px-4 pb-4">
      <div class="row justify-content-center">
        <div class="col-lg-8">

          <div class="mb-3"><a href="index.php?action=admin" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i>Retour</a></div>

          <?php if(!empty($errors)): ?>
          <div class="alert alert-danger">
            <strong>Erreurs :</strong>
            <ul class="mb-0 mt-1"><?php foreach($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
          </div>
          <?php endif; ?>

          <div class="card border-0 shadow-sm rounded-lg">
            <div class="card-header bg-white">
              <h6 class="font-weight-bold mb-0">Modifier le project</h6>
            </div>
            <div class="card-body">
              <form method="POST" action="index.php?action=admin_update&id=<?= $theme['id'] ?>">

                <div class="form-group">
                  <label class="font-weight-bold">Nom <span class="text-danger">*</span></label>
                  <input type="text" name="nom" class="form-control"
                         value="<?= htmlspecialchars($_POST['nom']??$theme['nom']) ?>">
                </div>

                <div class="form-group">
                  <label class="font-weight-bold">Description <span class="text-danger">*</span></label>
                  <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($_POST['description']??$theme['description']) ?></textarea>
                  <small class="text-muted">Minimum 20 caractères</small>
                </div>

                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="font-weight-bold">Catégorie <span class="text-danger">*</span></label>
                      <select name="categorie" class="form-control">
                        <?php $cats=['tech'=>'Tech & IA','digital'=>'Économie digitale','energie renouvelable'=>'Énergie renouvelable','agriculture durable'=>'Agriculture durable','economie circulaire'=>'Économie circulaire','mobilite verte'=>'Mobilité verte','entrepreneuriat'=>'Entrepreneuriat','autre'=>'Autre'];
                        $cur=$_POST['categorie']??$theme['categorie'];
                        foreach($cats as $v=>$l): ?>
                        <option value="<?=$v?>" <?=$cur===$v?'selected':''?>><?=$l?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="font-weight-bold">Score Green (0-100) <span class="text-danger">*</span></label>
                      <input type="number" name="score_green" id="sg" class="form-control" min="0" max="100"
                             value="<?= htmlspecialchars($_POST['score_green']??$theme['score_green']) ?>">
                      <div class="progress mt-2" style="height:5px;">
                        <div id="sgb" class="progress-bar" style="width:<?= $theme['score_green'] ?>%;background:#1D9E75;transition:width .2s"></div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <label class="font-weight-bold">Mots-clés <span class="text-danger">*</span></label>
                  <input type="text" name="mots_cles" class="form-control"
                         value="<?= htmlspecialchars($_POST['mots_cles']??$theme['mots_cles']) ?>"
                         placeholder="Séparés par des virgules">
                </div>

                <div class="d-flex gap-2">
                  <a href="index.php?action=admin" class="btn btn-outline-secondary"><i class="fas fa-times mr-1"></i>Annuler</a>
                  <button type="submit" class="btn btn-iv-green ml-2"><i class="fas fa-save mr-1"></i>Enregistrer</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
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
  <footer class="main-footer py-2 px-4"><small class="text-muted">ImpactVenture — Esprit School of Engineering | 2025–2026</small></footer>
</div>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script>
const sg=document.getElementById('sg'),sgb=document.getElementById('sgb');
function u(){const v=Math.min(100,Math.max(0,parseInt(sg.value)||0));sgb.style.width=v+'%';sgb.style.background=v>=70?'#1D9E75':v>=40?'#EF9F27':'#E24B4A';}
sg.addEventListener('input',u);u();
</script>
</body></html>
