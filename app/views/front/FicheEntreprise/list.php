<?php
$msgs = ['created'=>'✅ Fiche entreprise créée !','created_with_ai'=>'✅ Créée avec analyse IA !','updated'=>'✅ Mise à jour réussie.','deleted'=>'🗑️ Supprimée.'];
$msg  = isset($_GET['msg']) ? ($msgs[$_GET['msg']] ?? '') : '';
$fiches = $fiches ?? [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fiches Entreprises - ImpactVenture</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
  <style>
    :root{--green:#1D9E75;--purple:#534AB7;}
    body{font-family:'Inter',sans-serif;background:#F0F4F1;}
    .brand{font-family:'Space Grotesk',sans-serif;}
    .card{transition:all .3s;position:relative;overflow:hidden;}
    .card:hover{transform:translateY(-8px);box-shadow:0 25px 50px -12px rgba(0,0,0,.15);}
    .card:hover .card-back{transform:rotateY(180deg);}
    .card-front{transition:transform .6s;}
    .card-back{position:absolute;top:0;left:0;width:100%;height:100%;transform:rotateY(180deg);backface-visibility:hidden;transition:transform .6s;}
    .sparkline{width:60px;height:20px;}
    .sdg-badge{width:24px;height:24px;border-radius:4px;display:inline-block;margin:0 2px;position:relative;}
    .sdg-badge::after{content:attr(data-sdg);position:absolute;bottom:-8px;left:50%;transform:translateX(-50%);font-size:8px;background:#1f2937;color:white;border-radius:10px;padding:1px 4px;}
    .impact-ring{width:50px;height:50px;border-radius:50%;background:conic-gradient(from 0deg, #10b981 var(--score-angle), #e5e7eb var(--score-angle));display:flex;align-items:center;justify-content:center;font-weight:bold;font-size:12px;color:white;}
  </style>
</head>
<body class="min-h-screen">

<nav class="bg-white border-b shadow-sm sticky top-0 z-50">
  <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
    <a href="<?= htmlspecialchars(iv_url(['action'=>'fiche_list']), ENT_QUOTES, 'UTF-8') ?>" class="brand flex items-center gap-2">
      <img src="logo.png" alt="ImpactVenture Logo" style="height:40px;width:auto;object-fit:contain;">
      <span class="text-2xl font-bold"><span style="color:#1D9E75">Impact</span><span style="color:#534AB7">Venture</span></span>
    </a>
    <div class="flex gap-8 text-sm font-medium">
      <a href="<?= htmlspecialchars(iv_url(['action'=>'fiche_list']), ENT_QUOTES, 'UTF-8') ?>" class="text-[#1D9E75] font-semibold">Entreprises</a>
      <a href="<?= htmlspecialchars(iv_url(['action'=>'projet_list']), ENT_QUOTES, 'UTF-8') ?>" class="hover:text-[#1D9E75]">Projets</a>
      <a href="<?= htmlspecialchars(iv_url(['action'=>'admin']), ENT_QUOTES, 'UTF-8') ?>" class="text-amber-600">Admin</a>
    </div>
    <div class="flex gap-3">
      <a href="<?= htmlspecialchars(iv_url(['action'=>'fiche_create']), ENT_QUOTES, 'UTF-8') ?>" class="bg-[#1D9E75] text-white px-5 py-2.5 rounded-xl font-semibold text-sm">+ Entreprise</a>
      <a href="<?= htmlspecialchars(iv_url(['action'=>'projet_create']), ENT_QUOTES, 'UTF-8') ?>" class="border border-[#1D9E75] text-[#1D9E75] px-5 py-2.5 rounded-xl font-semibold text-sm">+ Projet</a>
    </div>
  </div>
</nav>

<div class="max-w-7xl mx-auto px-6 py-8">
  <?php if($msg): ?>
    <div class="bg-emerald-100 border border-emerald-400 text-emerald-700 px-6 py-4 rounded-2xl mb-8"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <h1 class="brand text-5xl font-bold mb-2">Fiches Entreprises</h1>
  <p class="text-gray-600 mb-10">Découvrez les entreprises incubées sur ImpactVenture</p>

  <?php if(empty($fiches)): ?>
    <div class="text-center py-20 text-gray-400">
      <div class="text-6xl mb-4">🏢</div>
      <p class="text-xl">Aucune entreprise pour le moment.</p>
      <a href="<?= htmlspecialchars(iv_url(['action'=>'fiche_create']), ENT_QUOTES, 'UTF-8') ?>" class="mt-6 inline-block bg-[#1D9E75] text-white px-8 py-3 rounded-2xl font-semibold">Créer la première</a>
    </div>
  <?php else: ?>
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    <?php foreach($fiches as $f): ?>
    <div class="bg-white rounded-3xl overflow-hidden shadow card" style="perspective:1000px;">
      <div class="card-front">
        <div class="h-2 bg-gradient-to-r from-[#1D9E75] via-[#534AB7] to-[#EF9F27]"></div>
        <div class="p-6">
          <?php if(!empty($f['logo'])): ?>
            <div class="flex justify-center mb-5">
              <img src="<?= htmlspecialchars($f['logo']) ?>" alt="Logo" class="h-20 w-auto max-w-full mx-auto object-contain rounded-2xl shadow px-2">
            </div>
          <?php endif; ?>

          <h3 class="font-bold text-2xl text-center mb-3"><?= htmlspecialchars($f['nom']) ?></h3>
          <p class="text-gray-600 text-sm mb-5 line-clamp-4"><?= htmlspecialchars(substr($f['description'], 0, 140)) ?>...</p>

          <div class="flex justify-between items-center text-sm mb-4">
            <span class="px-3 py-1 bg-gray-100 rounded-full"><?= htmlspecialchars($f['categorie']) ?></span>
            <div class="flex items-center gap-2">
              <?php 
              $avgScore = round($f['avg_score'] ?? 0);
              $scoreColor = $avgScore >= 70 ? '#1D9E75' : ($avgScore >= 50 ? '#EF9F27' : '#E24B4A');
              $scoreAngle = ($avgScore / 100) * 360;
              ?>
              <div class="impact-ring" style="--score-angle: <?= $scoreAngle ?>deg; background: conic-gradient(from 0deg, #10b981 <?= $scoreAngle ?>deg, #e5e7eb <?= $scoreAngle ?>deg);">
                <?= $avgScore ?>%
              </div>
            </div>
          </div>
          
          <!-- SDG Badges -->
          <div class="flex flex-wrap gap-1 mb-4 justify-center">
            <?php 
            $sdgColors = ['1'=>'#E5243B','2'=>'#DDA63B','3'=>'#4C9F38','4'=>'#C5192D','5'=>'#FF3A21','6'=>'#26BDE2','7'=>'#FCC30B','8'=>'#A21942','9'=>'#BD6931','10'=>'#FD1515','11'=>'#F99D26','12'=>'#BF8B2E','13'=>'#3F8E3A','14'=>'#A94433','15'=>'#C21F3C','16'=>'#1B5E20','17'=>'#E11C26'];
            $sdgIds = !empty($f['sdg_ids']) ? explode(',', $f['sdg_ids']) : [];
            foreach(array_slice($sdgIds, 0, 4) as $sdgId):
              $color = $sdgColors[$sdgId] ?? '#666';
            ?>
              <div class="sdg-badge" style="background:<?= $color ?>;" data-sdg="<?= $sdgId ?>"></div>
            <?php endforeach; ?>
          </div>

          <!-- Sparkline -->
          <div class="flex justify-center mb-4">
            <svg class="sparkline" viewBox="0 0 60 20">
              <polyline points="5,15 15,12 25,8 35,10 45,5 55,8" 
                        stroke="<?= $scoreColor ?>" stroke-width="2" fill="none"/>
            </svg>
          </div>

          <div class="text-xs text-gray-400 text-center mb-5"><?= $f['nb_projets'] ?> projet(s) associé(s)</div>

          <div class="flex gap-3">
            <a href="<?= htmlspecialchars(iv_url(['action'=>'entreprise_show','id'=>$f['id']]), ENT_QUOTES, 'UTF-8') ?>"
               class="flex-1 text-center py-3 border border-gray-300 rounded-2xl hover:bg-gray-50 text-sm">📊 Voir Analyse</a>
            <a href="<?= htmlspecialchars(iv_url(['action'=>'fiche_edit','id'=>$f['id']]), ENT_QUOTES, 'UTF-8') ?>"
               class="flex-1 text-center py-3 border border-gray-300 rounded-2xl hover:bg-gray-50 text-sm">Modifier</a>
            <a href="<?= htmlspecialchars(iv_url(['action'=>'fiche_delete','id'=>$f['id']]), ENT_QUOTES, 'UTF-8') ?>"
               onclick="return confirm('Supprimer cette entreprise ?')"
               class="flex-1 text-center py-3 bg-red-50 text-red-600 rounded-2xl hover:bg-red-100 text-sm">Supprimer</a>
          </div>
        </div>
      </div>
      
      <!-- Back of card (shown on hover) -->
      <div class="card-back bg-gradient-to-br from-[#1D9E75] to-[#534AB7] text-white p-6">
        <div class="text-center mb-4">
          <h4 class="text-xl font-bold mb-2"><?= htmlspecialchars($f['nom']) ?></h4>
          <div class="text-sm opacity-80 mb-2"><?= htmlspecialchars($f['categorie']) ?></div>
        </div>
        
        <div class="space-y-3 text-sm">
          <div class="flex justify-between">
            <span>Mentors intéressés:</span>
            <span class="font-bold"><?= $f['nb_projets'] ?? 0 ?></span>
          </div>
          <div class="flex justify-between">
            <span>Top Badge:</span>
            <span class="font-bold">🏆 Impact Leader</span>
          </div>
          <?php if($f['last_score_date']): ?>
          <div class="flex justify-between">
            <span>Évolution Score:</span>
            <span class="font-bold text-green-300">+12pts ce mois</span>
          </div>
          <?php endif; ?>
        </div>
        
        <div class="mt-4 text-center">
          <a href="<?= htmlspecialchars(iv_url(['action'=>'founder_dashboard','projet_id'=>$f['id']]), ENT_QUOTES, 'UTF-8') ?>"
             class="bg-white text-green-700 px-4 py-2 rounded-lg font-semibold hover:bg-green-50 transition">
            📊 Voir Dashboard
          </a>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

</body>
</html>