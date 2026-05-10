<?php 
$projet = $projet ?? []; 
$sdgs = $sdgs ?? [];
$mentors = $mentors ?? [];
$investors = $investors ?? [];
$sentimentData = $sentimentData ?? null;
$pitchScore = $pitchScore ?? ($projet['pitch_score'] ?? 0);
$scoreGreen = $scoreGreen ?? ($projet['score_green'] ?? 0);
$viabilityScore = isset($viabilityScore) ? $viabilityScore : ($projet['viability_score'] ?? 0);
$nextStepRecs = $nextStepRecs ?? [];
$scoreHistory = $scoreHistory ?? [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($projet['titre'] ?? '') ?> - ImpactVenture</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    body{font-family:'Inter',sans-serif; background-color: #F8FAFC;} 
    .brand{font-family:'Space Grotesk',sans-serif;} 
    .card{background: #fff; border-radius: 1.5rem; padding: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03); transition: transform 0.2s;}
    .card:hover{transform: translateY(-2px);}
    
    /* Tab styling */
    .tab-button{transition: all 0.3s ease; border-bottom: 3px solid transparent;}
    .tab-button.active{border-bottom-color: #1D9E75; color: #1D9E75; background: #f0fdf4;}
    .tab-content{display: none;}
    .tab-content.active{display: block;}
    
    /* Score animations */
    .score-counter{display: inline-block; animation: countUp 1.5s ease-out;}
    @keyframes countUp{from{opacity:0;transform:scale(0.5);}to{opacity:1;transform:scale(1);}}
    
    /* Badge tooltips */
    .badge-locked{position:relative;cursor:help;}
    .badge-locked:hover::after{content:attr(data-tooltip);position:absolute;bottom:100%;left:50%;transform:translateX(-50%);background:#1f2937;color:white;padding:8px 12px;border-radius:6px;white-space:nowrap;font-size:12px;z-index:1000;margin-bottom:8px;}
  </style>
</head>
<body class="min-h-screen text-slate-800 pb-20">

<nav class="bg-white border-b border-slate-200 sticky top-0 z-50">
  <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
    <a href="<?= htmlspecialchars(iv_url(['action'=>'fiche_list']), ENT_QUOTES, 'UTF-8') ?>" class="brand flex items-center gap-2">
      <img src="logo.png" alt="ImpactVenture Logo" style="height: 40px; width: auto; object-fit: contain;">
      <span class="text-2xl font-bold"><span class="text-[#1D9E75]">Impact</span><span class="text-[#534AB7]">Venture</span></span>
    </a>
    <div class="flex gap-6 text-sm font-medium">
      <a href="<?= htmlspecialchars(iv_url(['action'=>'projet_list']), ENT_QUOTES, 'UTF-8') ?>" class="text-slate-500 hover:text-[#1D9E75]">← Projets</a>
      <a href="<?= htmlspecialchars(iv_url(['action'=>'comprehensive_pdf','id_projet'=>$projet['id_projet']]), ENT_QUOTES, 'UTF-8') ?>" class="text-[#1D9E75] hover:underline">📄 Générer PDF</a>
    </div>
  </div>
</nav>

<div class="max-w-7xl mx-auto pt-10 px-6">
  <!-- Header Projet -->
  <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
      <div class="flex items-center gap-3 mb-2">
        <span class="px-3 py-1 text-xs font-bold uppercase tracking-wider rounded-full bg-emerald-100 text-emerald-700">
          <?= htmlspecialchars($projet['statut'] ?? '') ?>
        </span>
        <h1 class="brand text-4xl font-bold text-slate-900"><?= htmlspecialchars($projet['titre'] ?? '') ?></h1>
      </div>
      <p class="text-slate-500 text-lg">Incubé par <span class="font-semibold text-[#1D9E75]"><?= htmlspecialchars($projet['entreprise_nom'] ?? 'N/A') ?></span></p>
    </div>
    <div class="flex gap-3">
        <a href="<?= htmlspecialchars(iv_url(['action'=>'pitch_deck','id_projet'=>$projet['id_projet'],'generate'=>1]), ENT_QUOTES, 'UTF-8') ?>" class="px-5 py-2.5 bg-slate-900 text-white font-medium rounded-xl hover:bg-slate-800 transition shadow-lg">🎥 Pitch Deck Auto</a>
        <a href="<?= htmlspecialchars(iv_url(['action'=>'projet_edit','id'=>$projet['id_projet']]), ENT_QUOTES, 'UTF-8') ?>" class="px-5 py-2.5 bg-[#534AB7] text-white font-medium rounded-xl hover:bg-indigo-700 transition shadow-lg">✏️ Modifier</a>
    </div>
  </div>

  <!-- Tabs Navigation -->
  <div class="border-b border-slate-200 mb-8">
    <div class="flex space-x-8 overflow-x-auto">
      <button onclick="switchTab('general')" class="tab-button active px-4 py-3 font-semibold text-sm whitespace-nowrap">
        📊 Vue Générale
      </button>
      <button onclick="switchTab('pitch')" class="tab-button px-4 py-3 font-semibold text-sm whitespace-nowrap">
        🎤 Analyse Pitch
      </button>
      <button onclick="switchTab('matching')" class="tab-button px-4 py-3 font-semibold text-sm whitespace-nowrap">
        👥 Matching
      </button>
      <button onclick="switchTab('history')" class="tab-button px-4 py-3 font-semibold text-sm whitespace-nowrap">
        📈 Historique
      </button>
      <button onclick="switchTab('chat')" class="tab-button px-4 py-3 font-semibold text-sm whitespace-nowrap">
        🤖 Coach IA
      </button>
    </div>
  </div>

  <!-- Tab Contents -->
  
  <!-- Tab 1: Vue Générale -->
  <div id="general-tab" class="tab-content active">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      
      <!-- Scores Overview -->
      <div class="lg:col-span-2 space-y-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="card flex flex-col items-center justify-center text-center">
                <h3 class="text-slate-500 font-semibold mb-4 uppercase tracking-wider text-sm">Score Green</h3>
                <div class="relative w-32 h-32">
                    <canvas id="greenChart"></canvas>
                    <div class="absolute inset-0 flex items-center justify-center flex-col">
                        <span class="text-3xl font-bold text-[#1D9E75] score-counter" data-target="<?= $scoreGreen ?>"><?= $scoreGreen ?>%</span>
                    </div>
                </div>
                <p class="mt-4 text-sm text-slate-600">Impact environnemental estimé</p>
            </div>
            
            <div class="card flex flex-col items-center justify-center text-center">
                <h3 class="text-slate-500 font-semibold mb-4 uppercase tracking-wider text-sm">Score Viabilité</h3>
                <div class="relative w-32 h-32">
                    <canvas id="viabilityChart"></canvas>
                    <div class="absolute inset-0 flex items-center justify-center flex-col">
                        <span class="text-3xl font-bold text-[#534AB7] score-counter" data-target="<?= $viabilityScore ?>"><?= $viabilityScore ?>%</span>
                    </div>
                </div>
                <a href="index.php?action=viability&id_projet=<?= $projet['id_projet'] ?>" class="mt-4 text-sm text-[#534AB7] font-medium hover:underline">Recalculer les données →</a>
            </div>
        </div>

        <!-- Description -->
        <div class="card">
            <h3 class="text-xl font-bold brand mb-4 border-b pb-3 border-slate-100">Description du Projet</h3>
            <p class="text-slate-700 leading-relaxed"><?= nl2br(htmlspecialchars($projet['description'] ?? '')) ?></p>
        </div>

        <!-- Recommendations -->
        <?php if (!empty($nextStepRecs)): ?>
        <div class="card bg-yellow-50 border border-yellow-200">
            <h3 class="text-lg font-bold text-yellow-700 brand mb-4">🎯 Vos Prochaines Étapes</h3>
            <div class="space-y-3">
                <?php foreach ($nextStepRecs as $rec): ?>
                    <div class="p-3 bg-white rounded-lg border border-yellow-100">
                        <p class="text-sm text-gray-800"><?= $rec ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
      </div>

      <!-- Sidebar -->
      <div class="space-y-8">
        <!-- SDG Badges -->
        <div class="card">
            <h3 class="text-lg font-bold brand mb-4">🌍 ODD Détectés</h3>
            <div class="flex flex-wrap gap-2">
                <?php foreach ($sdgs as $sdg): ?>
                    <div class="px-3 py-2 rounded-lg text-sm font-semibold text-white" style="background: <?= $sdg['color'] ?>;">
                        ODD <?= $sdg['numero'] ?>: <?= $sdg['titre'] ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Badges -->
        <div class="card">
            <h3 class="text-lg font-bold brand mb-4">🏆 Badges Obtenus</h3>
            <div class="grid grid-cols-2 gap-3">
                <div class="text-center p-3 bg-green-50 rounded-xl border border-green-200">
                    <div class="text-2xl mb-1">🌱</div>
                    <div class="text-xs font-semibold text-green-700">Éco Warrior</div>
                </div>
                <div class="text-center p-3 bg-blue-50 rounded-xl border border-blue-200">
                    <div class="text-2xl mb-1">🚀</div>
                    <div class="text-xs font-semibold text-blue-700">Innovateur</div>
                </div>
                <div class="text-center p-3 bg-purple-50 rounded-xl border border-purple-200">
                    <div class="text-2xl mb-1">🏆</div>
                    <div class="text-xs font-semibold text-purple-700">Impact Leader</div>
                </div>
                <div class="text-center p-3 bg-gray-100 rounded-xl border border-gray-300 badge-locked" data-tooltip="Score final >90%">
                    <div class="text-2xl mb-1">🔒</div>
                    <div class="text-xs font-semibold text-gray-500">Verrouillé</div>
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Tab 2: Analyse Pitch -->
  <div id="pitch-tab" class="tab-content">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <div class="lg:col-span-2">
        <div class="card">
            <h3 class="text-xl font-bold brand mb-4 border-b pb-3 border-slate-100 flex justify-between items-center">
                Analyse du Pitch (NLP)
                <span class="text-lg font-bold <?= $pitchScore >= 70 ? 'text-green-500' : 'text-amber-500' ?>"><?= $pitchScore ?>/100</span>
            </h3>
            <p class="text-slate-700 leading-relaxed mb-6 italic border-l-4 border-slate-200 pl-4">"<?= nl2br(htmlspecialchars($projet['description'] ?? '')) ?>"</p>
            
            <?php if($sentimentData): ?>
            <div class="grid grid-cols-4 gap-4 mb-4">
                <div class="bg-slate-50 p-4 rounded-xl text-center">
                    <p class="text-2xl font-bold text-slate-700"><?= $sentimentData['sentiment'] ?? 0 ?></p>
                    <p class="text-xs text-slate-500">Sentiment</p>
                </div>
                <div class="bg-slate-50 p-4 rounded-xl text-center">
                    <p class="text-2xl font-bold text-slate-700"><?= $sentimentData['clarte'] ?? 0 ?></p>
                    <p class="text-xs text-slate-500">Clarté</p>
                </div>
                <div class="bg-slate-50 p-4 rounded-xl text-center">
                    <p class="text-2xl font-bold text-slate-700"><?= $sentimentData['professionnalisme'] ?? 0 ?></p>
                    <p class="text-xs text-slate-500">Professionnalisme</p>
                </div>
                <div class="bg-slate-50 p-4 rounded-xl text-center">
                    <p class="text-2xl font-bold text-slate-700"><?= $sentimentData['confiance'] ?? 0 ?></p>
                    <p class="text-xs text-slate-500">Confiance</p>
                </div>
            </div>
            <div class="bg-indigo-50 text-indigo-800 p-4 rounded-xl text-sm">
                <strong>💡 Conseil IA :</strong> <?= htmlspecialchars($sentimentData['advice'] ?? 'Améliorez votre description pour un meilleur impact.') ?>
            </div>
            <?php endif; ?>
        </div>
      </div>

      <div>
        <div class="card">
            <h3 class="text-lg font-bold brand mb-4">📊 Scores Détaillés</h3>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-sm text-gray-600">Qualité du Pitch</span>
                        <span class="text-sm font-semibold"><?= $pitchScore ?>/100</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: <?= $pitchScore ?>%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-sm text-gray-600">Score Green</span>
                        <span class="text-sm font-semibold"><?= $scoreGreen ?>/100</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: <?= $scoreGreen ?>%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-sm text-gray-600">Score Viabilité</span>
                        <span class="text-sm font-semibold"><?= $viabilityScore ?>/100</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-purple-600 h-2 rounded-full" style="width: <?= $viabilityScore ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Tab 3: Matching -->
  <div id="matching-tab" class="tab-content">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      
      <!-- Mentors -->
      <div class="card">
        <h3 class="text-xl font-bold brand mb-6">👥 Top Mentors Matchés</h3>
        <div class="space-y-3">
          <?php foreach(array_slice($mentors, 0, 3) as $m): ?>
          <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-100">
            <div class="flex-1">
              <p class="font-semibold text-sm"><?= htmlspecialchars($m['nom']) ?></p>
              <p class="text-xs text-slate-500"><?= htmlspecialchars($m['specialite']) ?></p>
            </div>
            <div class="flex items-center gap-2">
              <span class="text-xs font-bold text-emerald-600 bg-emerald-100 px-2 py-1 rounded-md"><?= $m['score'] ?>%</span>
              <button onclick="expressInterest('<?= htmlspecialchars($m['slug']) ?>', <?= $projet['id_projet'] ?>, this)" 
                      class="text-xs bg-pink-100 text-pink-700 hover:bg-pink-200 px-3 py-1.5 rounded-lg transition-all duration-200 flex items-center gap-1">
                💌 Intéressé
              </button>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Investors -->
      <div class="card">
        <h3 class="text-xl font-bold brand mb-6">💰 Top Investisseurs Matchés</h3>
        <div class="space-y-3">
          <?php foreach(array_slice($investors, 0, 3) as $inv): ?>
          <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-100">
            <div class="flex-1">
              <p class="font-semibold text-sm"><?= htmlspecialchars($inv['nom']) ?></p>
              <p class="text-xs text-slate-500"><?= htmlspecialchars($inv['focus']) ?></p>
            </div>
            <div class="flex items-center gap-2">
              <span class="text-xs font-bold text-blue-600 bg-blue-100 px-2 py-1 rounded-md"><?= $inv['score'] ?>%</span>
              <button class="text-xs bg-blue-100 text-blue-700 hover:bg-blue-200 px-3 py-1.5 rounded-lg transition-all duration-200">
                📧 Contacter
              </button>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Tab 4: Historique -->
  <div id="history-tab" class="tab-content">
    <div class="card">
      <h3 class="text-xl font-bold brand mb-6">📈 Évolution des Scores</h3>
      <?php if (!empty($scoreHistory)): ?>
        <div class="space-y-3 mb-6">
          <?php foreach (array_reverse($scoreHistory) as $i => $snapshot): ?>
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
              <div class="flex items-center space-x-3">
                <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                <span class="text-sm text-gray-600">
                  <?= date('d M Y', strtotime($snapshot['created_at'])) ?>
                </span>
                <?php if (!empty($snapshot['change_reason'])): ?>
                  <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">
                    <?= htmlspecialchars($snapshot['change_reason']) ?>
                  </span>
                <?php endif; ?>
              </div>
              <div class="text-lg font-semibold text-gray-800">
                <?= number_format($snapshot['final_score'], 1) ?>
                <?php if ($snapshot['delta_pts'] > 0): ?>
                  <span class="text-green-500 text-sm">+<?= $snapshot['delta_pts'] ?></span>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        
        <!-- Chart -->
        <div class="h-64">
          <canvas id="historyChart"></canvas>
        </div>
      <?php else: ?>
        <p class="text-gray-500 text-center py-8">Aucun historique de scores disponible</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Tab 5: Chat IA -->
  <div id="chat-tab" class="tab-content">
    <div class="card">
      <h3 class="text-xl font-bold brand mb-6">🤖 Coach IA Personnel</h3>
      
      <!-- Chat Container -->
      <div class="bg-gray-50 rounded-xl p-4 h-96 overflow-y-auto mb-4" id="chatContainer">
        <div class="text-center text-gray-500 py-8">
          <div class="text-4xl mb-4">🤖</div>
          <p>Bonjour ! Je suis votre coach IA personnel.</p>
          <p class="text-sm mt-2">Posez-moi des questions sur votre projet <?= htmlspecialchars($projet['titre']) ?>.</p>
        </div>
      </div>
      
      <!-- Input -->
      <div class="flex gap-2">
        <input type="text" id="chatInput" placeholder="Posez votre question..." 
               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1D9E75]">
        <button onclick="sendChatMessage()" class="px-6 py-2 bg-[#1D9E75] text-white rounded-lg hover:bg-[#15795A] transition">
          Envoyer
        </button>
      </div>
      
      <!-- Quick Suggestions -->
      <div class="flex flex-wrap gap-2 mt-4">
        <button onclick="quickQuestion('Comment améliorer mon score marché ?')" class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200">
          Comment améliorer mon score marché ?
        </button>
        <button onclick="quickQuestion('Quelle stratégie pour mon secteur ?')" class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200">
          Stratégie pour mon secteur
        </button>
        <button onclick="quickQuestion('Levée de fonds Tunisie')" class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200">
          Levée de fonds Tunisie
        </button>
      </div>
    </div>
  </div>
</div>

<script>
// Tab switching
function switchTab(tabName) {
  // Hide all tabs
  document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
  document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
  
  // Show selected tab
  document.getElementById(tabName + '-tab').classList.add('active');
  event.target.classList.add('active');
}

// Score counter animation
document.addEventListener('DOMContentLoaded', function() {
  const counters = document.querySelectorAll('.score-counter');
  counters.forEach(counter => {
    const target = parseInt(counter.getAttribute('data-target'));
    const duration = 1500;
    const step = target / (duration / 16);
    let current = 0;
    
    const timer = setInterval(() => {
      current += step;
      if (current >= target) {
        current = target;
        clearInterval(timer);
      }
      counter.textContent = Math.round(current) + '%';
    }, 16);
  });

  // Initialize charts
  initializeCharts();
});

function initializeCharts() {
  // Green Score Chart
  const greenCtx = document.getElementById('greenChart');
  if (greenCtx) {
    new Chart(greenCtx, {
      type: 'doughnut',
      data: {
        datasets: [{
          data: [<?= $scoreGreen ?>, 100 - <?= $scoreGreen ?>],
          backgroundColor: ['#1D9E75', '#e5e7eb'],
          borderWidth: 0
        }]
      },
      options: {
        cutout: '70%',
        plugins: { legend: { display: false } }
      }
    });
  }

  // Viability Score Chart
  const viabilityCtx = document.getElementById('viabilityChart');
  if (viabilityCtx) {
    new Chart(viabilityCtx, {
      type: 'doughnut',
      data: {
        datasets: [{
          data: [<?= $viabilityScore ?>, 100 - <?= $viabilityScore ?>],
          backgroundColor: ['#534AB7', '#e5e7eb'],
          borderWidth: 0
        }]
      },
      options: {
        cutout: '70%',
        plugins: { legend: { display: false } }
      }
    });
  }

  // History Chart
  const historyCtx = document.getElementById('historyChart');
  if (historyCtx && <?= !empty($scoreHistory) ? 'true' : 'false' ?>) {
    const historyData = <?= json_encode(array_map(function($s) {
      return [
        date('d M Y', strtotime($s['created_at'])),
        $s['final_score']
      ];
    }, $scoreHistory), JSON_UNESCAPED_UNICODE) ?>;
    
    new Chart(historyCtx, {
      type: 'line',
      data: {
        labels: historyData.map(d => d[0]),
        datasets: [{
          label: 'Score Final',
          data: historyData.map(d => d[1]),
          borderColor: '#1D9E75',
          backgroundColor: 'rgba(29, 158, 117, 0.1)',
          tension: 0.4,
          fill: true
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          y: { beginAtZero: true, max: 100 }
        }
      }
    });
  }
}

// Mentor Interest Function
function expressInterest(mentorSlug, projetId, button) {
  if (button.disabled) return;
  
  button.disabled = true;
  button.innerHTML = '⏳ Envoi...';
  
  fetch('index.php?action=mentor_interest', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `mentor_slug=${encodeURIComponent(mentorSlug)}&projet_id=${projetId}`
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      button.innerHTML = '✅ Intérêt enregistré';
      button.classList.remove('bg-pink-100', 'text-pink-700');
      button.classList.add('bg-green-100', 'text-green-700');
    } else {
      button.innerHTML = '❌ Erreur';
      button.disabled = false;
    }
  })
  .catch(error => {
    button.innerHTML = '❌ Erreur';
    button.disabled = false;
  });
}

// Chat functions
function sendChatMessage() {
  const input = document.getElementById('chatInput');
  const message = input.value.trim();
  if (!message) return;
  
  addChatMessage('user', message);
  input.value = '';
  
  // Simulate AI response
  setTimeout(() => {
    addChatMessage('assistant', 'Je suis en train d\'analyser votre projet pour vous donner la meilleure réponse possible...');
  }, 1000);
}

function quickQuestion(question) {
  document.getElementById('chatInput').value = question;
  sendChatMessage();
}

function addChatMessage(role, message) {
  const container = document.getElementById('chatContainer');
  const messageDiv = document.createElement('div');
  messageDiv.className = `mb-4 ${role === 'user' ? 'text-right' : 'text-left'}`;
  
  const bubble = document.createElement('div');
  bubble.className = `inline-block px-4 py-2 rounded-lg ${
    role === 'user' 
      ? 'bg-[#1D9E75] text-white' 
      : 'bg-white text-gray-800 border border-gray-200'
  }`;
  bubble.textContent = message;
  
  messageDiv.appendChild(bubble);
  container.appendChild(messageDiv);
  container.scrollTop = container.scrollHeight;
}
</script>

</body>
</html>
