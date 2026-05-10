<?php
$projets = $projets ?? [];
$msgs = [
    'created' => '✅ Projet soumis avec succès ! Il est maintenant en attente d\'évaluation.',
    'created_with_ai' => '✅ Projet créé — analyse IA enregistrée.',
    'updated' => '✅ Projet modifié.',
    'deleted' => '🗑️ Projet supprimé.'
];
$msgCode = $_GET['msg'] ?? '';
$msg = $msgs[$msgCode] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Projets - ImpactVenture</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
  <style>
  body{font-family:'Inter',sans-serif;} 
  .brand{font-family:'Space Grotesk',sans-serif;} 
  .card{transition:all .3s ease;} 
  .card:hover{transform:translateY(-6px);box-shadow:0 25px 50px -12px rgba(0,0,0,.15);} 
  .project-card{cursor:pointer;transition:all .3s ease;} 
  .project-card:hover{transform:translateY(-4px);box-shadow:0 20px 25px -5px rgba(0,0,0,.1),0 10px 10px -5px rgba(0,0,0,.04);}
  .score-bar{height:4px;border-radius:2px;transition:width 1s ease;}
  .score-ring{width:60px;height:60px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:bold;font-size:14px;color:white;}
  .quick-tip{background:linear-gradient(135deg,#fef3c7 0%,#fde68a 100%);border-left:4px solid #f59e0b;}
</style>
</head>
<body class="bg-gray-50 min-h-screen">

<nav class="bg-white border-b shadow-sm sticky top-0 z-50">
  <div class="max-w-7xl mx-auto px-6 py-5 flex items-center justify-between">
    <a href="<?= htmlspecialchars(iv_url(['action'=>'fiche_list']), ENT_QUOTES, 'UTF-8') ?>" class="brand flex items-center gap-2">
      <img src="logo.png" alt="ImpactVenture Logo" style="height: 40px; width: auto; object-fit: contain;">
      <span class="text-2xl font-bold"><span class="text-[#1D9E75]">Impact</span><span class="text-[#534AB7]">Venture</span></span>
    </a>
    <div class="flex items-center gap-10 text-sm font-medium">
      <a href="<?= htmlspecialchars(iv_url(['action'=>'fiche_list']), ENT_QUOTES, 'UTF-8') ?>" class="hover:text-[#1D9E75]">Entreprises</a>
      <a href="<?= htmlspecialchars(iv_url(['action'=>'projet_list']), ENT_QUOTES, 'UTF-8') ?>" class="text-[#534AB7] font-semibold">Projets</a>
      <a href="<?= htmlspecialchars(iv_url(['action'=>'financement']), ENT_QUOTES, 'UTF-8') ?>" class="text-[#EF9F27] font-semibold">💰 Financement</a>
      <a href="<?= htmlspecialchars(iv_url(['action'=>'admin']), ENT_QUOTES, 'UTF-8') ?>" class="text-amber-600 font-medium">Admin →</a>
    </div>
    <a href="<?= htmlspecialchars(iv_url(['action'=>'projet_create']), ENT_QUOTES, 'UTF-8') ?>"
       class="bg-[#1D9E75] hover:bg-[#15795A] text-white px-6 py-3 rounded-2xl font-semibold text-sm transition">
      + Proposer un Projet
    </a>
  </div>
</nav>

<div class="max-w-7xl mx-auto px-6 py-12">
  <?php if ($msg): ?>
    <div class="bg-emerald-50 border border-emerald-300 text-emerald-700 px-6 py-4 rounded-2xl mb-8 font-semibold"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <div class="mb-10">
    <h1 class="brand text-5xl font-bold text-gray-900 mb-3">Projets innovants</h1>
    <p class="text-gray-500 text-lg">Découvrez les projets des entrepreneurs tunisiens et l’entreprise associée.</p>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    <?php if (empty($projets)): ?>
      <div class="col-span-3 text-center py-20">
        <p class="text-2xl text-gray-400 mb-6">Aucun projet pour le moment</p>
        <a href="<?= htmlspecialchars(iv_url(['action'=>'projet_create']), ENT_QUOTES, 'UTF-8') ?>" class="bg-[#1D9E75] text-white px-8 py-4 rounded-2xl font-semibold">
          Soumettre le premier projet
        </a>
      </div>
    <?php else: ?>
      <?php foreach ($projets as $p): ?>
      <div class="bg-white rounded-3xl overflow-hidden shadow-sm card project-card" onclick="window.location.href='<?= htmlspecialchars(iv_url(['action'=>'projet_show','id'=>$p['id_projet']]), ENT_QUOTES, 'UTF-8') ?>'">
        <div class="h-2 bg-gradient-to-r from-[#1D9E75] via-[#534AB7] to-[#EF9F27]"></div>
        <div class="p-7">
          <div class="flex justify-between items-start mb-4">
            <span class="px-3 py-1 text-xs font-semibold rounded-full
              <?= $p['statut'] == 'accepté' ? 'bg-emerald-100 text-emerald-700' :
                ($p['statut'] == 'en_evaluation' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600') ?>">
              <?= ucfirst(htmlspecialchars($p['statut'])) ?>
            </span>
            <?php 
            $finalScore = round($p['final_score'] ?? ($p['score_ia'] ?? 0));
            $scoreColor = $finalScore >= 70 ? '#1D9E75' : ($finalScore >= 50 ? '#EF9F27' : '#E24B4A');
            ?>
            <div class="score-ring" style="background: conic-gradient(from 0deg, <?= $scoreColor ?> <?= ($finalScore / 100) * 360 ?>deg, #e5e7eb <?= ($finalScore / 100) * 360 ?>deg);">
              <?= $finalScore ?>%
            </div>
          </div>
          
          <h3 class="brand font-bold text-xl leading-tight mb-3"><?= htmlspecialchars($p['titre']) ?></h3>
          <p class="text-gray-600 text-sm leading-relaxed mb-4 line-clamp-3">
            <?php
              $snippet = function_exists('mb_substr')
                ? mb_substr($p['description'], 0, 160, 'UTF-8')
                : substr($p['description'], 0, 160);
              echo htmlspecialchars($snippet);
            ?>…
          </p>
          
          <!-- Mini Score Bars -->
          <div class="space-y-2 mb-4">
            <div class="flex items-center gap-2">
              <span class="text-xs text-gray-500 w-16">🚀 Innovation</span>
              <div class="flex-1 bg-gray-200 rounded-full overflow-hidden">
                <div class="score-bar bg-blue-500" style="width: <?= min(100, max(0, ($p['innovation_score'] ?? 0))) ?>%"></div>
              </div>
              <span class="text-xs font-semibold text-blue-600"><?= round($p['innovation_score'] ?? 0) ?></span>
            </div>
            <div class="flex items-center gap-2">
              <span class="text-xs text-gray-500 w-16">🌱 Durabilité</span>
              <div class="flex-1 bg-gray-200 rounded-full overflow-hidden">
                <div class="score-bar bg-green-500" style="width: <?= min(100, max(0, ($p['sustainability_score'] ?? 0))) ?>%"></div>
              </div>
              <span class="text-xs font-semibold text-green-600"><?= round($p['sustainability_score'] ?? 0) ?></span>
            </div>
            <div class="flex items-center gap-2">
              <span class="text-xs text-gray-500 w-16">📊 Marché</span>
              <div class="flex-1 bg-gray-200 rounded-full overflow-hidden">
                <div class="score-bar bg-purple-500" style="width: <?= min(100, max(0, ($p['market_score'] ?? 0))) ?>%"></div>
              </div>
              <span class="text-xs font-semibold text-purple-600"><?= round($p['market_score'] ?? 0) ?></span>
            </div>
            <div class="flex items-center gap-2">
              <span class="text-xs text-gray-500 w-16">⚠️ Risque</span>
              <div class="flex-1 bg-gray-200 rounded-full overflow-hidden">
                <div class="score-bar bg-red-500" style="width: <?= min(100, max(0, ($p['risk_score'] ?? 0))) ?>%"></div>
              </div>
              <span class="text-xs font-semibold text-red-600"><?= round($p['risk_score'] ?? 0) ?></span>
            </div>
          </div>
          
          <!-- Quick Recommendation -->
          <?php 
          $quickTip = '';
          $innovation = $p['innovation_score'] ?? 0;
          $market = $p['market_score'] ?? 0;
          $sustainability = $p['sustainability_score'] ?? 0;
          $risk = $p['risk_score'] ?? 0;
          
          if ($innovation < 50) {
            $quickTip = '🎯 Améliorez votre proposition de valeur et différenciation';
          } elseif ($market < 50) {
            $quickTip = '📈 Développez votre stratégie de marché et validation client';
          } elseif ($sustainability < 60) {
            $quickTip = '🌱 Renforcez l\'impact environnemental et social';
          } elseif ($risk > 60) {
            $quickTip = '⚠️ Travaillez sur la réduction des risques opérationnels';
          } else {
            $quickTip = '✅ Projet bien équilibré, continuez sur cette voie';
          }
          ?>
          <div class="quick-tip p-3 rounded-lg mb-4 text-sm">
            <span class="font-semibold text-amber-800">🎯 Recommandation rapide:</span>
            <span class="text-amber-700"><?= $quickTip ?></span>
          </div>
          
          <div class="text-xs text-gray-500 mb-5">
            🏢 <span class="font-semibold text-gray-700"><?= htmlspecialchars($p['entreprise_nom'] ?? 'Non associée') ?></span>
            <?php if (!empty($p['entreprise_categorie'])): ?>
              — <?= htmlspecialchars($p['entreprise_categorie']) ?>
            <?php endif; ?>
          </div>
          
          <div class="flex gap-2" onclick="event.stopPropagation()">
            <a href="<?= htmlspecialchars(iv_url(['action'=>'projet_show','id'=>$p['id_projet']]), ENT_QUOTES, 'UTF-8') ?>"
               class="flex-1 text-center py-2.5 text-xs border border-gray-200 rounded-xl hover:bg-gray-50 font-medium">Voir</a>
            <a href="<?= htmlspecialchars(iv_url(['action'=>'projet_edit','id'=>$p['id_projet']]), ENT_QUOTES, 'UTF-8') ?>"
               class="flex-1 text-center py-2.5 text-xs border border-[#1D9E75] text-[#1D9E75] rounded-xl hover:bg-green-50 font-medium">Modifier</a>
            <a href="<?= htmlspecialchars(iv_url(['action'=>'projet_delete','id'=>$p['id_projet']]), ENT_QUOTES, 'UTF-8') ?>"
               onclick="return confirm('Supprimer ce projet ?')"
               class="flex-1 text-center py-2.5 text-xs bg-red-50 text-red-600 rounded-xl hover:bg-red-100 font-medium">Supprimer</a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>
<script>
// Animate score bars on page load
document.addEventListener('DOMContentLoaded', function() {
    const scoreBars = document.querySelectorAll('.score-bar');
    scoreBars.forEach(bar => {
        const targetWidth = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.width = targetWidth;
        }, 100);
    });
});
</script>
</body>
</html>
