<?php $errors = $errors ?? []; $fiches = $fiches ?? []; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Journey de Création - ImpactVenture</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body{font-family:'Inter',sans-serif;}
        .brand{font-family:'Space Grotesk',sans-serif;}
        .step-indicator{transition:all 0.3s ease;}
        .step-indicator.active{background:#1D9E75;color:white;}
        .step-indicator.completed{background:#10B981;color:white;}
        .step-content{display:none;}
        .step-content.active{display:block;}
        .progress-bar{transition:width 0.5s ease;}
        .floating-card{animation:float 3s ease-in-out infinite;}
        @keyframes float{0%,100%{transform:translateY(0px);}50%{transform:translateY(-10px);}}
        .pulse-animation{animation:pulse 2s ease-in-out infinite;}
        @keyframes pulse{0%,100%{opacity:1;}50%{opacity:0.7;}}
        .journey-bg{background:linear-gradient(135deg, #1D9E75 0%, #534AB7 100%);}
        .glass-effect{background:rgba(255,255,255,0.1);backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,0.2);}
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
      <a href="<?= htmlspecialchars(iv_url(['action'=>'fiche_list']), ENT_QUOTES, 'UTF-8') ?>" class="hover:text-[#1D9E75]">Entreprises</a>
      <a href="<?= htmlspecialchars(iv_url(['action'=>'projet_list']), ENT_QUOTES, 'UTF-8') ?>" class="text-[#534AB7] font-semibold">Projets</a>
      <a href="<?= htmlspecialchars(iv_url(['action'=>'admin']), ENT_QUOTES, 'UTF-8') ?>" class="text-amber-600">Admin</a>
    </div>
  </div>
</nav>

<!-- Hero Section with Journey -->
<div class="journey-bg text-white py-16">
  <div class="max-w-6xl mx-auto px-6 text-center">
    <h1 class="brand text-5xl font-bold mb-4">Lancez votre aventure entrepreneuriale</h1>
    <p class="text-xl opacity-90 mb-8">Transformez votre idée en projet impactant avec notre parcours guidé</p>
    
    <!-- Progress Bar -->
    <div class="max-w-3xl mx-auto mb-8">
      <div class="flex justify-between items-center mb-4">
        <div class="step-indicator active rounded-full w-10 h-10 flex items-center justify-center font-bold" data-step="1">1</div>
        <div class="step-indicator rounded-full w-10 h-10 flex items-center justify-center font-bold" data-step="2">2</div>
        <div class="step-indicator rounded-full w-10 h-10 flex items-center justify-center font-bold" data-step="3">3</div>
        <div class="step-indicator rounded-full w-10 h-10 flex items-center justify-center font-bold" data-step="4">4</div>
        <div class="step-indicator rounded-full w-10 h-10 flex items-center justify-center font-bold" data-step="5">5</div>
      </div>
      <div class="bg-white/20 rounded-full h-2">
        <div class="progress-bar bg-white rounded-full h-2" style="width: 20%"></div>
      </div>
      <div class="flex justify-between text-xs mt-2 opacity-80">
        <span>Vision</span>
        <span>Détails</span>
        <span>Impact</span>
        <span>Équipe</span>
        <span>Finalisation</span>
      </div>
    </div>
  </div>
</div>

<div class="max-w-4xl mx-auto px-6 pb-16">
  <?php if (!empty($errors)): ?>
  <div class="bg-red-50 border border-red-300 text-red-700 px-6 py-4 rounded-2xl mb-8">
    <p class="font-semibold mb-2">Veuillez corriger les erreurs :</p>
    <?php foreach($errors as $err): ?>
      <p class="text-sm">• <?= htmlspecialchars($err) ?></p>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <form method="POST" action="<?= htmlspecialchars(iv_url(['action'=>'projet_store']), ENT_QUOTES, 'UTF-8') ?>" class="journey-form">
    
    <!-- STEP 1: Vision & Inspiration -->
    <div class="step-content active" data-step="1">
      <div class="bg-white rounded-3xl shadow-xl p-10">
        <div class="text-center mb-8">
          <div class="w-20 h-20 bg-gradient-to-br from-[#1D9E75] to-[#534AB7] rounded-full flex items-center justify-center mx-auto mb-4 floating-card">
            <span class="text-3xl">🚀</span>
          </div>
          <h2 class="brand text-3xl font-bold mb-4">Décrivez votre vision</h2>
          <p class="text-gray-600">Commencez par le grand vision - quel problème voulez-vous résoudre ?</p>
        </div>
        
        <div class="space-y-6">
          <div>
            <label class="block text-sm font-semibold mb-2 flex items-center gap-2">
              <span class="text-xl">🎯</span> Quel est le nom de votre projet ?
            </label>
            <input type="text" name="titre" id="projet_titre"
                   class="w-full px-5 py-4 border border-gray-300 rounded-2xl focus:outline-none focus:border-[#1D9E75] text-lg"
                   value="<?= htmlspecialchars($_POST['titre'] ?? '') ?>"
                   placeholder="Ex: EcoDeliver, HealthBridge, EduConnect...">
          </div>
          
          <div>
            <label class="block text-sm font-semibold mb-2 flex items-center gap-2">
              <span class="text-xl">💡</span> Quelle est votre grande idée ?
            </label>
            <textarea name="description" id="projet_description" rows="4"
                      class="w-full px-5 py-4 border border-gray-300 rounded-2xl focus:outline-none focus:border-[#1D9E75] text-lg"
                      placeholder="Décrivez votre vision en quelques phrases..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
            <div class="text-sm text-gray-500 mt-2">
              <span id="char-count">0</span> caractères - Essayez d'être clair et inspirant
            </div>
          </div>
          
          <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-2xl p-6 border border-blue-200">
            <h3 class="font-semibold text-blue-900 mb-3 flex items-center gap-2">
              <span class="text-xl">💭</span> Questions pour vous guider
            </h3>
            <ul class="space-y-2 text-sm text-blue-800">
              <li>• Quel problème spécifique voulez-vous résoudre ?</li>
              <li>• Pour qui cette solution est-elle destinée ?</li>
              <li>• Qu'est-ce qui rend votre approche unique ?</li>
              <li>• Quel impact positif voulez-vous créer ?</li>
            </ul>
          </div>
        </div>
        
        <div class="flex justify-between mt-8">
          <div></div>
          <button type="button" onclick="nextStep()" class="bg-gradient-to-r from-[#1D9E75] to-[#534AB7] text-white px-8 py-4 rounded-2xl font-semibold hover:shadow-lg transition">
            Continuer →
          </button>
        </div>
      </div>
    </div>

    <!-- STEP 2: Project Details -->
    <div class="step-content" data-step="2">
      <div class="bg-white rounded-3xl shadow-xl p-10">
        <div class="text-center mb-8">
          <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full flex items-center justify-center mx-auto mb-4 floating-card">
            <span class="text-3xl">📋</span>
          </div>
          <h2 class="brand text-3xl font-bold mb-4">Affinez les détails</h2>
          <p class="text-gray-600">Renseignez les informations essentielles de votre projet</p>
        </div>
        
        <div class="space-y-6">
          <div>
            <label class="block text-sm font-semibold mb-2 flex items-center gap-2">
              <span class="text-xl">🏢</span> Quelle entreprise porte ce projet ?
            </label>
            <select name="id_fiche_entreprise" id="entreprise_select"
                    class="w-full px-5 py-4 border border-gray-300 rounded-2xl focus:outline-none focus:border-[#1D9E75] text-lg">
              <option value="">Sélectionnez une entreprise...</option>
              <?php foreach ($fiches as $f): ?>
                <option value="<?= $f['id'] ?>" <?= (isset($_POST['id_fiche_entreprise']) && $_POST['id_fiche_entreprise'] == $f['id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($f['nom']) ?> - <?= htmlspecialchars($f['categorie']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-semibold mb-2 flex items-center gap-2">
              <span class="text-xl">💰</span> Quel budget demandez-vous ?
            </label>
            <input type="number" name="budget_demande" id="budget_demande"
                   class="w-full px-5 py-4 border border-gray-300 rounded-2xl focus:outline-none focus:border-[#1D9E75] text-lg"
                   value="<?= htmlspecialchars($_POST['budget_demande'] ?? '') ?>"
                   placeholder="Ex: 50000">
            <div class="text-sm text-gray-500 mt-2">En TND - Soyez réaliste et précis</div>
          </div>
          
          <div>
            <label class="block text-sm font-semibold mb-2 flex items-center gap-2">
              <span class="text-xl">👥</span> Taille de votre équipe
            </label>
            <select name="taille_equipe" id="taille_equipe"
                    class="w-full px-5 py-4 border border-gray-300 rounded-2xl focus:outline-none focus:border-[#1D9E75] text-lg">
              <option value="">Sélectionnez...</option>
              <option value="1" <?= (isset($_POST['taille_equipe']) && $_POST['taille_equipe'] == '1') ? 'selected' : '' ?>>1 personne (solo)</option>
              <option value="2" <?= (isset($_POST['taille_equipe']) && $_POST['taille_equipe'] == '2') ? 'selected' : '' ?>>2 personnes</option>
              <option value="3" <?= (isset($_POST['taille_equipe']) && $_POST['taille_equipe'] == '3') ? 'selected' : '' ?>>3 personnes</option>
              <option value="4" <?= (isset($_POST['taille_equipe']) && $_POST['taille_equipe'] == '4') ? 'selected' : '' ?>>4 personnes</option>
              <option value="5" <?= (isset($_POST['taille_equipe']) && $_POST['taille_equipe'] == '5') ? 'selected' : '' ?>>5+ personnes</option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-semibold mb-2 flex items-center gap-2">
              <span class="text-xl">📊</span> Modèle économique
            </label>
            <select name="modele_economique" id="modele_economique"
                    class="w-full px-5 py-4 border border-gray-300 rounded-2xl focus:outline-none focus:border-[#1D9E75] text-lg">
              <option value="">Sélectionnez...</option>
              <option value="saas" <?= (isset($_POST['modele_economique']) && $_POST['modele_economique'] == 'saas') ? 'selected' : '' ?>>SaaS (Software as a Service)</option>
              <option value="marketplace" <?= (isset($_POST['modele_economique']) && $_POST['modele_economique'] == 'marketplace') ? 'selected' : '' ?>>Marketplace</option>
              <option value="freemium" <?= (isset($_POST['modele_economique']) && $_POST['modele_economique'] == 'freemium') ? 'selected' : '' ?>>Freemium</option>
              <option value="b2b" <?= (isset($_POST['modele_economique']) && $_POST['modele_economique'] == 'b2b') ? 'selected' : '' ?>>B2B</option>
              <option value="b2c" <?= (isset($_POST['modele_economique']) && $_POST['modele_economique'] == 'b2c') ? 'selected' : '' ?>>B2C</option>
              <option value="commission" <?= (isset($_POST['modele_economique']) && $_POST['modele_economique'] == 'commission') ? 'selected' : '' ?>>Commission</option>
            </select>
          </div>
        </div>
        
        <div class="flex justify-between mt-8">
          <button type="button" onclick="previousStep()" class="bg-gray-200 text-gray-700 px-8 py-4 rounded-2xl font-semibold hover:bg-gray-300 transition">
            ← Précédent
          </button>
          <button type="button" onclick="nextStep()" class="bg-gradient-to-r from-[#1D9E75] to-[#534AB7] text-white px-8 py-4 rounded-2xl font-semibold hover:shadow-lg transition">
            Continuer →
          </button>
        </div>
      </div>
    </div>

    <!-- STEP 3: Impact & Sustainability -->
    <div class="step-content" data-step="3">
      <div class="bg-white rounded-3xl shadow-xl p-10">
        <div class="text-center mb-8">
          <div class="w-20 h-20 bg-gradient-to-br from-green-500 to-emerald-500 rounded-full flex items-center justify-center mx-auto mb-4 floating-card">
            <span class="text-3xl">🌱</span>
          </div>
          <h2 class="brand text-3xl font-bold mb-4">Impact & Durabilité</h2>
          <p class="text-gray-600">Comment votre projet crée-t-il un impact positif ?</p>
        </div>
        
        <div class="space-y-6">
          <div>
            <label class="block text-sm font-semibold mb-2 flex items-center gap-2">
              <span class="text-xl">🌍</span> Taille du marché visé
            </label>
            <select name="taille_marche" id="taille_marche"
                    class="w-full px-5 py-4 border border-gray-300 rounded-2xl focus:outline-none focus:border-[#1D9E75] text-lg">
              <option value="">Sélectionnez...</option>
              <option value="niche" <?= (isset($_POST['taille_marche']) && $_POST['taille_marche'] == 'niche') ? 'selected' : '' ?>>Marché de niche</option>
              <option value="petit" <?= (isset($_POST['taille_marche']) && $_POST['taille_marche'] == 'petit') ? 'selected' : '' ?>>Petit marché local</option>
              <option value="moyen" <?= (isset($_POST['taille_marche']) && $_POST['taille_marche'] == 'moyen') ? 'selected' : '' ?>>Marché moyen (national)</option>
              <option value="grand" <?= (isset($_POST['taille_marche']) && $_POST['taille_marche'] == 'grand') ? 'selected' : '' ?>>Grand marché (international)</option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-semibold mb-2 flex items-center gap-2">
              <span class="text-xl">⚔️</span> Niveau de concurrence
            </label>
            <select name="niveau_concurrence" id="niveau_concurrence"
                    class="w-full px-5 py-4 border border-gray-300 rounded-2xl focus:outline-none focus:border-[#1D9E75] text-lg">
              <option value="">Sélectionnez...</option>
              <option value="faible" <?= (isset($_POST['niveau_concurrence']) && $_POST['niveau_concurrence'] == 'faible') ? 'selected' : '' ?>>Faible concurrence</option>
              <option value="moyen" <?= (isset($_POST['niveau_concurrence']) && $_POST['niveau_concurrence'] == 'moyen') ? 'selected' : '' ?>>Concurrence moyenne</option>
              <option value="fort" <?= (isset($_POST['niveau_concurrence']) && $_POST['niveau_concurrence'] == 'fort') ? 'selected' : '' ?>>Forte concurrence</option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-semibold mb-2 flex items-center gap-2">
              <span class="text-xl">💚</span> Coût initial estimé
            </label>
            <input type="number" name="cout_initial" id="cout_initial"
                   class="w-full px-5 py-4 border border-gray-300 rounded-2xl focus:outline-none focus:border-[#1D9E75] text-lg"
                   value="<?= htmlspecialchars($_POST['cout_initial'] ?? '') ?>"
                   placeholder="Ex: 10000">
            <div class="text-sm text-gray-500 mt-2">En TND - Investissement initial nécessaire</div>
          </div>
          
          <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl p-6 border border-green-200">
            <h3 class="font-semibold text-green-900 mb-3 flex items-center gap-2">
              <span class="text-xl">🎯</span> Objectifs de Développement Durable
            </h3>
            <p class="text-sm text-green-800 mb-4">Votre projet contribue-t-il aux ODD ? Sélectionnez tous ceux qui s'appliquent :</p>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
              <label class="flex items-center space-x-2 text-sm">
                <input type="checkbox" name="sdg_1" value="1" class="rounded">
                <span>Pas de pauvreté</span>
              </label>
              <label class="flex items-center space-x-2 text-sm">
                <input type="checkbox" name="sdg_2" value="2" class="rounded">
                <span>Faim zéro</span>
              </label>
              <label class="flex items-center space-x-2 text-sm">
                <input type="checkbox" name="sdg_3" value="3" class="rounded">
                <span>Bonne santé</span>
              </label>
              <label class="flex items-center space-x-2 text-sm">
                <input type="checkbox" name="sdg_4" value="4" class="rounded">
                <span>Éducation</span>
              </label>
              <label class="flex items-center space-x-2 text-sm">
                <input type="checkbox" name="sdg_5" value="5" class="rounded">
                <span>Égalité des sexes</span>
              </label>
              <label class="flex items-center space-x-2 text-sm">
                <input type="checkbox" name="sdg_6" value="6" class="rounded">
                <span>Eau propre</span>
              </label>
            </div>
          </div>
        </div>
        
        <div class="flex justify-between mt-8">
          <button type="button" onclick="previousStep()" class="bg-gray-200 text-gray-700 px-8 py-4 rounded-2xl font-semibold hover:bg-gray-300 transition">
            ← Précédent
          </button>
          <button type="button" onclick="nextStep()" class="bg-gradient-to-r from-[#1D9E75] to-[#534AB7] text-white px-8 py-4 rounded-2xl font-semibold hover:shadow-lg transition">
            Continuer →
          </button>
        </div>
      </div>
    </div>

    <!-- STEP 4: Team & Expertise -->
    <div class="step-content" data-step="4">
      <div class="bg-white rounded-3xl shadow-xl p-10">
        <div class="text-center mb-8">
          <div class="w-20 h-20 bg-gradient-to-br from-orange-500 to-red-500 rounded-full flex items-center justify-center mx-auto mb-4 floating-card">
            <span class="text-3xl">👥</span>
          </div>
          <h2 class="brand text-3xl font-bold mb-4">Équipe & Expertise</h2>
          <p class="text-gray-600">Qui porte ce projet et quelles sont vos compétences ?</p>
        </div>
        
        <div class="space-y-6">
          <div class="bg-gradient-to-r from-orange-50 to-red-50 rounded-2xl p-6 border border-orange-200">
            <h3 class="font-semibold text-orange-900 mb-4">Vos forces (sélectionnez jusqu'à 3)</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
              <label class="flex items-center space-x-2 text-sm">
                <input type="checkbox" name="expertise[]" value="tech" class="rounded">
                <span>🔧 Tech & Développement</span>
              </label>
              <label class="flex items-center space-x-2 text-sm">
                <input type="checkbox" name="expertise[]" value="business" class="rounded">
                <span>💼 Business & Stratégie</span>
              </label>
              <label class="flex items-center space-x-2 text-sm">
                <input type="checkbox" name="expertise[]" value="marketing" class="rounded">
                <span>📱 Marketing & Vente</span>
              </label>
              <label class="flex items-center space-x-2 text-sm">
                <input type="checkbox" name="expertise[]" value="finance" class="rounded">
                <span>💰 Finance & Comptabilité</span>
              </label>
              <label class="flex items-center space-x-2 text-sm">
                <input type="checkbox" name="expertise[]" value="design" class="rounded">
                <span>🎨 Design & UX</span>
              </label>
              <label class="flex items-center space-x-2 text-sm">
                <input type="checkbox" name="expertise[]" value="data" class="rounded">
                <span>📊 Data & Analytics</span>
              </label>
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-semibold mb-2 flex items-center gap-2">
              <span class="text-xl">🎓</span> Formation ou expérience pertinente
            </label>
            <textarea name="experience" id="experience" rows="3"
                      class="w-full px-5 py-4 border border-gray-300 rounded-2xl focus:outline-none focus:border-[#1D9E75]"
                      placeholder="Décrivez votre formation ou expérience pertinente pour ce projet..."><?= htmlspecialchars($_POST['experience'] ?? '') ?></textarea>
          </div>
          
          <div>
            <label class="block text-sm font-semibold mb-2 flex items-center gap-2">
              <span class="text-xl">🤝</span> Cherchez-vous des mentors ?
            </label>
            <div class="flex gap-4">
              <label class="flex items-center space-x-2">
                <input type="radio" name="mentor_search" value="yes" class="rounded">
                <span>Oui, j'aimerais être accompagné</span>
              </label>
              <label class="flex items-center space-x-2">
                <input type="radio" name="mentor_search" value="no" class="rounded">
                <span>Non pour le moment</span>
              </label>
              <label class="flex items-center space-x-2">
                <input type="radio" name="mentor_search" value="maybe" class="rounded">
                <span>Peut-être plus tard</span>
              </label>
            </div>
          </div>
        </div>
        
        <div class="flex justify-between mt-8">
          <button type="button" onclick="previousStep()" class="bg-gray-200 text-gray-700 px-8 py-4 rounded-2xl font-semibold hover:bg-gray-300 transition">
            ← Précédent
          </button>
          <button type="button" onclick="nextStep()" class="bg-gradient-to-r from-[#1D9E75] to-[#534AB7] text-white px-8 py-4 rounded-2xl font-semibold hover:shadow-lg transition">
            Continuer →
          </button>
        </div>
      </div>
    </div>

    <!-- STEP 5: Finalisation -->
    <div class="step-content" data-step="5">
      <div class="bg-white rounded-3xl shadow-xl p-10">
        <div class="text-center mb-8">
          <div class="w-20 h-20 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center mx-auto mb-4 floating-card">
            <span class="text-3xl">🎉</span>
          </div>
          <h2 class="brand text-3xl font-bold mb-4">Finalisation</h2>
          <p class="text-gray-600">Revoyez votre projet et lancez votre aventure !</p>
        </div>
        
        <div class="space-y-6">
          <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-2xl p-6 border border-purple-200">
            <h3 class="font-semibold text-purple-900 mb-4">Résumé de votre projet</h3>
            <div id="project-summary" class="space-y-3 text-sm">
              <!-- Summary will be populated by JavaScript -->
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-semibold mb-2 flex items-center gap-2">
              <span class="text-xl">📝</span> Notes additionnelles
            </label>
            <textarea name="notes" id="notes" rows="3"
                      class="w-full px-5 py-4 border border-gray-300 rounded-2xl focus:outline-none focus:border-[#1D9E75]"
                      placeholder="Informations complémentaires que vous souhaitez partager..."><?= htmlspecialchars($_POST['notes'] ?? '') ?></textarea>
          </div>
          
          <div class="bg-green-50 border border-green-200 rounded-2xl p-6">
            <h3 class="font-semibold text-green-900 mb-3 flex items-center gap-2">
              <span class="text-xl">✨</span> Prochaines étapes
            </h3>
            <ul class="space-y-2 text-sm text-green-800">
              <li>• Votre projet sera analysé par notre IA</li>
              <li>• Vous recevrez des scores et recommandations</li>
              <li>• Des mentors pourront vous contacter</li>
              <li>• Accès au pitch deck et PDF personnalisés</li>
            </ul>
          </div>
          
          <div class="text-center">
            <p class="text-sm text-gray-600 mb-4">En soumettant ce projet, vous acceptez nos conditions d'utilisation</p>
            <button type="submit" class="bg-gradient-to-r from-green-500 to-emerald-500 text-white px-12 py-4 rounded-2xl font-bold text-lg hover:shadow-lg transition pulse-animation">
              🚀 Lancer mon projet
            </button>
          </div>
        </div>
        
        <div class="flex justify-between mt-8">
          <button type="button" onclick="previousStep()" class="bg-gray-200 text-gray-700 px-8 py-4 rounded-2xl font-semibold hover:bg-gray-300 transition">
            ← Précédent
          </button>
          <div></div>
        </div>
      </div>
    </div>
  </form>
</div>

<script>
let currentStep = 1;
const totalSteps = 5;

function updateProgress() {
    const progress = (currentStep / totalSteps) * 100;
    document.querySelector('.progress-bar').style.width = progress + '%';
    
    // Update step indicators
    document.querySelectorAll('.step-indicator').forEach((indicator, index) => {
        const stepNum = index + 1;
        indicator.classList.remove('active', 'completed');
        
        if (stepNum < currentStep) {
            indicator.classList.add('completed');
            indicator.innerHTML = '✓';
        } else if (stepNum === currentStep) {
            indicator.classList.add('active');
        } else {
            indicator.innerHTML = stepNum;
        }
    });
    
    // Show/hide step content
    document.querySelectorAll('.step-content').forEach(content => {
        content.classList.remove('active');
    });
    document.querySelector(`.step-content[data-step="${currentStep}"]`).classList.add('active');
}

function nextStep() {
    if (currentStep < totalSteps) {
        // Validate current step
        if (validateStep(currentStep)) {
            currentStep++;
            updateProgress();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }
}

function previousStep() {
    if (currentStep > 1) {
        currentStep--;
        updateProgress();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

function validateStep(step) {
    let isValid = true;
    
    if (step === 1) {
        const titre = document.getElementById('projet_titre').value.trim();
        const description = document.getElementById('projet_description').value.trim();
        
        if (!titre) {
            alert('Veuillez donner un nom à votre projet');
            isValid = false;
        } else if (titre.length < 3) {
            alert('Le nom du projet doit contenir au moins 3 caractères');
            isValid = false;
        } else if (!description) {
            alert('Veuillez décrire votre projet');
            isValid = false;
        } else if (description.length < 20) {
            alert('La description doit contenir au moins 20 caractères');
            isValid = false;
        }
    } else if (step === 2) {
        const entreprise = document.getElementById('entreprise_select').value;
        const budget = document.getElementById('budget_demande').value;
        const equipe = document.getElementById('taille_equipe').value;
        
        if (!entreprise) {
            alert('Veuillez sélectionner une entreprise');
            isValid = false;
        } else if (!budget || budget < 0) {
            alert('Veuillez indiquer un budget valide');
            isValid = false;
        } else if (!equipe) {
            alert('Veuillez indiquer la taille de votre équipe');
            isValid = false;
        }
    }
    
    return isValid;
}

function updateSummary() {
    const titre = document.getElementById('projet_titre').value || 'Non spécifié';
    const description = document.getElementById('projet_description').value || 'Non spécifiée';
    const entreprise = document.getElementById('entreprise_select').options[document.getElementById('entreprise_select').selectedIndex]?.text || 'Non spécifiée';
    const budget = document.getElementById('budget_demande').value || 'Non spécifié';
    const equipe = document.getElementById('taille_equipe').options[document.getElementById('taille_equipe').selectedIndex]?.text || 'Non spécifiée';
    
    const summary = `
        <div class="grid grid-cols-2 gap-4">
            <div><strong>Titre:</strong> ${titre}</div>
            <div><strong>Entreprise:</strong> ${entreprise}</div>
            <div><strong>Budget:</strong> ${budget} TND</div>
            <div><strong>Équipe:</strong> ${equipe}</div>
        </div>
        <div class="mt-3">
            <strong>Description:</strong><br>
            ${description.substring(0, 200)}${description.length > 200 ? '...' : ''}
        </div>
    `;
    
    document.getElementById('project-summary').innerHTML = summary;
}

// Character counter
document.getElementById('projet_description')?.addEventListener('input', function() {
    const count = this.value.length;
    document.getElementById('char-count').textContent = count;
    
    if (count > 500) {
        document.getElementById('char-count').style.color = '#ef4444';
    } else if (count > 300) {
        document.getElementById('char-count').style.color = '#f59e0b';
    } else {
        document.getElementById('char-count').style.color = '#6b7280';
    }
});

// Update summary when reaching final step
document.addEventListener('DOMContentLoaded', function() {
    updateProgress();
    
    // Add input listeners to update summary
    const inputs = ['projet_titre', 'projet_description', 'entreprise_select', 'budget_demande', 'taille_equipe'];
    inputs.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.addEventListener('change', updateSummary);
            element.addEventListener('input', updateSummary);
        }
    });
    
    updateSummary();
});
</script>

</body>
</html>
