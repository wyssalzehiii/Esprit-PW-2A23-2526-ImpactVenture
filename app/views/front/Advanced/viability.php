<?php $projets=$projets??[];$result=$result??null;$selectedProjet=$selectedProjet??null; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Score Viabilité - ImpactVenture</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    body{font-family:'Inter',sans-serif;background:#0f172a;color:#e2e8f0;min-height:100vh;}
    .brand{font-family:'Space Grotesk',sans-serif;}
    .glass{background:rgba(255,255,255,.05);backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,.1);}
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

<div class="max-w-5xl mx-auto px-6 py-12">
  <div class="text-center mb-12">
    <h1 class="brand text-5xl font-bold mb-3">📊 <span class="bg-gradient-to-r from-[#EF9F27] to-[#E24B4A] bg-clip-text text-transparent">Score de Viabilité Startup</span></h1>
    <p class="text-gray-400 text-lg">Évaluation data-driven de 0 à 100 basée sur 4 critères pondérés</p>
  </div>

  <div class="glass rounded-3xl p-8 mb-10 max-w-3xl mx-auto">
    <form method="POST" action="index.php?action=viability" class="space-y-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm font-semibold mb-2">Projet *</label>
          <select name="id_projet" required class="w-full bg-white/10 border border-white/20 rounded-2xl px-5 py-4 text-white focus:outline-none focus:border-[#EF9F27]">
            <option value="">— Choisir —</option>
            <?php foreach($projets as $p): ?>
              <option value="<?= $p['id_projet'] ?>" class="text-black" <?= ($selectedProjet && $selectedProjet['id_projet']==$p['id_projet'])?'selected':'' ?>>
                <?= htmlspecialchars($p['titre']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-sm font-semibold mb-2">Taille du marché</label>
          <select name="taille_marche" class="w-full bg-white/10 border border-white/20 rounded-2xl px-5 py-4 text-white focus:outline-none focus:border-[#EF9F27]">
            <option value="grand" class="text-black">Grand (>10M TND)</option>
            <option value="moyen" class="text-black" selected>Moyen (1-10M TND)</option>
            <option value="petit" class="text-black">Petit (<1M TND)</option>
            <option value="niche" class="text-black">Niche</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-semibold mb-2">Taille équipe</label>
          <input type="number" name="taille_equipe" min="1" value="3" class="w-full bg-white/10 border border-white/20 rounded-2xl px-5 py-4 text-white focus:outline-none focus:border-[#EF9F27]">
        </div>
        <div>
          <label class="block text-sm font-semibold mb-2">Niveau concurrence</label>
          <select name="niveau_concurrence" class="w-full bg-white/10 border border-white/20 rounded-2xl px-5 py-4 text-white focus:outline-none focus:border-[#EF9F27]">
            <option value="faible" class="text-black">Faible</option>
            <option value="moyen" class="text-black" selected>Moyen</option>
            <option value="fort" class="text-black">Fort</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-semibold mb-2">Coût initial (TND)</label>
          <input type="number" name="cout_initial" value="50000" class="w-full bg-white/10 border border-white/20 rounded-2xl px-5 py-4 text-white focus:outline-none focus:border-[#EF9F27]">
        </div>
        <div>
          <label class="block text-sm font-semibold mb-2">Budget demandé (TND)</label>
          <input type="number" name="budget_demande" value="100000" class="w-full bg-white/10 border border-white/20 rounded-2xl px-5 py-4 text-white focus:outline-none focus:border-[#EF9F27]">
        </div>
      </div>
      <div>
        <label class="block text-sm font-semibold mb-2">Modèle économique</label>
        <input type="text" name="modele_economique" placeholder="Ex: SaaS, marketplace, commission, B2B..." class="w-full bg-white/10 border border-white/20 rounded-2xl px-5 py-4 text-white focus:outline-none focus:border-[#EF9F27]">
      </div>
      <div>
        <label class="block text-sm font-semibold mb-2">Description du projet</label>
        <textarea name="description" rows="3" placeholder="Description pour analyse innovation..." class="w-full bg-white/10 border border-white/20 rounded-2xl px-5 py-4 text-white focus:outline-none focus:border-[#EF9F27]"><?= htmlspecialchars($selectedProjet['description'] ?? '') ?></textarea>
      </div>
      <button type="submit" class="w-full bg-gradient-to-r from-[#EF9F27] to-[#E24B4A] text-white py-4 rounded-2xl font-semibold text-lg hover:opacity-90 transition">
        📊 Calculer le Score de Viabilité
      </button>
    </form>
  </div>

  <?php if($result): ?>
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Gauge -->
    <div class="glass rounded-3xl p-8 text-center">
      <h3 class="font-bold text-xl mb-6">Score Global</h3>
      <div class="relative w-48 h-48 mx-auto mb-6">
        <canvas id="gaugeChart"></canvas>
        <div class="absolute inset-0 flex flex-col items-center justify-center">
          <span class="text-4xl font-bold" style="color:<?= $result['risk']['color'] ?>"><?= $result['global_score'] ?></span>
          <span class="text-xs text-gray-400">/100</span>
        </div>
      </div>
      <div class="inline-block px-4 py-2 rounded-full text-sm font-bold" style="background:<?= $result['risk']['color'] ?>22;color:<?= $result['risk']['color'] ?>">
        Risque <?= $result['risk']['level'] ?> — <?= $result['risk']['label'] ?>
      </div>
    </div>

    <!-- Radar -->
    <div class="glass rounded-3xl p-8">
      <h3 class="font-bold text-xl mb-6 text-center">Détail par Critère</h3>
      <canvas id="radarChart"></canvas>
    </div>

    <!-- Scores détaillés -->
    <div class="glass rounded-3xl p-8 lg:col-span-2">
      <h3 class="font-bold text-xl mb-6">📋 Analyse Détaillée</h3>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <?php
        $labels = ['marche'=>['📊','Marché','#3B82F6'],'equipe'=>['👥','Équipe','#8B5CF6'],'innovation'=>['💡','Innovation','#1D9E75'],'finances'=>['💰','Finances','#EF9F27']];
        foreach($result['scores'] as $k=>$v): $l=$labels[$k]; ?>
        <div class="bg-white/5 rounded-2xl p-5 text-center">
          <div class="text-2xl mb-2"><?= $l[0] ?></div>
          <p class="text-xs text-gray-400"><?= $l[1] ?> (<?= round($result['weights'][$k]*100) ?>%)</p>
          <p class="text-2xl font-bold mt-2" style="color:<?= $l[2] ?>"><?= round($v) ?></p>
          <div class="mt-3 bg-white/10 rounded-full h-2"><div class="h-full rounded-full" style="width:<?= $v ?>%;background:<?= $l[2] ?>"></div></div>
        </div>
        <?php endforeach; ?>
      </div>
      <!-- Recommandations -->
      <h4 class="font-bold mb-4">🎯 Recommandations</h4>
      <?php foreach($result['recommendations'] as $rec): ?>
      <div class="flex items-start gap-3 mb-3 bg-white/5 rounded-2xl p-4">
        <span class="text-xl"><?= $rec['icon'] ?></span>
        <p class="text-gray-300 text-sm"><?= htmlspecialchars($rec['text']) ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <script>
  // Gauge chart
  new Chart(document.getElementById('gaugeChart'),{
    type:'doughnut',
    data:{datasets:[{data:[<?= $result['global_score'] ?>,<?= 100-$result['global_score'] ?>],backgroundColor:['<?= $result['risk']['color'] ?>','rgba(255,255,255,.05)'],borderWidth:0}]},
    options:{cutout:'80%',rotation:-90,circumference:180,responsive:true,plugins:{legend:{display:false},tooltip:{enabled:false}}}
  });
  // Radar chart
  new Chart(document.getElementById('radarChart'),{
    type:'radar',
    data:{labels:['Marché','Équipe','Innovation','Finances'],datasets:[{label:'Score',data:[<?= round($result['scores']['marche']) ?>,<?= round($result['scores']['equipe']) ?>,<?= round($result['scores']['innovation']) ?>,<?= round($result['scores']['finances']) ?>],backgroundColor:'rgba(29,158,117,.2)',borderColor:'#1D9E75',pointBackgroundColor:'#1D9E75',pointRadius:5}]},
    options:{responsive:true,scales:{r:{min:0,max:100,ticks:{color:'#94a3b8',backdropColor:'transparent'},grid:{color:'rgba(255,255,255,.1)'},pointLabels:{color:'#e2e8f0',font:{size:13}}}},plugins:{legend:{display:false}}}
  });
  </script>
  <?php endif; ?>
</div>
</body>
</html>
