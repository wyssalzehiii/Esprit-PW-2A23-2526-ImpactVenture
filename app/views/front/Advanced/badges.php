<?php $fiches=$fiches??[];$allBadges=$allBadges??[];$leaderboard=$leaderboard??[];$selectedFiche=$selectedFiche??null;$ficheBadges=$ficheBadges??[];$progress=$progress??null; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Badges & Gamification - ImpactVenture</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    body{font-family:'Inter',sans-serif;background:#0f172a;color:#e2e8f0;min-height:100vh;}
    .brand{font-family:'Space Grotesk',sans-serif;}
    .glass{background:rgba(255,255,255,.05);backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,.1);}
    .badge-card{transition:all .3s;} .badge-card:hover{transform:translateY(-4px) scale(1.02);}
    @keyframes shine{0%{background-position:200% center}100%{background-position:-200% center}}
    .shine{background:linear-gradient(90deg,transparent,rgba(255,255,255,.1),transparent);background-size:200%;animation:shine 3s infinite;}
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
    <h1 class="brand text-5xl font-bold mb-3">🏆 <span class="bg-gradient-to-r from-[#06B6D4] to-[#EF9F27] bg-clip-text text-transparent">Badges & Gamification</span></h1>
    <p class="text-gray-400 text-lg">Gagnez des badges en atteignant vos objectifs entrepreneuriaux</p>
  </div>

  <!-- Sélection entreprise -->
  <div class="glass rounded-3xl p-8 mb-10 max-w-2xl mx-auto">
    <form method="GET" class="flex gap-4">
      <input type="hidden" name="action" value="badges">
      <select name="id_fiche" class="flex-1 bg-white/10 border border-white/20 rounded-2xl px-5 py-4 text-white focus:outline-none focus:border-[#06B6D4]">
        <option value="">— Choisir une entreprise —</option>
        <?php foreach($fiches as $f): ?>
          <option value="<?= $f['id'] ?>" class="text-black" <?= ($selectedFiche && $selectedFiche['id']==$f['id'])?'selected':'' ?>><?= htmlspecialchars($f['nom']) ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="bg-gradient-to-r from-[#06B6D4] to-[#EF9F27] text-white px-8 py-4 rounded-2xl font-semibold">🔍 Voir</button>
    </form>
  </div>

  <?php if($selectedFiche && $progress): ?>
  <!-- Progression -->
  <div class="glass rounded-3xl p-8 mb-10 max-w-2xl mx-auto text-center">
    <h3 class="font-bold text-xl mb-2"><?= htmlspecialchars($selectedFiche['nom']) ?></h3>
    <p class="text-gray-400 text-sm mb-6"><?= $progress['earned'] ?> / <?= $progress['total'] ?> badges débloqués</p>
    <div class="bg-white/10 rounded-full h-4 overflow-hidden mb-3">
      <div class="h-full rounded-full bg-gradient-to-r from-[#06B6D4] to-[#EF9F27] transition-all duration-1000" style="width:<?= $progress['percent'] ?>%"></div>
    </div>
    <p class="text-[#EF9F27] font-bold text-lg"><?= $progress['percent'] ?>% complété</p>
  </div>

  <!-- Badges gagnés -->
  <?php if(!empty($ficheBadges)): ?>
  <h2 class="brand text-2xl font-bold mb-6">🎖️ Badges Débloqués</h2>
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-12">
    <?php foreach($ficheBadges as $b): ?>
    <div class="glass rounded-2xl p-5 text-center badge-card shine" style="border-bottom:3px solid <?= $b['couleur'] ?>">
      <div class="text-4xl mb-3"><?= $b['icone'] ?></div>
      <h4 class="font-bold text-sm" style="color:<?= $b['couleur'] ?>"><?= htmlspecialchars($b['nom']) ?></h4>
      <p class="text-[10px] text-gray-400 mt-1"><?= htmlspecialchars($b['description']) ?></p>
      <p class="text-[10px] text-gray-500 mt-2">🗓 <?= date('d/m/Y', strtotime($b['awarded_at'])) ?></p>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
  <?php endif; ?>

  <!-- Tous les badges disponibles -->
  <h2 class="brand text-2xl font-bold mb-6">📋 Tous les Badges Disponibles</h2>
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-12">
    <?php foreach($allBadges as $b): 
      $earned = false;
      foreach($ficheBadges as $fb) { if($fb['id']==$b['id']) { $earned=true; break; } }
    ?>
    <div class="glass rounded-2xl p-5 text-center <?= $earned ? '' : 'opacity-40' ?>" style="border-bottom:3px solid <?= $b['couleur'] ?>">
      <div class="text-3xl mb-2"><?= $b['icone'] ?></div>
      <h4 class="font-bold text-sm" style="color:<?= $b['couleur'] ?>"><?= htmlspecialchars($b['nom']) ?></h4>
      <p class="text-[10px] text-gray-400 mt-1"><?= htmlspecialchars($b['description']) ?></p>
      <?= $earned ? '<p class="text-[10px] text-emerald-400 mt-2">✅ Débloqué</p>' : '<p class="text-[10px] text-gray-500 mt-2">🔒 Verrouillé</p>' ?>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Leaderboard -->
  <h2 class="brand text-2xl font-bold mb-6">🏅 Classement</h2>
  <div class="glass rounded-3xl overflow-hidden">
    <table class="w-full">
      <thead><tr class="border-b border-white/10">
        <th class="text-left px-6 py-4 text-xs text-gray-400">Rang</th>
        <th class="text-left px-6 py-4 text-xs text-gray-400">Entreprise</th>
        <th class="text-center px-6 py-4 text-xs text-gray-400">Badges</th>
        <th class="text-center px-6 py-4 text-xs text-gray-400">Score Green</th>
      </tr></thead>
      <tbody>
        <?php foreach($leaderboard as $i => $l): 
          $medals = ['🥇','🥈','🥉'];
          $medal = $medals[$i] ?? ($i+1);
        ?>
        <tr class="border-b border-white/5 hover:bg-white/5">
          <td class="px-6 py-4 text-lg"><?= $medal ?></td>
          <td class="px-6 py-4 font-bold"><?= htmlspecialchars($l['nom']) ?></td>
          <td class="px-6 py-4 text-center"><span class="bg-[#EF9F27]/20 text-[#EF9F27] px-3 py-1 rounded-full text-sm font-bold"><?= $l['badge_count'] ?></span></td>
          <td class="px-6 py-4 text-center"><span class="text-[#1D9E75] font-bold"><?= $l['score_green'] ?>%</span></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
