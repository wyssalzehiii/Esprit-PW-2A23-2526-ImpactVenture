<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ImpactVenture – Proposer un thème</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet"/>
  <style>
    body{font-family:'Inter',sans-serif;background:#F0F4F1;}
    h1,h2,h3,.brand{font-family:'Space Grotesk',sans-serif;}
    .field-input{transition:border-color .2s,box-shadow .2s;}
    .field-input:focus{outline:none;border-color:#1D9E75;box-shadow:0 0 0 3px rgba(29,158,117,.15);}
    .field-input.error{border-color:#EF4444;box-shadow:0 0 0 3px rgba(239,68,68,.1);}
    .btn-primary{background:#1D9E75;color:#fff;transition:background .2s;}
    .btn-primary:hover{background:#15795A;}
    .step-active{background:#1D9E75;color:#fff;}
    .step-todo{background:#E5E7EB;color:#6B7280;}
    @keyframes fadeUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:none}}
    .fade-up{animation:fadeUp .45s ease both;}
  </style>
</head>
<body class="min-h-screen">

<nav class="bg-white border-b border-gray-100 sticky top-0 z-50 shadow-sm">
  <div class="max-w-5xl mx-auto px-6 flex items-center justify-between h-16">
    <a href="index.php?action=list" class="brand text-xl font-bold flex items-center gap-2">
      <span style="color:#1D9E75">Impact</span><span style="color:#534AB7">Venture</span>
    </a>
    <a href="index.php?action=list" class="text-sm text-gray-500 hover:text-[#1D9E75] flex items-center gap-1">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
      Retour aux thèmes
    </a>
  </div>
</nav>

<div class="max-w-3xl mx-auto px-6 py-10">

  <div class="mb-8 fade-up">
    <p class="text-xs font-semibold uppercase tracking-widest mb-1" style="color:#1D9E75">Smart Theme Engine</p>
    <h1 class="brand text-3xl font-bold text-gray-900 mb-2">Proposer un nouveau thème</h1>
    <p class="text-gray-500 text-sm">Notre IA analysera et calculera automatiquement le score d'impact.</p>
  </div>

  <!-- Steps -->
  <div class="flex items-center gap-2 mb-8 fade-up">
    <div class="step-active w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold">1</div>
    <div class="h-px flex-1 bg-gray-200"></div>
    <div class="step-todo w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold">2</div>
    <div class="h-px flex-1 bg-gray-200"></div>
    <div class="step-todo w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold">3</div>
    <span class="text-xs text-gray-400 ml-2">Infos → Vérification IA → Confirmation</span>
  </div>

  <!-- Erreurs PHP -->
  <?php if (!empty($errors)): ?>
  <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4 fade-up">
    <p class="text-sm font-bold text-red-700 mb-2">Veuillez corriger les erreurs :</p>
    <ul class="list-disc list-inside space-y-1">
      <?php foreach ($errors as $e): ?><li class="text-sm text-red-600"><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
    </ul>
  </div>
  <?php endif; ?>

  <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 fade-up">
    <!-- ACTION = PHP controller, pas JavaScript -->
    <form method="POST" action="index.php?action=store">

      <div class="mb-6">
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nom du thème <span class="text-red-400">*</span></label>
        <input type="text" name="nom"
               value="<?= htmlspecialchars($_POST['nom']??'') ?>"
               placeholder="Ex: Économie circulaire, Santé digitale…"
               class="field-input w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
        <p class="text-xs text-gray-400 mt-1">Minimum 3 caractères, maximum 150</p>
      </div>

      <div class="mb-6">
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Description <span class="text-red-400">*</span></label>
        <textarea name="description" id="desc" rows="3"
                  placeholder="Décrivez brièvement ce thème et les types de projets qu'il regroupe…"
                  class="field-input w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm resize-none"><?= htmlspecialchars($_POST['description']??'') ?></textarea>
        <div class="flex justify-between mt-1">
          <p class="text-xs text-gray-400">Minimum 20 caractères</p>
          <p id="char-count" class="text-xs text-gray-400">0 / 300</p>
        </div>
      </div>

      <div class="mb-6">
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Catégorie <span class="text-red-400">*</span></label>
        <select name="categorie" id="cat-select" class="field-input w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white">
          <option value="">— Sélectionner une catégorie —</option>
          <?php $cats=['tech'=>'Tech & IA','digital'=>'Économie digitale','energie renouvelable'=>'Énergie renouvelable','agriculture durable'=>'Agriculture durable','economie circulaire'=>'Économie circulaire','mobilite verte'=>'Mobilité verte','entrepreneuriat'=>'Entrepreneuriat','autre'=>'Autre'];
          $curCat=$_POST['categorie']??'';
          foreach($cats as $v=>$l): ?>
          <option value="<?=$v?>" <?=$curCat===$v?'selected':''?>><?=$l?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-6">
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Mots-clés <span class="text-red-400">*</span></label>
        <p class="text-xs text-gray-400 mb-2">Séparez par des virgules</p>
        <input type="text" name="mots_cles"
               value="<?= htmlspecialchars($_POST['mots_cles']??'') ?>"
               placeholder="Ex: recyclage, économie, déchets…"
               class="field-input w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
      </div>

      <div class="mb-6">
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Score Green (0-100) <span class="text-red-400">*</span></label>
        <div class="flex items-center gap-4">
          <!-- Score ring SVG preview -->
          <svg width="60" height="60" viewBox="0 0 90 90" id="score-svg">
            <circle cx="45" cy="45" r="38" fill="none" stroke="#D1FAE5" stroke-width="9"/>
            <circle cx="45" cy="45" r="38" fill="none" stroke="#1D9E75" stroke-width="9"
              stroke-dasharray="239" stroke-dashoffset="239" stroke-linecap="round"
              transform="rotate(-90 45 45)" id="score-arc"/>
            <text x="45" y="50" text-anchor="middle" font-size="16" font-weight="700"
              fill="#1D9E75" font-family="Space Grotesk" id="score-txt">0</text>
          </svg>
          <div class="flex-1">
            <input type="number" name="score_green" id="score-input" min="0" max="100"
                   value="<?= htmlspecialchars($_POST['score_green']??'50') ?>"
                   class="field-input w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm mb-2">
            <div class="bg-gray-100 rounded-full h-2">
              <div id="score-bar" class="h-2 rounded-full transition-all" style="background:#1D9E75;width:50%"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Green preview box -->
      <div class="mb-6 p-4 rounded-xl border" style="background:#F0F9F5;border-color:#C6EDD9;">
        <div class="flex items-center gap-3">
          <svg class="w-5 h-5 flex-shrink-0" style="color:#1D9E75" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zM9 12l2 2 4-4"/></svg>
          <div>
            <p class="text-sm font-semibold" style="color:#0F6E56">Score Green estimé par l'IA</p>
            <p class="text-xs" style="color:#1D9E75">Calculé automatiquement selon la catégorie</p>
          </div>
        </div>
      </div>

      <!-- Warning -->
      <div class="mb-8 p-4 bg-amber-50 rounded-xl border border-amber-200 flex gap-3">
        <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
        <div>
          <p class="text-xs font-semibold text-amber-800">Validation côté serveur (PHP)</p>
          <p class="text-xs text-amber-700 mt-0.5">Tous les champs marqués * sont obligatoires. La validation est effectuée en PHP, pas en HTML5.</p>
        </div>
      </div>

      <div class="flex gap-3">
        <a href="index.php?action=list" class="flex-1 text-center py-3 rounded-xl text-sm font-semibold border border-gray-200 text-gray-600 hover:bg-gray-50">Annuler</a>
        <button type="submit" class="btn-primary flex-grow py-3 rounded-xl text-sm font-bold flex items-center justify-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
          Soumettre le thème
        </button>
      </div>
    </form>
  </div>
</div>

<script>
const desc=document.getElementById('desc');
const cnt=document.getElementById('char-count');
desc.addEventListener('input',()=>cnt.textContent=desc.value.length+' / 300');
cnt.textContent=desc.value.length+' / 300';

const inp=document.getElementById('score-input');
const arc=document.getElementById('score-arc');
const txt=document.getElementById('score-txt');
const bar=document.getElementById('score-bar');
function updateScore(){
  const v=Math.min(100,Math.max(0,parseInt(inp.value)||0));
  const offset=239-(239*v/100);
  arc.setAttribute('stroke-dashoffset',offset);
  txt.textContent=v;
  bar.style.width=v+'%';
  const col=v>=70?'#1D9E75':v>=40?'#EF9F27':'#E24B4A';
  arc.setAttribute('stroke',col);
  txt.setAttribute('fill',col);
  bar.style.background=col;
}
inp.addEventListener('input',updateScore);
updateScore();

const scores={'energie renouvelable':90,'agriculture durable':88,'economie circulaire':75,'mobilite verte':75,'tech':60,'digital':55,'entrepreneuriat':50,'autre':40};
document.getElementById('cat-select').addEventListener('change',function(){
  if(scores[this.value]){inp.value=scores[this.value];updateScore();}
});
</script>
</body>
</html>
