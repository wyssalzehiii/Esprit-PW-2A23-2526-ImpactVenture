<?php $projets=$projets??[];$analysis=$analysis??null;$selectedProjet=$selectedProjet??null; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Analyse Sentiment - ImpactVenture</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    body{font-family:'Inter',sans-serif;background:#0f172a;color:#e2e8f0;min-height:100vh;}
    .brand{font-family:'Space Grotesk',sans-serif;}
    .glass{background:rgba(255,255,255,.05);backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,.1);}
    @keyframes fillBar{from{width:0}to{width:var(--w)}}
    .bar-anim{animation:fillBar 1s ease forwards;}
  </style>
</head>
<body>
<nav class="glass sticky top-0 z-50 border-b border-white/10">
  <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
    <a href="index.php?action=fiche_list" class="brand text-2xl font-bold"><span class="text-[#1D9E75]">Impact</span><span class="text-[#534AB7]">Venture</span></a>
    <div class="flex gap-6 text-sm"><a href="index.php?action=advanced_dashboard" class="text-[#1D9E75] font-semibold">← Dashboard</a></div>
  </div>
</nav>

<div class="max-w-5xl mx-auto px-6 py-12">
  <div class="text-center mb-12">
    <h1 class="brand text-5xl font-bold mb-3">🎯 <span class="bg-gradient-to-r from-[#F59E0B] to-[#E24B4A] bg-clip-text text-transparent">Analyse de Pitch</span></h1>
    <p class="text-gray-400 text-lg">Sentiment • Clarté • Professionnalisme • Confiance — Analyse NLP automatique</p>
  </div>

  <div class="glass rounded-3xl p-8 mb-10 max-w-2xl mx-auto">
    <form method="GET" class="flex gap-4">
      <input type="hidden" name="action" value="sentiment">
      <select name="id_projet" class="flex-1 bg-white/10 border border-white/20 rounded-2xl px-5 py-4 text-white focus:outline-none focus:border-[#F59E0B]">
        <option value="">— Choisir un projet à analyser —</option>
        <?php foreach($projets as $p): ?>
          <option value="<?= $p['id_projet'] ?>" class="text-black" <?= ($selectedProjet && $selectedProjet['id_projet']==$p['id_projet'])?'selected':'' ?>><?= htmlspecialchars($p['titre']) ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="bg-gradient-to-r from-[#F59E0B] to-[#E24B4A] text-white px-8 py-4 rounded-2xl font-semibold">🔍 Analyser</button>
    </form>
  </div>

  <?php if($analysis && $selectedProjet): ?>
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Score principal -->
    <div class="glass rounded-3xl p-8 text-center">
      <h3 class="font-bold text-lg mb-4">Pitch Quality Score</h3>
      <div class="relative w-40 h-40 mx-auto mb-4">
        <canvas id="qualityGauge"></canvas>
        <div class="absolute inset-0 flex flex-col items-center justify-center">
          <span class="text-4xl font-bold text-[#F59E0B]"><?= $analysis['pitch_quality'] ?></span>
          <span class="text-xs text-gray-400">/100</span>
        </div>
      </div>
      <div class="inline-block px-4 py-2 rounded-full text-sm font-bold <?= $analysis['sentiment']>70?'bg-emerald-500/20 text-emerald-400':($analysis['sentiment']>45?'bg-yellow-500/20 text-yellow-400':'bg-red-500/20 text-red-400') ?>">
        Sentiment: <?= $analysis['sentiment_label'] ?>
      </div>
    </div>

    <!-- Barres détaillées -->
    <div class="glass rounded-3xl p-8 lg:col-span-2">
      <h3 class="font-bold text-lg mb-6">Analyse Détaillée</h3>
      <?php
      $metrics = [
        ['Sentiment','sentiment','#10B981','😊'],
        ['Clarté','clarte','#3B82F6','📝'],
        ['Professionnalisme','professionnalisme','#8B5CF6','💼'],
        ['Confiance','confiance','#F59E0B','🔒']
      ];
      foreach($metrics as $m): $val = $analysis[$m[1]]; ?>
      <div class="mb-5">
        <div class="flex justify-between items-center mb-2">
          <span class="text-sm font-medium"><?= $m[3] ?> <?= $m[0] ?></span>
          <span class="text-sm font-bold" style="color:<?= $m[2] ?>"><?= $val ?>%</span>
        </div>
        <div class="bg-white/10 rounded-full h-3 overflow-hidden">
          <div class="h-full rounded-full bar-anim" style="--w:<?= $val ?>%;background:<?= $m[2] ?>"></div>
        </div>
      </div>
      <?php endforeach; ?>

      <!-- Radar -->
      <div class="mt-6"><canvas id="sentimentRadar" height="200"></canvas></div>
    </div>

    <!-- Conseils -->
    <div class="glass rounded-3xl p-8 lg:col-span-3">
      <h3 class="font-bold text-lg mb-4">💡 Conseils d'Amélioration</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach($analysis['conseils'] ?? [] as $i => $conseil): 
          $colors = ['#1D9E75','#534AB7','#EF9F27','#3B82F6','#E24B4A'];
          $c = $colors[$i % count($colors)]; ?>
        <div class="bg-white/5 rounded-2xl p-4 border-l-4" style="border-color:<?= $c ?>">
          <p class="text-sm text-gray-300"><?= htmlspecialchars($conseil) ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Texte analysé -->
    <div class="glass rounded-3xl p-8 lg:col-span-3">
      <h3 class="font-bold text-lg mb-4">📄 Texte Analysé</h3>
      <div class="bg-white/5 rounded-2xl p-6">
        <h4 class="font-bold text-[#1D9E75] mb-2"><?= htmlspecialchars($selectedProjet['titre']) ?></h4>
        <p class="text-gray-400 text-sm leading-relaxed"><?= nl2br(htmlspecialchars($selectedProjet['description'])) ?></p>
      </div>
    </div>
  </div>

  <script>
  new Chart(document.getElementById('qualityGauge'),{
    type:'doughnut',
    data:{datasets:[{data:[<?= $analysis['pitch_quality'] ?>,<?= 100-$analysis['pitch_quality'] ?>],backgroundColor:['#F59E0B','rgba(255,255,255,.05)'],borderWidth:0}]},
    options:{cutout:'80%',rotation:-90,circumference:180,responsive:true,plugins:{legend:{display:false},tooltip:{enabled:false}}}
  });
  new Chart(document.getElementById('sentimentRadar'),{
    type:'radar',
    data:{labels:['Sentiment','Clarté','Professionnalisme','Confiance'],datasets:[{label:'Score',data:[<?= $analysis['sentiment'] ?>,<?= $analysis['clarte'] ?>,<?= $analysis['professionnalisme'] ?>,<?= $analysis['confiance'] ?>],backgroundColor:'rgba(245,158,11,.15)',borderColor:'#F59E0B',pointBackgroundColor:'#F59E0B',pointRadius:5}]},
    options:{responsive:true,scales:{r:{min:0,max:100,ticks:{color:'#94a3b8',backdropColor:'transparent'},grid:{color:'rgba(255,255,255,.08)'},pointLabels:{color:'#e2e8f0',font:{size:12}}}},plugins:{legend:{display:false}}}
  });
  </script>
  <?php endif; ?>
</div>
</body>
</html>
