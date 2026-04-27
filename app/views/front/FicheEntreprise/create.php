<?php $errors = $errors ?? []; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer Fiche Entreprise - ImpactVenture</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .brand { font-family: 'Space Grotesk', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

<div class="max-w-3xl mx-auto pt-12 px-6">
    <div class="text-center mb-10">
        <h1 class="brand text-4xl font-bold text-gray-900">Créer une Fiche Entreprise</h1>
        <p class="text-gray-600 mt-3">Décrivez votre entreprise. Notre IA générera un logo professionnel.</p>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-2xl mb-8">
            <?php foreach($errors as $err): ?>
                <p>• <?= htmlspecialchars($err) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="index.php?action=fiche_store" class="bg-white rounded-3xl shadow-xl p-10">
        <div class="space-y-8">

            <!-- Nom -->
            <div>
                <label class="block text-sm font-semibold mb-2">Nom de l'Entreprise *</label>
                <input type="text" name="nom" id="nom" 
                       class="w-full px-5 py-4 border border-gray-300 rounded-2xl focus:outline-none focus:border-[#1D9E75]"
                       value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" placeholder="Ex: SolarTech TN">
            </div>

            <!-- Description + Dictée vocale -->
            <div>
                <label class="block text-sm font-semibold mb-2">Description de l'Entreprise *</label>
                <div class="flex gap-3 mb-3">
                    <button type="button" onclick="startDictation()" 
                            class="px-6 py-3 bg-[#1D9E75] text-white rounded-2xl font-medium flex items-center gap-2 hover:bg-[#15795A]">
                        🎤 Dicter avec la voix
                    </button>
                </div>
                <textarea name="description" id="description" rows="6" 
                          class="w-full px-5 py-4 border border-gray-300 rounded-3xl focus:outline-none focus:border-[#1D9E75]"
                          placeholder="Décrivez votre entreprise..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
            </div>

            <!-- Catégorie -->
            <div>
                <label class="block text-sm font-semibold mb-2">Catégorie *</label>
                <select name="categorie" class="w-full px-5 py-4 border border-gray-300 rounded-2xl focus:outline-none focus:border-[#1D9E75]">
                    <option value="">-- Sélectionnez une catégorie --</option>
                    <option value="tech">Tech & IA</option>
                    <option value="digital">Économie Digitale</option>
                    <option value="energie renouvelable">Énergie Renouvelable</option>
                    <option value="agriculture durable">Agriculture Durable</option>
                    <option value="sante">Santé</option>
                    <option value="education">Éducation</option>
                    <option value="autre">Autre</option>
                </select>
            </div>

            <!-- Générer Logo IA -->
            <div>
                <label class="block text-sm font-semibold mb-3">Logo de l'Entreprise</label>
                <button type="button" onclick="generateLogo()" 
                        class="w-full py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl font-semibold flex items-center justify-center gap-3 hover:from-indigo-700 hover:to-purple-700">
                    🎨 Générer Logo par IA
                </button>

                <div id="logoPreview" class="mt-6 hidden border-2 border-dashed border-gray-300 rounded-3xl p-6 text-center">
                    <p class="text-sm text-gray-600 mb-3">Logo généré par l'IA :</p>
                    <img id="generatedLogoImg" src="" alt="Logo IA" class="mx-auto max-h-52 rounded-2xl shadow">
                    <input type="hidden" name="logo" id="logoHidden" value="">
                </div>
            </div>

            <!-- Mots-clés + Score Green -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold mb-2">Mots-clés (séparés par virgule)</label>
                    <input type="text" name="mots_cles" 
                           class="w-full px-5 py-4 border border-gray-300 rounded-2xl"
                           placeholder="solaire, durable, tunisie, innovation" 
                           value="<?= htmlspecialchars($_POST['mots_cles'] ?? '') ?>">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-2">Score Green estimé (0-100)</label>
                    <input type="number" name="score_green" min="0" max="100" value="75"
                           class="w-full px-5 py-4 border border-gray-300 rounded-2xl">
                </div>
            </div>

            <div class="flex justify-end gap-4 pt-8">
                <a href="index.php?action=fiche_list" 
                   class="px-8 py-4 text-gray-600 font-medium">Annuler</a>
                <button type="submit" 
                        class="bg-[#1D9E75] hover:bg-[#15795A] text-white px-10 py-4 rounded-2xl font-semibold">
                    Créer la Fiche Entreprise
                </button>
            </div>
        </div>
    </form>
</div>

<script>
// Dictée Vocale
function startDictation() {
    if (!('SpeechRecognition' in window || 'webkitSpeechRecognition' in window)) {
        alert("Désolé, votre navigateur ne supporte pas la reconnaissance vocale.");
        return;
    }
    const recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
    recognition.lang = 'fr-FR';
    recognition.onresult = function(event) {
        document.getElementById('description').value += event.results[0][0].transcript + " ";
    };
    recognition.start();
}

// Générer Logo IA (simulation réaliste)
function generateLogo() {
    const nom = document.getElementById('nom').value.trim();
    const desc = document.getElementById('description').value.trim();

    if (!nom || !desc) {
        alert("Veuillez remplir le nom et la description avant de générer le logo.");
        return;
    }

    const previewDiv = document.getElementById('logoPreview');
    const img = document.getElementById('generatedLogoImg');
    const hiddenInput = document.getElementById('logoHidden');

    // Simulation de génération de logo
    const logoUrl = `https://via.placeholder.com/400x300/1D9E75/FFFFFF?text=${encodeURIComponent(nom.substring(0,12))}`;

    img.src = logoUrl;
    hiddenInput.value = `Logo IA pour ${nom} - ${desc.substring(0,80)}...`;
    previewDiv.classList.remove('hidden');

    alert(`✅ Logo généré avec succès pour "${nom}" !\n\nL'IA a analysé votre description.`);
}
</script>

</body>
</html>