<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Trending | ImpactVenture</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet"/>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <style>
    :root{--iv-green:#1D9E75;--iv-purple:#534AB7;--iv-gold:#EF9F27;}
    .sidebar-dark-primary{background:#1a1f2e;}.brand-logo{font-family:'Space Grotesk',sans-serif;font-weight:700;}
    .nav-sidebar .nav-item .nav-link.active{background:var(--iv-green);}
    .rank-badge{width:28px;height:28px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-weight:700;font-size:.8rem;}
    .rank-1{background:#EF9F27;color:#fff;}.rank-2{background:#A0AEC0;color:#fff;}.rank-3{background:#CD7F32;color:#fff;}.rank-n{background:#EDE9FE;color:#534AB7;}
    .trend-row{transition:background .15s;}.trend-row:hover{background:#F7F8FC;}
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed" style="font-size:.9rem;">
<div class="wrapper">
  <nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
      <li class="nav-item"><span class="nav-link text-muted">Module 02 /</span></li>
      <li class="nav-item"><span class="nav-link font-weight-bold">Trending Dashboard</span></li>
    </ul>
  </nav>
  <aside class="main-sidebar sidebar-dark-primary elevation-2">
    <a href="index.php?action=admin" class="brand-link px-4 py-3" style="border-bottom:1px solid rgba(255,255,255,.1);">
      <span class="brand-logo brand-text" style="color:#1D9E75;">Impact</span><span class="brand-logo brand-text" style="color:#534AB7;">Venture</span>
    </a>
    <div class="sidebar"><nav class="mt-3"><ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview">
      <li class="nav-item"><a href="index.php?action=admin" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>
      <li class="nav-header" style="color:rgba(255,255,255,.3);font-size:.7rem;">MODULE 02</li>
      <li class="nav-item"><a href="index.php?action=admin" class="nav-link"><i class="nav-icon fas fa-tags"></i><p>Gestion Thèmes</p></a></li>
      <li class="nav-item"><a href="index.php?action=trending" class="nav-link active"><i class="nav-icon fas fa-chart-line"></i><p>Trending Dashboard</p></a></li>
    </ul></nav></div>
  </aside>
  <div class="content-wrapper" style="background:#F7F8FC;">
    <div class="content-header py-3 px-4">
      <h5 class="mb-0 font-weight-bold" style="font-family:'Space Grotesk',sans-serif;">Trending Dashboard</h5>
      <small class="text-muted">Données réelles — <?= $total ?> thèmes, score Green moyen <?= $avgGreen ?>%</small>
    </div>
    <section class="content px-4 pb-4">
      <div class="row">
        <div class="col-lg-8 mb-4">
          <div class="card border-0 shadow-sm rounded-lg h-100">
            <div class="card-header bg-white border-0 pt-4 pb-0">
              <h6 class="font-weight-bold mb-0" style="font-family:'Space Grotesk',sans-serif;">Projets par thème</h6>
            </div>
            <div class="card-body"><canvas id="barChart" height="220"></canvas></div>
          </div>
        </div>
        <div class="col-lg-4 mb-4">
          <div class="card border-0 shadow-sm rounded-lg h-100">
            <div class="card-header bg-white border-0 pt-4 pb-0">
              <h6 class="font-weight-bold mb-0" style="font-family:'Space Grotesk',sans-serif;">Score Green</h6>
            </div>
            <div class="card-body d-flex align-items-center"><canvas id="doughnutChart"></canvas></div>
          </div>
        </div>
        <div class="col-12 mb-4">
          <div class="card border-0 shadow-sm rounded-lg">
            <div class="card-header bg-white border-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
              <div><h6 class="font-weight-bold mb-0" style="font-family:'Space Grotesk',sans-serif;">Classement des thèmes</h6><small class="text-muted">Données réelles</small></div>
              <span class="badge" style="background:#D1FAE5;color:#065F46;border-radius:8px;padding:5px 10px;font-size:.75rem;">Live</span>
            </div>
            <div class="card-body p-0">
              <table class="table mb-0">
                <thead style="background:#F8FAFC;">
                  <tr><th class="pl-4">Rang</th><th>Thème</th><th>Catégorie</th><th>Projets</th><th>Score Green</th></tr>
                </thead>
                <tbody>
                  <?php foreach ($topThemes as $i => $t):
                    $rc=$i===0?'rank-1':($i===1?'rank-2':($i===2?'rank-3':'rank-n'));
                    $bc=$t['score_green']>=70?'#1D9E75':($t['score_green']>=40?'#EF9F27':'#E24B4A');
                    $tags=array_filter(array_map('trim',explode(',',$t['mots_cles'])));
                  ?>
                  <tr class="trend-row">
                    <td class="pl-4"><span class="rank-badge <?=$rc?>"><?=$i+1?></span></td>
                    <td><strong><?=htmlspecialchars($t['nom'])?></strong><br><small class="text-muted"><?=htmlspecialchars(implode(', ',array_slice($tags,0,3)))?></small></td>
                    <td><span class="badge badge-secondary"><?=htmlspecialchars($t['categorie'])?></span></td>
                    <td><strong><?=$t['nb']?></strong></td>
                    <td>
                      <div class="d-flex align-items-center" style="gap:8px;">
                        <strong style="color:<?=$bc?>"><?=$t['score_green']?></strong>
                        <div class="progress flex-grow-1" style="height:5px;"><div class="progress-bar" style="width:<?=$t['score_green']?>%;background:<?=$bc?>"></div></div>
                      </div>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
  <footer class="main-footer py-2 px-4"><small class="text-muted">ImpactVenture — Esprit School of Engineering | 2025–2026</small></footer>
</div>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script>
const L=<?=json_encode(array_column($topThemes,'nom'))?>,P=<?=json_encode(array_column($topThemes,'nb'))?>,G=<?=json_encode(array_column($topThemes,'score_green'))?>;
const C=['#534AB7','#1D9E75','#EF9F27','#3B82F6','#10B981','#F59E0B'];
new Chart(document.getElementById('barChart'),{type:'bar',data:{labels:L,datasets:[{label:'Projets',data:P,backgroundColor:C,borderRadius:8,borderSkipped:false}]},options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true},x:{grid:{display:false}}}}});
new Chart(document.getElementById('doughnutChart'),{type:'doughnut',data:{labels:L,datasets:[{data:G,backgroundColor:C,borderWidth:0}]},options:{responsive:true,cutout:'65%',plugins:{legend:{position:'bottom',labels:{font:{size:11},padding:8}}}}});
</script>
</body>
</html>
