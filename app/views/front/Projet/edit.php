<?php $errors = $errors ?? []; $fiches = $fiches ?? []; $projet = $projet ?? []; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Modifier le Projet - ImpactVenture</title>
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
    <div class="flex gap-8 text-sm font-medium">
      <a href="index.php?action=projet_list" class="text-[#534AB7] font-semibold">← Retour aux Projets</a>
    </div>
  </div>
</nav>

<div class="max-w-3xl mx-auto pt-12 px-6 pb-16">
  <div class="mb-10">
    <h1 class="brand text-4xl font-bold text-gray-900">Modifier le Projet</h1>
    <p class="text-gray-500 mt-1"><?= htmlspecialchars($projet['titre'] ?? '') ?></p>
  </div>

  <?php if (!empty($errors)): ?>
  <div class="bg-red-50 border border-red-300 text-red-700 px-6 py-4 rounded-2xl mb-8">
    <?php foreach($errors as $err): ?><p class="text-sm">• <?= htmlspecialchars($err) ?></p><?php endforeach; ?>
  </div>
  <?php endif; ?>

  <form method="POST" action="index.php?action=projet_update&id=<?= $projet['id_projet'] ?>" class="bg-white rounded-3xl shadow-xl p-10 space-y-6">

    <div>
      <label class="block text-sm font-semibold mb-2">Titre <span class="text-red-500">*</span></label>
      <input type="text" name="titre"
             class="w-full px-5 py-4 border border-gray-300 rounded-2xl focus:outline-none focus:border-[#1D9E75]"
             value="<?= htmlspecialchars($_POST['titre'] ?? $projet['titre']) ?>">
    </div>

    <div>
      <label class="block text-sm font-semibold mb-2">Description <span class="text-red-500">*</span></label>
      <textarea name="description" rows="5"
                class="w-full px-5 py-4 border border-gray-300 rounded-2xl focus:outline-none focus:border-[#1D9E75]"><?= htmlspecialchars($_POST['description'] ?? $projet['description']) ?></textarea>
    </div>

    <div>
      <label class="block text-sm font-semibold mb-2">Entreprise Associée <span class="text-red-500">*</span></label>
      <select name="id_fiche_entreprise"
              class="w-full px-5 py-4 border border-gray-300 rounded-2xl bg-white focus:outline-none focus:border-[#1D9E75]">
        <?php foreach ($fiches as $f): ?>
          <option value="<?= $f['id'] ?>"
            <?= (($_POST['id_fiche_entreprise'] ?? $projet['id_fiche_entreprise']) == $f['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($f['nom']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="flex justify-between items-center pt-4">
      <a href="index.php?action=projet_list" class="text-gray-500 text-sm">← Annuler</a>
      <button type="submit" class="bg-[#1D9E75] hover:bg-[#15795A] text-white px-10 py-4 rounded-2xl font-semibold">
        Enregistrer les modifications
      </button>
    </div>
  </form>
</div>
</body>
</html>
