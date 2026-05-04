<?php
$projets = $projets ?? []; $mentors = $mentors ?? []; $investors = $investors ?? []; $selectedProjet = $selectedProjet ?? null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Matching IA - ImpactVenture</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    body{font-family:'Inter',sans-serif;background:#0f172a;color:#e2e8f0;min-height:100vh;}
    .brand{font-family:'Space Grotesk',sans-serif;}
    .glass{background:rgba(255,255,255,.05);backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,.1);}
    .match-card{transition:all .3s;} .match-card:hover{transform:translateY(-4px);border-color:rgba(29,158,117,.5);}
    .score-ring{width:60px;height:60px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.9rem;}
    @keyframes fadeIn{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
    .anim{animation:fadeIn .5s ease forwards;opacity:0;}
  </style>
</head>
<body>
<nav class="glass sticky top-0 z-50 border-b border-white/10">
  <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
    <a href="index.php?action=fiche_list" class="brand text-2xl font-bold"><span class="text-[#1D9E75]">Impact</span><span class="text-[#534AB7]">Venture</span></a>
    <div class="flex gap-6 text-sm">
      <a href="index.php?action=advanced_dashboard" class="text-[#1D9E75] font-semibold">← Dashboard Avancé</a>
      <a href="index.php?action=projet_list" class="hover:text-[#1D9E75]">Projets</a>
      <a href="index.php?action=admin" class="text-amber-400">Admin</a>
    </div>
  </div>
</nav>

<div class="max-w-7xl mx-auto px-6 py-12">
  <div class="text-center mb-12">
    <h1 class="brand text-5xl font-bold mb-3">🤝 <span class="bg-gradient-to-r from-[#1D9E75] to-[#534AB7] bg-clip-text text-transparent">Matching Intelligent</span></h1>
    <p class="text-gray-400 text-lg">Trouvez les mentors et investisseurs les plus compatibles avec votre projet</p>
  </div>

  <!-- Sélection projet -->
  <div class="glass rounded-3xl p-8 mb-10 max-w-2xl mx-auto">
    <h2 class="font-bold text-lg mb-4">📌 Sélectionnez un projet</h2>
    <form method="GET" class="flex gap-4">
      <input type="hidden" name="action" value="matching">
      <select name="id_projet" class="flex-1 bg-white/10 border border-white/20 rounded-2xl px-5 py-4 text-white focus:outline-none focus:border-[#1D9E75]">
        <option value="">— Choisir un projet —</option>
        <?php foreach($projets as $p): ?>
          <option value="<?= $p['id_projet'] ?>" <?= ($selectedProjet && $selectedProjet['id_projet']==$p['id_projet'])?'selected':'' ?> class="text-black">
            <?= htmlspecialchars($p['titre']) ?> (<?= htmlspecialchars($p['entreprise_categorie'] ?? '') ?>)
          </option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="bg-gradient-to-r from-[#1D9E75] to-[#534AB7] text-white px-8 py-4 rounded-2xl font-semibold hover:opacity-90 transition">
        🔍 Analyser
      </button>
    </form>
  </div>

  <?php if ($selectedProjet && (!empty($mentors) || !empty($investors))): ?>

  <!-- Projet sélectionné -->
  <div class="glass rounded-2xl p-6 mb-10 max-w-2xl mx-auto border-l-4 border-[#1D9E75]">
    <h3 class="font-bold text-xl text-[#1D9E75]"><?= htmlspecialchars($selectedProjet['titre']) ?></h3>
    <p class="text-gray-400 text-sm mt-1"><?= htmlspecialchars($selectedProjet['entreprise_nom'] ?? '') ?> — <?= htmlspecialchars($selectedProjet['entreprise_categorie'] ?? '') ?></p>
    <p class="text-gray-500 text-sm mt-2"><?= htmlspecialchars(substr($selectedProjet['description'],0,200)) ?>...</p>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
    <!-- Mentors -->
    <div>
      <h2 class="brand text-2xl font-bold mb-6 flex items-center gap-3">
        <span class="text-3xl">🎓</span> Top Mentors Recommandés
      </h2>
      <?php foreach($mentors as $i => $m): $delay = $i * 100; $scoreColor = $m['match_score']>=70?'#1D9E75':($m['match_score']>=40?'#EF9F27':'#E24B4A'); ?>
      <div class="glass rounded-2xl p-6 mb-4 match-card anim" style="animation-delay:<?= $delay ?>ms">
        <div class="flex items-start gap-4">
          <div class="score-ring" style="background:<?= $scoreColor ?>22;color:<?= $scoreColor ?>;border:2px solid <?= $scoreColor ?>">
            <?= $m['match_score'] ?>%
          </div>
          <div class="flex-1">
            <h3 class="font-bold text-lg"><?= htmlspecialchars($m['nom']) ?></h3>
            <p class="text-[#1D9E75] text-sm font-medium"><?= htmlspecialchars($m['specialite']) ?></p>
            <p class="text-gray-400 text-xs mt-1">📍 <?= htmlspecialchars($m['ville']) ?> · <?= $m['experience_annees'] ?> ans d'expérience</p>
            <p class="text-gray-500 text-sm mt-2"><?= htmlspecialchars(substr($m['bio'] ?? '',0,120)) ?></p>
            <div class="flex gap-2 mt-3 flex-wrap">
              <?php foreach(array_slice(explode(',',$m['mots_cles']),0,4) as $kw): if(trim($kw)): ?>
              <span class="text-[10px] px-2 py-1 rounded-full bg-[#534AB7]/20 text-[#a78bfa]"><?= trim($kw) ?></span>
              <?php endif; endforeach; ?>
            </div>
          </div>
        </div>
        <!-- Score bar -->
        <div class="mt-4 bg-white/5 rounded-full h-2 overflow-hidden">
          <div class="h-full rounded-full transition-all duration-1000" style="width:<?= $m['match_score'] ?>%;background:<?= $scoreColor ?>"></div>
        </div>
      </div>
      <?php endforeach; ?>
      <?php if(empty($mentors)): ?><p class="text-gray-500 text-center py-8">Aucun mentor trouvé</p><?php endif; ?>
    </div>

    <!-- Investisseurs -->
    <div>
      <h2 class="brand text-2xl font-bold mb-6 flex items-center gap-3">
        <span class="text-3xl">💰</span> Top Investisseurs Recommandés
      </h2>
      <?php foreach($investors as $i => $inv): $delay = $i * 100 + 200; $scoreColor = $inv['match_score']>=70?'#1D9E75':($inv['match_score']>=40?'#EF9F27':'#E24B4A'); ?>
      <div class="glass rounded-2xl p-6 mb-4 match-card anim" style="animation-delay:<?= $delay ?>ms">
        <div class="flex items-start gap-4">
          <div class="score-ring" style="background:<?= $scoreColor ?>22;color:<?= $scoreColor ?>;border:2px solid <?= $scoreColor ?>">
            <?= $inv['match_score'] ?>%
          </div>
          <div class="flex-1">
            <h3 class="font-bold text-lg"><?= htmlspecialchars($inv['nom']) ?></h3>
            <p class="text-[#EF9F27] text-sm font-medium"><?= ucfirst(str_replace('_',' ',$inv['type_investissement'])) ?></p>
            <p class="text-gray-400 text-xs mt-1">📍 <?= htmlspecialchars($inv['ville']) ?> · Budget: <?= number_format($inv['budget_min']) ?> – <?= number_format($inv['budget_max']) ?> TND</p>
            <p class="text-gray-500 text-sm mt-2"><?= htmlspecialchars(substr($inv['bio'] ?? '',0,120)) ?></p>
            <div class="flex gap-2 mt-3 flex-wrap">
              <?php foreach(array_slice(explode(',',$inv['secteurs']),0,3) as $s): if(trim($s)): ?>
              <span class="text-[10px] px-2 py-1 rounded-full bg-[#EF9F27]/20 text-[#fbbf24]"><?= trim($s) ?></span>
              <?php endif; endforeach; ?>
            </div>
          </div>
        </div>
        <div class="mt-4 bg-white/5 rounded-full h-2 overflow-hidden">
          <div class="h-full rounded-full transition-all duration-1000" style="width:<?= $inv['match_score'] ?>%;background:<?= $scoreColor ?>"></div>
        </div>
      </div>
      <?php endforeach; ?>
      <?php if(empty($investors)): ?><p class="text-gray-500 text-center py-8">Aucun investisseur trouvé</p><?php endif; ?>
    </div>
  </div>
  <?php endif; ?>
</div>
</body>
</html>
