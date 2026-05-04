<?php $projets=$projets??[];$sdgResults=$sdgResults??[];$selectedProjet=$selectedProjet??null;$allSdgs=$allSdgs??[]; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ODD Classifier - ImpactVenture</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    body{font-family:'Inter',sans-serif;background:#0f172a;color:#e2e8f0;min-height:100vh;}
    .brand{font-family:'Space Grotesk',sans-serif;}
    .glass{background:rgba(255,255,255,.05);backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,.1);}
    .sdg-badge{transition:all .3s;} .sdg-badge:hover{transform:scale(1.05);}
    @keyframes pop{0%{transform:scale(0)}50%{transform:scale(1.1)}100%{transform:scale(1)}}
    .pop{animation:pop .4s ease forwards;}
  </style>
</head>
<body>
<nav class="glass sticky top-0 z-50 border-b border-white/10">
  <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
    <a href="index.php?action=fiche_list" class="brand text-2xl font-bold"><span class="text-[#1D9E75]">Impact</span><span class="text-[#534AB7]">Venture</span></a>
    <div class="flex gap-6 text-sm">
      <a href="index.php?action=advanced_dashboard" class="text-[#1D9E75] font-semibold">← Dashboard</a>
    </div>
  </div>
</nav>

<div class="max-w-6xl mx-auto px-6 py-12">
  <div class="text-center mb-12">
    <h1 class="brand text-5xl font-bold mb-3">🌍 <span class="bg-gradient-to-r from-[#3B82F6] to-[#1D9E75] bg-clip-text text-transparent">Analyse ODD / SDG</span></h1>
    <p class="text-gray-400 text-lg">Détection automatique des Objectifs de Développement Durable (ONU)</p>
  </div>

  <div class="glass rounded-3xl p-8 mb-10 max-w-2xl mx-auto">
    <form method="GET" class="flex gap-4">
      <input type="hidden" name="action" value="sdg">
      <select name="id_projet" class="flex-1 bg-white/10 border border-white/20 rounded-2xl px-5 py-4 text-white focus:outline-none focus:border-[#3B82F6]">
        <option value="">— Choisir un projet —</option>
        <?php foreach($projets as $p): ?>
          <option value="<?= $p['id_projet'] ?>" class="text-black" <?= ($selectedProjet && $selectedProjet['id_projet']==$p['id_projet'])?'selected':'' ?>>
            <?= htmlspecialchars($p['titre']) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="bg-gradient-to-r from-[#3B82F6] to-[#1D9E75] text-white px-8 py-4 rounded-2xl font-semibold">🔍 Analyser</button>
    </form>
  </div>

  <?php if($selectedProjet && !empty($sdgResults)): ?>
  <div class="glass rounded-2xl p-6 mb-8 border-l-4 border-[#3B82F6] max-w-2xl mx-auto">
    <h3 class="font-bold text-xl text-[#3B82F6]"><?= htmlspecialchars($selectedProjet['titre']) ?></h3>
    <p class="text-gray-400 text-sm mt-1"><?= count($sdgResults) ?> ODD détectés</p>
  </div>

  <!-- Résultats SDG -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
    <?php foreach($sdgResults as $i => $sdg): ?>
    <div class="glass rounded-3xl p-6 sdg-badge pop" style="animation-delay:<?= $i*100 ?>ms;border-left:4px solid <?= $sdg['couleur'] ?>">
      <div class="flex items-center gap-4 mb-4">
        <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl" style="background:<?= $sdg['couleur'] ?>22">
          <?= $sdg['icone'] ?>
        </div>
        <div>
          <p class="text-xs text-gray-400">ODD <?= $sdg['numero'] ?></p>
          <h4 class="font-bold" style="color:<?= $sdg['couleur'] ?>"><?= htmlspecialchars($sdg['nom']) ?></h4>
        </div>
        <div class="ml-auto">
          <span class="text-lg font-bold" style="color:<?= $sdg['couleur'] ?>"><?= $sdg['relevance'] ?>%</span>
        </div>
      </div>
      <p class="text-xs text-gray-400 mb-3"><?= htmlspecialchars($sdg['description']) ?></p>
      <div class="bg-white/5 rounded-full h-2 mb-3">
        <div class="h-full rounded-full" style="width:<?= $sdg['relevance'] ?>%;background:<?= $sdg['couleur'] ?>"></div>
      </div>
      <div class="flex flex-wrap gap-1">
        <?php foreach($sdg['keywords_matched'] as $kw): ?>
        <span class="text-[10px] px-2 py-0.5 rounded-full" style="background:<?= $sdg['couleur'] ?>22;color:<?= $sdg['couleur'] ?>"><?= htmlspecialchars($kw) ?></span>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <!-- Tous les ODD -->
  <div class="mt-12">
    <h2 class="brand text-2xl font-bold mb-6 text-center">Les 17 Objectifs de Développement Durable</h2>
    <div class="grid grid-cols-3 md:grid-cols-6 lg:grid-cols-9 gap-3">
      <?php foreach($allSdgs as $s): 
        $isMatched = false;
        foreach($sdgResults as $r) { if($r['numero'] == $s['numero']) { $isMatched = true; break; } }
      ?>
      <div class="rounded-2xl p-3 text-center transition-all <?= $isMatched ? 'ring-2 ring-white/50 scale-105' : 'opacity-40' ?>" style="background:<?= $s['couleur'] ?>">
        <div class="text-2xl"><?= $s['icone'] ?></div>
        <p class="text-[10px] font-bold text-white mt-1"><?= $s['numero'] ?>. <?= htmlspecialchars(substr($s['nom'],0,15)) ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
</body>
</html>
