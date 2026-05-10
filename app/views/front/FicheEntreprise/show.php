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
                    <div class="flex items-center gap-3">
                        <span class="px-4 py-2 bg-gradient-to-r from-[#1D9E75] to-[#534AB7] text-white rounded-full text-sm font-semibold">
                            <?= htmlspecialchars($entreprise['categorie']) ?>
                        </span>
                        <span class="text-gray-500 text-sm">Incubé par ImpactVenture</span>
                    </div>
                </div>
            </div>
            <p class="text-gray-600 leading-relaxed mb-6"><?= nl2br(htmlspecialchars($entreprise['description'])) ?></p>
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
                    <span>�</span> Marché
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
                         style="width: <?= min(100, max(0, (100 - ($entreprise['avg_score'] ?? 0)))) ?>%"></div>
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

        <!-- Badges Earned -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
            <h3 class="text-xl font-bold text-gray-700 mb-6 flex items-center gap-2">
                <span>🏆</span> Badges Obtenus
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <div class="text-center p-4 bg-green-50 rounded-xl border-2 border-green-200">
                    <div class="text-3xl mb-2">🌱</div>
                    <div class="font-semibold text-green-700">Éco Warrior</div>
                    <div class="text-xs text-green-600">Score vert >80%</div>
                </div>
                <div class="text-center p-4 bg-blue-50 rounded-xl border-2 border-blue-200">
                    <div class="text-3xl mb-2">🚀</div>
                    <div class="font-semibold text-blue-700">Innovateur</div>
                    <div class="text-xs text-blue-600">Score innovation >70%</div>
                </div>
                <div class="text-center p-4 bg-purple-50 rounded-xl border-2 border-purple-200">
                    <div class="text-3xl mb-2">🏆</div>
                    <div class="font-semibold text-purple-700">Impact Leader</div>
                    <div class="text-xs text-purple-600">Top 10% des scores</div>
                </div>
                <div class="text-center p-4 bg-gray-100 rounded-xl border-2 border-gray-300">
                    <div class="text-3xl mb-2">🔒</div>
                    <div class="font-semibold text-gray-500">Verrouillé</div>
                    <div class="text-xs text-gray-400">Score final >90%</div>
                </div>
            </div>
        </div>

        <!-- Associated Projects -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
            <h3 class="text-xl font-bold text-gray-700 mb-6 flex items-center gap-2">
                <span>📋</span> Projets Associés
            </h3>
            <?php if(!empty($projects)): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach($projects as $project): ?>
                        <div class="border border-gray-200 rounded-xl p-5 hover:shadow-lg transition-all duration-300">
                            <div class="flex justify-between items-start mb-3">
                                <h4 class="font-semibold text-gray-800"><?= htmlspecialchars($project['titre']) ?></h4>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    <?= $project['statut'] == 'accepté' ? 'bg-green-100 text-green-700' :
                                        ($project['statut'] == 'en_evaluation' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600') ?>">
                                    <?= ucfirst(htmlspecialchars($project['statut'])) ?>
                                </span>
                            </div>
                            <p class="text-gray-600 text-sm mb-3 line-clamp-2"><?= htmlspecialchars(substr($project['description'], 0, 120)) ?>...</p>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500">Score IA:</span>
                                <span class="font-bold text-lg <?= $project['final_score'] >= 70 ? 'text-green-600' : ($project['final_score'] >= 50 ? 'text-amber-600' : 'text-red-600') ?>">
                                    <?= round($project['final_score']) ?>/100
                                </span>
                            </div>
                            <?php if($project['change_reason']): ?>
                                <div class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">
                                    <?= htmlspecialchars($project['change_reason']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-gray-500 text-center">Aucun projet associé à cette entreprise.</p>
            <?php endif; ?>
        </div>

        <!-- Matched Mentors -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
            <h3 class="text-xl font-bold text-gray-700 mb-6 flex items-center gap-2">
                <span>👥</span> Mentors Matchés
            </h3>
            <?php if(!empty($mentors)): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach($mentors as $mentor): ?>
                        <div class="border border-gray-200 rounded-xl p-5 hover:shadow-lg transition-all duration-300">
                            <div class="flex justify-between items-start mb-3">
                                <h4 class="font-semibold text-gray-800"><?= htmlspecialchars($mentor['nom']) ?></h4>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                    <?= round($mentor['score']) ?>% Match
                                </span>
                            </div>
                            <p class="text-gray-600 text-sm mb-3"><?= htmlspecialchars($mentor['specialite']) ?></p>
                            <div class="flex gap-2">
                                <a href="mailto:<?= htmlspecialchars($mentor['email']) ?>" 
                                   class="flex-1 text-center py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                                    📧 Contacter
                                </a>
                                <a href="index.php?action=chatbot&id_projet=<?= $projects[0]['id_projet'] ?>" 
                                   class="flex-1 text-center py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium">
                                    🤖 Coach IA
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-gray-500 text-center">Aucun mentor matché pour cette entreprise.</p>
            <?php endif; ?>
        </div>

        <!-- Score Evolution Chart -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <h3 class="text-xl font-bold text-gray-700 mb-6 flex items-center gap-2">
                <span>📈</span> Évolution des Scores
            </h3>
            <div class="h-64">
                <canvas id="scoreChart"></canvas>
            </div>
        </div>

        <!-- Navigation -->
        <div class="text-center mt-8">
            <a href="index.php?action=fiche_list" 
               class="inline-flex items-center gap-2 bg-gray-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-gray-700 transition">
                ← Retour à la liste
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Animate score bars on page load
document.addEventListener('DOMContentLoaded', function() {
    const scoreBars = document.querySelectorAll('.bg-gradient-to-r');
    scoreBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.width = width;
        }, 100);
    });

    // Score evolution chart
    <?php if(!empty($projects)): ?>
        const ctx = document.getElementById('scoreChart').getContext('2d');
        
        // Prepare score data properly
        const rawScoreData = <?= json_encode(array_map(function($p) {
            $scoreDate = $p['date_soumission'] ?? '';
            $finalScore = (float)($p['score_ia'] ?? 0);
            return [
                $scoreDate ? new Date($scoreDate).toISOString().split('T')[0] : '',
                $finalScore
            ];
        }, $projects), JSON_UNESCAPED_UNICODE) ?>;
        
        // Filter out empty data and sort by date
        const scoreData = rawScoreData.filter(d => d[0] && d[1] !== null && d[1] > 0).sort((a, b) => new Date(a[0]) - new Date(b[0]));
        
        // Debug: log data to console
        console.log('Raw score data:', rawScoreData);
        console.log('Filtered score data:', scoreData);
        console.log('Number of projects:', count($projects));
        
        <?php if(!empty($scoreData)): ?>
            <div class="text-center py-8">
                <p class="text-gray-500">Aucune donnée de score disponible pour cette entreprise.</p>
            </div>
        <?php else: ?>
            new Chart(ctx, {
            type: 'line',
            data: {
                labels: scoreData.map(d => d[0]),
                datasets: [{
                    label: 'Score Final',
                    data: scoreData.map(d => d[1]),
                    borderColor: '#1D9E75',
                    backgroundColor: 'rgba(29, 158, 117, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });
    <?php else: ?>
        // Show message when no data
        const ctx = document.getElementById('scoreChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Aucune donnée'],
                datasets: [{
                    label: 'Score Final',
                    data: [0],
                    borderColor: '#1D9E75',
                    backgroundColor: 'rgba(29, 158, 117, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });
    <?php endif; ?>
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
