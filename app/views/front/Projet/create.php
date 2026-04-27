<?php 
$fiches = $fiches ?? [];
$errors = $errors ?? [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Proposer un Projet - ImpactVenture</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">

<div class="max-w-2xl mx-auto pt-12 px-6">
  <h1 class="text-3xl font-bold mb-8 text-center">Proposer un Nouveau Projet</h1>

  <?php if(!empty($errors)): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
      <?php foreach($errors as $err): ?><p><?= htmlspecialchars($err) ?></p><?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form method="POST" action="index.php?action=projet_store" class="bg-white p-8 rounded-3xl shadow">
    <div class="space-y-6">
      <div>
        <label class="block text-sm font-medium mb-1">Fiche Entreprise *</label>
        <select name="id_fiche_entreprise" class="w-full border rounded-2xl px-4 py-3">
          <option value="">-- Sélectionner une entreprise --</option>
          <?php foreach($fiches as $f): ?>
            <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['nom']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Titre du Projet *</label>
        <input type="text" name="titre" class="w-full border rounded-2xl px-4 py-3">
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Description du Projet *</label>
        <textarea name="description" rows="5" class="w-full border rounded-2xl px-4 py-3"></textarea>
      </div>

      <div class="flex justify-end gap-4 pt-6">
        <a href="index.php?action=fiche_list" class="px-6 py-3 text-gray-600">Annuler</a>
        <button type="submit" class="bg-[#1D9E75] text-white px-8 py-3 rounded-2xl font-semibold">Soumettre le Projet</button>
      </div>
    </div>
  </form>
</div>
</body>
</html>s