<?php $projets=$projets??[];$history=$history??[];$id_projet=$_GET['id_projet']??null; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Chatbot Coach - ImpactVenture</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    body{font-family:'Inter',sans-serif;background:#0f172a;color:#e2e8f0;min-height:100vh;}
    .brand{font-family:'Space Grotesk',sans-serif;}
    .glass{background:rgba(255,255,255,.05);backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,.1);}
    #chatMessages{height:55vh;overflow-y:auto;scroll-behavior:smooth;}
    #chatMessages::-webkit-scrollbar{width:4px;}
    #chatMessages::-webkit-scrollbar-thumb{background:#334155;border-radius:4px;}
    .msg-user{background:linear-gradient(135deg,#1D9E75,#15795A);margin-left:auto;max-width:75%;border-radius:20px 20px 4px 20px;}
    .msg-bot{background:rgba(255,255,255,.08);max-width:75%;border-radius:20px 20px 20px 4px;}
    .typing-dot{width:8px;height:8px;border-radius:50%;background:#1D9E75;animation:bounce 1.4s infinite;}
    .typing-dot:nth-child(2){animation-delay:.2s;} .typing-dot:nth-child(3){animation-delay:.4s;}
    @keyframes bounce{0%,80%,100%{transform:translateY(0)}40%{transform:translateY(-8px)}}
  </style>
</head>
<body>
<nav class="glass sticky top-0 z-50 border-b border-white/10">
  <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
    <a href="index.php?action=fiche_list" class="brand text-2xl font-bold"><span class="text-[#1D9E75]">Impact</span><span class="text-[#534AB7]">Venture</span></a>
    <div class="flex gap-6 text-sm">
      <a href="index.php?action=advanced_dashboard" class="text-[#1D9E75] font-semibold">← Dashboard</a>
    </div>
  </div>
</nav>

<div class="max-w-4xl mx-auto px-6 py-8">
  <div class="text-center mb-8">
    <h1 class="brand text-4xl font-bold mb-2">🤖 <span class="bg-gradient-to-r from-[#8B5CF6] to-[#1D9E75] bg-clip-text text-transparent">Coach Entrepreneur IA</span></h1>
    <p class="text-gray-400">Votre assistant intelligent pour améliorer votre pitch, stratégie et levée de fonds</p>
  </div>

  <!-- Sélection projet contextuel -->
  <div class="glass rounded-2xl p-4 mb-6">
    <div class="flex items-center gap-4">
      <span class="text-sm text-gray-400">Contexte projet :</span>
      <select id="contextProjet" class="flex-1 bg-white/10 border border-white/20 rounded-xl px-4 py-2 text-sm text-white focus:outline-none focus:border-[#8B5CF6]">
        <option value="">Aucun (conversation libre)</option>
        <?php foreach($projets as $p): ?>
          <option value="<?= $p['id_projet'] ?>" <?= ($id_projet==$p['id_projet'])?'selected':'' ?> class="text-black"><?= htmlspecialchars($p['titre']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <!-- Suggestions rapides -->
  <div class="flex flex-wrap gap-2 mb-6">
    <?php $suggestions = ['Comment améliorer mon pitch ?','Stratégies marketing startup','Comment lever des fonds en Tunisie ?','Conseils pour mon business model','Comment calculer mon TAM/SAM/SOM ?'];
    foreach($suggestions as $s): ?>
    <button onclick="sendSuggestion('<?= $s ?>')" class="glass px-4 py-2 rounded-full text-xs hover:bg-white/10 transition text-gray-300 hover:text-white">
      <?= $s ?>
    </button>
    <?php endforeach; ?>
  </div>

  <!-- Chat -->
  <div class="glass rounded-3xl overflow-hidden">
    <div id="chatMessages" class="p-6 space-y-4">
      <!-- Historique -->
      <?php foreach($history as $h): ?>
      <div class="<?= $h['role']==='user' ? 'msg-user' : 'msg-bot' ?> px-5 py-3">
        <p class="text-sm whitespace-pre-wrap"><?= nl2br(htmlspecialchars($h['message'])) ?></p>
      </div>
      <?php endforeach; ?>

      <!-- Message d'accueil -->
      <?php if(empty($history)): ?>
      <div class="msg-bot px-5 py-4">
        <p class="text-sm">👋 Bonjour ! Je suis votre <strong>Coach Entrepreneur IA</strong>.</p>
        <p class="text-sm mt-2">Je peux vous aider à :</p>
        <ul class="text-sm mt-1 space-y-1 text-gray-300">
          <li>• 🎯 Améliorer votre pitch</li>
          <li>• 📊 Définir votre stratégie marketing</li>
          <li>• 💰 Comprendre la levée de fonds</li>
          <li>• 📋 Corriger votre description de projet</li>
        </ul>
        <p class="text-sm mt-2">Posez-moi votre question ! 🚀</p>
      </div>
      <?php endif; ?>
    </div>

    <!-- Input -->
    <div class="border-t border-white/10 p-4">
      <form onsubmit="sendMessage(event)" class="flex gap-3">
        <input type="text" id="chatInput" placeholder="Posez votre question..." 
               class="flex-1 bg-white/10 border border-white/20 rounded-2xl px-5 py-4 text-white focus:outline-none focus:border-[#8B5CF6]">
        <button type="submit" id="sendBtn" class="bg-gradient-to-r from-[#8B5CF6] to-[#1D9E75] text-white px-8 py-4 rounded-2xl font-semibold hover:opacity-90 transition">
          Envoyer
        </button>
      </form>
    </div>
  </div>
</div>

<script>
const chatMessages = document.getElementById('chatMessages');
const chatInput = document.getElementById('chatInput');
const sendBtn = document.getElementById('sendBtn');

function addMessage(text, role) {
    const div = document.createElement('div');
    div.className = role === 'user' ? 'msg-user px-5 py-3' : 'msg-bot px-5 py-4';
    div.innerHTML = '<p class="text-sm whitespace-pre-wrap">' + text.replace(/\n/g,'<br>').replace(/\*\*(.*?)\*\*/g,'<strong>$1</strong>') + '</p>';
    chatMessages.appendChild(div);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

function showTyping() {
    const div = document.createElement('div');
    div.className = 'msg-bot px-5 py-4 flex gap-2 items-center';
    div.id = 'typing';
    div.innerHTML = '<div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div>';
    chatMessages.appendChild(div);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

function hideTyping() {
    const el = document.getElementById('typing');
    if (el) el.remove();
}

function sendSuggestion(text) {
    chatInput.value = text;
    sendMessage(new Event('submit'));
}

async function sendMessage(e) {
    e.preventDefault();
    const message = chatInput.value.trim();
    if (!message) return;

    addMessage(message, 'user');
    chatInput.value = '';
    sendBtn.disabled = true;
    showTyping();

    try {
        const response = await fetch('index.php?action=chatbot_api', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                message: message,
                id_projet: document.getElementById('contextProjet').value || null
            })
        });
        const data = await response.json();
        hideTyping();
        addMessage(data.response || 'Erreur de réponse', 'bot');
    } catch (err) {
        hideTyping();
        addMessage('❌ Erreur de connexion. Veuillez réessayer.', 'bot');
    }
    sendBtn.disabled = false;
    chatInput.focus();
}
</script>
</body>
</html>
