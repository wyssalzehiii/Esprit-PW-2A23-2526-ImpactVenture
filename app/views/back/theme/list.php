<?php
$msgs=['created'=>'Thème créé avec succès !','updated'=>'Thème mis à jour.','deleted'=>'Thème supprimé.'];
$msg=isset($_GET['msg'])?($msgs[$_GET['msg']]??''):'';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Back Office – Gestion Thèmes | ImpactVenture</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet"/>
  <style>
    :root{--iv-green:#1D9E75;--iv-purple:#534AB7;--iv-gold:#EF9F27;}
    .brand-logo{font-family:'Space Grotesk',sans-serif;font-weight:700;}
    .sidebar-dark-primary{background:#1a1f2e;}
    .nav-sidebar .nav-item .nav-link.active{background:var(--iv-green);}
    .nav-sidebar .nav-item .nav-link:hover{background:rgba(29,158,117,.15);}
    .badge-cat{font-size:.7rem;padding:3px 8px;border-radius:999px;font-weight:600;}
    .badge-tech{background:#FEF3C7;color:#92400E;}
    .badge-energie{background:#DBEAFE;color:#1E40AF;}
    .badge-digital{background:#D1FAE5;color:#065F46;}
    .badge-agri{background:#D1FAE5;color:#065F46;}
    .badge-autre{background:#F3F4F6;color:#374151;}
    .btn-iv-green{background:var(--iv-green);color:#fff;border:none;}
    .btn-iv-green:hover{background:#15795A;color:#fff;}
    .score-high{color:#1D9E75;font-weight:700;}
    .score-med{color:#EF9F27;font-weight:700;}
    .score-low{color:#EF4444;font-weight:700;}
    .card-stat-icon{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;}
    .table td,.table th{vertical-align:middle;}
    .form-control:focus{border-color:var(--iv-green);box-shadow:0 0 0 .2rem rgba(29,158,117,.2);}
    .modal-content{border-radius:16px;overflow:hidden;}
    /* Toast */
    #toast{position:fixed;bottom:1.5rem;right:1.5rem;background:var(--iv-green);color:#fff;padding:.8rem 1.4rem;border-radius:12px;font-weight:600;font-size:.9rem;z-index:9999;display:none;align-items:center;gap:.5rem;box-shadow:0 8px 24px rgba(0,0,0,.15);}
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed" style="font-size:.9rem;">
<div class="wrapper">

  <!-- NAVBAR -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
      <li class="nav-item"><a href="index.php?action=admin" class="nav-link text-muted" style="font-size:.8rem;">Dashboard</a></li>
      <li class="nav-item"><span class="nav-link text-muted">/</span></li>
      <li class="nav-item"><span class="nav-link font-weight-bold">Gestion des Thèmes</span></li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a href="index.php?action=list" class="nav-link btn btn-sm btn-outline-success mr-2">
          <i class="fas fa-eye mr-1"></i> Voir Front Office
        </a>
      </li>
      <li class="nav-item dropdown">
        <a href="#" class="nav-link" data-toggle="dropdown">
          <img src="https://i.pravatar.cc/32?img=5" class="rounded-circle" width="30" alt="admin"/>
          <span class="ml-2 d-none d-md-inline" style="font-size:.85rem;">Admin</span>
        </a>
      </li>
    </ul>
  </nav>

  <!-- SIDEBAR -->
  <aside class="main-sidebar sidebar-dark-primary elevation-2">
    <a href="index.php?action=admin" class="brand-link px-4 py-3" style="border-bottom:1px solid rgba(255,255,255,.1);">
      <span class="brand-logo brand-text" style="color:#1D9E75;font-size:1.1rem;">Impact</span>
      <span class="brand-logo brand-text" style="color:#534AB7;">Venture</span>
      <span class="text-muted ml-2" style="font-size:.7rem;color:rgba(255,255,255,.4)!important;">Admin</span>
    </a>
    <div class="sidebar"><nav class="mt-3">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview">
        <li class="nav-item"><a href="index.php?action=admin" class="nav-link active"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>
        <li class="nav-header" style="color:rgba(255,255,255,.3);font-size:.7rem;">MODULE 02</li>
        <li class="nav-item"><a href="index.php?action=admin" class="nav-link"><i class="nav-icon fas fa-tags"></i><p>Gestion Thèmes</p></a></li>
        <li class="nav-item"><a href="index.php?action=trending" class="nav-link"><i class="nav-icon fas fa-chart-line"></i><p>Trending Dashboard</p></a></li>
        <li class="nav-header" style="color:rgba(255,255,255,.3);font-size:.7rem;">AUTRES MODULES</li>
        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fas fa-users"></i><p>Utilisateurs</p></a></li>
        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fas fa-lightbulb"></i><p>Projets</p></a></li>
        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fas fa-handshake"></i><p>Mentors</p></a></li>
        <li class="nav-item"><a href="index.php?action=admin_financement" class="nav-link"><i class="nav-icon fas fa-coins"></i><p>Financement</p></a></li>
        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fas fa-graduation-cap"></i><p>Formations</p></a></li>
      </ul>
    </nav></div>
  </aside>

  <!-- CONTENT -->
  <div class="content-wrapper" style="background:#F7F8FC;">
    <div class="content-header py-3 px-4">
      <div class="d-flex align-items-center justify-content-between">
        <div>
          <h5 class="mb-0 font-weight-bold" style="font-family:'Space Grotesk',sans-serif;">Gestion des Thèmes</h5>
          <small class="text-muted">Smart Theme Engine — CRUD complet</small>
        </div>
        <a href="index.php?action=admin_create" class="btn btn-iv-green btn-sm rounded-pill px-4">
          <i class="fas fa-plus mr-2"></i>Nouveau thème
        </a>
      </div>
    </div>

    <section class="content px-4 pb-4">

      <?php if($msg): ?>
      <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle mr-2"></i><?= htmlspecialchars($msg) ?>
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
      </div>
      <?php endif; ?>

      <!-- STAT CARDS -->
      <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
          <div class="card border-0 shadow-sm rounded-lg">
            <div class="card-body d-flex align-items-center gap-3">
              <div class="card-stat-icon" style="background:#D1FAE5;"><i class="fas fa-tags" style="color:#1D9E75;"></i></div>
              <div><p class="mb-0 font-weight-bold" style="font-size:1.5rem;font-family:'Space Grotesk',sans-serif;"><?= $total ?></p><small class="text-muted">Thèmes actifs</small></div>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
          <div class="card border-0 shadow-sm rounded-lg">
            <div class="card-body d-flex align-items-center gap-3">
              <div class="card-stat-icon" style="background:#FEF3C7;"><i class="fas fa-leaf" style="color:#EF9F27;"></i></div>
              <div><p class="mb-0 font-weight-bold" style="font-size:1.5rem;font-family:'Space Grotesk',sans-serif;"><?= $avgGreen ?>%</p><small class="text-muted">Score Green moy.</small></div>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
          <div class="card border-0 shadow-sm rounded-lg">
            <div class="card-body d-flex align-items-center gap-3">
              <div class="card-stat-icon" style="background:#EDE9FE;"><i class="fas fa-project-diagram" style="color:#534AB7;"></i></div>
              <div><p class="mb-0 font-weight-bold" style="font-size:1.5rem;font-family:'Space Grotesk',sans-serif;"><?= $totalP ?></p><small class="text-muted">Projets soumis</small></div>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
          <div class="card border-0 shadow-sm rounded-lg">
            <div class="card-body d-flex align-items-center gap-3">
              <div class="card-stat-icon" style="background:#DBEAFE;"><i class="fas fa-fire" style="color:#3B82F6;"></i></div>
              <div><p class="mb-0 font-weight-bold" style="font-size:1rem;font-family:'Space Grotesk',sans-serif;"><?= !empty($topThemes)?htmlspecialchars(mb_substr($topThemes[0]['nom'],0,14)):'—' ?></p><small class="text-muted">Top thème</small></div>
            </div>
          </div>
        </div>
      </div>

      <!-- TABLE CRUD -->
      <div class="card border-0 shadow-sm rounded-lg">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
          <div>
            <h6 class="font-weight-bold mb-0" style="font-family:'Space Grotesk',sans-serif;">Tous les thèmes</h6>
            <small class="text-muted"><?= $total ?> thème(s) enregistré(s)</small>
          </div>
          <input type="text" id="searchInput" placeholder="Rechercher…" class="form-control form-control-sm w-auto" style="min-width:180px;">
        </div>
        <div class="card-body p-0">
          <table class="table table-hover mb-0" id="themesTable">
            <thead style="background:#F8FAFC;">
              <tr>
                <th class="pl-4">#</th>
                <th>Nom</th>
                <th>Catégorie</th>
                <th>Mots-clés</th>
                <th>Score Green</th>
                <th>Projets</th>
                <th>Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php $catBadges=['tech'=>'badge-tech','digital'=>'badge-digital','energie renouvelable'=>'badge-energie','agriculture durable'=>'badge-agri','economie circulaire'=>'badge-tech','mobilite verte'=>'badge-agri','entrepreneuriat'=>'badge-tech','autre'=>'badge-autre'];
              foreach ($themes as $t):
                $bc=$catBadges[$t['categorie']]??'badge-autre';
                $sc=$t['score_green']>=70?'score-high':($t['score_green']>=40?'score-med':'score-low');
              ?>
              <tr>
                <td class="pl-4 text-muted"><?= $t['id'] ?></td>
                <td><strong><?= htmlspecialchars($t['nom']) ?></strong></td>
                <td><span class="badge-cat <?= $bc ?>"><?= htmlspecialchars($t['categorie']) ?></span></td>
                <td><small class="text-muted"><?= htmlspecialchars(mb_substr($t['mots_cles'],0,28)) ?></small></td>
                <td>
                  <div class="d-flex align-items-center" style="gap:8px;">
                    <span class="<?= $sc ?>"><?= $t['score_green'] ?></span>
                    <div class="progress flex-grow-1" style="height:5px;">
                      <div class="progress-bar" style="width:<?= $t['score_green'] ?>%;background:<?= $t['score_green']>=70?'#1D9E75':($t['score_green']>=40?'#EF9F27':'#E24B4A') ?>"></div>
                    </div>
                  </div>
                </td>
                <td><span class="badge badge-info"><?= $t['nb_projets'] ?></span></td>
                <td><small class="text-muted"><?= date('d/m/Y',strtotime($t['created_at'])) ?></small></td>
                <td>
                  <a href="index.php?action=admin_edit&id=<?= $t['id'] ?>" class="btn btn-xs btn-warning mr-1" title="Modifier">
                    <i class="fas fa-edit"></i>
                  </a>
                  <a href="index.php?action=admin_delete&id=<?= $t['id'] ?>" class="btn btn-xs btn-danger"
                     title="Supprimer" onclick="return confirm('Supprimer ce thème et ses projets ?')">
                    <i class="fas fa-trash"></i>
                  </a>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php if(empty($themes)): ?>
              <tr><td colspan="8" class="text-center text-muted py-4">Aucun thème. <a href="index.php?action=admin_create">En créer un</a></td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    </section>
  </div>

  <footer class="main-footer py-2 px-4">
    <small class="text-muted">ImpactVenture — Esprit School of Engineering | PW 2A23 | 2025–2026 | Module 02</small>
  </footer>
</div><!-- end wrapper -->

<!-- Toast -->
<div id="toast" style="position:fixed;bottom:1.5rem;right:1.5rem;background:#1D9E75;color:#fff;padding:.8rem 1.4rem;border-radius:12px;font-weight:600;z-index:9999;display:none;">
  <i class="fas fa-check mr-2"></i><span id="toastMsg"></span>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script>
// Search filter
document.getElementById('searchInput').addEventListener('input',function(){
  const q=this.value.toLowerCase();
  document.querySelectorAll('#themesTable tbody tr').forEach(r=>{
    r.style.display=r.textContent.toLowerCase().includes(q)?'':'none';
  });
});

// Show toast if message
<?php if($msg): ?>
const t=document.getElementById('toast');
document.getElementById('toastMsg').textContent="<?= addslashes($msg) ?>";
t.style.display='flex';
setTimeout(()=>t.style.display='none',3000);
<?php endif; ?>
</script>
</body>
</html>
