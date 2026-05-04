<?php $result = $result ?? null; $history = $history ?? []; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Simulateur CO2 - ImpactVenture</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body class="bg-gray-50">
<!-- Navbar ... -->

<div class="max-w-4xl mx-auto pt-12 px-6">
    <h1 class="text-4xl font-bold brand text-center mb-10">🌍 Simulateur d'Impact CO₂</h1>

    <form method="POST" class="bg-white rounded-3xl shadow-xl p-10 grid grid-cols-2 gap-6">
        <div>
            <label>Transport (km)</label>
            <input type="number" name="transport_km" class="w-full border rounded-2xl px-5 py-4" step="0.1">
        </div>
        <div>
            <label>Énergie (kWh)</label>
            <input type="number" name="energie_kwh" class="w-full border rounded-2xl px-5 py-4" step="0.1">
        </div>
        <div>
            <label>Déchets (kg)</label>
            <input type="number" name="dechets_kg" class="w-full border rounded-2xl px-5 py-4" step="0.1">
        </div>
        <div>
            <label>Eau (m³)</label>
            <input type="number" name="eau_m3" class="w-full border rounded-2xl px-5 py-4" step="0.1">
        </div>

        <div class="col-span-2">
            <select name="id_fiche_entreprise" class="w-full border rounded-2xl px-5 py-4">
                <option value="">Lier à une entreprise...</option>
                <?php foreach($fiches as $f): ?>
                    <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['nom']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="col-span-2 bg-[#1D9E75] text-white py-4 rounded-2xl font-semibold">
            Calculer l'Impact CO₂
        </button>
    </form>

    <?php if($result): ?>
    <div class="mt-10 bg-white rounded-3xl p-10">
        <h2 class="text-3xl font-bold text-center text-red-600"><?= $result['total'] ?> kg CO₂</h2>
        <p class="text-center text-gray-600"><?= $result['equivalence'] ?></p>
        
        <canvas id="co2Chart" class="mt-8"></canvas>
    </div>
    <?php endif; ?>

    <!-- Historique -->
    <h3 class="mt-12 mb-4 font-semibold">Historique des calculs</h3>
    <!-- Tableau historique -->
</div>

<script>
const ctx = document.getElementById('co2Chart');
if(ctx) new Chart(ctx, { type: 'bar', data: { ... } });
</script>
</body>
</html>