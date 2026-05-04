<?php if (!isset($investisseur)) exit; ?>
<?php
$demandes   = $demandes   ?? [];
$en_attente = $en_attente ?? [];
$acceptees  = $acceptees  ?? [];
$refusees   = $refusees   ?? [];
$total      = $total      ?? 0;
$nb_accepte = $nb_accepte ?? 0;
$nb_attente = $nb_attente ?? 0;
$taux       = $taux       ?? 0;
$total_invested = $total_invested ?? 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Espace Investisseur – ImpactVenture</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>
    <style>
        :root { --green:#1D9E75; --purple:#534AB7; --gold:#EF9F27; }
        body  { font-family:'Inter',sans-serif; background:#F0F4F1; }
        h1,h2,h3,.brand { font-family:'Space Grotesk',sans-serif; }
        .stat-card { transition: transform .18s ease, box-shadow .18s ease; }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 12px 32px rgba(0,0,0,.09); }
        .motif-box { display:none; }
        .motif-box.open { display:block; }
        .badge-attente { background:#FEF3C7; color:#92400E; }
        .badge-accepte { background:#D1FAE5; color:#065F46; }
        .badge-refuse  { background:#FEE2E2; color:#991B1B; }
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
            <a href="index.php?action=list" class="hover:text-[#1D9E75]">Thèmes</a>
            <a href="#" class="hover:text-[#1D9E75]">Projets</a>
            <a href="#" class="hover:text-[#1D9E75]">Mentors</a>
            <a href="index.php?action=financement" class="hover:text-[#1D9E75]">Financement</a>
            <a href="index.php?action=mes_demandes" class="hover:text-[#1D9E75]">Mes Demandes</a>
            <a href="index.php?action=espace_investisseur"
               class="font-semibold flex items-center gap-1"
               style="color:#534AB7">
                <i class="fas fa-briefcase"></i> Espace Investisseur
            </a>
            <a href="index.php?action=admin" class="font-semibold" style="color:#EF9F27">Admin ↗</a>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-sm font-medium text-gray-700">
                <?= htmlspecialchars($investisseur['nom']) ?>
            </span>
            <img src="https://i.pravatar.cc/36?img=3"
                 class="w-9 h-9 rounded-full border-2"
                 style="border-color:#534AB7" alt="profil"/>
        </div>
    </div>
</nav>

<div class="max-w-7xl mx-auto px-6 py-10 space-y-12">

    <!-- HERO HEADER -->
    <div class="rounded-3xl p-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6"
         style="background: linear-gradient(135deg,#534AB7 0%,#1D9E75 100%);">
        <div class="text-white">
            <p class="text-sm font-medium opacity-80 mb-1 uppercase tracking-widest">Tableau de bord</p>
            <h1 class="brand text-4xl font-bold">
                Bienvenue, <?= htmlspecialchars($investisseur['nom']) ?> 👋
            </h1>
            <p class="mt-2 opacity-80 text-sm">
                <?= htmlspecialchars($investisseur['organisation'] ?? '') ?>
                <?php if (!empty($investisseur['secteur_focus'])): ?>
                    · Focus : <?= htmlspecialchars($investisseur['secteur_focus']) ?>
                <?php endif; ?>
            </p>
        </div>
        <div class="flex gap-3 flex-wrap">
            <a href="index.php?action=financement"
               class="px-5 py-2.5 rounded-xl bg-white/20 text-white text-sm font-semibold hover:bg-white/30 transition">
                <i class="fas fa-users mr-2"></i>Voir les projets
            </a>
            <?php if (!empty($investisseur['linkedin'])): ?>
                <a href="<?= htmlspecialchars($investisseur['linkedin']) ?>" target="_blank"
                   class="px-5 py-2.5 rounded-xl bg-white/20 text-white text-sm font-semibold hover:bg-white/30 transition">
                    <i class="fab fa-linkedin mr-2"></i>LinkedIn
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- STAT CARDS -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-5">
        <?php
        $stats = [
                ['icon'=>'fa-inbox', 'label'=>'Demandes reçues', 'value'=>$total, 'color'=>'#534AB7', 'bg'=>'#EEF0FF'],
                ['icon'=>'fa-hourglass-half', 'label'=>'En attente', 'value'=>$nb_attente, 'color'=>'#EF9F27', 'bg'=>'#FEF6E8'],
                ['icon'=>'fa-check-circle', 'label'=>'Acceptées', 'value'=>$nb_accepte, 'color'=>'#1D9E75', 'bg'=>'#E8F7F2'],
                ['icon'=>'fa-percent', 'label'=>"Taux d'acceptation", 'value'=>$taux.'%', 'color'=>'#E05D8A', 'bg'=>'#FDEEF4'],
                ['icon'=>'fa-money-bill-wave', 'label'=>'Total Investi', 'value'=> number_format($total_invested, 0, ',', ' ') . ' TND', 'color'=>'#10B981', 'bg'=>'#ECFDF5'],
        ];
        foreach ($stats as $s): ?>
            <div class="stat-card bg-white rounded-2xl p-6 flex flex-col gap-3 cursor-default">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center"
                     style="background:<?= $s['bg'] ?>">
                    <i class="fas <?= $s['icon'] ?> text-lg" style="color:<?= $s['color'] ?>"></i>
                </div>
                <div>
                    <p class="text-3xl font-bold brand" style="color:<?= $s['color'] ?>"><?= $s['value'] ?></p>
                    <p class="text-xs text-gray-500 mt-0.5"><?= $s['label'] ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- DEMANDES REÇUES -->
    <section>
        <div class="flex items-center justify-between mb-6">
            <h2 class="brand text-2xl font-bold flex items-center gap-3">
                <span class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-sm"
                      style="background:#534AB7">
                    <i class="fas fa-envelope-open-text"></i>
                </span>
                Demandes Reçues
            </h2>
            <span class="text-xs font-semibold px-4 py-1.5 rounded-full bg-purple-100 text-purple-700">
                <?= count($en_attente) ?> en attente
            </span>
        </div>

        <?php if (empty($demandes)): ?>
            <div class="bg-white rounded-3xl p-12 text-center">
                <p class="text-5xl mb-4">📭</p>
                <p class="text-lg font-medium text-gray-600">Aucune demande reçue pour le moment.</p>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($demandes as $d):
                    $isAttente = $d['statut'] === 'en_attente';
                    $badgeClass = $d['statut'] === 'accepte' ? 'badge-accepte'
                            : ($d['statut'] === 'refuse' ? 'badge-refuse' : 'badge-attente');
                    $badgeLabel = $d['statut'] === 'accepte' ? '✓ Acceptée'
                            : ($d['statut'] === 'refuse' ? '✗ Refusée' : '⏳ En attente');
                    ?>
                    <div class="bg-white rounded-2xl p-6 flex flex-col md:flex-row gap-5 items-start
                                <?= $isAttente ? 'border-l-4' : 'opacity-90' ?>"
                         style="<?= $isAttente ? 'border-color:#EF9F27' : '' ?>">

                        <!-- Avatar -->
                        <div class="w-12 h-12 rounded-2xl flex-shrink-0 flex items-center justify-center text-white font-bold text-lg"
                             style="background: linear-gradient(135deg,#534AB7,#1D9E75)">
                            <?= strtoupper(mb_substr($d['investisseur_nom'] ?? 'E', 0, 1)) ?>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-3 mb-1">
                                <h3 class="font-bold text-gray-900 text-lg">
                                    <?= htmlspecialchars($d['investisseur_nom']) ?>
                                </h3>
                                <span class="text-xs font-semibold px-3 py-1 rounded-full <?= $badgeClass ?>">
                                    <?= $badgeLabel ?>
                                </span>
                            </div>

                            <?php if (!empty($d['organisation'])): ?>
                                <p class="text-xs text-[#1D9E75] font-medium mb-2">
                                    <?= htmlspecialchars($d['organisation']) ?>
                                </p>
                            <?php endif; ?>

                            <p class="text-sm text-gray-600 leading-relaxed line-clamp-3">
                                <?= nl2br(htmlspecialchars($d['message'])) ?>
                            </p>

                            <!-- NEW INFORMATION -->
                            <?php if (!empty($d['montant_demande'])): ?>
                                <p class="mt-3 font-semibold text-green-700">
                                    Montant demandé : <?= number_format($d['montant_demande'], 0, ',', ' ') ?>
                                    <?= htmlspecialchars($d['currency'] ?? 'TND') ?>
                                </p>
                            <?php endif; ?>

                            <?php if (!empty($d['phone'])): ?>
                                <div class="mt-2 flex items-center gap-2">
                                    <span class="text-xs text-gray-500">📞</span>
                                    <span class="font-medium"><?= htmlspecialchars($d['phone']) ?></span>
                                    <button onclick="copyPhone('<?= htmlspecialchars($d['phone']) ?>', this)"
                                            class="text-blue-600 hover:text-blue-700 text-xs font-semibold ml-2">
                                        Copier
                                    </button>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($d['motif_refus'])): ?>
                                <p class="mt-2 text-xs text-red-600 bg-red-50 rounded-lg px-3 py-2">
                                    <i class="fas fa-comment-slash mr-1"></i>
                                    Motif : <?= htmlspecialchars($d['motif_refus']) ?>
                                </p>
                            <?php endif; ?>

                            <p class="text-xs text-gray-400 mt-2">
                                <i class="far fa-calendar-alt mr-1"></i>
                                <?= date('d M Y', strtotime($d['date_demande'])) ?>
                            </p>
                        </div>

                        <!-- Actions -->
                        <?php if ($isAttente): ?>
                            <div class="flex flex-col gap-2 flex-shrink-0 min-w-[160px]" id="actions-<?= $d['id'] ?>">
                                <a href="index.php?action=investisseur_action&id=<?= $d['id'] ?>&status=accepte"
                                   onclick="return confirm('Accepter cette demande ?')"
                                   class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-green-100 text-green-700 hover:bg-green-600 hover:text-white transition">
                                    <i class="fas fa-check"></i> Accepter
                                </a>

                                <button type="button" onclick="toggleRefus(<?= $d['id'] ?>)"
                                        class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-red-100 text-red-600 hover:bg-red-600 hover:text-white transition">
                                    <i class="fas fa-times"></i> Refuser
                                </button>

                                <div class="motif-box" id="motif-<?= $d['id'] ?>">
                                    <form method="POST" action="index.php?action=investisseur_action&id=<?= $d['id'] ?>&status=refuse" class="mt-1 space-y-2">
                                        <textarea name="motif" rows="3" placeholder="Motif de refus..." class="w-full text-xs rounded-xl border border-red-200 p-3 resize-none focus:outline-none focus:ring-2 focus:ring-red-400"></textarea>
                                        <button type="submit" class="w-full py-2 rounded-xl bg-red-600 text-white text-xs font-semibold hover:bg-red-700 transition">
                                            Confirmer le refus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- MON PORTFOLIO -->
    <section>
        <div class="flex items-center justify-between mb-6">
            <h2 class="brand text-2xl font-bold flex items-center gap-3">
                <span class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-sm"
                      style="background:#1D9E75">
                    <i class="fas fa-folder-open"></i>
                </span>
                Mon Portfolio
            </h2>
            <span class="text-xs font-semibold px-4 py-1.5 rounded-full bg-green-100 text-green-700">
                <?= count($acceptees) ?> projet<?= count($acceptees) > 1 ? 's' : '' ?>
            </span>
        </div>

        <?php if (empty($acceptees)): ?>
            <div class="bg-white rounded-3xl p-12 text-center">
                <p class="text-5xl mb-4">📂</p>
                <p class="text-lg font-medium text-gray-600">Aucun projet accepté pour l'instant.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                <?php foreach ($acceptees as $d): ?>
                    <div class="bg-white rounded-2xl p-6 border-t-4 flex flex-col gap-4"
                         style="border-color:#1D9E75">

                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl flex-shrink-0 flex items-center justify-center text-white font-bold text-base"
                                 style="background: linear-gradient(135deg,#1D9E75,#EF9F27)">
                                <?= strtoupper(mb_substr($d['investisseur_nom'] ?? 'P', 0, 1)) ?>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 leading-tight">
                                    <?= htmlspecialchars($d['investisseur_nom']) ?>
                                </p>
                                <?php if (!empty($d['organisation'])): ?>
                                    <p class="text-xs text-[#1D9E75] font-medium">
                                        <?= htmlspecialchars($d['organisation']) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <p class="text-sm text-gray-600 leading-relaxed line-clamp-4 flex-1">
                            <?= nl2br(htmlspecialchars($d['message'])) ?>
                        </p>

                        <?php if (!empty($d['montant_demande'])): ?>
                            <p class="font-semibold text-green-700">
                                Montant demandé : <?= number_format($d['montant_demande'], 0, ',', ' ') ?>
                                <?= htmlspecialchars($d['currency'] ?? 'TND') ?>
                            </p>
                        <?php endif; ?>

                        <?php if (!empty($d['phone'])): ?>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-500">📞</span>
                                <span class="font-medium"><?= htmlspecialchars($d['phone']) ?></span>
                                <button onclick="copyPhone('<?= addslashes(htmlspecialchars($d['phone'])) ?>', this)"
                                        class="text-blue-600 hover:text-blue-700 text-xs font-semibold ml-2">
                                    Copier
                                </button>
                            </div>
                        <?php endif; ?>

                        <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold badge-accepte">
                                ✓ Accepté
                            </span>
                            <span class="text-xs text-gray-400">
                                <i class="far fa-calendar-check mr-1"></i>
                                <?= date('d M Y', strtotime($d['date_demande'])) ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

</div>

<footer class="mt-16 border-t border-gray-200 bg-white py-6 text-center text-xs text-gray-400">
    ImpactVenture © <?= date('Y') ?> · Espace Investisseur
</footer>

<script>
    function toggleRefus(id) {
        const box = document.getElementById('motif-' + id);
        box.classList.toggle('open');
    }

    function copyPhone(phone, btn) {
        navigator.clipboard.writeText(phone).then(() => {
            const original = btn.textContent;
            btn.textContent = "Copié !";
            btn.style.color = "#10B981";
            setTimeout(() => {
                btn.textContent = original;
                btn.style.color = "";
            }, 2000);
        });
    }
</script>

</body>
</html>