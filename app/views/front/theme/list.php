<?php
$msgs = ['created'=>'Thème soumis avec succès !','updated'=>'Thème mis à jour.','deleted'=>'Thème supprimé.'];
$msg  = isset($_GET['msg']) ? ($msgs[$_GET['msg']] ?? '') : '';
$action = $_GET['action'] ?? 'list';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>ImpactVenture – Thèmes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>
    <style>
        :root{--green:#1D9E75;--purple:#534AB7;--gold:#EF9F27;}
        body{font-family:'Inter',sans-serif;background:#F0F4F1;}
        h1,h2,h3,.brand{font-family:'Space Grotesk',sans-serif;}
        .card-hover{transition:transform .2s,box-shadow .2s;}
        .card-hover:hover{transform:translateY(-4px);box-shadow:0 12px 28px rgba(0,0,0,.1);}
        .btn-primary{background:#1D9E75;color:#fff;transition:background .2s;}
        .btn-primary:hover{background:#15795A;}
        .btn-outline{border:1.5px solid #1D9E75;color:#1D9E75;transition:all .2s;}
        .btn-outline:hover{background:#1D9E75;color:#fff;}
        .search-input:focus{outline:none;border-color:#1D9E75;box-shadow:0 0 0 3px rgba(29,158,117,.15);}
        .score-ring{stroke-dasharray:251;stroke-linecap:round;transition:stroke-dashoffset .8s ease;}
        @keyframes fadeUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:none}}
        .fade-up{animation:fadeUp .5s ease both;}
        .fade-up:nth-child(2){animation-delay:.07s}.fade-up:nth-child(3){animation-delay:.14s}
        .fade-up:nth-child(4){animation-delay:.21s}.fade-up:nth-child(5){animation-delay:.28s}
        .filter-btn{transition:all .2s;} .filter-btn.active{background:#1D9E75;color:#fff;border-color:#1D9E75;}
        .cat-tech{background:#EDE9FE;color:#4C1D95;} .cat-digital{background:#D1FAE5;color:#065F46;}
        .cat-energie{background:#DBEAFE;color:#1E40AF;} .cat-agri{background:#D1FAE5;color:#065F46;}
        .cat-eco{background:#FEF3C7;color:#92400E;} .cat-mob{background:#FDE8D8;color:#7C2D12;}
        .cat-entre{background:#EDE9FE;color:#4C1D95;} .cat-autre{background:#F3F4F6;color:#374151;}
    </style>
</head>
<body class="min-h-screen">

<!-- NAVBAR -->
<nav class="bg-white border-b border-gray-100 sticky top-0 z-50 shadow-sm">
    <div class="max-w-7xl mx-auto px-6 flex items-center justify-between h-16">
        <a href="index.php?action=list" class="brand text-xl font-bold flex items-center gap-2">
            <span style="color:#1D9E75">Impact</span><span style="color:#534AB7">Venture</span>
        </a>

        <div class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-600">
            <a href="index.php?action=list"
               class="font-semibold flex items-center gap-1
               <?= $action === 'list' ? 'text-[#1D9E75]' : 'hover:text-[#1D9E75]' ?>">
                <i class="fas fa-layer-group"></i> Thèmes
            </a>

            <a href="#" class="hover:text-[#1D9E75]">Projets</a>
            <a href="#" class="hover:text-[#1D9E75]">Mentors</a>

            <a href="index.php?action=financement"
               class="font-semibold flex items-center gap-1
               <?= in_array($action, ['financement','investisseur_profile']) ? 'text-[#1D9E75]' : 'hover:text-[#1D9E75]' ?>">
                <i class="fas fa-handshake"></i> Financement
            </a>

            <a href="index.php?action=mes_demandes"
               class="font-semibold flex items-center gap-1
               <?= $action === 'mes_demandes' ? 'text-[#EF9F27]' : 'hover:text-[#1D9E75]' ?>">
                <i class="fas fa-file-invoice"></i> Mes Demandes
            </a>

            <a href="index.php?action=espace_investisseur"
               class="font-semibold flex items-center gap-1
               <?= $action === 'espace_investisseur' ? 'text-[#534AB7]' : 'hover:text-[#534AB7]' ?>">
                <i class="fas fa-briefcase"></i> Espace Investisseur
            </a>

            <a href="index.php?action=admin" class="font-semibold" style="color:#EF9F27">Admin ↗</a>
        </div>

        <div class="flex items-center gap-3">
            <a href="index.php?action=create" class="btn-primary px-4 py-2 rounded-lg text-sm font-semibold">
                + Proposer un thème
            </a>
            <img src="https://i.pravatar.cc/36?img=12"
                 class="w-9 h-9 rounded-full border-2" style="border-color:#1D9E75" alt="profil"/>
        </div>
    </div>
</nav>

<?php if ($msg): ?>
    <div class="max-w-7xl mx-auto px-6 pt-4">
        <div class="flex items-center gap-3 bg-green-50 border border-green-300 text-green-800 rounded-xl px-4 py-3 text-sm font-semibold">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M5 13l4 4L19 7"/>
            </svg>
            <?= htmlspecialchars($msg) ?>
        </div>
    </div>
<?php endif; ?>

<!-- HERO -->
<section class="bg-white border-b border-gray-100 py-12">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest mb-1" style="color:#1D9E75">Smart Theme Engine</p>
                <h1 class="brand text-3xl font-bold text-gray-900 mb-2">Explorez les thèmes disponibles</h1>
                <p class="text-gray-500 text-sm max-w-lg">Trouvez le thème qui correspond à votre projet. Chaque thème possède un score d'impact Green calculé par notre IA.</p>
            </div>
            <div class="flex gap-6">
                <div class="text-center">
                    <p class="brand text-2xl font-bold" style="color:#1D9E75"><?= $total ?></p>
                    <p class="text-xs text-gray-500">Thèmes actifs</p>
                </div>
                <div class="text-center">
                    <p class="brand text-2xl font-bold" style="color:#534AB7"><?= $totalP ?></p>
                    <p class="text-xs text-gray-500">Projets soumis</p>
                </div>
                <div class="text-center">
                    <p class="brand text-2xl font-bold" style="color:#EF9F27"><?= $avg ?>%</p>
                    <p class="text-xs text-gray-500">Score Green moy.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FILTERS -->
<section class="max-w-7xl mx-auto px-6 py-6">
    <div class="flex flex-col md:flex-row gap-4 items-start md:items-center justify-between">
        <div class="relative w-full md:w-80">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                 fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
            </svg>
            <input type="text" id="searchInput" placeholder="Rechercher un thème…"
                   class="search-input w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm"/>
        </div>
        <div class="flex flex-wrap gap-2">
            <button onclick="filterCat('all',this)"
                    class="filter-btn active px-4 py-1.5 rounded-full text-sm font-semibold border border-gray-200">Tous</button>
            <button onclick="filterCat('tech',this)"
                    class="filter-btn px-4 py-1.5 rounded-full text-sm font-semibold border border-gray-200">Tech & IA</button>
            <button onclick="filterCat('energie renouvelable',this)"
                    class="filter-btn px-4 py-1.5 rounded-full text-sm font-semibold border border-gray-200">Énergie</button>
            <button onclick="filterCat('agriculture durable',this)"
                    class="filter-btn px-4 py-1.5 rounded-full text-sm font-semibold border border-gray-200">AgriTech</button>
            <button onclick="filterCat('digital',this)"
                    class="filter-btn px-4 py-1.5 rounded-full text-sm font-semibold border border-gray-200">Digital</button>
        </div>
    </div>
</section>

<!-- THEME CARDS -->
<section class="max-w-7xl mx-auto px-6 pb-16" id="themeGrid">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php
        $catMap = [
                'tech'                 => ['cls'=>'cat-tech',   'bar'=>'#534AB7'],
                'digital'              => ['cls'=>'cat-digital', 'bar'=>'#1D9E75'],
                'energie renouvelable' => ['cls'=>'cat-energie', 'bar'=>'#1D9E75'],
                'agriculture durable'  => ['cls'=>'cat-agri',   'bar'=>'#10B981'],
                'economie circulaire'  => ['cls'=>'cat-eco',    'bar'=>'#EF9F27'],
                'mobilite verte'       => ['cls'=>'cat-mob',    'bar'=>'#EF9F27'],
                'entrepreneuriat'      => ['cls'=>'cat-entre',  'bar'=>'#534AB7'],
                'autre'                => ['cls'=>'cat-autre',  'bar'=>'#888'],
        ];

        foreach ($themes as $i => $t):
            $cm       = $catMap[$t['categorie']] ?? $catMap['autre'];
            $barColor = $t['score_green'] >= 70 ? '#1D9E75' : ($t['score_green'] >= 40 ? '#EF9F27' : '#E24B4A');
            $tags     = array_filter(array_map('trim', explode(',', $t['mots_cles'])));
            ?>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm card-hover fade-up theme-card"
                 data-cat="<?= htmlspecialchars($t['categorie']) ?>"
                 data-name="<?= htmlspecialchars(strtolower($t['nom'])) ?>"
                 style="animation-delay:<?= $i * .06 ?>s">

                <!-- Top accent bar -->
                <div class="h-1 rounded-t-2xl" style="background:<?= $cm['bar'] ?>"></div>

                <div class="p-5">
                    <!-- Header row -->
                    <div class="flex items-start justify-between mb-3">
          <span class="<?= $cm['cls'] ?> text-xs font-bold px-2.5 py-1 rounded-full">
            <?= htmlspecialchars($t['categorie']) ?>
          </span>
                        <!-- Score ring -->
                        <svg width="44" height="44" viewBox="0 0 90 90">
                            <circle cx="45" cy="45" r="38" fill="none" stroke="#E5E7EB" stroke-width="9"/>
                            <circle cx="45" cy="45" r="38" fill="none" stroke="<?= $barColor ?>" stroke-width="9"
                                    stroke-dasharray="239" stroke-dashoffset="<?= 239 - (239 * $t['score_green'] / 100) ?>"
                                    stroke-linecap="round" transform="rotate(-90 45 45)"/>
                            <text x="45" y="50" text-anchor="middle" font-size="18" font-weight="700"
                                  fill="<?= $barColor ?>" font-family="Space Grotesk"><?= $t['score_green'] ?></text>
                        </svg>
                    </div>

                    <h3 class="brand text-base font-bold text-gray-900 mb-1">
                        <?= htmlspecialchars($t['nom']) ?>
                    </h3>
                    <p class="text-xs text-gray-500 mb-3 leading-relaxed">
                        <?= htmlspecialchars(mb_substr($t['description'], 0, 85)) ?>...
                    </p>

                    <!-- Tags -->
                    <div class="flex flex-wrap gap-1.5 mb-3">
                        <?php foreach (array_slice($tags, 0, 3) as $tag): ?>
                            <span class="bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded-full">
            <?= htmlspecialchars($tag) ?>
          </span>
                        <?php endforeach; ?>
                    </div>

                    <!-- Project count -->
                    <p class="text-xs text-gray-400 mb-4">
                        <span class="font-semibold text-gray-600"><?= $t['nb_projets'] ?></span> projet(s) associé(s)
                    </p>

                    <!-- Actions -->
                    <div class="flex gap-2 pt-3 border-t border-gray-100">
                        <a href="index.php?action=edit&id=<?= $t['id'] ?>"
                           class="flex-1 text-center py-2 rounded-xl text-xs font-semibold btn-outline">
                            <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Modifier
                        </a>
                        <a href="index.php?action=delete&id=<?= $t['id'] ?>"
                           class="flex-1 text-center py-2 rounded-xl text-xs font-semibold bg-red-50 border border-red-200 text-red-600 hover:bg-red-100 transition"
                           onclick="return confirm('Supprimer ce thème ?')">
                            <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Supprimer
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (empty($themes)): ?>
            <div class="col-span-3 text-center py-16 text-gray-400">
                <p class="text-5xl mb-4">🎯</p>
                <p class="brand text-lg font-semibold">Aucun thème pour l'instant</p>
                <a href="index.php?action=create"
                   class="btn-primary inline-block mt-4 px-5 py-2.5 rounded-xl text-sm font-semibold">
                    + Proposer le premier thème
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<footer class="border-t border-gray-200 py-5 text-center">
    <p class="text-xs text-gray-400">© 2025–2026 ImpactVenture — Esprit School of Engineering | Module 2</p>
</footer>

<script>
    function filterCat(cat, btn) {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        document.querySelectorAll('.theme-card').forEach(c => {
            c.style.display = (cat === 'all' || c.dataset.cat === cat) ? '' : 'none';
        });
    }

    document.getElementById('searchInput').addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.theme-card').forEach(c => {
            c.style.display = c.dataset.name.includes(q) ? '' : 'none';
        });
    });
</script>
</body>
</html>