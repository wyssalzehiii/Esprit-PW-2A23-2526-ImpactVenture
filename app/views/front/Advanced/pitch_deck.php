<?php $projets=$projets??[];$slides=$slides??null;$selectedProjet=$selectedProjet??null; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pitch Deck Generator - ImpactVenture</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    body{font-family:'Inter',sans-serif;background:#0f172a;color:#e2e8f0;min-height:100vh;}
    .brand{font-family:'Space Grotesk',sans-serif;}
    .glass{background:rgba(255,255,255,.05);backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,.1);}
    .slide-preview{aspect-ratio:16/9;transition:all .3s;cursor:pointer;}
    .slide-preview:hover{transform:scale(1.03);box-shadow:0 20px 40px rgba(0,0,0,.4);}
    .slide-full{aspect-ratio:16/9;display:flex;flex-direction:column;justify-content:center;align-items:center;text-align:center;padding:40px;}
    @keyframes slideIn{from{opacity:0;transform:scale(.95)}to{opacity:1;transform:scale(1)}}
    .slide-anim{animation:slideIn .4s ease forwards;}
  </style>
</head>
<body>
<nav class="glass sticky top-0 z-50 border-b border-white/10">
  <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
    <a href="index.php?action=fiche_list" class="brand text-2xl font-bold"><span class="text-[#1D9E75]">Impact</span><span class="text-[#534AB7]">Venture</span></a>
    <div class="flex gap-6 text-sm"><a href="index.php?action=advanced_dashboard" class="text-[#1D9E75] font-semibold">← Dashboard</a></div>
  </div>
</nav>

<div class="max-w-6xl mx-auto px-6 py-12">
  <div class="text-center mb-12">
    <h1 class="brand text-5xl font-bold mb-3">📱 <span class="bg-gradient-to-r from-[#EC4899] to-[#534AB7] bg-clip-text text-transparent">Pitch Deck Generator</span></h1>
    <p class="text-gray-400 text-lg">Générez automatiquement un pitch deck de 10 slides professionnelles</p>
  </div>

  <?php if(!$slides): ?>
  <div class="glass rounded-3xl p-8 max-w-2xl mx-auto">
    <form method="GET" class="space-y-4">
      <input type="hidden" name="action" value="pitch_deck">
      <input type="hidden" name="generate" value="1">
      <select name="id_projet" required class="w-full bg-white/10 border border-white/20 rounded-2xl px-5 py-4 text-white focus:outline-none focus:border-[#EC4899]">
        <option value="">— Choisir un projet —</option>
        <?php foreach($projets as $p): ?>
          <option value="<?= $p['id_projet'] ?>" class="text-black"><?= htmlspecialchars($p['titre']) ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="w-full bg-gradient-to-r from-[#EC4899] to-[#534AB7] text-white py-4 rounded-2xl font-semibold text-lg hover:opacity-90 transition">
        🤖 Générer le Pitch Deck (10 slides)
      </button>
    </form>
  </div>
  <?php else: ?>

  <!-- Actions -->
  <div class="flex justify-center gap-4 mb-10">
    <a href="index.php?action=pitch_deck&export=1&id_projet=<?= $selectedProjet['id_projet'] ?>" target="_blank" class="glass px-6 py-3 rounded-2xl hover:bg-white/10 transition font-medium">📄 Exporter Plein Écran</a>
    <button onclick="window.print()" class="glass px-6 py-3 rounded-2xl hover:bg-white/10 transition font-medium">🖨️ Imprimer PDF</button>
    <a href="index.php?action=pitch_deck" class="glass px-6 py-3 rounded-2xl hover:bg-white/10 transition font-medium">🔄 Nouveau</a>
  </div>

  <!-- Slide viewer -->
  <div class="mb-10">
    <div id="slideViewer" class="glass rounded-3xl overflow-hidden slide-anim">
      <?php $colors=['#1D9E75','#534AB7','#EF9F27','#3B82F6','#10B981','#8B5CF6','#F59E0B','#06B6D4','#E24B4A','#EC4899']; ?>
      <?php foreach($slides as $i => $slide): $bg = $colors[$i % count($colors)]; ?>
      <div class="slide-full <?= $i>0?'hidden':'' ?>" data-slide="<?= $i ?>" style="background:linear-gradient(135deg,<?= $bg ?>,<?= $bg ?>cc);min-height:400px;">
        <p class="text-xs opacity-60 mb-4">Slide <?= $i+1 ?> / <?= count($slides) ?></p>
        <h2 class="text-3xl md:text-4xl font-bold mb-6"><?= htmlspecialchars($slide['titre'] ?? 'Slide '.($i+1)) ?></h2>
        <div class="text-lg opacity-90 whitespace-pre-line max-w-xl leading-relaxed"><?= nl2br(htmlspecialchars($slide['contenu'] ?? '')) ?></div>
        <p class="text-xs opacity-40 mt-8"><?= htmlspecialchars($selectedProjet['titre']) ?> — ImpactVenture</p>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Navigation -->
    <div class="flex justify-center items-center gap-4 mt-6">
      <button onclick="prevSlide()" class="glass px-6 py-3 rounded-2xl hover:bg-white/10 transition">← Précédent</button>
      <span id="slideCounter" class="text-sm text-gray-400">1 / <?= count($slides) ?></span>
      <button onclick="nextSlide()" class="glass px-6 py-3 rounded-2xl hover:bg-white/10 transition">Suivant →</button>
    </div>
  </div>

  <!-- Miniatures -->
  <h3 class="brand text-xl font-bold mb-4">📑 Toutes les Slides</h3>
  <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
    <?php foreach($slides as $i => $slide): $bg = $colors[$i % count($colors)]; ?>
    <div onclick="goToSlide(<?= $i ?>)" class="slide-preview rounded-2xl p-4 flex flex-col justify-center items-center text-center" style="background:linear-gradient(135deg,<?= $bg ?>,<?= $bg ?>cc);">
      <p class="text-[10px] opacity-60 mb-1">Slide <?= $i+1 ?></p>
      <h4 class="text-xs font-bold"><?= htmlspecialchars($slide['titre'] ?? '') ?></h4>
    </div>
    <?php endforeach; ?>
  </div>

  <script>
  let current = 0;
  const total = <?= count($slides) ?>;
  const allSlides = document.querySelectorAll('[data-slide]');

  function showSlide(n) {
    allSlides.forEach(s => s.classList.add('hidden'));
    allSlides[n].classList.remove('hidden');
    document.getElementById('slideCounter').textContent = (n+1) + ' / ' + total;
    current = n;
  }
  function nextSlide() { showSlide((current + 1) % total); }
  function prevSlide() { showSlide((current - 1 + total) % total); }
  function goToSlide(n) { showSlide(n); window.scrollTo({top:0,behavior:'smooth'}); }

  // Keyboard navigation
  document.addEventListener('keydown', e => {
    if (e.key === 'ArrowRight') nextSlide();
    if (e.key === 'ArrowLeft') prevSlide();
  });
  </script>
  <?php endif; ?>
</div>
</body>
</html>
