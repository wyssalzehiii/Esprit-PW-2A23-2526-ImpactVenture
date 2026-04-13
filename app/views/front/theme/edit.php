<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ImpactVenture – Modifier un thème</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet"/>
  <style>
    body{font-family:'Inter',sans-serif;background:#F0F4F1;}
    h1,h2,h3,.brand{font-family:'Space Grotesk',sans-serif;}
    .field-input{transition:border-color .2s,box-shadow .2s;}
    .field-input:focus{outline:none;border-color:#1D9E75;box-shadow:0 0 0 3px rgba(29,158,117,.15);}
    .field-input.error{border-color:#EF4444;}
    .btn-primary{background:#1D9E75;color:#fff;transition:background .2s;}
    .btn-primary:hover{background:#15795A;}
    @keyframes fadeUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:none}}
    .fade-up{animation:fadeUp .4s ease both;}
  </style>
</head>
<body class="min-h-screen">
<nav class="bg-white border-b border-gray-100 sticky top-0 z-50 shadow-sm">
  <div class="max-w-5xl mx-auto px-6 flex items-center justify-between h-16">
    <a href="index.php?action=list" class="brand text-xl font-bold">
      <span style="color:#1D9E75">Impact</span><span style="color:#534AB7">Venture</span>
    </a>
    <a href="index.php?action=list" class="text-sm text-gray-500 hover:text-[#1D9E75] flex items-center gap-1">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
      Retour
    </a>
  </div>
</nav>

<div class="max-w-3xl mx-auto px-6 py-10">
  <div class="mb-6 fade-up">
    <p class="text-xs font-semibold uppercase tracking-widest mb-1" style="color:#1D9E75">Modifier</p>
    <h1 class="brand text-2xl font-bold text-gray-900"><?= htmlspecialchars($theme['nom']) ?></h1>
  </div>

  <?php if(!empty($errors)): ?>
  <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
    <p class="text-sm font-bold text-red-700 mb-2">Erreurs :</p>
    <ul class="list-disc list-inside space-y-1">
      <?php foreach($errors as $e): ?><li class="text-sm text-red-600"><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
    </ul>
  </div>
  <?php endif; ?>

  <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 fade-up">
    <form method="POST" action="index.php?action=update&id=<?= $theme['id'] ?>">

      <div class="mb-5">
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nom *</label>
        <input type="text" name="nom" class="field-input w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm"
               value="<?= htmlspecialchars($_POST['nom']??$theme['nom']) ?>">
      </div>

      <div class="mb-5">
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Description *</label>
        <textarea name="description" rows="3" class="field-input w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm resize-none"><?= htmlspecialchars($_POST['description']??$theme['description']) ?></textarea>
      </div>

      <div class="mb-5">
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Catégorie *</label>
        <select name="categorie" class="field-input w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white">
          <?php $cats=['tech'=>'Tech & IA','digital'=>'Économie digitale','energie renouvelable'=>'Énergie renouvelable','agriculture durable'=>'Agriculture durable','economie circulaire'=>'Économie circulaire','mobilite verte'=>'Mobilité verte','entrepreneuriat'=>'Entrepreneuriat','autre'=>'Autre'];
          $cur=$_POST['categorie']??$theme['categorie'];
          foreach($cats as $v=>$l): ?>
          <option value="<?=$v?>" <?=$cur===$v?'selected':''?>><?=$l?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-5">
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Mots-clés *</label>
        <input type="text" name="mots_cles" class="field-input w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm"
               value="<?= htmlspecialchars($_POST['mots_cles']??$theme['mots_cles']) ?>"
               placeholder="Séparés par des virgules">
      </div>

      <div class="mb-8">
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Score Green (0-100) *</label>
        <div class="flex items-center gap-4">
          <input type="number" name="score_green" id="si" min="0" max="100"
                 value="<?= htmlspecialchars($_POST['score_green']??$theme['score_green']) ?>"
                 class="field-input w-28 border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
          <div class="flex-1 bg-gray-100 rounded-full h-2">
            <div id="sb" class="h-2 rounded-full transition-all" style="background:#1D9E75;width:<?= $theme['score_green'] ?>%"></div>
          </div>
          <span id="sv" class="text-sm font-bold" style="color:#1D9E75"><?= $theme['score_green'] ?>%</span>
        </div>
      </div>

      <div class="flex gap-3">
        <a href="index.php?action=list" class="flex-1 text-center py-3 rounded-xl text-sm font-semibold border border-gray-200 text-gray-600 hover:bg-gray-50">Annuler</a>
        <button type="submit" class="btn-primary flex-grow py-3 rounded-xl text-sm font-bold">Enregistrer</button>
      </div>
    </form>
  </div>
</div>

<script>
const si=document.getElementById('si'),sb=document.getElementById('sb'),sv=document.getElementById('sv');
function u(){const v=Math.min(100,Math.max(0,parseInt(si.value)||0));sb.style.width=v+'%';sv.textContent=v+'%';const c=v>=70?'#1D9E75':v>=40?'#EF9F27':'#E24B4A';sb.style.background=c;sv.style.color=c;}
si.addEventListener('input',u);u();
</script>
</body></html>
