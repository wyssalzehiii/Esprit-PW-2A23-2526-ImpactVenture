<?php
// app/views/front/financement/profile.php
if (!isset($investisseur)) {
    header("Location: ?action=financement");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Profil Investisseur - ImpactVenture</title>
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

<!-- NAVBAR (same as list.php) -->
<nav class="bg-white border-b border-gray-100 sticky top-0 z-50 shadow-sm">
    <div class="max-w-7xl mx-auto px-6 flex items-center justify-between h-16">
        <a href="index.php?action=list" class="brand text-xl font-bold flex items-center gap-2">
            <span style="color:#1D9E75">Impact</span><span style="color:#534AB7">Venture</span>
        </a>
        <div class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-600">
            <a href="index.php?action=list" class="nav-link font-semibold" style="color:#1D9E75">Thèmes</a>
            <a href="#" class="hover:text-[#1D9E75]">Projets</a>
            <a href="#" class="hover:text-[#1D9E75]">Mentors</a>
            <a href="index.php?action=financement" class="hover:text-[#1D9E75] font-semibold flex items-center gap-1">Financement</a>
            <a href="index.php?action=mes_demandes" class="hover:text-[#1D9E75] font-semibold flex items-center gap-1">Mes Demandes</a>
            <a href="index.php?action=admin" class="font-semibold" style="color:#EF9F27">Admin ↗</a>
        </div>
        <div class="flex items-center gap-3">
            <a href="index.php?action=create" class="btn-primary px-4 py-2 rounded-lg text-sm font-semibold">+ Proposer un thème</a>
            <img src="https://i.pravatar.cc/36?img=12" class="w-9 h-9 rounded-full border-2" style="border-color:#1D9E75" alt="profil"/>
        </div>
    </div>
</nav>

<div class="max-w-4xl mx-auto px-6 py-10">
    <a href="?action=financement" class="inline-flex items-center gap-2 text-gray-600 hover:text-[#1D9E75] mb-8">
        ← Retour aux investisseurs
    </a>

    <div class="bg-white rounded-3xl shadow-sm overflow-hidden">
        <div class="h-48 bg-gradient-to-r from-[#1D9E75] to-[#534AB7]"></div>

        <div class="px-10 -mt-12 relative">
            <div class="flex flex-col md:flex-row gap-8">
                <div class="w-32 h-32 bg-white rounded-2xl shadow p-2 flex-shrink-0">
                    <div class="w-full h-full bg-gray-200 rounded-xl flex items-center justify-center text-6xl">👔</div>
                </div>

                <div class="pt-8 flex-1">
                    <h1 class="brand text-4xl font-bold"><?= htmlspecialchars($investisseur['nom']) ?></h1>
                    <p class="text-2xl text-[#1D9E75]"><?= htmlspecialchars($investisseur['organisation']) ?></p>
                    <span class="inline-block mt-4 px-5 py-2 bg-green-100 text-green-700 rounded-2xl text-sm font-medium">
                        <?= htmlspecialchars($investisseur['secteur_focus']) ?>
                    </span>
                </div>
            </div>

            <div class="mt-12 grid grid-cols-1 md:grid-cols-2 gap-10">
                <div>
                    <h3 class="text-lg font-semibold mb-4">À propos</h3>
                    <p class="text-gray-600 leading-relaxed"><?= nl2br(htmlspecialchars($investisseur['description'])) ?></p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Informations</h3>
                    <p><strong>Montant :</strong> <?= number_format($investisseur['montant_min'], 0) ?> – <?= number_format($investisseur['montant_max'], 0) ?> TND</p>
                    <p class="mt-3"><strong>LinkedIn :</strong> <a href="<?= htmlspecialchars($investisseur['linkedin']) ?>" target="_blank" class="text-blue-600">Voir le profil →</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>