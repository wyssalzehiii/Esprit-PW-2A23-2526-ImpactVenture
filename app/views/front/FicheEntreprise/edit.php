<?php $errors = $errors ?? []; $fiche = $fiche ?? []; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Modifier Fiche Entreprise - ImpactVenture</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
  <style>body{font-family:'Inter',sans-serif;} .brand{font-family:'Space Grotesk',sans-serif;}</style>
</head>
<body class="bg-gray-50 min-h-screen">

<nav class="bg-white border-b shadow-sm sticky top-0 z-50">
  <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
    <a href="index.php?action=fiche_list" class="brand text-2xl font-bold">
      <span class="text-[#1D9E75]">Impact</span><span class="text-[#534AB7]">Venture</span>
    </a>
    <a href="index.php?action=fiche_list" class="text-sm text-gray-500 hover:text-[#1D9E75]">← Retour</a>
  </div>
</nav>

<div class="max-w-3xl mx-auto pt-12 px-6 pb-16">
  <div class="mb-8">
    <h1 class="brand text-4xl font-bold text-gray-900">Modifier l'Entreprise</h1>
    <p class="text-gray-500 mt-1"><?= htmlspecialchars($fiche['nom'] ?? '') ?></p>
  </div>

  <?php if (!empty($errors)): ?>
  <div class="bg-red-50 border border-red-300 text-red-700 px-6 py-4 rounded-2xl mb-8">
    <?php foreach($errors as $err): ?><p class="text-sm">• <?= htmlspecialchars($err) ?></p><?php endforeach; ?>
  </div>
  <?php endif; ?>

  <form method="POST" action="index.php?action=fiche_update&id=<?= $fiche['id'] ?>" class="bg-white rounded-3xl shadow-xl p-10 space-y-6">

    <div>
      <label class="block text-sm font-semibold mb-2">Nom <span class="text-red-500">*</span></label>
      <input type="text" name="nom"
             class="w-full px-5 py-4 border border-gray-300 rounded-2xl focus:outline-none focus:border-[#1D9E75]"
             value="<?= htmlspecialchars($_POST['nom'] ?? $fiche['nom']) ?>">
    </div>

    <div>
      <label class="block text-sm font-semibold mb-2">Description <span class="text-red-500">*</span></label>
      <textarea name="description" rows="5"
                class="w-full px-5 py-4 border border-gray-300 rounded-2xl focus:outline-none focus:border-[#1D9E75]"><?= htmlspecialchars($_POST['description'] ?? $fiche['description']) ?></textarea>
    </div>

    <div>
      <label class="block text-sm font-semibold mb-2">Catégorie <span class="text-red-500">*</span></label>
      <select name="categorie" class="w-full px-5 py-4 border border-gray-300 rounded-2xl bg-white focus:outline-none focus:border-[#1D9E75]">
        <?php $cats=['tech'=>'Tech & IA','digital'=>'Économie digitale','energie renouvelable'=>'Énergie renouvelable','agriculture durable'=>'Agriculture durable','economie circulaire'=>'Économie circulaire','mobilite verte'=>'Mobilité verte','entrepreneuriat'=>'Entrepreneuriat','sante'=>'Santé','education'=>'Éducation','autre'=>'Autre'];
        $cur = $_POST['categorie'] ?? $fiche['categorie'];
        foreach($cats as $v=>$l): ?>
          <option value="<?=$v?>" <?=$cur===$v?'selected':''?>><?=$l?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="grid grid-cols-2 gap-6">
      <div>
        <label class="block text-sm font-semibold mb-2">Mots-clés</label>
        <input type="text" name="mots_cles"
               class="w-full px-5 py-4 border border-gray-300 rounded-2xl"
               value="<?= htmlspecialchars($_POST['mots_cles'] ?? $fiche['mots_cles']) ?>"
               placeholder="ia, solaire, innovation">
      </div>
      <div>
        <label class="block text-sm font-semibold mb-2">Score Green (0-100) <span class="text-red-500">*</span></label>
        <input type="number" name="score_green" min="0" max="100"
               class="w-full px-5 py-4 border border-gray-300 rounded-2xl"
               value="<?= htmlspecialchars($_POST['score_green'] ?? $fiche['score_green']) ?>">
      </div>
    </div>

    <div class="flex justify-between items-center pt-4">
      <a href="index.php?action=fiche_list" class="text-gray-500 text-sm">← Annuler</a>
      <button type="submit" class="bg-[#1D9E75] hover:bg-[#15795A] text-white px-10 py-4 rounded-2xl font-semibold">
        Enregistrer les modifications
      </button>
    </div>
  </form>
</div>
</body>
</html>
