<?php
if (!isset($investisseurs)) $investisseurs = [];

$totalInvestisseurs = is_array($investisseurs) ? count($investisseurs) : 0;
$withPhoto = 0;
$withLinkedin = 0;
$sectorCounts = [];

foreach ($investisseurs as $inv) {
    if (!empty($inv['photo'])) $withPhoto++;
    if (!empty($inv['linkedin'])) $withLinkedin++;
    $sector = trim($inv['secteur_focus'] ?? 'Autre');
    if ($sector === '') $sector = 'Autre';
    if (!isset($sectorCounts[$sector])) $sectorCounts[$sector] = 0;
    $sectorCounts[$sector]++;
}

arsort($sectorCounts);
$topSector = !empty($sectorCounts) ? array_key_first($sectorCounts) : '—';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>ImpactVenture – Admin Financement</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>

    <style>
        :root{
            --green:#1D9E75;
            --purple:#534AB7;
            --gold:#EF9F27;
            --bg:#F0F4F1;
        }
        body{
            font-family:'Inter',sans-serif;
            background:var(--bg);
        }
        h1,h2,h3,.brand{
            font-family:'Space Grotesk',sans-serif;
        }
        .glass-card{
            background:rgba(255,255,255,.78);
            backdrop-filter: blur(10px);
            border:1px solid rgba(255,255,255,.55);
            box-shadow:0 12px 40px rgba(20,35,20,.08);
        }
        .soft-shadow{
            box-shadow:0 10px 30px rgba(16,24,40,.08);
        }
        .btn-green{
            background:var(--green);
            color:#fff;
            transition:all .2s ease;
        }
        .btn-green:hover{
            background:#15795A;
            transform:translateY(-1px);
        }
        .btn-ghost{
            border:1px solid rgba(29,158,117,.25);
            color:var(--green);
            background:#fff;
            transition:all .2s ease;
        }
        .btn-ghost:hover{
            background:rgba(29,158,117,.07);
        }
        .tag{
            display:inline-flex;
            align-items:center;
            gap:.35rem;
            padding:.35rem .7rem;
            border-radius:999px;
            font-size:.72rem;
            font-weight:700;
            background:#E8F5EF;
            color:#0F6E56;
        }
        .search-input:focus{
            outline:none;
            border-color:var(--green);
            box-shadow:0 0 0 4px rgba(29,158,117,.12);
        }
        .inv-card{
            transition:transform .18s ease, box-shadow .18s ease;
        }
        .inv-card:hover{
            transform:translateY(-3px);
            box-shadow:0 18px 45px rgba(16,24,40,.10);
        }
        .muted{
            color:#6B7280;
        }
        .mini-stat{
            border:1px solid rgba(17,24,39,.06);
        }
    </style>
</head>
<body class="min-h-screen">

<nav class="bg-white/90 backdrop-blur border-b border-gray-100 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
        <a href="index.php?action=admin" class="brand text-xl font-bold flex items-center gap-2">
            <span style="color:#1D9E75">Impact</span><span style="color:#534AB7">Venture</span>
            <span class="text-xs font-semibold text-gray-400 ml-1">Admin</span>
        </a>

        <div class="flex items-center gap-3">
            <a href="index.php?action=admin_financement" class="px-4 py-2 rounded-xl text-sm font-semibold bg-[#1D9E75] text-white">
                <i class="fas fa-coins mr-2"></i>Financement
            </a>
            <a href="index.php?action=admin_demandes" class="px-4 py-2 rounded-xl text-sm font-semibold border border-gray-200 text-gray-700 hover:bg-gray-50">
                <i class="fas fa-inbox mr-2"></i>Demandes
            </a>
            <a href="index.php?action=admin_create" class="px-4 py-2 rounded-xl text-sm font-semibold border border-gray-200 text-gray-700 hover:bg-gray-50">
                <i class="fas fa-plus mr-2"></i>Thème
            </a>
        </div>
    </div>
</nav>

<div class="max-w-7xl mx-auto px-6 py-10">

    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6 mb-8">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.22em] text-[#1D9E75] mb-2">Module financement</p>
            <h1 class="brand text-4xl font-bold text-gray-900">Gestion des investisseurs</h1>
            <p class="text-gray-500 mt-2 max-w-2xl">
                Ajoutez, supprimez et suivez les investisseurs visibles dans la page financement du front office.
            </p>
        </div>

        <div class="flex flex-wrap gap-3">
            <a href="index.php?action=admin_add_investisseur"
               class="btn-green px-5 py-3 rounded-2xl font-semibold shadow-sm inline-flex items-center gap-2">
                <i class="fas fa-user-plus"></i>
                Ajouter un investisseur
            </a>
            <a href="index.php?action=admin_demandes"
               class="btn-ghost px-5 py-3 rounded-2xl font-semibold inline-flex items-center gap-2">
                <i class="fas fa-file-signature"></i>
                Voir les demandes
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
        <div class="glass-card mini-stat rounded-3xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm muted">Investisseurs</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1"><?= (int)$totalInvestisseurs ?></p>
                </div>
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center bg-[#E8F5EF] text-[#1D9E75]">
                    <i class="fas fa-handshake"></i>
                </div>
            </div>
        </div>

        <div class="glass-card mini-stat rounded-3xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm muted">Avec photo</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1"><?= (int)$withPhoto ?></p>
                </div>
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center bg-[#EEF2FF] text-[#534AB7]">
                    <i class="fas fa-image"></i>
                </div>
            </div>
        </div>

        <div class="glass-card mini-stat rounded-3xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm muted">Avec LinkedIn</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1"><?= (int)$withLinkedin ?></p>
                </div>
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center bg-[#FFF7E6] text-[#EF9F27]">
                    <i class="fab fa-linkedin"></i>
                </div>
            </div>
        </div>

        <div class="glass-card mini-stat rounded-3xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm muted">Secteur dominant</p>
                    <p class="text-lg font-bold text-gray-900 mt-2 leading-tight">
                        <?= htmlspecialchars($topSector) ?>
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center bg-[#F5EEFF] text-[#7C3AED]">
                    <i class="fas fa-layer-group"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search / filters -->
    <div class="glass-card rounded-3xl p-5 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center gap-4 justify-between">
            <div>
                <h2 class="text-lg font-bold text-gray-900">Liste des investisseurs</h2>
                <p class="text-sm muted"><?= $totalInvestisseurs ?> investisseur(s) enregistré(s)</p>
            </div>

            <div class="w-full lg:w-[360px]">
                <div class="relative">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input
                        id="searchInput"
                        type="text"
                        placeholder="Rechercher un investisseur..."
                        class="search-input w-full pl-11 pr-4 py-3 rounded-2xl border border-gray-200 bg-white text-sm"
                    />
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <?php if (empty($investisseurs)): ?>
        <div class="glass-card rounded-3xl p-12 text-center">
            <div class="w-20 h-20 mx-auto rounded-full bg-[#E8F5EF] text-[#1D9E75] flex items-center justify-center text-3xl mb-5">
                <i class="fas fa-user-slash"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Aucun investisseur pour le moment</h3>
            <p class="text-gray-500 mb-6">Ajoutez le premier investisseur pour alimenter la page financement du front office.</p>
            <a href="index.php?action=admin_add_investisseur"
               class="btn-green inline-flex items-center gap-2 px-6 py-3 rounded-2xl font-semibold">
                <i class="fas fa-plus"></i> Ajouter un investisseur
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6" id="investorGrid">
            <?php foreach ($investisseurs as $inv): ?>
                <?php
                $nom = $inv['nom'] ?? '';
                $organisation = $inv['organisation'] ?? '';
                $secteur = $inv['secteur_focus'] ?? 'Autre';
                $desc = $inv['description'] ?? '';
                $photo = trim($inv['photo'] ?? '');
                $linkedin = trim($inv['linkedin'] ?? '');
                $montantMin = isset($inv['montant_min']) ? number_format((float)$inv['montant_min'], 0, ',', ' ') : '0';
                $montantMax = isset($inv['montant_max']) ? number_format((float)$inv['montant_max'], 0, ',', ' ') : '0';
                $initials = strtoupper(mb_substr($nom, 0, 1, 'UTF-8'));
                ?>
                <div class="inv-card glass-card rounded-3xl p-6 flex flex-col gap-5 investor-card"
                     data-search="<?= htmlspecialchars(mb_strtolower($nom . ' ' . $organisation . ' ' . $secteur . ' ' . $desc, 'UTF-8')) ?>">
                    <div class="flex items-start gap-4">
                        <div class="w-16 h-16 rounded-2xl overflow-hidden flex-shrink-0 bg-gradient-to-br from-[#E8F5EF] to-[#EEF2FF] flex items-center justify-center text-[#1D9E75] font-bold text-xl">
                            <?php if (!empty($photo)): ?>
                                <img src="<?= htmlspecialchars($photo) ?>" alt="<?= htmlspecialchars($nom) ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <?= htmlspecialchars($initials ?: 'I') ?>
                            <?php endif; ?>
                        </div>

                        <div class="min-w-0 flex-1">
                            <h3 class="text-xl font-bold text-gray-900 leading-tight truncate">
                                <?= htmlspecialchars($nom) ?>
                            </h3>
                            <p class="text-sm font-semibold text-[#1D9E75] truncate">
                                <?= htmlspecialchars($organisation ?: 'Organisation non renseignée') ?>
                            </p>

                            <div class="mt-3 flex flex-wrap gap-2">
                                <span class="tag">
                                    <i class="fas fa-seedling"></i>
                                    <?= htmlspecialchars($secteur ?: 'Autre') ?>
                                </span>
                                <span class="tag">
                                    <i class="fas fa-coins"></i>
                                    <?= $montantMin ?> – <?= $montantMax ?> TND
                                </span>
                            </div>
                        </div>
                    </div>

                    <p class="text-sm text-gray-600 leading-relaxed line-clamp-4">
                        <?= nl2br(htmlspecialchars($desc ?: 'Aucune description fournie.')) ?>
                    </p>

                    <?php if (!empty($linkedin)): ?>
                        <a href="<?= htmlspecialchars($linkedin) ?>" target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-2 text-sm font-semibold text-[#534AB7] hover:underline">
                            <i class="fab fa-linkedin"></i>
                            Voir LinkedIn
                        </a>
                    <?php endif; ?>

                    <div class="flex gap-3 pt-2">
                        <a href="index.php?action=investisseur_profile&id=<?= (int)$inv['id'] ?>"
                           class="flex-1 text-center px-4 py-3 rounded-2xl border border-[#1D9E75] text-[#1D9E75] font-semibold hover:bg-[#1D9E75] hover:text-white transition">
                            <i class="fas fa-eye mr-2"></i>Profil
                        </a>

                        <a href="index.php?action=admin_delete_investisseur&id=<?= (int)$inv['id'] ?>"
                           onclick="return confirm('Supprimer cet investisseur ? Cette action est irréversible.')"
                           class="px-4 py-3 rounded-2xl bg-red-50 text-red-600 font-semibold hover:bg-red-600 hover:text-white transition inline-flex items-center justify-center">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
    const searchInput = document.getElementById('searchInput');
    const cards = Array.from(document.querySelectorAll('.investor-card'));

    searchInput?.addEventListener('input', function () {
        const q = this.value.toLowerCase().trim();
        cards.forEach(card => {
            const txt = card.getAttribute('data-search') || '';
            card.style.display = txt.includes(q) ? '' : 'none';
        });
    });
</script>

</body>
</html>