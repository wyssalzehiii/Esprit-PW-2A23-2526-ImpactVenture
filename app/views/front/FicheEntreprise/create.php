<?php $errors = $errors ?? []; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Proposer une Fiche Entreprise - ImpactVenture</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">

<div class="max-w-2xl mx-auto pt-12 px-6">
  <h1 class="text-3xl font-bold mb-8 text-center">Proposer une Nouvelle Entreprise</h1>

  <?php if(!empty($errors)): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6">
      <?php foreach($errors as $err): ?><p><?= htmlspecialchars($err) ?></p><?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form method="POST" action="index.php?action=fiche_store" class="bg-white p-8 rounded-3xl shadow">
    <div class="space-y-6">
      <div>
        <label class="block text-sm font-medium mb-1">Nom de l'Entreprise *</label>
        <input type="text" name="nom" class="w-full border rounded-2xl px-4 py-3" value="<?= htmlspecialchars($_POST['nom']??'') ?>">
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Description *</label>
        <div class="flex gap-2 mb-2">
          <button type="button" onclick="startDictation()" class="px-4 py-2 bg-[#1D9E75] text-white rounded-xl text-sm flex items-center gap-2">
            🎤 Dicter (Voix)
          </button>
        </div>
        <textarea name="description" id="description" rows="5" class="w-full border rounded-2xl px-4 py-3"><?= htmlspecialchars($_POST['description']??'') ?></textarea>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Catégorie *</label>
        <select name="categorie" class="w-full border rounded-2xl px-4 py-3">
          <option value="">-- Choisir --</option>
          <option value="tech">Tech & IA</option>
          <option value="digital">Économie Digitale</option>
          <option value="energie renouvelable">Énergie Renouvelable</option>
          <option value="agriculture durable">Agriculture Durable</option>
          <option value="sante">Santé</option>
          <option value="education">Éducation</option>
          <option value="autre">Autre</option>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Mots-clés (séparés par virgule) *</label>
        <input type="text" name="mots_cles" class="w-full border rounded-2xl px-4 py-3" value="<?= htmlspecialchars($_POST['mots_cles']??'') ?>">
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Score Green estimé (0-100) *</label>
        <input type="number" name="score_green" min="0" max="100" class="w-full border rounded-2xl px-4 py-3" value="<?= htmlspecialchars($_POST['score_green']??'75') ?>">
      </div>

      <div class="flex justify-end gap-4 pt-6">
        <a href="index.php?action=fiche_list" class="px-6 py-3 text-gray-600">Annuler</a>
        <button type="submit" class="bg-[#1D9E75] text-white px-8 py-3 rounded-2xl font-semibold">Créer la Fiche Entreprise</button>
      </div>
    </div>
  </form>
</div>

<script>
// Fonctionnalité Vocale (Speech-to-Text)
function startDictation() {
  if (!('SpeechRecognition' in window || 'webkitSpeechRecognition' in window)) {
    alert("Votre navigateur ne supporte pas la reconnaissance vocale.");
    return;
  }
  const recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
  recognition.lang = 'fr-FR';
  recognition.onresult = function(event) {
    document.getElementById('description').value += event.results[0][0].transcript + ' ';
  };
  recognition.start();
}
</script>
</body>
</html>