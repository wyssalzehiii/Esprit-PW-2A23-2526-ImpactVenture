<?php $errors = $errors ?? []; $fiche = $fiche ?? []; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Fiche Entreprise - ImpactVenture</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .brand { font-family: 'Space Grotesk', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

<nav class="bg-white border-b shadow-sm sticky top-0 z-50">
  <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
    <a href="<?= htmlspecialchars(iv_url(['action'=>'fiche_list']), ENT_QUOTES, 'UTF-8') ?>" class="brand flex items-center gap-2">
      <img src="logo.png" alt="ImpactVenture Logo" style="height: 40px; width: auto; object-fit: contain;">
      <span class="text-2xl font-bold"><span class="text-[#1D9E75]">Impact</span><span class="text-[#534AB7]">Venture</span></span>
    </a>
    <div class="flex gap-8 text-sm font-medium">
      <a href="<?= htmlspecialchars(iv_url(['action'=>'fiche_list']), ENT_QUOTES, 'UTF-8') ?>" class="text-[#1D9E75]">Entreprises</a>
      <a href="<?= htmlspecialchars(iv_url(['action'=>'projet_list']), ENT_QUOTES, 'UTF-8') ?>" class="hover:text-[#1D9E75]">Projets</a>
      <a href="<?= htmlspecialchars(iv_url(['action'=>'admin']), ENT_QUOTES, 'UTF-8') ?>" class="text-gray-500 hover:text-gray-900">Admin</a>
    </div>
  </div>
</nav>

<div class="max-w-3xl mx-auto pt-12 px-6 pb-12">
    <div class="text-center mb-10">
        <h1 class="brand text-4xl font-bold text-gray-900">Modifier une Fiche Entreprise</h1>
        <p class="text-gray-600 mt-3">Mettez a jour vos informations et regenez un logo IA si besoin.</p>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-2xl mb-8">
            <?php foreach($errors as $err): ?>
                <p>- <?= htmlspecialchars($err) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= htmlspecialchars(iv_url(['action'=>'fiche_update','id'=>(int)($fiche['id'] ?? 0)]), ENT_QUOTES, 'UTF-8') ?>" class="bg-white rounded-3xl shadow-xl p-10">
        <div class="space-y-8">

            <div>
                <label class="block text-sm font-semibold mb-2">Nom de l'Entreprise *</label>
                <input type="text" name="nom" id="nom"
                       class="w-full px-5 py-4 border border-gray-300 rounded-2xl focus:outline-none focus:border-[#1D9E75]"
                       value="<?= htmlspecialchars($_POST['nom'] ?? ($fiche['nom'] ?? '')) ?>" placeholder="Ex: SolarTech TN">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-2">Description de l'Entreprise *</label>
                <textarea name="description" id="description" rows="6"
                          class="w-full px-5 py-4 border border-gray-300 rounded-3xl focus:outline-none focus:border-[#1D9E75]"
                          placeholder="Decrivez votre entreprise..."><?= htmlspecialchars($_POST['description'] ?? ($fiche['description'] ?? '')) ?></textarea>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-2">Categorie *</label>
                <?php $selectedCategorie = $_POST['categorie'] ?? ($fiche['categorie'] ?? ''); ?>
                <select name="categorie" class="w-full px-5 py-4 border border-gray-300 rounded-2xl focus:outline-none focus:border-[#1D9E75]">
                    <option value="">-- Selectionnez une categorie --</option>
                    <option value="tech" <?= $selectedCategorie === 'tech' ? 'selected' : '' ?>>Tech & IA</option>
                    <option value="digital" <?= $selectedCategorie === 'digital' ? 'selected' : '' ?>>Economie Digitale</option>
                    <option value="energie renouvelable" <?= $selectedCategorie === 'energie renouvelable' ? 'selected' : '' ?>>Energie Renouvelable</option>
                    <option value="agriculture durable" <?= $selectedCategorie === 'agriculture durable' ? 'selected' : '' ?>>Agriculture Durable</option>
                    <option value="sante" <?= $selectedCategorie === 'sante' ? 'selected' : '' ?>>Sante</option>
                    <option value="education" <?= $selectedCategorie === 'education' ? 'selected' : '' ?>>Education</option>
                    <option value="autre" <?= $selectedCategorie === 'autre' ? 'selected' : '' ?>>Autre</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-2">Mots-cles (separes par virgule)</label>
                <input type="text" name="mots_cles"
                       class="w-full px-5 py-4 border border-gray-300 rounded-2xl"
                       placeholder="solaire, durable, tunisie, innovation"
                       value="<?= htmlspecialchars($_POST['mots_cles'] ?? ($fiche['mots_cles'] ?? '')) ?>">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-3">Logo de l'Entreprise</label>
                <button type="button" id="generateLogoBtn" onclick="generateLogoSuggestions()"
                        class="w-full py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl font-semibold flex items-center justify-center gap-3 hover:from-indigo-700">
                    Generer un logo IA
                </button>

                <p class="text-sm text-gray-500 mt-3">
                    L'IA genere 3 propositions basees sur le nom, la description, la categorie et les mots-cles.
                </p>

                <div id="logoLoading" class="hidden mt-4 text-sm text-indigo-700 font-medium">Generation des logos IA en cours...</div>
                <div id="logoError" class="hidden mt-4 text-sm text-red-600"></div>
                <div id="logoSuggestions" class="mt-6 hidden grid grid-cols-1 md:grid-cols-3 gap-4"></div>

                <div id="logoPreview" class="mt-6 border-2 border-dashed border-gray-300 rounded-3xl p-6 text-center <?= empty($_POST['logo'] ?? ($fiche['logo'] ?? '')) ? 'hidden' : '' ?>">
                    <p class="text-sm text-gray-600 mb-3">Logo selectionne :</p>
                    <img id="generatedLogoImg" src="<?= htmlspecialchars($_POST['logo'] ?? ($fiche['logo'] ?? '')) ?>" alt="Logo choisi" class="mx-auto max-h-52 rounded-2xl shadow bg-white p-3">
                </div>

                <input type="hidden" name="logo" id="logoHidden" value="<?= htmlspecialchars($_POST['logo'] ?? ($fiche['logo'] ?? '')) ?>">
            </div>

            <div class="flex justify-end gap-4 pt-8">
                <a href="<?= htmlspecialchars(iv_url(['action'=>'fiche_list']), ENT_QUOTES, 'UTF-8') ?>" class="px-8 py-4 text-gray-600 font-medium">Annuler</a>
                <button type="submit" class="bg-[#1D9E75] hover:bg-[#15795A] text-white px-10 py-4 rounded-2xl font-semibold">
                    Enregistrer les modifications
                </button>
            </div>
        </div>
    </form>
</div>

<script>
async function generateLogoSuggestions() {
    const nom = document.getElementById('nom').value.trim();
    const desc = document.getElementById('description').value.trim();
    const categorieEl = document.querySelector('select[name="categorie"]');
    const motsClesEl = document.querySelector('input[name="mots_cles"]');
    const categorie = categorieEl ? categorieEl.value.trim() : '';
    const motsCles = motsClesEl ? motsClesEl.value.trim() : '';

    if (!nom || !desc) {
        alert("Veuillez renseigner le nom et la description pour generer un logo.");
        return;
    }

    const btn = document.getElementById('generateLogoBtn');
    const loading = document.getElementById('logoLoading');
    const errorBox = document.getElementById('logoError');
    const container = document.getElementById('logoSuggestions');

    loading.classList.remove('hidden');
    errorBox.classList.add('hidden');
    container.classList.add('hidden');
    container.innerHTML = '';
    btn.disabled = true;
    btn.classList.add('opacity-70', 'cursor-not-allowed');

    const data = new FormData();
    data.append('nom', nom);
    data.append('description', desc);
    data.append('categorie', categorie);
    data.append('mots_cles', motsCles);

    try {
        const response = await fetch('<?= htmlspecialchars(iv_url(['action'=>'fiche_generate_logo']), ENT_QUOTES, 'UTF-8') ?>', { method: 'POST', body: data });
        const payload = await response.json();
        if (!response.ok || !payload.success || !payload.suggestions) {
            throw new Error(payload.message || 'Erreur durant la generation.');
        }
        renderLogoSuggestions(payload.suggestions);
    } catch (error) {
        errorBox.textContent = `Impossible de generer les logos: ${error.message}`;
        errorBox.classList.remove('hidden');
    } finally {
        loading.classList.add('hidden');
        btn.disabled = false;
        btn.classList.remove('opacity-70', 'cursor-not-allowed');
    }
}

function renderLogoSuggestions(suggestions) {
    const container = document.getElementById('logoSuggestions');
    container.innerHTML = '';
    let firstCard = null;
    let firstUrl = '';

    suggestions.forEach((logo, index) => {
        const card = document.createElement('div');
        card.className = 'border border-gray-200 rounded-2xl p-3 bg-white';
        const fallbackUrl = buildFallbackLogoDataUrl(index);
        card.innerHTML = `
            <img src="${logo.url}" alt="Logo IA ${index + 1}" class="w-full h-40 object-contain rounded-xl bg-gray-50 p-2 shadow-sm">
            <button type="button" class="mt-3 w-full py-2 rounded-xl font-semibold bg-gray-100 hover:bg-indigo-600 hover:text-white transition">
                Choisir ce logo
            </button>
        `;
        const imageEl = card.querySelector('img');
        imageEl.addEventListener('error', function () { this.src = fallbackUrl; }, { once: true });
        card.querySelector('button').addEventListener('click', () => selectLogo(imageEl.src, card));
        container.appendChild(card);

        if (index === 0) {
            firstCard = card;
            firstUrl = logo.url;
        }
    });

    container.classList.remove('hidden');
    if (firstCard && firstUrl) {
        selectLogo(firstUrl, firstCard);
    }
}

function selectLogo(url, cardEl) {
    document.getElementById('logoHidden').value = url;

    const preview = document.getElementById('logoPreview');
    const img = document.getElementById('generatedLogoImg');
    img.src = url;
    img.onerror = function () {
        this.src = buildFallbackLogoDataUrl(0);
        document.getElementById('logoHidden').value = this.src;
    };
    preview.classList.remove('hidden');

    document.querySelectorAll('#logoSuggestions > div').forEach((c) => {
        c.classList.remove('ring-2', 'ring-indigo-500', 'border-indigo-500');
    });
    cardEl.classList.add('ring-2', 'ring-indigo-500', 'border-indigo-500');
}

function buildFallbackLogoDataUrl(variantIndex) {
    const nom = document.getElementById('nom').value.trim() || 'Entreprise';
    const desc = (document.getElementById('description').value || '').toLowerCase();
    const categorieEl = document.querySelector('select[name="categorie"]');
    const categorie = (categorieEl ? categorieEl.value : '').toLowerCase();
    const context = `${nom.toLowerCase()} ${desc} ${categorie}`;

    let palette = ['#16A34A', '#22C55E'];
    if (categorie.includes('tech') || categorie.includes('digital')) palette = ['#4F46E5', '#7C3AED'];
    if (categorie.includes('energie')) palette = ['#F59E0B', '#F97316'];
    if (categorie.includes('sante')) palette = ['#0EA5E9', '#14B8A6'];

    const symbols = buildSymbolSet(context, variantIndex);
    const svg = `
<svg xmlns="http://www.w3.org/2000/svg" width="512" height="512" viewBox="0 0 512 512">
  <defs>
    <linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="${palette[0]}"/>
      <stop offset="100%" stop-color="${palette[1]}"/>
    </linearGradient>
  </defs>
  <rect width="512" height="512" rx="96" fill="url(#g)"/>
  ${symbols}
</svg>`;

    return `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(svg)}`;
}

function buildSymbolSet(context, variantIndex) {
    const eco = /agri|agriculture|eco|durable|green|nature|bio|farm|leaf/.test(context);
    const tech = /tech|ia|ai|digital|software|data|cloud|robot/.test(context);
    const energy = /energie|energy|solar|solaire|co2|wind|renewable/.test(context);
    const health = /sante|health|care|medical/.test(context);

    const base = [
        `<circle cx="256" cy="256" r="150" fill="white" opacity="0.10"/>`,
        `<circle cx="256" cy="256" r="96" fill="white" opacity="0.14"/>`
    ];

    if (eco) {
        const ecoVariants = [
            [`<path d="M256 142 C324 156 356 228 318 298 C295 340 258 364 256 364 C254 364 217 340 194 298 C156 228 188 156 256 142 Z" fill="white" opacity="0.24"/>`,
             `<path d="M196 282 C238 256 270 218 304 172" stroke="white" stroke-width="14" stroke-linecap="round" opacity="0.92"/>`],
            [`<circle cx="256" cy="256" r="112" fill="white" opacity="0.16"/>`,
             `<path d="M206 278 C208 224 240 186 298 186 C296 240 264 278 206 278 Z" fill="white" opacity="0.92"/>`,
             `<path d="M306 286 C304 232 272 194 214 194 C216 248 248 286 306 286 Z" fill="white" opacity="0.72"/>`],
            [`<path d="M154 304 C186 224 228 176 256 154 C284 176 326 224 358 304" stroke="white" stroke-width="16" stroke-linecap="round" opacity="0.9"/>`,
             `<circle cx="256" cy="316" r="38" fill="white" opacity="0.78"/>`]
        ];
        base.push(...ecoVariants[variantIndex % ecoVariants.length]);
    } else if (tech) {
        const techVariants = [
            [`<rect x="166" y="166" width="180" height="180" rx="36" fill="white" opacity="0.2"/>`,
             `<rect x="216" y="216" width="80" height="80" rx="16" fill="white" opacity="0.38"/>`,
             `<circle cx="146" cy="256" r="11" fill="white"/><circle cx="366" cy="256" r="11" fill="white"/>`,
             `<line x1="157" y1="256" x2="216" y2="256" stroke="white" stroke-width="8"/><line x1="296" y1="256" x2="355" y2="256" stroke="white" stroke-width="8"/>`],
            [`<circle cx="256" cy="256" r="106" fill="white" opacity="0.14"/>`,
             `<path d="M178 256 L256 176 L334 256 L256 336 Z" fill="white" opacity="0.82"/>`,
             `<path d="M210 256 L256 210 L302 256 L256 302 Z" fill="#6D5CFF" opacity="0.9"/>`],
            [`<rect x="168" y="182" width="176" height="148" rx="22" fill="white" opacity="0.18"/>`,
             `<circle cx="256" cy="256" r="52" fill="white" opacity="0.9"/>`,
             `<circle cx="214" cy="256" r="8" fill="#6D5CFF"/><circle cx="298" cy="256" r="8" fill="#6D5CFF"/>`,
             `<line x1="222" y1="256" x2="290" y2="256" stroke="#6D5CFF" stroke-width="7"/>`]
        ];
        base.push(...techVariants[variantIndex % techVariants.length]);
    } else if (energy) {
        const energyVariants = [
            [`<circle cx="256" cy="256" r="82" fill="white" opacity="0.26"/>`,
             `<path d="M268 132 L212 264 H258 L238 380 L302 244 H254 Z" fill="white" opacity="0.95"/>`],
            [`<circle cx="256" cy="256" r="68" fill="white" opacity="0.8"/>`,
             `<g fill="white" opacity="0.92"><path d="M256 116 l16 42 h-32z"/><path d="M396 256 l-42 16 v-32z"/><path d="M256 396 l16-42 h-32z"/><path d="M116 256 l42 16 v-32z"/></g>`],
            [`<path d="M148 300 C178 214 238 170 256 160 C274 170 334 214 364 300" stroke="white" stroke-width="14" fill="none" opacity="0.9"/>`,
             `<circle cx="256" cy="300" r="48" fill="white" opacity="0.82"/>`,
             `<path d="M256 270 L243 303 H257 L249 332 L270 297 H256 Z" fill="#F59E0B"/>`]
        ];
        base.push(...energyVariants[variantIndex % energyVariants.length]);
    } else if (health) {
        const healthVariants = [
            [`<circle cx="256" cy="256" r="102" fill="white" opacity="0.2"/>`,
             `<rect x="232" y="182" width="48" height="148" rx="12" fill="white" opacity="0.94"/>`,
             `<rect x="182" y="232" width="148" height="48" rx="12" fill="white" opacity="0.94"/>`],
            [`<path d="M256 372 C198 334 162 292 162 242 C162 206 188 180 222 180 C240 180 252 188 256 198 C260 188 272 180 290 180 C324 180 350 206 350 242 C350 292 314 334 256 372 Z" fill="white" opacity="0.9"/>`],
            [`<circle cx="224" cy="256" r="58" fill="white" opacity="0.9"/><circle cx="288" cy="256" r="58" fill="white" opacity="0.58"/>`,
             `<rect x="246" y="210" width="20" height="92" rx="8" fill="#0EA5E9"/><rect x="210" y="246" width="92" height="20" rx="8" fill="#0EA5E9"/>`]
        ];
        base.push(...healthVariants[variantIndex % healthVariants.length]);
    } else {
        const variants = [
            `<path d="M256 132 L364 318 H148 Z" fill="white" opacity="0.22"/>`,
            `<rect x="170" y="170" width="172" height="172" rx="44" fill="white" opacity="0.2"/>`,
            `<circle cx="256" cy="256" r="124" fill="white" opacity="0.2"/>`
        ];
        base.push(variants[variantIndex % variants.length]);
    }

    return base.join('');
}
</script>

</body>
</html>
