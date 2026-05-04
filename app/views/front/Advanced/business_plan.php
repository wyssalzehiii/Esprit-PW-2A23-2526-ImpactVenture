<?php $projets=$projets??[];$businessPlan=$businessPlan??null;$selectedProjet=$selectedProjet??null; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Business Plan IA - ImpactVenture</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    body{font-family:'Inter',sans-serif;background:#0f172a;color:#e2e8f0;min-height:100vh;}
    .brand{font-family:'Space Grotesk',sans-serif;}
    .glass{background:rgba(255,255,255,.05);backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,.1);}
    .swot-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
    @keyframes slideUp{from{opacity:0;transform:translateY(30px)}to{opacity:1;transform:translateY(0)}}
    .anim{animation:slideUp .6s ease forwards;opacity:0;}
    @media print{body{background:#fff;color:#333;} .no-print{display:none!important;} .glass{background:#fff;border:1px solid #e5e7eb;}}
  </style>
</head>
<body>
<nav class="glass sticky top-0 z-50 border-b border-white/10 no-print">
  <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
    <a href="index.php?action=fiche_list" class="brand text-2xl font-bold"><span class="text-[#1D9E75]">Impact</span><span class="text-[#534AB7]">Venture</span></a>
    <div class="flex gap-6 text-sm">
      <a href="index.php?action=advanced_dashboard" class="text-[#1D9E75] font-semibold">← Dashboard</a>
      <a href="index.php?action=projet_list" class="hover:text-[#1D9E75]">Projets</a>
    </div>
  </div>
</nav>

<div class="max-w-5xl mx-auto px-6 py-12">
  <div class="text-center mb-12 no-print">
    <h1 class="brand text-5xl font-bold mb-3">📋 <span class="bg-gradient-to-r from-[#534AB7] to-[#1D9E75] bg-clip-text text-transparent">Business Plan IA</span></h1>
    <p class="text-gray-400 text-lg">Générez un business plan professionnel structuré par Intelligence Artificielle</p>
  </div>

  <?php if(!$businessPlan): ?>
  <div class="glass rounded-3xl p-8 max-w-2xl mx-auto no-print">
    <h2 class="font-bold text-lg mb-4">📌 Sélectionnez un projet</h2>
    <form method="GET" class="space-y-4">
      <input type="hidden" name="action" value="business_plan">
      <input type="hidden" name="generate" value="1">
      <select name="id_projet" class="w-full bg-white/10 border border-white/20 rounded-2xl px-5 py-4 text-white focus:outline-none focus:border-[#534AB7]">
        <option value="">— Choisir un projet —</option>
        <?php foreach($projets as $p): ?>
          <option value="<?= $p['id_projet'] ?>" class="text-black"><?= htmlspecialchars($p['titre']) ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="w-full bg-gradient-to-r from-[#534AB7] to-[#1D9E75] text-white py-4 rounded-2xl font-semibold text-lg hover:opacity-90 transition">
        🤖 Générer le Business Plan
      </button>
    </form>
  </div>
  <?php else: ?>

  <!-- Actions -->
  <div class="flex justify-center gap-4 mb-10 no-print">
    <button onclick="window.print()" class="glass px-6 py-3 rounded-2xl hover:bg-white/10 transition font-medium">🖨️ Imprimer / PDF</button>
    <a href="index.php?action=business_plan&pdf=1&id_projet=<?= $selectedProjet['id_projet'] ?>" target="_blank" class="glass px-6 py-3 rounded-2xl hover:bg-white/10 transition font-medium">📄 Ouvrir PDF</a>
    <a href="index.php?action=business_plan" class="glass px-6 py-3 rounded-2xl hover:bg-white/10 transition font-medium">🔄 Nouveau</a>
  </div>

  <!-- Header -->
  <div class="glass rounded-3xl p-10 mb-8 text-center anim">
    <div class="h-1 bg-gradient-to-r from-[#1D9E75] via-[#534AB7] to-[#EF9F27] rounded-full mb-8"></div>
    <h2 class="brand text-3xl font-bold text-white mb-2"><?= htmlspecialchars($selectedProjet['titre'] ?? 'Business Plan') ?></h2>
    <p class="text-gray-400">Généré par ImpactVenture IA — <?= date('d/m/Y H:i') ?></p>
  </div>

  <!-- Sections -->
  <?php if(isset($businessPlan['resume_executif'])): ?>
  <div class="glass rounded-3xl p-8 mb-6 anim" style="animation-delay:100ms">
    <h3 class="text-xl font-bold text-[#1D9E75] mb-4 flex items-center gap-2">📝 1. Résumé Exécutif</h3>
    <p class="text-gray-300 leading-relaxed"><?= nl2br(htmlspecialchars($businessPlan['resume_executif'])) ?></p>
  </div>
  <?php endif; ?>

  <?php if(isset($businessPlan['probleme'])): ?>
  <div class="glass rounded-3xl p-8 mb-6 anim" style="animation-delay:200ms">
    <h3 class="text-xl font-bold text-[#E24B4A] mb-4">⚠️ 2. Problème Identifié</h3>
    <p class="text-gray-300 leading-relaxed"><?= nl2br(htmlspecialchars($businessPlan['probleme'])) ?></p>
  </div>
  <?php endif; ?>

  <?php if(isset($businessPlan['solution'])): ?>
  <div class="glass rounded-3xl p-8 mb-6 anim" style="animation-delay:300ms">
    <h3 class="text-xl font-bold text-[#1D9E75] mb-4">💡 3. Solution Proposée</h3>
    <p class="text-gray-300 leading-relaxed"><?= nl2br(htmlspecialchars($businessPlan['solution'])) ?></p>
  </div>
  <?php endif; ?>

  <?php if(isset($businessPlan['marche'])): ?>
  <div class="glass rounded-3xl p-8 mb-6 anim" style="animation-delay:400ms">
    <h3 class="text-xl font-bold text-[#3B82F6] mb-4">📊 4. Analyse de Marché</h3>
    <p class="text-gray-300 leading-relaxed"><?= nl2br(htmlspecialchars($businessPlan['marche'])) ?></p>
  </div>
  <?php endif; ?>

  <?php if(isset($businessPlan['modele_economique'])): ?>
  <div class="glass rounded-3xl p-8 mb-6 anim" style="animation-delay:500ms">
    <h3 class="text-xl font-bold text-[#EF9F27] mb-4">💰 5. Modèle Économique</h3>
    <p class="text-gray-300 leading-relaxed"><?= nl2br(htmlspecialchars($businessPlan['modele_economique'])) ?></p>
  </div>
  <?php endif; ?>

  <?php if(isset($businessPlan['swot'])): ?>
  <div class="glass rounded-3xl p-8 mb-6 anim" style="animation-delay:600ms">
    <h3 class="text-xl font-bold text-[#8B5CF6] mb-4">🔄 6. Analyse SWOT</h3>
    <div class="swot-grid">
      <div class="bg-emerald-500/10 border border-emerald-500/30 rounded-2xl p-5">
        <h4 class="font-bold text-emerald-400 mb-2">💪 Forces</h4>
        <ul class="text-sm text-gray-300 space-y-1"><?php foreach($businessPlan['swot']['forces']??[] as $i): ?><li>• <?= htmlspecialchars($i) ?></li><?php endforeach; ?></ul>
      </div>
      <div class="bg-red-500/10 border border-red-500/30 rounded-2xl p-5">
        <h4 class="font-bold text-red-400 mb-2">⚠️ Faiblesses</h4>
        <ul class="text-sm text-gray-300 space-y-1"><?php foreach($businessPlan['swot']['faiblesses']??[] as $i): ?><li>• <?= htmlspecialchars($i) ?></li><?php endforeach; ?></ul>
      </div>
      <div class="bg-blue-500/10 border border-blue-500/30 rounded-2xl p-5">
        <h4 class="font-bold text-blue-400 mb-2">🚀 Opportunités</h4>
        <ul class="text-sm text-gray-300 space-y-1"><?php foreach($businessPlan['swot']['opportunites']??[] as $i): ?><li>• <?= htmlspecialchars($i) ?></li><?php endforeach; ?></ul>
      </div>
      <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-2xl p-5">
        <h4 class="font-bold text-yellow-400 mb-2">🔴 Menaces</h4>
        <ul class="text-sm text-gray-300 space-y-1"><?php foreach($businessPlan['swot']['menaces']??[] as $i): ?><li>• <?= htmlspecialchars($i) ?></li><?php endforeach; ?></ul>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <?php if(isset($businessPlan['plan_financier'])): ?>
  <div class="glass rounded-3xl p-8 mb-6 anim" style="animation-delay:700ms">
    <h3 class="text-xl font-bold text-[#10B981] mb-4">📈 7. Plan Financier</h3>
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
      <?php foreach($businessPlan['plan_financier'] as $k => $v): ?>
      <div class="bg-white/5 rounded-2xl p-4 text-center">
        <p class="text-xs text-gray-400"><?= ucfirst(str_replace('_',' ',$k)) ?></p>
        <p class="text-lg font-bold text-[#1D9E75] mt-1"><?= htmlspecialchars($v) ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <?php if(isset($businessPlan['timeline'])): ?>
  <div class="glass rounded-3xl p-8 mb-6 anim" style="animation-delay:800ms">
    <h3 class="text-xl font-bold text-[#06B6D4] mb-4">📅 8. Timeline</h3>
    <div class="space-y-4">
      <?php foreach($businessPlan['timeline'] as $period => $task): ?>
      <div class="flex items-center gap-4">
        <div class="w-3 h-3 rounded-full bg-[#1D9E75] flex-shrink-0"></div>
        <div class="flex-1 bg-white/5 rounded-2xl p-4">
          <span class="font-bold text-[#534AB7]"><?= htmlspecialchars($period) ?></span>
          <span class="text-gray-300 ml-3"><?= htmlspecialchars($task) ?></span>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <?php endif; ?>
</div>
</body>
</html>
