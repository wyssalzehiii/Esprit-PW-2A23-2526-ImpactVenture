<?php 
$projet = $projet ?? []; 
$sdgs = $sdgs ?? [];
$mentors = $mentors ?? [];
$investors = $investors ?? [];
$sentimentData = $sentimentData ?? null;
$pitchScore = $pitchScore ?? 0;
$scoreGreen = $scoreGreen ?? 0;
$viabilityScore = $projet['viability_score'] ?? 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Évaluateur IA - <?= htmlspecialchars($projet['titre'] ?? '') ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    body{font-family:'Inter',sans-serif; background-color: #F8FAFC;} 
    .brand{font-family:'Space Grotesk',sans-serif;} 
    .card{background: #fff; border-radius: 1.5rem; padding: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03); transition: transform 0.2s;}
    .card:hover{transform: translateY(-2px);}
  </style>
</head>
<body class="min-h-screen text-slate-800 pb-20">

<nav class="bg-white border-b border-slate-200 sticky top-0 z-50">
  <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
    <a href="index.php?action=fiche_list" class="brand flex items-center gap-2">
      <img src="logo.png" alt="ImpactVenture Logo" style="height: 40px; width: auto; object-fit: contain;">
      <span class="text-2xl font-bold"><span class="text-[#1D9E75]">Impact</span><span class="text-[#534AB7]">Venture</span></span>
    </a>
    <div class="flex gap-6 text-sm font-medium">
      <a href="index.php?action=projet_list" class="text-slate-500 hover:text-[#1D9E75]">← Projets</a>
      <a href="index.php?action=chatbot&id_projet=<?= $projet['id_projet'] ?>" class="text-[#534AB7] hover:underline">💬 Chatbot Coach</a>
      <a href="index.php?action=business_plan&id_projet=<?= $projet['id_projet'] ?>&generate=1" class="text-[#1D9E75] hover:underline">📄 Générer PDF</a>
    </div>
  </div>
</nav>

<div class="max-w-7xl mx-auto pt-10 px-6">
  <!-- Header Projet -->
  <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
      <div class="flex items-center gap-3 mb-2">
        <span class="px-3 py-1 text-xs font-bold uppercase tracking-wider rounded-full bg-emerald-100 text-emerald-700">
          <?= htmlspecialchars($projet['statut'] ?? '') ?>
        </span>
        <h1 class="brand text-4xl font-bold text-slate-900"><?= htmlspecialchars($projet['titre'] ?? '') ?></h1>
      </div>
      <p class="text-slate-500 text-lg">Incubé par <span class="font-semibold text-[#1D9E75]"><?= htmlspecialchars($projet['entreprise_nom'] ?? 'N/A') ?></span></p>
    </div>
    <div class="flex gap-3">
        <a href="index.php?action=pitch_deck&id_projet=<?= $projet['id_projet'] ?>&generate=1" class="px-5 py-2.5 bg-slate-900 text-white font-medium rounded-xl hover:bg-slate-800 transition shadow-lg">🎥 Pitch Deck Auto</a>
        <a href="index.php?action=business_plan&id_projet=<?= $projet['id_projet'] ?>&generate=1" class="px-5 py-2.5 bg-[#534AB7] text-white font-medium rounded-xl hover:bg-indigo-700 transition shadow-lg">📄 Rapport Complet</a>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Colonne Principale -->
    <div class="lg:col-span-2 space-y-8">
        
        <!-- Score Overview -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="card flex flex-col items-center justify-center text-center">
                <h3 class="text-slate-500 font-semibold mb-4 uppercase tracking-wider text-sm">Score Green</h3>
                <div class="relative w-32 h-32">
                    <canvas id="greenChart"></canvas>
                    <div class="absolute inset-0 flex items-center justify-center flex-col">
                        <span class="text-3xl font-bold text-[#1D9E75]"><?= $scoreGreen ?>%</span>
                    </div>
                </div>
                <p class="mt-4 text-sm text-slate-600">Impact environnemental estimé</p>
            </div>
            
            <div class="card flex flex-col items-center justify-center text-center">
                <h3 class="text-slate-500 font-semibold mb-4 uppercase tracking-wider text-sm">Score Viabilité</h3>
                <div class="relative w-32 h-32">
                    <canvas id="viabilityChart"></canvas>
                    <div class="absolute inset-0 flex items-center justify-center flex-col">
                        <span class="text-3xl font-bold text-[#534AB7]"><?= $viabilityScore ?>%</span>
                    </div>
                </div>
                <a href="index.php?action=viability&id_projet=<?= $projet['id_projet'] ?>" class="mt-4 text-sm text-[#534AB7] font-medium hover:underline">Recalculer les données →</a>
            </div>
        </div>

        <!-- Description & NLP -->
        <div class="card">
            <h3 class="text-xl font-bold brand mb-4 border-b pb-3 border-slate-100 flex justify-between items-center">
                Analyse du Pitch (NLP)
                <span class="text-lg font-bold <?= $pitchScore >= 70 ? 'text-green-500' : 'text-amber-500' ?>"><?= $pitchScore ?>/100</span>
            </h3>
            <p class="text-slate-700 leading-relaxed mb-6 italic border-l-4 border-slate-200 pl-4">"<?= nl2br(htmlspecialchars($projet['description'] ?? '')) ?>"</p>
            
            <?php if($sentimentData): ?>
            <div class="grid grid-cols-3 gap-4 mb-4">
                <div class="bg-slate-50 p-4 rounded-xl text-center">
                    <p class="text-xs text-slate-500 mb-1">Clarté</p>
                    <p class="font-bold text-slate-800 text-lg"><?= $sentimentData['clarity'] ?? 0 ?>%</p>
                </div>
                <div class="bg-slate-50 p-4 rounded-xl text-center">
                    <p class="text-xs text-slate-500 mb-1">Confiance</p>
                    <p class="font-bold text-slate-800 text-lg"><?= $sentimentData['confidence'] ?? 0 ?>%</p>
                </div>
                <div class="bg-slate-50 p-4 rounded-xl text-center">
                    <p class="text-xs text-slate-500 mb-1">Professionnalisme</p>
                    <p class="font-bold text-slate-800 text-lg"><?= $sentimentData['professionalism'] ?? 0 ?>%</p>
                </div>
            </div>
            <div class="bg-indigo-50 text-indigo-800 p-4 rounded-xl text-sm">
                <strong>💡 Conseil IA :</strong> <?= htmlspecialchars($sentimentData['advice'] ?? 'Améliorez votre description pour un meilleur impact.') ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Matching -->
        <div class="card">
            <h3 class="text-xl font-bold brand mb-5 border-b pb-3 border-slate-100">🤝 Matching IA: Mentors & Investisseurs</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Mentors -->
                <div>
                    <h4 class="font-semibold text-slate-600 mb-3 text-sm uppercase">Top Mentors</h4>
                    <?php if(empty($mentors)): ?>
                        <p class="text-sm text-slate-400">Aucun mentor trouvé.</p>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php foreach(array_slice($mentors, 0, 3) as $m): ?>
                            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <div>
                                    <p class="font-semibold text-sm"><?= htmlspecialchars($m['nom']) ?></p>
                                    <p class="text-xs text-slate-500"><?= htmlspecialchars($m['specialite']) ?></p>
                                </div>
                                <span class="text-xs font-bold text-emerald-600 bg-emerald-100 px-2 py-1 rounded-md"><?= $m['score'] ?>%</span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Investisseurs -->
                <div>
                    <h4 class="font-semibold text-slate-600 mb-3 text-sm uppercase">Top Investisseurs</h4>
                    <?php if(empty($investors)): ?>
                        <p class="text-sm text-slate-400">Aucun investisseur trouvé.</p>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php foreach(array_slice($investors, 0, 3) as $inv): ?>
                            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <div>
                                    <p class="font-semibold text-sm"><?= htmlspecialchars($inv['nom']) ?></p>
                                    <p class="text-xs text-slate-500"><?= htmlspecialchars($inv['type_investissement']) ?></p>
                                </div>
                                <span class="text-xs font-bold text-[#534AB7] bg-indigo-100 px-2 py-1 rounded-md"><?= $inv['score'] ?>%</span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="mt-4 text-center">
                <a href="index.php?action=matching&id_projet=<?= $projet['id_projet'] ?>" class="text-sm text-[#1D9E75] font-semibold hover:underline">Voir tous les résultats de matching →</a>
            </div>
        </div>

    </div>

    <!-- Colonne Latérale -->
    <div class="space-y-8">
        
        <!-- SDGs Detected -->
        <div class="card bg-gradient-to-br from-slate-900 to-slate-800 text-white">
            <h3 class="text-lg font-bold brand mb-4 text-slate-100">🌍 ODD Détectés (ONU)</h3>
            <?php if(empty($sdgs)): ?>
                <p class="text-sm text-slate-400">L'IA n'a pas détecté d'Objectifs de Développement Durable clairs. Précisez l'impact social ou environnemental.</p>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach($sdgs as $sdg): ?>
                    <div class="flex items-center gap-4 p-3 bg-white/10 rounded-xl backdrop-blur-sm">
                        <div class="w-12 h-12 flex items-center justify-center rounded-lg text-2xl" style="background-color: <?= $sdg['couleur'] ?>;">
                            <?= $sdg['icone'] ?>
                        </div>
                        <div>
                            <p class="font-bold text-sm">ODD <?= $sdg['numero'] ?></p>
                            <p class="text-xs text-slate-300"><?= htmlspecialchars($sdg['nom']) ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Badges Entreprise -->
        <div class="card">
            <h3 class="text-lg font-bold brand mb-4 border-b pb-3 border-slate-100">🏆 Badges de l'Entreprise</h3>
            <p class="text-sm text-slate-500 mb-4">Ces badges sont gagnés grâce aux performances IA de la plateforme.</p>
            <a href="index.php?action=badges&id_fiche=<?= $projet['id_fiche_entreprise'] ?>" class="block w-full text-center py-3 bg-slate-100 text-slate-700 rounded-xl font-semibold hover:bg-slate-200 transition text-sm">
                Voir les Badges Obtenus
            </a>
        </div>

        <!-- Actions Rapides -->
        <div class="card bg-[#1D9E75]/10 border border-[#1D9E75]/20">
            <h3 class="text-lg font-bold text-[#1D9E75] brand mb-4">Actions Rapides</h3>
            <div class="space-y-2">
                <a href="index.php?action=projet_edit&id=<?= $projet['id_projet'] ?>" class="block w-full py-2.5 px-4 bg-white text-slate-700 rounded-lg text-sm font-medium hover:shadow-sm transition">✏️ Modifier le projet</a>
                <a href="index.php?action=projet_delete&id=<?= $projet['id_projet'] ?>" onclick="return confirm('Êtes-vous sûr ?')" class="block w-full py-2.5 px-4 bg-white text-red-600 rounded-lg text-sm font-medium hover:shadow-sm transition">🗑️ Supprimer</a>
            </div>
        </div>

    </div>

  </div>
</div>

<script>
// Graphiques Jauges (Doughnut)
const createGauge = (ctxId, value, color) => {
    new Chart(document.getElementById(ctxId), {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [value, 100 - value],
                backgroundColor: [color, '#e2e8f0'],
                borderWidth: 0,
                cutout: '80%',
                circumference: 270,
                rotation: 225,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { tooltip: { enabled: false } },
            animation: { animateScale: true, animateRotate: true }
        }
    });
};

createGauge('greenChart', <?= $scoreGreen ?>, '#1D9E75');
createGauge('viabilityChart', <?= $viabilityScore ?>, '#534AB7');
</script>

</body>
</html>
