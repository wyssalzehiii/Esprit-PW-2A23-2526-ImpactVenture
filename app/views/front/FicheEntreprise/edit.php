<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ImpactVenture – Modifier l'Entreprise</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
    body{font-family:'Inter',sans-serif;background:#F0F4F1;}
    h1,.brand{font-family:'Space Grotesk',sans-serif;}
    .field-input{transition:border-color .2s,box-shadow .2s;border:1.5px solid #E5E7EB;border-radius:12px;padding:10px 14px;width:100%;font-size:.875rem;background:#fff;}
    .field-input:focus{outline:none;border-color:#1D9E75;box-shadow:0 0 0 3px rgba(29,158,117,.15);}
    .field-input.error{border-color:#EF4444;}
    .btn-green{background:#1D9E75;color:#fff;border:none;border-radius:12px;padding:11px 24px;font-weight:600;font-size:.875rem;cursor:pointer;transition:all .2s;}
    .btn-green:hover{background:#15795A;}
    .section-card{background:#fff;border-radius:20px;padding:28px;box-shadow:0 2px 16px rgba(0,0,0,.06);border:1px solid #f0f0f0;}
    select.field-input{appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%236B7280' stroke-width='1.5' fill='none'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 14px center;}
    .required-star{color:#EF4444;}
    @keyframes fadeUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:none}} .fade-up{animation:fadeUp .4s ease both;}
  </style>
</head>
<body>
<nav class="bg-white border-b border-gray-100 sticky top-0 z-50 shadow-sm">
  <div class="max-w-5xl mx-auto px-6 flex items-center justify-between h-16">
    <a href="index.php?action=entreprise_list" class="brand text-xl font-bold flex items-center gap-2">
      <svg width="30" height="30" viewBox="0 0 32 32" fill="none"><rect width="32" height="32" rx="8" fill="url(#el)"/><path d="M16 8l2.5 5 5.5.8-4 3.9.9 5.3L16 20.5l-4.9 2.5.9-5.3-4-3.9 5.5-.8z" fill="#fff"/><defs><linearGradient id="el" x1="0" y1="0" x2="32" y2="32" gradientUnits="userSpaceOnUse"><stop stop-color="#1D9E75"/><stop offset="1" stop-color="#534AB7"/></linearGradient></defs></svg>
      <span style="color:#1D9E75">Impact</span><span style="color:#534AB7">Venture</span>
    </a>
    <a href="index.php?action=entreprise_list" class="text-sm text-gray-500 hover:text-[#1D9E75] flex items-center gap-1">
      <i class="fas fa-arrow-left text-xs"></i> Retour
    </a>
  </div>
</nav>

<div class="max-w-4xl mx-auto px-6 py-10">
  <div class="mb-6 fade-up">
    <h1 class="brand text-2xl font-bold text-gray-900">Modifier l'Entreprise</h1>
    <p class="text-gray-500 text-sm"><?= htmlspecialchars($entreprise['nom']) ?></p>
  </div>

  <?php if (!empty($errors)): ?>
  <div class="mb-6 bg-red-50 border border-red-200 rounded-2xl p-5 fade-up">
    <p class="text-sm font-bold text-red-700 mb-2">Erreurs :</p>
    <ul class="space-y-1">
      <?php foreach ($errors as $e): ?><li class="text-sm text-red-600 flex items-start gap-2"><i class="fas fa-times-circle text-xs mt-0.5"></i><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
    </ul>
  </div>
  <?php endif; ?>

  <form method="POST" action="index.php?action=entreprise_update&id=<?= $entreprise['id'] ?>" id="editForm">
    <div class="section-card mb-6 fade-up">
      <h2 class="brand text-lg font-bold text-gray-900 mb-5">Identité</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="md:col-span-2">
          <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nom <span class="required-star">*</span></label>
          <input type="text" name="nom" id="nom" value="<?= htmlspecialchars($_POST['nom']??$entreprise['nom']) ?>" class="field-input" maxlength="150">
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1.5">Catégorie <span class="required-star">*</span></label>
          <select name="categorie" class="field-input">
            <?php $cats = ['tech'=>'💻 Tech & IA','digital'=>'🌐 Digital','energie renouvelable'=>'☀️ Énergie Renouvelable','agriculture durable'=>'🌱 Agriculture Durable','economie circulaire'=>'♻️ Économie Circulaire','mobilite verte'=>'🚲 Mobilité Verte','entrepreneuriat'=>'🚀 Entrepreneuriat','sante'=>'🏥 Santé','education'=>'📚 Éducation','autre'=>'✨ Autre'];
            foreach ($cats as $val=>$label): ?>
              <option value="<?= $val ?>" <?= ($_POST['categorie']??$entreprise['categorie'])===$val?'selected':'' ?>><?= $label ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1.5">Secteur</label>
          <input type="text" name="secteur" value="<?= htmlspecialchars($_POST['secteur']??$entreprise['secteur']) ?>" class="field-input">
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1.5">Ville</label>
          <input type="text" name="ville" value="<?= htmlspecialchars($_POST['ville']??$entreprise['ville']) ?>" class="field-input">
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1.5">Année de création</label>
          <input type="text" name="annee_creation" id="annee_creation" value="<?= htmlspecialchars($_POST['annee_creation']??$entreprise['annee_creation']) ?>" class="field-input" maxlength="4">
          <p id="annee_err" class="text-xs text-red-500 mt-1 hidden">Année invalide</p>
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nombre d'employés</label>
          <input type="text" name="nb_employes" id="nb_employes" value="<?= htmlspecialchars($_POST['nb_employes']??$entreprise['nb_employes']) ?>" class="field-input">
          <p id="emp_err" class="text-xs text-red-500 mt-1 hidden">Nombre invalide</p>
        </div>
      </div>
    </div>

    <div class="section-card mb-6 fade-up">
      <div class="flex items-center justify-between mb-4">
        <h2 class="brand text-lg font-bold text-gray-900">Description <span class="required-star">*</span></h2>
        <div class="flex items-center gap-2">
          <span class="text-xs text-gray-400">Dicter :</span>
          <button type="button" id="voiceBtn" class="w-9 h-9 rounded-full flex items-center justify-center text-white border-none cursor-pointer" style="background:#534AB7" title="Saisie vocale">
            <i class="fas fa-microphone text-sm"></i>
          </button>
        </div>
      </div>
      <textarea name="description" id="description" rows="5" class="field-input" style="resize:vertical;"><?= htmlspecialchars($_POST['description']??$entreprise['description']) ?></textarea>
      <p id="descErr" class="text-xs text-red-500 mt-1 hidden">Minimum 30 caractères</p>
    </div>

    <div class="section-card mb-6 fade-up">
      <h2 class="brand text-lg font-bold text-gray-900 mb-5">Contact & Logo</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
          <input type="text" name="email" id="email" value="<?= htmlspecialchars($_POST['email']??$entreprise['email']) ?>" class="field-input">
          <p id="email_err" class="text-xs text-red-500 mt-1 hidden">Email invalide</p>
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1.5">Téléphone</label>
          <input type="text" name="telephone" id="telephone" value="<?= htmlspecialchars($_POST['telephone']??$entreprise['telephone']) ?>" class="field-input">
          <p id="tel_err" class="text-xs text-red-500 mt-1 hidden">Téléphone invalide</p>
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1.5">Site web</label>
          <input type="text" name="site_web" id="site_web" value="<?= htmlspecialchars($_POST['site_web']??$entreprise['site_web']) ?>" class="field-input">
          <p id="site_err" class="text-xs text-red-500 mt-1 hidden">URL invalide</p>
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1.5">Mots-clés</label>
          <input type="text" name="mots_cles" value="<?= htmlspecialchars($_POST['mots_cles']??$entreprise['mots_cles']) ?>" class="field-input">
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-semibold text-gray-700 mb-1.5">URL du logo</label>
          <input type="text" name="logo_url" value="<?= htmlspecialchars($_POST['logo_url']??$entreprise['logo_url']) ?>" class="field-input" placeholder="https://...">
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-semibold text-gray-700 mb-1.5">Score Green <span class="required-star">*</span></label>
          <div class="flex items-center gap-4">
            <input type="range" id="scoreRange" min="0" max="100" value="<?= $_POST['score_green']??$entreprise['score_green'] ?>"
                   oninput="document.getElementById('scoreNum').value=this.value" style="flex:1;accent-color:#1D9E75;">
            <input type="text" name="score_green" id="scoreNum" value="<?= htmlspecialchars($_POST['score_green']??$entreprise['score_green']) ?>"
                   class="field-input w-20 text-center font-bold" style="color:#1D9E75;"
                   oninput="document.getElementById('scoreRange').value=this.value">
          </div>
          <p id="score_err" class="text-xs text-red-500 mt-1 hidden">Score entre 0 et 100</p>
        </div>
      </div>
    </div>

    <div class="flex items-center justify-between fade-up">
      <a href="index.php?action=entreprise_list" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
        <i class="fas fa-arrow-left text-xs"></i> Annuler
      </a>
      <button type="submit" class="btn-green flex items-center gap-2">
        <i class="fas fa-save"></i> Enregistrer les modifications
      </button>
    </div>
  </form>
</div>

<script>
// Validation live
document.getElementById('email').addEventListener('blur', function() {
  const err = document.getElementById('email_err');
  const ok = !this.value || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.value);
  this.classList.toggle('error', !ok); err.classList.toggle('hidden', ok);
});
document.getElementById('site_web').addEventListener('blur', function() {
  const err = document.getElementById('site_err');
  const ok = !this.value || /^https?:\/\/.+/.test(this.value);
  this.classList.toggle('error', !ok); err.classList.toggle('hidden', ok);
});
document.getElementById('telephone').addEventListener('blur', function() {
  const err = document.getElementById('tel_err');
  const ok = !this.value || /^[\+\d\s\-\(\)]{6,20}$/.test(this.value);
  this.classList.toggle('error', !ok); err.classList.toggle('hidden', ok);
});
document.getElementById('annee_creation').addEventListener('blur', function() {
  const err = document.getElementById('annee_err');
  const ok = !this.value || (parseInt(this.value)>=1900 && parseInt(this.value)<=new Date().getFullYear());
  this.classList.toggle('error', !ok); err.classList.toggle('hidden', ok);
});

document.getElementById('editForm').addEventListener('submit', function(e) {
  const nom = document.getElementById('nom').value.trim();
  const desc = document.getElementById('description').value.trim();
  const score = parseInt(document.getElementById('scoreNum').value);
  let ok = true;
  if (nom.length < 2) { document.getElementById('nom').classList.add('error'); ok = false; }
  if (desc.length < 30) { document.getElementById('description').classList.add('error'); document.getElementById('descErr').classList.remove('hidden'); ok = false; }
  if (isNaN(score)||score<0||score>100) { document.getElementById('score_err').classList.remove('hidden'); ok = false; }
  if (!ok) e.preventDefault();
});

// Voice input
const voiceBtn = document.getElementById('voiceBtn');
let recognition, isRec = false;
if ('SpeechRecognition' in window || 'webkitSpeechRecognition' in window) {
  const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
  recognition = new SR(); recognition.lang='fr-FR'; recognition.continuous=true; recognition.interimResults=true;
  let final = document.getElementById('description').value;
  recognition.onresult = function(ev) {
    let interim='';
    for(let i=ev.resultIndex;i<ev.results.length;i++){if(ev.results[i].isFinal)final+=ev.results[i][0].transcript+' ';else interim+=ev.results[i][0].transcript;}
    document.getElementById('description').value=final+interim;
  };
  recognition.onend = () => { if(isRec) recognition.start(); };
  voiceBtn.addEventListener('click', function(){
    if(!isRec){isRec=true;this.style.background='#EF4444';recognition.start();}
    else{isRec=false;this.style.background='#534AB7';recognition.stop();}
  });
}
</script>
</body>
</html>