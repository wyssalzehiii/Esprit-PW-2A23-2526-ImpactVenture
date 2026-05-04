<?php
$action = $_GET['action'] ?? '';
if (!isset($demandes)) $demandes = [];
$success = $_GET['success'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Mes Demandes - ImpactVenture</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        :root{--green:#1D9E75;--purple:#534AB7;--gold:#EF9F27;}
        body{font-family:'Inter',sans-serif;background:#F0F4F1;}
        h1,h2,h3,.brand{font-family:'Space Grotesk',sans-serif;}
    </style>
</head>
<body class="min-h-screen">

<nav class="bg-white border-b border-gray-100 sticky top-0 z-50 shadow-sm">
    <div class="max-w-7xl mx-auto px-6 flex items-center justify-between h-16">
        <a href="index.php?action=list" class="brand text-xl font-bold flex items-center gap-2">
            <span style="color:#1D9E75">Impact</span><span style="color:#534AB7">Venture</span>
        </a>
        <div class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-600">
            <a href="index.php?action=list" class="font-semibold" style="color:#1D9E75">Thèmes</a>
            <a href="#" class="hover:text-[#1D9E75]">Projets</a>
            <a href="#" class="hover:text-[#1D9E75]">Mentors</a>
            <a href="index.php?action=financement" class="hover:text-[#1D9E75] font-semibold">Financement</a>
            <a href="index.php?action=mes_demandes" class="font-semibold text-[#EF9F27] flex items-center gap-1">
                <i class="fas fa-file-invoice"></i> Mes Demandes
            </a>
            <a href="index.php?action=admin" class="font-semibold" style="color:#EF9F27">Admin ↗</a>
        </div>
        <div class="flex items-center gap-3">
            <a href="index.php?action=create" class="px-4 py-2 rounded-lg text-sm font-semibold bg-[#1D9E75] text-white">+ Proposer un thème</a>
            <img src="https://i.pravatar.cc/36?img=12" class="w-9 h-9 rounded-full border-2" style="border-color:#1D9E75" alt="profil"/>
        </div>
    </div>
</nav>

<div class="max-w-7xl mx-auto px-6 py-10">
    <h1 class="brand text-4xl font-bold mb-8">Mes Demandes de Financement</h1>

    <?php if (empty($demandes)): ?>
        <div class="bg-white rounded-3xl p-12 text-center">
            <p class="text-6xl mb-6">📭</p>
            <p class="text-xl font-medium">Vous n'avez encore envoyé aucune demande.</p>
            <a href="?action=financement" class="mt-6 inline-block bg-[#1D9E75] text-white px-8 py-3 rounded-2xl font-semibold">
                Voir les investisseurs
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 gap-6">
            <?php foreach ($demandes as $d):
                $statusColor = $d['statut'] === 'accepte' ? 'green' : ($d['statut'] === 'refuse' ? 'red' : 'yellow');
                ?>
                <div class="bg-white rounded-3xl p-8 flex flex-col md:flex-row gap-6 items-start justify-between">

                    <div class="flex-1">
                        <h3 class="font-bold text-2xl"><?= htmlspecialchars($d['investisseur_nom']) ?></h3>
                        <p class="text-[#1D9E75]"><?= htmlspecialchars($d['organisation']) ?></p>
                        <p class="text-gray-600 mt-4 text-sm leading-relaxed">
                            <?= nl2br(htmlspecialchars($d['message'])) ?>
                        </p>
                    </div>

                    <div class="flex flex-col items-end gap-3">
                        <!-- STATUS -->
                        <span class="px-5 py-2 text-sm font-semibold rounded-2xl bg-<?= $statusColor ?>-100 text-<?= $statusColor ?>-700">
                            <?= strtoupper($d['statut']) ?>
                        </span>

                        <!-- DATE -->
                        <p class="text-xs text-gray-500">
                            <?= date('d M Y', strtotime($d['date_demande'])) ?>
                        </p>

                        <!-- EDIT BUTTON (NEW) -->
                        <a href="index.php?action=edit_demande&id=<?= $d['id'] ?>"
                           class="mt-2 px-5 py-2 bg-blue-100 text-blue-700 rounded-xl text-sm font-semibold hover:bg-blue-600 hover:text-white transition flex items-center gap-2">
                            <i class="fas fa-edit"></i> Modifier
                        </a>

                        <!-- DELETE BUTTON -->
                        <a href="index.php?action=delete_demande&id=<?= $d['id'] ?>"
                           onclick="return confirm('Supprimer cette demande ? Cette action est irréversible.')"
                           class="px-5 py-2 bg-red-100 text-red-600 rounded-xl text-sm font-semibold hover:bg-red-600 hover:text-white transition flex items-center gap-2">
                            <i class="fas fa-trash"></i> Supprimer
                        </a>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>