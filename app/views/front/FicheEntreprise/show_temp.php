<?php
$entreprise = $entreprise ?? [];
$entreprise_odds = $entreprise_odds ?? [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entreprise Analyse - ImpactVenture</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
    <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 py-8 px-4">
    <div class="max-w-6xl mx-auto">
        
        <!-- Hero Section -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
            <div class="flex items-center gap-6 mb-6">
                <?php if(!empty($entreprise['logo'])): ?>
                    <img src="<?= htmlspecialchars($entreprise['logo']) ?>" alt="Logo" class="h-24 w-auto object-contain rounded-2xl shadow-lg">
                <?php endif; ?>
                <div class="flex-1">
                    <h1 class="text-4xl font-bold text-gray-800 mb-2"><?= htmlspecialchars($entreprise['nom']) ?></h1>
                    <p class="text-gray-600"><?= htmlspecialchars($entreprise['description']) ?></p>
                </div>
            </div>
        </div>

        <!-- Scores Dashboard -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-xl p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center gap-2">
                    <span>🚀</span> Innovation
                </h3>
                <div class="relative w-full h-4 bg-gray-200 rounded-full overflow-hidden">
                    <div class="absolute top-0 left-0 h-full bg-gradient-to-r from-blue-500 to-blue-600 rounded-full transition-all duration-1000" 
                         style="width: <?= min(100, max(0, ($entreprise['avg_score'] ?? 0))) ?>%"></div>
                </div>
                <div class="text-center mt-2">
                    <span class="text-2xl font-bold text-blue-600"><?= round($entreprise['avg_score'] ?? 0) ?>/100</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center gap-2">
                    <span>🌱</span> Durabilité
                </h3>
                <div class="relative w-full h-4 bg-gray-200 rounded-full overflow-hidden">
                    <div class="absolute top-0 left-0 h-full bg-gradient-to-r from-green-500 to-green-600 rounded-full transition-all duration-1000" 
                         style="width: <?= min(100, max(0, ($entreprise['avg_green_score'] ?? 0))) ?>%"></div>
                </div>
                <div class="text-center mt-2">
                    <span class="text-2xl font-bold text-green-600"><?= round($entreprise['avg_green_score'] ?? 0) ?>/100</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center gap-2">
                    <span>📈</span> Marché
                </h3>
                <div class="relative w-full h-4 bg-gray-200 rounded-full overflow-hidden">
                    <div class="absolute top-0 left-0 h-full bg-gradient-to-r from-purple-500 to-purple-600 rounded-full transition-all duration-1000" 
                         style="width: <?= min(100, max(0, ($entreprise['avg_score'] ?? 0))) ?>%"></div>
                </div>
                <div class="text-center mt-2">
                    <span class="text-2xl font-bold text-purple-600"><?= round($entreprise['avg_score'] ?? 0) ?>/100</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center gap-2">
                    <span>⚠️</span> Risque
                </h3>
                <div class="relative w-full h-4 bg-gray-200 rounded-full overflow-hidden">
                    <div class="absolute top-0 left-0 h-full bg-gradient-to-r from-red-500 to-red-600 rounded-full transition-all duration-1000" 
                         style="width: <?= min(100, max(0, (100 - ($entreprise['avg_score'] ?? 0))) ?>%"></div>
                </div>
                <div class="text-center mt-2">
                    <span class="text-2xl font-bold text-red-600"><?= round(100 - ($entreprise['avg_score'] ?? 0)) ?>/100</span>
                </div>
            </div>
        </div>

        <!-- SDGs Detected -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
            <h3 class="text-xl font-bold text-gray-700 mb-6 flex items-center gap-2">
                <span>🌍</span> ODD Détectés
            </h3>
            <?php if(!empty($entreprise_odds)): ?>
                <div class="flex flex-wrap gap-3">
                    <?php foreach ($entreprise_odds as $odd): ?>
                        <div class="flex items-center gap-2 bg-gray-50 rounded-lg p-3">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold" style="background: <?= $odd['color'] ?>">
                                <?= $odd['numero'] ?>
                            </div>
                            <div>
                                <strong class="text-gray-800"><?= $odd['titre'] ?></strong>
                                <p class="text-sm text-gray-600 mt-1"><?= $odd['description'] ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-gray-500 text-center py-8">Aucun ODD détecté pour cette entreprise.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
