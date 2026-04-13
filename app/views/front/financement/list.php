<?php
// app/views/front/financement/list.php
$action = $_GET['action'] ?? '';
if (!isset($investisseurs)) $investisseurs = [];
$success = $_GET['success'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>ImpactVenture – Financement</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        :root{--green:#1D9E75;--purple:#534AB7;--gold:#EF9F27;}
        body{font-family:'Inter',sans-serif;background:#F0F4F1;}
        h1,h2,h3,.brand{font-family:'Space Grotesk',sans-serif;}
        .card-hover{transition:transform .2s,box-shadow .2s;}
        .card-hover:hover{transform:translateY(-4px);box-shadow:0 12px 28px rgba(0,0,0,.1);}
        .btn-primary{background:#1D9E75;color:#fff;transition:background .2s;}
        .btn-primary:hover{background:#15795A;}
    </style>
</head>
<body class="min-h-screen">

<!-- NAVBAR - Smart Highlighting -->
<nav class="bg-white border-b border-gray-100 sticky top-0 z-50 shadow-sm">
    <div class="max-w-7xl mx-auto px-6 flex items-center justify-between h-16">
        <a href="index.php?action=list" class="brand text-xl font-bold flex items-center gap-2">
            <span style="color:#1D9E75">Impact</span><span style="color:#534AB7">Venture</span>
        </a>

        <div class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-600">
            <a href="index.php?action=list" class="nav-link font-semibold <?php echo ($action ?? '') === 'list' ? 'text-[#1D9E75]' : 'hover:text-[#1D9E75]'; ?>">Thèmes</a>
            <a href="#" class="hover:text-[#1D9E75]">Projets</a>
            <a href="#" class="hover:text-[#1D9E75]">Mentors</a>

            <!-- Financement Link - Highlighted only on this page -->
            <a href="index.php?action=financement"
               class="font-semibold flex items-center gap-1
                <?php echo ($action ?? '') === 'financement' || ($action ?? '') === 'investisseur_profile' ? 'text-[#1D9E75]' : 'hover:text-[#1D9E75]'; ?>">
                <i class="fas fa-handshake"></i> Financement
            </a>

            <!-- Mes Demandes Link - Highlighted only on its page -->
            <a href="index.php?action=mes_demandes"
               class="font-semibold flex items-center gap-1
                <?php echo ($action ?? '') === 'mes_demandes' ? 'text-[#EF9F27]' : 'hover:text-[#1D9E75]'; ?>">
                <i class="fas fa-file-invoice"></i> Mes Demandes
            </a>

            <a href="index.php?action=admin" class="font-semibold" style="color:#EF9F27">Admin ↗</a>
        </div>

        <div class="flex items-center gap-3">
            <a href="index.php?action=create" class="btn-primary px-4 py-2 rounded-lg text-sm font-semibold">+ Proposer un thème</a>
            <img src="https://i.pravatar.cc/36?img=12" class="w-9 h-9 rounded-full border-2" style="border-color:#1D9E75" alt="profil"/>
        </div>
    </div>
</nav>

<div class="max-w-7xl mx-auto px-6 py-10">
    <div class="text-center mb-12">
        <h1 class="brand text-4xl font-bold text-gray-900 mb-4">
            <i class="fas fa-handshake text-[#1D9E75] mr-3"></i> Financement à Impact
        </h1>
        <p class="text-xl text-gray-600 max-w-2xl mx-auto">
            Connectez votre projet directement à des investisseurs tunisiens qui soutiennent l'innovation verte et l'IA
        </p>
    </div>

    <?php if ($success === '1'): ?>
        <div class="max-w-2xl mx-auto mb-10 bg-green-50 border border-green-400 text-green-800 rounded-3xl px-8 py-5 flex items-center gap-4">
            <i class="fas fa-check-circle text-3xl"></i>
            <div>
                <p class="font-semibold text-lg">Demande envoyée avec succès !</p>
                <p class="text-green-700">L'investisseur a été notifié. Vous serez contacté bientôt.</p>
            </div>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach ($investisseurs as $inv): ?>
            <div class="bg-white rounded-3xl p-8 card-hover shadow-sm">
                <div class="flex justify-between mb-6">
                    <div>
                        <h3 class="font-bold text-2xl"><?= htmlspecialchars($inv['nom']) ?></h3>
                        <p class="text-[#1D9E75] font-medium"><?= htmlspecialchars($inv['organisation']) ?></p>
                    </div>
                    <span class="px-4 py-1.5 bg-green-100 text-green-700 text-sm font-semibold rounded-2xl">
            <?= htmlspecialchars($inv['secteur_focus']) ?>
        </span>
                </div>

                <p class="text-gray-600 mb-6 leading-relaxed">
                    <?= nl2br(htmlspecialchars($inv['description'])) ?>
                </p>

                <div class="mb-8">
                    <p class="text-sm text-gray-500">Montant d'investissement</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        <?= number_format($inv['montant_min'], 0) ?> – <?= number_format($inv['montant_max'], 0) ?> TND
                    </p>
                </div>

                <div class="flex gap-3 mt-6">
                    <!-- View Profile -->
                    <a href="index.php?action=investisseur_profile&id=<?= $inv['id'] ?>"
                       class="flex-1 text-center py-3 border border-[#1D9E75] text-[#1D9E75] font-semibold rounded-2xl hover:bg-[#1D9E75] hover:text-white transition">
                        👁️ Voir le profil
                    </a>

                    <!-- Ask for funding -->
                    <button onclick="showApplyModal(<?= $inv['id'] ?>, '<?= addslashes(htmlspecialchars($inv['nom'])) ?>')"
                            class="flex-1 bg-[#1D9E75] hover:bg-[#15795A] text-white font-semibold py-3 rounded-2xl transition flex items-center justify-center gap-2">
                        <i class="fas fa-paper-plane"></i> Demander
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal -->
<div id="applyModal" class="hidden fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-[100]">
    <div class="bg-white rounded-3xl p-8 w-full max-w-lg mx-4">
        <h3 class="text-2xl font-bold mb-6">Nouvelle demande de financement</h3>

        <form action="?action=financement_submit" method="POST">
            <input type="hidden" name="investisseur_id" id="modal_investisseur_id">
            <input type="hidden" name="fiche_entreprise_id" value="1">

            <div class="mb-6">
                <label class="block text-gray-600 font-medium mb-2">Investisseur</label>
                <p id="modal_investisseur_name" class="text-xl font-semibold text-[#1D9E75]"></p>
            </div>

            <div class="mb-6">
                <label class="block text-gray-600 font-medium mb-2">Votre message / Pitch</label>
                <textarea name="message" rows="6" class="w-full border border-gray-300 rounded-2xl p-5 focus:outline-none focus:border-[#1D9E75]"
                          placeholder="Décrivez brièvement votre projet..." required></textarea>
            </div>

            <div class="flex gap-4">
                <button type="button" onclick="hideModal()"
                        class="flex-1 py-4 border border-gray-300 rounded-2xl font-medium">Annuler</button>
                <button type="submit"
                        class="flex-1 py-4 bg-[#1D9E75] text-white rounded-2xl font-semibold">Envoyer la demande</button>
            </div>
        </form>
    </div>
</div>

<script>
    function showApplyModal(id, name) {
        document.getElementById('modal_investisseur_id').value = id;
        document.getElementById('modal_investisseur_name').textContent = name;
        document.getElementById('applyModal').classList.remove('hidden');
    }

    function hideModal() {
        document.getElementById('applyModal').classList.add('hidden');
    }
</script>

</body>
</html>