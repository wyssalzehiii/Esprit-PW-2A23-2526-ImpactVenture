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
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; }
        .brand { font-family: 'Space Grotesk', sans-serif; }
        .card { transition: all 0.3s ease; }
        .card:hover { transform: translateY(-8px); box-shadow: 0 25px 50px -12px rgb(0 0 0 / 0.15); }
    </style>
</head>
<body class="min-h-screen">

<!-- Navbar avec Logo -->
<nav class="bg-white border-b shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 py-5 flex items-center justify-between">
        <a href="index.php" class="flex items-center gap-3">
            <img src="https://i.ibb.co.com/0jKzK7Z/impact-venture-logo.png" alt="ImpactVenture" class="h-10 w-10">
            <div>
                <span class="brand text-2xl font-bold tracking-tight">
                    <span class="text-[#1D9E75]">Impact</span>
                    <span class="text-[#534AB7]">Venture</span>
                </span>
            </div>
        </a>

        <div class="flex items-center gap-10 text-sm font-medium">
            <a href="index.php?action=fiche_list" class="hover:text-[#1D9E75]">Entreprises</a>
            <a href="index.php?action=projet_list" class="text-[#534AB7] font-semibold">Projets</a>
            <a href="#" class="hover:text-[#1D9E75]">Mentors</a>
            <a href="index.php?action=admin" class="text-amber-600 font-medium">Admin →</a>
        </div>

        <a href="index.php?action=projet_create" 
           class="bg-[#1D9E75] hover:bg-[#15795A] text-white px-6 py-3 rounded-2xl font-semibold text-sm transition">
            + Proposer un Projet
        </a>
    </div>
</nav>

<div class="max-w-7xl mx-auto px-6 py-12">
    <?php if ($msg === 'created'): ?>
        <div class="bg-emerald-100 border border-emerald-400 text-emerald-700 px-6 py-4 rounded-2xl mb-10 flex items-center gap-3">
            <span class="text-2xl">✅</span>
            <span class="font-semibold">Projet soumis avec succès ! Il est maintenant en attente d'évaluation.</span>
        </div>
    <?php endif; ?>

    <div class="mb-10">
        <h1 class="brand text-5xl font-bold text-gray-900 mb-3">Découvrir les Projets Innovants</h1>
        <p class="text-gray-600 text-lg">Explorez les projets soumis par les entrepreneurs tunisiens et leur entreprise associée.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php if (empty($projets)): ?>
            <div class="col-span-3 text-center py-20">
                <p class="text-2xl text-gray-400">Aucun projet pour le moment</p>
                <a href="index.php?action=projet_create" class="mt-6 inline-block bg-[#1D9E75] text-white px-8 py-4 rounded-2xl font-semibold">
                    Soumettre le premier projet
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($projets as $p): ?>
                <div class="bg-white rounded-3xl overflow-hidden shadow-sm card">
                    <div class="h-2 bg-gradient-to-r from-[#1D9E75] via-[#534AB7] to-[#EF9F27]"></div>
                    <div class="p-7">
                        <div class="flex justify-between items-start mb-5">
                            <span class="px-4 py-1.5 text-xs font-semibold rounded-full 
                                <?= $p['statut'] == 'accepté' ? 'bg-emerald-100 text-emerald-700' : 
                                   ($p['statut'] == 'en_evaluation' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600') ?>">
                                <?= ucfirst(htmlspecialchars($p['statut'])) ?>
                            </span>
                            <?php if (!empty($p['score_ia'])): ?>
                                <span class="font-mono text-sm font-bold text-[#1D9E75]">
                                    IA <?= round($p['score_ia']) ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <h3 class="font-bold text-xl leading-tight mb-4"><?= htmlspecialchars($p['titre']) ?></h3>
                        
                        <p class="text-gray-600 text-[15px] leading-relaxed line-clamp-4 mb-6">
                            <?= htmlspecialchars($p['description']) ?>
                        </p>

                        <div class="text-sm">
                            <span class="text-gray-500">Entreprise :</span><br>
                            <span class="font-semibold text-gray-800"><?= htmlspecialchars($p['entreprise_nom'] ?? 'Non associée') ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

</body>
</html>