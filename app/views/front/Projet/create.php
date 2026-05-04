<?php $errors = $errors ?? []; $fiches = $fiches ?? []; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proposer un Projet - ImpactVenture</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <style>body{font-family:'Inter',sans-serif;} .brand{font-family:'Space Grotesk',sans-serif;}</style>
</head>
<body class="bg-gray-50 min-h-screen">

<nav class="bg-white border-b shadow-sm sticky top-0 z-50">
  <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
    <a href="index.php?action=fiche_list" class="brand flex items-center gap-2">
      <img src="logo.png" alt="ImpactVenture Logo" style="height: 40px; width: auto; object-fit: contain;">
      <span class="text-2xl font-bold"><span class="text-[#1D9E75]">Impact</span><span class="text-[#534AB7]">Venture</span></span>
    </a>
    <div class="flex gap-8 text-sm font-medium">
      <a href="index.php?action=fiche_list" class="hover:text-[#1D9E75]">Entreprises</a>
      <a href="index.php?action=projet_list" class="text-[#534AB7] font-semibold">Projets</a>
      <a href="index.php?action=admin" class="text-amber-600">Admin</a>
    </div>
  </div>
</nav>

<div class="max-w-3xl mx-auto pt-12 px-6 pb-16">
  <div class="mb-10">
    <h1 class="brand text-4xl font-bold text-gray-900">Proposer un Projet</h1>
    <p class="text-gray-600 mt-2">Soumettez votre idée de projet et associez-la à une entreprise incubée.</p>
  </div>

  <?php if (!empty($errors)): ?>
  <div class="bg-red-50 border border-red-300 text-red-700 px-6 py-4 rounded-2xl mb-8">
    <p class="font-semibold mb-2">Veuillez corriger les erreurs :</p>
    <?php foreach($errors as $err): ?>
      <p class="text-sm">• <?= htmlspecialchars($err) ?></p>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <form method="POST" action="index.php?action=projet_store" class="bg-white rounded-3xl shadow-xl p-10 space-y-6">

    <div>
      <label class="block text-sm font-semibold mb-2">Titre du Projet <span class="text-red-500">*</span></label>
      <input type="text" name="titre" id="projet_titre"
             class="w-full px-5 py-4 border border-gray-300 rounded-2xl focus:outline-none focus:border-[#1D9E75]"
             value="<?= htmlspecialchars($_POST['titre'] ?? '') ?>"
             placeholder="Ex: EcoDeliver – Livraison écologique à Tunis">
    </div>

    <div>
      <label class="block text-sm font-semibold mb-2">Description <span class="text-red-500">*</span></label>
      <textarea name="description" id="projet_desc" rows="5"
                class="w-full px-5 py-4 border border-gray-300 rounded-2xl focus:outline-none focus:border-[#1D9E75]"
                placeholder="Décrivez votre projet, son impact, sa cible..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
    </div>

    <div>
      <label class="block text-sm font-semibold mb-2">Entreprise Associée <span class="text-red-500">*</span></label>
      <select name="id_fiche_entreprise"
              class="w-full px-5 py-4 border border-gray-300 rounded-2xl focus:outline-none focus:border-[#1D9E75] bg-white">
        <option value="">— Sélectionner une entreprise —</option>
        <?php foreach ($fiches as $f): ?>
          <option value="<?= $f['id'] ?>"
            <?= (($_POST['id_fiche_entreprise'] ?? '') == $f['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($f['nom']) ?> (<?= htmlspecialchars($f['categorie']) ?>)
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- AI Analysis Widget -->
    <div class="bg-[#F8FAFC] rounded-2xl p-6 border border-slate-200">
      <div class="flex justify-between items-center mb-4">
        <h3 class="font-bold text-slate-800 flex items-center gap-2">🤖 Assistant IA</h3>
        <button type="button" id="btnAnalyze" class="px-4 py-2 bg-indigo-100 text-indigo-700 rounded-lg text-sm font-semibold hover:bg-indigo-200 transition">✨ Analyser le Projet</button>
      </div>
      <div id="aiLoading" class="hidden text-sm text-slate-500 text-center py-4">Analyse en cours par l'IA...</div>
      <div id="aiResults" class="hidden grid grid-cols-3 gap-4">
        <div class="bg-white p-3 rounded-xl border border-slate-100 text-center">
          <p class="text-[10px] text-slate-500 uppercase font-bold mb-1">Score Viabilité</p>
          <p id="resViability" class="text-xl font-bold text-[#534AB7]">-</p>
        </div>
        <div class="bg-white p-3 rounded-xl border border-slate-100 text-center">
          <p class="text-[10px] text-slate-500 uppercase font-bold mb-1">Qualité Pitch</p>
          <p id="resPitch" class="text-xl font-bold text-emerald-600">-</p>
        </div>
        <div class="bg-white p-3 rounded-xl border border-slate-100 text-center">
          <p class="text-[10px] text-slate-500 uppercase font-bold mb-1">ODD Détectés</p>
          <p id="resSdg" class="text-xs font-bold text-slate-800 mt-2">-</p>
        </div>
      </div>
    </div>

    <div class="flex justify-between items-center pt-4">
      <a href="index.php?action=projet_list" class="text-gray-500 hover:text-gray-700 text-sm">← Annuler</a>
      <button type="submit"
              class="bg-[#1D9E75] hover:bg-[#15795A] text-white px-10 py-4 rounded-2xl font-semibold transition">
        Soumettre le Projet
      </button>
    </div>
  </form>
</div>

<script>
document.getElementById('btnAnalyze').addEventListener('click', function() {
    const titre = document.getElementById('projet_titre').value;
    const desc = document.getElementById('projet_desc').value;
    
    if(desc.length < 20) {
        alert('La description doit contenir au moins 20 caractères pour être analysée.');
        return;
    }

    document.getElementById('aiLoading').classList.remove('hidden');
    document.getElementById('aiResults').classList.add('hidden');

    const formData = new FormData();
    formData.append('titre', titre);
    formData.append('description', desc);

    fetch('index.php?action=analyze_projet', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('aiLoading').classList.add('hidden');
        document.getElementById('aiResults').classList.remove('hidden');
        
        document.getElementById('resViability').textContent = data.viability + '%';
        document.getElementById('resPitch').textContent = data.pitch_score + '/100';
        document.getElementById('resSdg').textContent = data.sdgs.length > 0 ? data.sdgs.join(', ') : 'Aucun';
    })
    .catch(err => {
        alert('Erreur lors de l\'analyse IA.');
        document.getElementById('aiLoading').classList.add('hidden');
    });
});
</script>
</body>
</html>
