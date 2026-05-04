<?php
// expects: $investisseur from controller
if (!isset($investisseur)) {
    die("Investisseur non trouvé");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Back Office - Modifier Investisseur | ImpactVenture</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>

    <style>
        :root { --iv-green: #1D9E75; }
        body { font-family: 'Inter', system-ui, sans-serif; background: #F0F4F1; }
        .brand { font-family: 'Space Grotesk', sans-serif; }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1); }
    </style>
</head>

<body class="min-h-screen">

<div class="max-w-4xl mx-auto px-6 py-10">

    <!-- Header -->
    <div class="flex items-center justify-between mb-10">
        <div>
            <a href="index.php?action=admin" class="text-[#1D9E75] hover:underline flex items-center gap-2 text-sm">
                ← Retour au Dashboard Admin
            </a>
            <h1 class="brand text-4xl font-bold text-gray-900 mt-3">Modifier l'Investisseur</h1>
            <p class="text-gray-600 mt-1">Mettez à jour les informations de l'investisseur</p>
        </div>

        <a href="index.php?action=admin"
           class="px-6 py-3 border border-gray-300 rounded-2xl text-gray-700 hover:bg-gray-100 transition">
            Annuler
        </a>
    </div>

    <!-- Card -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 card-hover overflow-hidden">

        <div class="px-10 py-8 border-b border-gray-100 bg-gray-50">
            <h2 class="text-2xl font-semibold text-gray-800 flex items-center gap-3">
                <i class="fas fa-user-edit text-[#1D9E75]"></i>
                Modifier les informations
            </h2>
        </div>

        <div class="p-10">

            <form method="POST"
                  action="index.php?action=admin_investisseur_update&id=<?= $investisseur['id'] ?>"
                  class="space-y-8">

                <!-- Nom + org -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nom Complet *</label>
                        <input type="text" name="nom" required
                               value="<?= htmlspecialchars($investisseur['nom']) ?>"
                               class="w-full px-5 py-3 border border-gray-300 rounded-2xl focus:outline-none focus:border-[#1D9E75] transition">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Organisation</label>
                        <input type="text" name="organisation"
                               value="<?= htmlspecialchars($investisseur['organisation'] ?? '') ?>"
                               class="w-full px-5 py-3 border border-gray-300 rounded-2xl focus:outline-none focus:border-[#1D9E75] transition">
                    </div>
                </div>

                <!-- Secteur -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Secteur Focus</label>
                    <input type="text" name="secteur_focus"
                           value="<?= htmlspecialchars($investisseur['secteur_focus'] ?? '') ?>"
                           class="w-full px-5 py-3 border border-gray-300 rounded-2xl focus:outline-none focus:border-[#1D9E75] transition">
                </div>

                <!-- Montants -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Montant Minimum</label>
                        <input type="number" name="montant_min"
                               value="<?= htmlspecialchars($investisseur['montant_min'] ?? '') ?>"
                               class="w-full px-5 py-3 border border-gray-300 rounded-2xl focus:outline-none focus:border-[#1D9E75] transition">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Montant Maximum</label>
                        <input type="number" name="montant_max"
                               value="<?= htmlspecialchars($investisseur['montant_max'] ?? '') ?>"
                               class="w-full px-5 py-3 border border-gray-300 rounded-2xl focus:outline-none focus:border-[#1D9E75] transition">
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="4"
                              class="w-full px-5 py-4 border border-gray-300 rounded-3xl focus:outline-none focus:border-[#1D9E75] transition"><?= htmlspecialchars($investisseur['description'] ?? '') ?></textarea>
                </div>

                <!-- Photo + LinkedIn -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Photo URL</label>
                        <input type="text" name="photo"
                               value="<?= htmlspecialchars($investisseur['photo'] ?? '') ?>"
                               class="w-full px-5 py-3 border border-gray-300 rounded-2xl">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">LinkedIn</label>
                        <input type="text" name="linkedin"
                               value="<?= htmlspecialchars($investisseur['linkedin'] ?? '') ?>"
                               class="w-full px-5 py-3 border border-gray-300 rounded-2xl">
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex gap-4 pt-6 border-t border-gray-100">

                    <a href="index.php?action=admin"
                       class="flex-1 py-4 text-center border border-gray-300 rounded-2xl font-medium hover:bg-gray-50 transition">
                        Annuler
                    </a>

                    <button type="submit"
                            class="flex-1 py-4 bg-[#1D9E75] hover:bg-[#15795A] text-white font-semibold rounded-2xl transition flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i>
                        Sauvegarder
                    </button>

                </div>

            </form>

        </div>
    </div>

</div>

</body>
</html>