<?php
// expects: $demande
if (!isset($demande)) {
    die("Demande introuvable");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Modifier Demande - ImpactVenture</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #F0F4F1;
        }
        .brand { font-family: 'Space Grotesk', sans-serif; }
    </style>
</head>

<body class="min-h-screen">

<div class="max-w-3xl mx-auto px-6 py-10">

    <!-- Header -->
    <div class="mb-8">
        <a href="index.php?action=mes_demandes" class="text-[#1D9E75] hover:underline text-sm">
            ← Retour aux demandes
        </a>

        <h1 class="brand text-3xl font-bold mt-3">Modifier la demande</h1>
        <p class="text-gray-500 text-sm">Mettez à jour votre message de financement</p>
    </div>

    <!-- Card -->
    <div class="bg-white rounded-3xl shadow-sm p-8">

        <form method="POST"
              action="index.php?action=update_demande&id=<?= $demande['id'] ?>">

            <!-- Investisseur (readonly) -->
            <div class="mb-6">
                <label class="block text-sm font-semibold mb-2">Investisseur</label>
                <input type="text"
                       value="<?= htmlspecialchars($demande['investisseur_nom'] ?? '') ?>"
                       disabled
                       class="w-full px-4 py-3 border rounded-2xl bg-gray-100 text-gray-600">
            </div>

            <!-- Message -->
            <div class="mb-6">
                <label class="block text-sm font-semibold mb-2">Message</label>
                <textarea name="message" rows="6"
                          class="w-full px-4 py-3 border rounded-2xl focus:outline-none focus:border-[#1D9E75]"><?= htmlspecialchars($demande['message']) ?></textarea>
            </div>

            <!-- Status (optional display only) -->
            <div class="mb-6">
                <label class="block text-sm font-semibold mb-2">Statut</label>
                <input type="text"
                       value="<?= strtoupper($demande['statut']) ?>"
                       disabled
                       class="w-full px-4 py-3 border rounded-2xl bg-gray-100">
            </div>

            <!-- Buttons -->
            <div class="flex gap-4">

                <a href="index.php?action=mes_demandes"
                   class="flex-1 text-center py-3 border rounded-2xl hover:bg-gray-50">
                    Annuler
                </a>

                <button type="submit"
                        class="flex-1 bg-[#1D9E75] hover:bg-[#15795A] text-white font-semibold py-3 rounded-2xl">
                    <i class="fas fa-save mr-2"></i> Sauvegarder
                </button>

            </div>

        </form>

    </div>
</div>

</body>
</html>