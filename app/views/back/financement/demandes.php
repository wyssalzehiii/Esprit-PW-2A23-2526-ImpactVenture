<?php if (!isset($demandes)) $demandes = []; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Gestion des Demandes de Financement</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
        .brand { font-family: 'Space Grotesk', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

<div class="max-w-7xl mx-auto px-6 py-10">

    <!-- Header -->
    <div class="flex justify-between items-center mb-10">
        <div>
            <h1 class="brand text-4xl font-bold text-gray-900">Gestion des Demandes de Financement</h1>
            <p class="text-gray-600 mt-2">Suivez et gérez toutes les demandes envoyées aux investisseurs</p>
        </div>
        <a href="?action=admin"
           class="px-5 py-3 bg-gray-800 text-white rounded-2xl hover:bg-gray-700 transition flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Retour au Dashboard Admin
        </a>
    </div>

    <?php if (empty($demandes)): ?>
        <div class="bg-white rounded-3xl p-16 text-center">
            <p class="text-6xl mb-6">📭</p>
            <h3 class="text-2xl font-semibold text-gray-800">Aucune demande pour le moment</h3>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-3xl shadow-sm overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-100">
                <tr>
                    <th class="px-8 py-5 text-left font-semibold text-gray-700">Investisseur</th>
                    <th class="px-8 py-5 text-left font-semibold text-gray-700">Message / Pitch</th>
                    <th class="px-8 py-5 text-left font-semibold text-gray-700">Date</th>
                    <th class="px-8 py-5 text-center font-semibold text-gray-700">Statut</th>
                    <th class="px-8 py-5 text-center font-semibold text-gray-700">Actions</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                <?php foreach ($demandes as $d):
                    $statusClass = $d['statut'] === 'accepte' ? 'bg-green-100 text-green-700' :
                        ($d['statut'] === 'refuse' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700');
                    ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-8 py-6">
                            <div class="font-semibold"><?= htmlspecialchars($d['investisseur_nom']) ?></div>
                            <div class="text-sm text-gray-500"><?= htmlspecialchars($d['organisation'] ?? '') ?></div>
                        </td>
                        <td class="px-8 py-6 max-w-md">
                            <p class="text-gray-700 line-clamp-2"><?= htmlspecialchars($d['message']) ?></p>
                        </td>
                        <td class="px-8 py-6 text-sm text-gray-500">
                            <?= date('d M Y à H:i', strtotime($d['date_demande'])) ?>
                        </td>
                        <td class="px-8 py-6 text-center">
                                <span class="px-5 py-2 text-sm font-semibold rounded-2xl <?= $statusClass ?>">
                                    <?= strtoupper($d['statut']) ?>
                                </span>
                        </td>
                        <td class="px-8 py-6 text-center">
                            <div class="flex gap-3 justify-center">
                                <?php if ($d['statut'] === 'en_attente'): ?>
                                    <a href="?action=update_demande_status&id=<?= $d['id'] ?>&status=accepte"
                                       class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-2xl transition">
                                        Accepter
                                    </a>
                                    <a href="?action=update_demande_status&id=<?= $d['id'] ?>&status=refuse"
                                       class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-2xl transition">
                                        Refuser
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</div>

</body>
</html>