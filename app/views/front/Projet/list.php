<?php
$projets = $projets ?? [];
$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Projets - ImpactVenture</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Inter', system-ui, sans-serif; }
    .brand { font-family: 'Space Grotesk', sans-serif; }
  </style>
</head>
<body class="bg-gray-50 min-h-screen">

<nav class="bg-white shadow-sm border-b">
  <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
    <a href="index.php" class="brand text-2xl font-bold text-[#1D9E75]">ImpactVenture</a>
    <div class="flex gap-8">
      <a href="index.php?action=fiche_list" class="hover:text-[#1D9E75]">Entreprises</a>
      <a href="index.php?action=projet_list" class="text-[#534AB7] font-semibold">Projets</a>
    </div>
    <a href="index.php?action=projet_create" class="bg-[#1D9E75] text-white px-5 py-2.5 rounded-xl text-sm font-semibold">+ Proposer un Projet</a>
  </div>
</nav>

<div class="max-w-7xl mx-auto px-6 py-8">
  <?php if($msg === 'created'): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6">
      ✅ Projet soumis avec succès !
    </div>
  <?php endif; ?>

  <h1 class="text-4xl font-bold mb-2">Découvrir les Projets</h1>
  <p class="text-gray-600 mb-8">Explorez les projets innovants soumis par les entrepreneurs</p>

  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php if(empty($projets)): ?>
      <p class="col-span-3 text-center text-gray-500 py-12">Aucun projet pour le moment.</p>
    <?php else: ?>
      <?php foreach($projets as $p): ?>
        <div class="bg-white rounded-2xl shadow-sm p-6 hover:shadow-md transition">
          <div class="flex justify-between items-start mb-4">
            <span class="px-3 py-1 text-xs font-medium rounded-full 
              <?= $p['statut'] === 'accepté' ? 'bg-green-100 text-green-700' : 
                 ($p['statut'] === 'en_evaluation' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700') ?>">
              <?= htmlspecialchars($p['statut']) ?>
            </span>
            <?php if(!empty($p['score_ia'])): ?>
              <span class="text-sm font-semibold text-[#1D9E75]"><?= round($p['score_ia']) ?> IA</span>
            <?php endif; ?>
          </div>

          <h3 class="font-bold text-lg mb-2"><?= htmlspecialchars($p['titre']) ?></h3>
          <p class="text-gray-600 text-sm line-clamp-3 mb-4">
            <?= htmlspecialchars(substr($p['description'], 0, 150)) ?>...
          </p>

          <div class="text-xs text-gray-500">
            Entreprise : <strong><?= htmlspecialchars($p['entreprise_nom'] ?? 'Non associée') ?></strong>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

</body>
</html>