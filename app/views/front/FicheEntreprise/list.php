<?php 
$msgs = ['created'=>'Fiche entreprise créée !','updated'=>'Mise à jour réussie.','deleted'=>'Supprimée.'];
$msg = isset($_GET['msg']) ? ($msgs[$_GET['msg']] ?? '') : '';
$fiches = $fiches ?? [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ImpactVenture - Fiches Entreprises</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
  <style>
    :root{--green:#1D9E75;--purple:#534AB7;}
    body{font-family:'Inter',sans-serif;background:#F0F4F1;}
    .brand{font-family:'Space Grotesk',sans-serif;}
  </style>
</head>
<body class="min-h-screen">

<nav class="bg-white border-b shadow-sm sticky top-0 z-50">
  <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
    <a href="index.php?action=fiche_list" class="brand text-2xl font-bold flex items-center gap-1">
      <span style="color:#1D9E75">Impact</span><span style="color:#534AB7">Venture</span>
    </a>
    <div class="flex gap-8 text-sm font-medium">
      <a href="index.php?action=fiche_list" class="text-[#1D9E75] font-semibold">Fiches Entreprises</a>
      <a href="index.php?action=projet_list" class="hover:text-[#1D9E75]">Mes Projets</a>
      <a href="index.php?action=admin" class="text-[#EF9F27]">Admin</a>
    </div>
    <div class="flex gap-3">
      <a href="index.php?action=fiche_create" class="bg-[#1D9E75] text-white px-5 py-2.5 rounded-xl font-semibold text-sm">+ Proposer une Entreprise</a>
      <a href="index.php?action=projet_create" class="border border-[#1D9E75] text-[#1D9E75] px-5 py-2.5 rounded-xl font-semibold text-sm">+ Proposer un Projet</a>
    </div>
  </div>
</nav>

<div class="max-w-7xl mx-auto px-6 py-8">
  <?php if($msg): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <h1 class="brand text-4xl font-bold mb-2">Fiches Entreprises</h1>
  <p class="text-gray-600 mb-8">Découvrez les entreprises incubées sur ImpactVenture</p>

  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach($fiches as $f): ?>
    <div class="bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-xl transition">
      <div class="h-2 bg-gradient-to-r from-[#1D9E75] to-[#534AB7]"></div>
      <div class="p-6">
        <h3 class="font-bold text-xl mb-2"><?= htmlspecialchars($f['nom']) ?></h3>
        <p class="text-sm text-gray-600 line-clamp-3 mb-4"><?= htmlspecialchars(substr($f['description'],0,120)) ?>...</p>
        
        <div class="flex justify-between text-xs text-gray-500 mb-4">
          <span><?= htmlspecialchars($f['categorie']) ?></span>
          <span class="font-semibold text-[#1D9E75]"><?= $f['score_green'] ?>% Green</span>
        </div>

        <div class="text-xs text-gray-400">
          <?= $f['nb_projets'] ?> projet(s) associé(s)
        </div>

        <div class="flex gap-3 mt-6">
          <a href="index.php?action=fiche_edit&id=<?= $f['id'] ?>" class="flex-1 text-center py-3 text-sm border border-gray-300 rounded-2xl hover:bg-gray-50">Modifier</a>
          <a href="index.php?action=fiche_delete&id=<?= $f['id'] ?>" onclick="return confirm('Supprimer ?')" 
             class="flex-1 text-center py-3 text-sm bg-red-50 text-red-600 rounded-2xl hover:bg-red-100">Supprimer</a>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>

</body>
</html>