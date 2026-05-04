<?php
$projets = $projets ?? [];
$fiches = $fiches ?? [];
$totalProjets = count($projets);
$totalFiches = count($fiches);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Avancé - ImpactVenture</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    body{font-family:'Inter',sans-serif;background:linear-gradient(135deg,#0f172a 0%,#1e293b 100%);min-height:100vh;color:#e2e8f0;}
    .brand{font-family:'Space Grotesk',sans-serif;}
    .glass{background:rgba(255,255,255,.05);backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,.1);}
    .card-feature{transition:all .3s;cursor:pointer;}
    .card-feature:hover{transform:translateY(-8px);box-shadow:0 25px 50px rgba(0,0,0,.3);border-color:rgba(29,158,117,.5);}
    .glow{box-shadow:0 0 30px rgba(29,158,117,.2);}
  </style>
</head>
<body>

<nav class="glass sticky top-0 z-50 border-b border-white/10">
  <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
    <a href="index.php?action=fiche_list" class="brand text-2xl font-bold">
      <span class="text-[#1D9E75]">Impact</span><span class="text-[#534AB7]">Venture</span>
      <span class="text-xs ml-2 px-2 py-1 bg-[#1D9E75]/20 text-[#1D9E75] rounded-full">Advanced</span>
    </a>
    <div class="flex gap-6 text-sm">
      <a href="index.php?action=fiche_list" class="hover:text-[#1D9E75] transition">Entreprises</a>
      <a href="index.php?action=projet_list" class="hover:text-[#1D9E75] transition">Projets</a>
      <a href="index.php?action=admin" class="text-amber-400 hover:text-amber-300 transition">Admin</a>
    </div>
  </div>
</nav>

<div class="max-w-7xl mx-auto px-6 py-12">
  <div class="text-center mb-16">
    <h1 class="brand text-5xl font-bold mb-4">
      <span class="bg-gradient-to-r from-[#1D9E75] via-[#534AB7] to-[#EF9F27] bg-clip-text text-transparent">
        10 Fonctionnalités Avancées
      </span>
    </h1>
    <p class="text-gray-400 text-lg max-w-2xl mx-auto">IA + Scoring + NLP + Gamification — Plateforme d'impact entrepreneurial nouvelle génération</p>
    <div class="flex justify-center gap-8 mt-8">
      <div class="glass rounded-2xl px-6 py-4 glow"><span class="text-3xl font-bold text-[#1D9E75]"><?= $totalProjets ?></span><p class="text-xs text-gray-400 mt-1">Projets</p></div>
      <div class="glass rounded-2xl px-6 py-4"><span class="text-3xl font-bold text-[#534AB7]"><?= $totalFiches ?></span><p class="text-xs text-gray-400 mt-1">Entreprises</p></div>
      <div class="glass rounded-2xl px-6 py-4"><span class="text-3xl font-bold text-[#EF9F27]">10</span><p class="text-xs text-gray-400 mt-1">Modules IA</p></div>
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
    <?php
    $features = [
      ['matching','🤝','Matching IA','Mentor & Investisseur','#1D9E75','Recommandation intelligente'],
      ['business_plan','📋','Business Plan','Génération PDF + IA','#534AB7','Export professionnel'],
      ['viability','📊','Score Viabilité','Prédiction 0-100','#EF9F27','Data-driven'],
      ['sdg','🌍','ODD Classifier','17 objectifs ONU','#3B82F6','Impact scoring'],
      ['map','🗺️','Carte Interactive','Startups Tunisie','#10B981','Géolocalisation'],
      ['chatbot','🤖','Chatbot Coach','Assistant IA','#8B5CF6','Coaching startup'],
      ['sentiment','🎯','Analyse Pitch','NLP Sentiment','#F59E0B','Qualité pitch'],
      ['badges','🏆','Badges','Gamification','#06B6D4','Motivation'],
      ['co2_simulator','♻️','CO2 Simulator','Impact carbone','#E24B4A','Environnement'],
      ['pitch_deck','📱','Pitch Deck','Slides auto','#EC4899','Présentation IA']
    ];
    foreach ($features as $f): ?>
    <a href="index.php?action=<?= $f[0] ?>" class="glass rounded-3xl p-6 card-feature block text-center">
      <div class="text-4xl mb-4"><?= $f[1] ?></div>
      <h3 class="font-bold text-sm mb-1" style="color:<?= $f[4] ?>"><?= $f[2] ?></h3>
      <p class="text-xs text-gray-400 mb-3"><?= $f[3] ?></p>
      <span class="inline-block text-[10px] px-2 py-1 rounded-full" style="background:<?= $f[4] ?>22;color:<?= $f[4] ?>"><?= $f[5] ?></span>
    </a>
    <?php endforeach; ?>
  </div>
</div>
</body>
</html>
