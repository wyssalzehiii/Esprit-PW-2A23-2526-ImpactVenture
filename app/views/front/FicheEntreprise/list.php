<?php
$msgs = ['created'=>'✅ Fiche créée !','updated'=>'✅ Mise à jour réussie.','deleted'=>'🗑️ Supprimée.'];
$msg  = isset($_GET['msg']) ? ($msgs[$_GET['msg']] ?? '') : '';
$fiches = $fiches ?? [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fiches Entreprises - ImpactVenture</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
  <style>
    body{font-family:'Inter',sans-serif;background:#F0F4F1;}
    .brand{font-family:'Space Grotesk',sans-serif;}
    .card{transition:all .3s;}
    .card:hover{transform:translateY(-8px);box-shadow:0 25px 50px -12px rgba(0,0,0,.15);}
  </style>
</head>
<body class="min-h-screen">

<nav class="bg-white border-b shadow-sm sticky top-0 z-50">
  <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
    <a href="index.php?action=fiche_list" class="brand flex items-center gap-2">
      <img src="logo.png" alt="ImpactVenture Logo" style="height: 40px; width: auto; object-fit: contain;">
      <span class="text-2xl font-bold"><span style="color:#1D9E75">Impact</span><span style="color:#534AB7">Venture</span></span>
    </a>
    <div class="flex gap-8 text-sm font-medium">
      <a href="index.php?action=fiche_list" class="text-[#1D9E75] font-semibold">Entreprises</a>
      <a href="index.php?action=projet_list" class="hover:text-[#1D9E75]">Projets</a>
      <a href="index.php?action=admin" class="text-amber-600">Admin</a>
    </div>
 <div class="flex gap-3">
    <a href="index.php?action=fiche_create" 
       class="bg-[#1D9E75] text-white px-6 py-3 rounded-2xl font-semibold text-sm">
        + Nouvelle Entreprise
    </a>
    
    <a href="index.php?action=projet_create" 
       class="bg-[#534AB7] text-white px-6 py-3 rounded-2xl font-semibold text-sm">
        + Nouveau Projet
    </a>
</div>
  </div>
</nav>

<div class="max-w-7xl mx-auto px-6 py-8">
  <?php if($msg): ?>
    <div class="bg-emerald-100 border border-emerald-400 text-emerald-700 px-6 py-4 rounded-2xl mb-8"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <h1 class="brand text-5xl font-bold mb-2">Fiches Entreprises</h1>
  <p class="text-gray-600 mb-10">Découvrez les entreprises incubées sur ImpactVenture</p>

  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    <?php foreach($fiches as $f): ?>
    <div class="bg-white rounded-3xl overflow-hidden shadow card">
      <div class="h-2 bg-gradient-to-r from-[#1D9E75] via-[#534AB7] to-[#EF9F27]"></div>
      
      <div class="p-6">
        <!-- Logo -->
        <?php if(!empty($f['logo'])): ?>
          <div class="flex justify-center mb-5">
            <img src="<?= htmlspecialchars($f['logo']) ?>" 
                 alt="Logo" 
                 class="h-20 w-20 object-contain rounded-2xl shadow">
          </div>
        <?php endif; ?>

        <h3 class="font-bold text-2xl text-center mb-3"><?= htmlspecialchars($f['nom']) ?></h3>
        
        <p class="text-gray-600 text-sm mb-5 line-clamp-4">
          <?= htmlspecialchars(substr($f['description'], 0, 140)) ?>...
        </p>

        <div class="flex justify-between items-center text-sm mb-6">
          <span class="px-3 py-1 bg-gray-100 rounded-full"><?= htmlspecialchars($f['categorie']) ?></span>
          <span class="font-semibold text-emerald-600"><?= $f['score_green'] ?>% Green 🌱</span>
        </div>

        <div class="flex gap-3">
          <a href="index.php?action=fiche_edit&id=<?= $f['id'] ?>" 
             class="flex-1 text-center py-3 border border-gray-300 rounded-2xl hover:bg-gray-50">Modifier</a>
          <a href="index.php?action=fiche_delete&id=<?= $f['id'] ?>" 
             onclick="return confirm('Supprimer cette entreprise ?')" 
             class="flex-1 text-center py-3 bg-red-50 text-red-600 rounded-2xl hover:bg-red-100">Supprimer</a>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>
</body>
</html>