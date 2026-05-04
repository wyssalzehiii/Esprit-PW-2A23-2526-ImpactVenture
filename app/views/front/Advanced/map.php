<?php $entreprises=$entreprises??[]; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Carte Interactive - ImpactVenture</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
  <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css"/>
  <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    body{font-family:'Inter',sans-serif;background:#0f172a;color:#e2e8f0;min-height:100vh;margin:0;}
    .brand{font-family:'Space Grotesk',sans-serif;}
    .glass{background:rgba(255,255,255,.05);backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,.1);}
    #map{height:70vh;border-radius:24px;z-index:1;}
    .leaflet-popup-content-wrapper{border-radius:16px;padding:0;overflow:hidden;}
    .leaflet-popup-content{margin:0;min-width:220px;}
    .popup-card{padding:16px;font-family:'Inter',sans-serif;}
    .popup-card h3{font-weight:700;font-size:14px;color:#1D9E75;margin-bottom:4px;}
    .popup-card p{font-size:12px;color:#666;margin:2px 0;}
    .popup-card .score{display:inline-block;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:600;}
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

<div class="max-w-7xl mx-auto px-6 py-8">
  <div class="text-center mb-8">
    <h1 class="brand text-5xl font-bold mb-3">🗺️ <span class="bg-gradient-to-r from-[#10B981] to-[#3B82F6] bg-clip-text text-transparent">Carte Interactive</span></h1>
    <p class="text-gray-400">Startups et entreprises impact en Tunisie — <?= count($entreprises) ?> entreprises géolocalisées</p>
  </div>

  <div class="glass rounded-3xl p-4 mb-8">
    <div id="map"></div>
  </div>

  <!-- Stats -->
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    <?php
    $villes = [];
    foreach($entreprises as $e) { $v = $e['ville'] ?? 'Inconnu'; $villes[$v] = ($villes[$v] ?? 0) + 1; }
    arsort($villes);
    foreach(array_slice($villes, 0, 4, true) as $ville => $count): ?>
    <div class="glass rounded-2xl p-5 text-center">
      <p class="text-2xl font-bold text-[#1D9E75]"><?= $count ?></p>
      <p class="text-xs text-gray-400 mt-1">📍 <?= htmlspecialchars($ville) ?></p>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<script>
const map = L.map('map').setView([34.5, 9.5], 7);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap',
    maxZoom: 18
}).addTo(map);

const markers = L.markerClusterGroup({
    spiderfyOnMaxZoom: true,
    showCoverageOnHover: false,
    maxClusterRadius: 50
});

const customIcon = (color) => L.divIcon({
    className: 'custom-marker',
    html: `<div style="background:${color};width:32px;height:32px;border-radius:50%;border:3px solid white;box-shadow:0 4px 12px rgba(0,0,0,.3);display:flex;align-items:center;justify-content:center;font-size:14px;">🏢</div>`,
    iconSize: [32, 32],
    iconAnchor: [16, 16]
});

const entreprises = <?= json_encode($entreprises, JSON_UNESCAPED_UNICODE) ?>;

entreprises.forEach(e => {
    if (!e.latitude || !e.longitude) return;
    const color = (e.score_green >= 70) ? '#1D9E75' : ((e.score_green >= 40) ? '#EF9F27' : '#E24B4A');
    const scoreStyle = `background:${color}22;color:${color}`;
    const marker = L.marker([parseFloat(e.latitude), parseFloat(e.longitude)], {icon: customIcon(color)});
    marker.bindPopup(`
        <div class="popup-card">
            <h3>${e.nom}</h3>
            <p>📂 ${e.categorie}</p>
            <p>📍 ${e.ville || 'Tunisie'}</p>
            <p>📊 ${e.nb_projets || 0} projet(s)</p>
            <p><span class="score" style="${scoreStyle}">🌱 ${e.score_green}% Green</span></p>
        </div>
    `);
    markers.addLayer(marker);
});

map.addLayer(markers);
</script>
</body>
</html>
