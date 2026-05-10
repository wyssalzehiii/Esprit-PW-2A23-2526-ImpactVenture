<?php require_once __DIR__ . '/../../layouts/header.php'; ?>

<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 py-8 px-4">
    <div class="max-w-6xl mx-auto">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">Tableau de Bord Fondateur</h1>
            <p class="text-gray-600">Suivez l'évolution de votre projet</p>
        </div>

        <!-- Project Score Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 text-center">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4"><?= htmlspecialchars($projet['titre']) ?></h2>
            <div class="text-6xl font-bold text-blue-600 mb-2">
                <?= number_format($projet['score_ia'] ?? 0, 1) ?>
            </div>
            <div class="text-gray-500">Score Global / 100</div>
        </div>

        <!-- Score Evolution Chart -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <h3 class="text-xl font-semibold text-gray-700 mb-4">📈 Évolution des Scores</h3>
            <?php if (!empty($scoreHistory)): ?>
                <div class="space-y-3">
                    <?php foreach (array_reverse($scoreHistory) as $i => $snapshot): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                <span class="text-sm text-gray-600">
                                    <?= date('d M Y', strtotime($snapshot['created_at'])) ?>
                                </span>
                                <?php if (!empty($snapshot['change_reason'])): ?>
                                    <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">
                                        <?= htmlspecialchars($snapshot['change_reason']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="text-lg font-semibold text-gray-800">
                                <?= number_format($snapshot['final_score'], 1) ?>
                                <?php if ($snapshot['delta_pts'] > 0): ?>
                                    <span class="text-green-500 text-sm">+<?= $snapshot['delta_pts'] ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-gray-500 text-center py-4">Aucun historique de scores disponible</p>
            <?php endif; ?>
        </div>

        <div class="grid md:grid-cols-2 gap-8">
            <!-- Top Mentors -->
            <div class="bg-white rounded-2xl shadow-xl p-6">
                <h3 class="text-xl font-semibold text-gray-700 mb-4">👥 Vos Mentors Recommandés</h3>
                <?php if (!empty($topMentors)): ?>
                    <div class="space-y-3">
                        <?php foreach ($topMentors as $mentor): ?>
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <h4 class="font-semibold text-gray-800"><?= htmlspecialchars($mentor['nom']) ?></h4>
                                        <p class="text-sm text-gray-600"><?= htmlspecialchars($mentor['specialite']) ?></p>
                                    </div>
                                    <span class="text-sm bg-green-100 text-green-700 px-2 py-1 rounded">
                                        <?= round($mentor['score']) ?>%
                                    </span>
                                </div>
                                <?php if (!empty($mentor['email'])): ?>
                                    <button onclick="window.location.href='mailto:<?= htmlspecialchars($mentor['email']) ?>'" 
                                            class="mt-2 w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                                        📧 Contacter
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500 text-center py-4">Aucun mentor recommandé pour le moment</p>
                <?php endif; ?>
                
                <?php if ($interestedMentorsCount > 0): ?>
                    <div class="mt-4 p-3 bg-orange-50 border border-orange-200 rounded-lg">
                        <p class="text-orange-700 text-sm font-medium">
                            🔥 <?= $interestedMentorsCount ?> mentor(s) ont marqué votre projet comme intéressant
                        </p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Next Steps Recommendations -->
            <div class="bg-white rounded-2xl shadow-xl p-6">
                <h3 class="text-xl font-semibold text-gray-700 mb-4">🎯 Vos Prochaines Étapes</h3>
                <div class="space-y-3">
                    <?php foreach ($recommendations as $rec): ?>
                        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <p class="text-gray-800"><?= $rec ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 text-center">
            <a href="index.php?action=projet_edit&id=<?= $projet['id_projet'] ?>" 
               class="inline-block bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition mr-3">
                ✏️ Modifier le projet
            </a>
            <a href="index.php?action=projet_list" 
               class="inline-block bg-gray-400 text-white px-6 py-3 rounded-lg hover:bg-gray-500 transition">
                ← Retour à la liste
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
