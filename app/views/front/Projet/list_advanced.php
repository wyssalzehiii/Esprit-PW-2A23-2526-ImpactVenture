<?php
// Pagination settings
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 12;
$offset = ($page - 1) * $perPage;

// Search and filters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sector = isset($_GET['sector']) ? $_GET['sector'] : '';
$odd = isset($_GET['odd']) ? $_GET['odd'] : '';
$funding_stage = isset($_GET['funding_stage']) ? $_GET['funding_stage'] : '';
$region = isset($_GET['region']) ? $_GET['region'] : '';
$sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'date';
$sortOrder = isset($_GET['order']) ? $_GET['order'] : 'desc';

// Column visibility
$columns = isset($_COOKIE['project_columns']) ? json_decode($_COOKIE['project_columns'], true) : [
    'name' => true, 'sector' => true, 'score' => true, 'date' => true, 'odd' => true, 'funding' => true
];

// Get filtered projects
$allProjects = $projets ?? [];
$filteredProjects = [];

foreach ($allProjects as $project) {
    $match = true;
    
    // Search filter
    if (!empty($search)) {
        $searchLower = strtolower($search);
        $match = (
            strpos(strtolower($project['titre'] ?? ''), $searchLower) !== false ||
            strpos(strtolower($project['entreprise_nom'] ?? ''), $searchLower) !== false ||
            strpos(strtolower($project['description'] ?? ''), $searchLower) !== false
        );
        if (!$match) continue;
    }
    
    // Sector filter
    if (!empty($sector) && strtolower($project['entreprise_categorie'] ?? '') !== strtolower($sector)) {
        continue;
    }
    
    // ODD filter
    if (!empty($odd) && strpos($project['sdg_tags'] ?? '', $odd) === false) {
        continue;
    }
    
    // Funding stage filter
    if (!empty($funding_stage)) {
        $projectFunding = 'unknown';
        if (($project['budget_demande'] ?? 0) < 50000) $projectFunding = 'seed';
        elseif (($project['budget_demande'] ?? 0) < 200000) $projectFunding = 'series-a';
        else $projectFunding = 'series-b';
        
        if ($projectFunding !== $funding_stage) continue;
    }
    
    // Region filter
    if (!empty($region) && strtolower($project['entreprise_ville'] ?? '') !== strtolower($region)) {
        continue;
    }
    
    $filteredProjects[] = $project;
}

// Sort projects
usort($filteredProjects, function($a, $b) use ($sortBy, $sortOrder) {
    $valA = $valB = 0;
    
    switch ($sortBy) {
        case 'name':
            $valA = strtolower($a['titre'] ?? '');
            $valB = strtolower($b['titre'] ?? '');
            break;
        case 'sector':
            $valA = strtolower($a['entreprise_categorie'] ?? '');
            $valB = strtolower($b['entreprise_categorie'] ?? '');
            break;
        case 'score':
            $valA = (float)($a['score_ia'] ?? 0);
            $valB = (float)($b['score_ia'] ?? 0);
            break;
        case 'date':
            $valA = strtotime($a['date_soumission'] ?? '1970-01-01');
            $valB = strtotime($b['date_soumission'] ?? '1970-01-01');
            break;
        default:
            $valA = 0;
            $valB = 0;
    }
    
    if ($sortOrder === 'desc') {
        return $valB <=> $valA;
    } else {
        return $valA <=> $valB;
    }
});

// Pagination
$totalProjects = count($filteredProjects);
$totalPages = ceil($totalProjects / $perPage);
$pagedProjects = array_slice($filteredProjects, $offset, $perPage);

// Export functionality
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="projects_export.csv"');
    
    $output = fopen('php://output', 'w');
    
    // CSV Header
    fputcsv($output, [
        'ID', 'Titre', 'Entreprise', 'Secteur', 'Score IA', 'Score Green', 
        'Budget', 'Statut', 'Date Soumission', 'ODD Tags'
    ]);
    
    // CSV Data
    foreach ($filteredProjects as $project) {
        fputcsv($output, [
            $project['id_projet'] ?? '',
            $project['titre'] ?? '',
            $project['entreprise_nom'] ?? '',
            $project['entreprise_categorie'] ?? '',
            round($project['score_ia'] ?? 0),
            round($project['score_green'] ?? 0),
            number_format($project['budget_demande'] ?? 0, 0, ',', ' '),
            $project['statut'] ?? '',
            date('d/m/Y', strtotime($project['date_soumission'] ?? 'now')),
            $project['sdg_tags'] ?? ''
        ]);
    }
    
    fclose($output);
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projets Avancés - ImpactVenture</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy-dark: #0f172a;
            --navy-darker: #0a0e27;
            --teal-primary: #1D9E75;
            --teal-light: #534AB7;
            --cyan-accent: #06b6d4;
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--navy-dark) 0%, var(--navy-darker) 100%);
            color: #e2e8f0;
            min-height: 100vh;
        }
        
        .brand { font-family: 'Space Grotesk', sans-serif; }
        
        .glass {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }
        
        .glass-dark {
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .project-card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .project-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
            border-color: var(--teal-primary);
        }
        
        .score-badge {
            background: linear-gradient(135deg, var(--teal-primary), var(--teal-light));
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .odd-badge {
            background: linear-gradient(135deg, var(--cyan-accent), #0891b2);
            color: white;
            padding: 2px 6px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 500;
        }
        
        .search-input {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            padding: 12px 16px;
            color: white;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .search-input:focus {
            outline: none;
            border-color: var(--teal-primary);
            box-shadow: 0 0 0 3px rgba(29, 158, 117, 0.2);
        }
        
        .filter-select {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 8px;
            padding: 8px 12px;
            color: white;
            font-size: 14px;
        }
        
        .filter-select:focus {
            outline: none;
            border-color: var(--teal-primary);
        }
        
        .action-btn {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--teal-light);
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .action-btn:hover {
            background: var(--teal-primary);
            color: white;
            transform: translateY(-1px);
        }
        
        .action-btn.active {
            background: var(--teal-primary);
            color: white;
        }
        
        .pagination-btn {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--teal-light);
            padding: 8px 12px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .pagination-btn:hover {
            background: var(--teal-primary);
            color: white;
        }
        
        .pagination-btn.active {
            background: var(--teal-primary);
            color: white;
        }
        
        .column-toggle {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 6px;
            padding: 4px 8px;
            color: var(--teal-light);
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .column-toggle.active {
            background: var(--teal-primary);
            color: white;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="glass sticky top-0 z-50 border-b border-white/10">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <a href="index.php?action=fiche_list" class="brand text-2xl font-bold flex items-center gap-2">
                <span class="text-[#1D9E75]">Impact</span><span class="text-[#534AB7]">Venture</span>
            </a>
            <div class="flex gap-6 text-sm">
                <a href="index.php?action=advanced_dashboard" class="action-btn">🏠 Dashboard</a>
                <a href="index.php?action=projet_list_advanced" class="action-btn active">📋 Projets</a>
                <a href="index.php?action=projet_create" class="action-btn">➕ Nouveau Projet</a>
                <a href="index.php?action=chatbot" class="action-btn active">🤖 Coach IA</a>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-6 py-8">
        <div class="text-center mb-8">
            <h1 class="brand text-4xl font-bold mb-2">
                📋 <span class="bg-gradient-to-r from-[#1D9E75] to-[#534AB7] bg-clip-text text-transparent">Projets Avancés</span>
            </h1>
            <p class="text-gray-400">Gestion avancée avec recherche, filtrage et exportation</p>
        </div>
        
        <!-- Search and Filters -->
        <div class="glass rounded-2xl p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Search -->
                <div class="lg:col-span-2">
                    <label class="block text-sm text-gray-400 mb-2">🔍 Recherche</label>
                    <input type="text" 
                           name="search" 
                           value="<?= htmlspecialchars($search) ?>"
                           placeholder="Rechercher par nom, entreprise, mot-clé..." 
                           class="search-input w-full">
                </div>
                
                <!-- Sector Filter -->
                <div>
                    <label class="block text-sm text-gray-400 mb-2">🏢 Secteur</label>
                    <select name="sector" class="filter-select w-full">
                        <option value="">Tous les secteurs</option>
                        <option value="tech" <?= $sector === 'tech' ? 'selected' : '' ?>>Technologie</option>
                        <option value="agriculture" <?= $sector === 'agriculture' ? 'selected' : '' ?>>Agriculture</option>
                        <option value="fintech" <?= $sector === 'fintech' ? 'selected' : '' ?>>FinTech</option>
                        <option value="health" <?= $sector === 'health' ? 'selected' : '' ?>>Santé</option>
                        <option value="education" <?= $sector === 'education' ? 'selected' : '' ?>>Éducation</option>
                        <option value="retail" <?= $sector === 'retail' ? 'selected' : '' ?>>Commerce</option>
                    </select>
                </div>
                
                <!-- ODD Filter -->
                <div>
                    <label class="block text-sm text-gray-400 mb-2">🌍 ODD</label>
                    <select name="odd" class="filter-select w-full">
                        <option value="">Tous les ODD</option>
                        <option value="ODD1" <?= $odd === 'ODD1' ? 'selected' : '' ?>>ODD 1</option>
                        <option value="ODD2" <?= $odd === 'ODD2' ? 'selected' : '' ?>>ODD 2</option>
                        <option value="ODD3" <?= $odd === 'ODD3' ? 'selected' : '' ?>>ODD 3</option>
                        <option value="ODD4" <?= $odd === 'ODD4' ? 'selected' : '' ?>>ODD 4</option>
                        <option value="ODD5" <?= $odd === 'ODD5' ? 'selected' : '' ?>>ODD 5</option>
                        <option value="ODD6" <?= $odd === 'ODD6' ? 'selected' : '' ?>>ODD 6</option>
                        <option value="ODD7" <?= $odd === 'ODD7' ? 'selected' : '' ?>>ODD 7</option>
                        <option value="ODD8" <?= $odd === 'ODD8' ? 'selected' : '' ?>>ODD 8</option>
                        <option value="ODD9" <?= $odd === 'ODD9' ? 'selected' : '' ?>>ODD 9</option>
                    </select>
                </div>
                
                <!-- Funding Stage Filter -->
                <div>
                    <label class="block text-sm text-gray-400 mb-2">💰 Stade de financement</label>
                    <select name="funding_stage" class="filter-select w-full">
                        <option value="">Tous les stades</option>
                        <option value="seed" <?= $funding_stage === 'seed' ? 'selected' : '' ?>>Seed</option>
                        <option value="series-a" <?= $funding_stage === 'series-a' ? 'selected' : '' ?>>Série A</option>
                        <option value="series-b" <?= $funding_stage === 'series-b' ? 'selected' : '' ?>>Série B</option>
                    </select>
                </div>
            </div>
            
            <!-- Second row of filters -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Region Filter -->
                <div>
                    <label class="block text-sm text-gray-400 mb-2">📍 Région</label>
                    <input type="text" 
                           name="region" 
                           value="<?= htmlspecialchars($region) ?>"
                           placeholder="Tunis, Sfax, Sousse..." 
                           class="search-input w-full">
                </div>
                
                <!-- Sort -->
                <div>
                    <label class="block text-sm text-gray-400 mb-2">🔃 Trier par</label>
                    <select name="sort" class="filter-select w-full">
                        <option value="date" <?= $sortBy === 'date' ? 'selected' : '' ?>>Date</option>
                        <option value="name" <?= $sortBy === 'name' ? 'selected' : '' ?>>Nom</option>
                        <option value="score" <?= $sortBy === 'score' ? 'selected' : '' ?>>Score</option>
                        <option value="sector" <?= $sortBy === 'sector' ? 'selected' : '' ?>>Secteur</option>
                    </select>
                </div>
                
                <!-- Order -->
                <div>
                    <label class="block text-sm text-gray-400 mb-2">📊 Ordre</label>
                    <select name="order" class="filter-select w-full">
                        <option value="desc" <?= $sortOrder === 'desc' ? 'selected' : '' ?>>Décroissant</option>
                        <option value="asc" <?= $sortOrder === 'asc' ? 'selected' : '' ?>>Croissant</option>
                    </select>
                </div>
                
                <!-- Per Page -->
                <div>
                    <label class="block text-sm text-gray-400 mb-2">📄 Par page</label>
                    <select name="per_page" class="filter-select w-full">
                        <option value="6" <?= $perPage === 6 ? 'selected' : '' ?>>6</option>
                        <option value="12" <?= $perPage === 12 ? 'selected' : '' ?>>12</option>
                        <option value="24" <?= $perPage === 24 ? 'selected' : '' ?>>24</option>
                        <option value="48" <?= $perPage === 48 ? 'selected' : '' ?>>48</option>
                    </select>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex gap-3 mt-6">
                <button type="submit" class="action-btn">
                    🔍 Appliquer les filtres
                </button>
                <button type="button" onclick="toggleColumns()" class="action-btn">
                    📥 Colonnes
                </button>
                <button type="button" onclick="exportCSV()" class="action-btn">
                    📥 Exporter CSV
                </button>
                <button type="button" onclick="resetFilters()" class="action-btn">
                    🔄 Réinitialiser
                </button>
            </div>
        </div>
        
        <!-- Column Toggles -->
        <div id="columnToggles" class="glass rounded-2xl p-4 mb-8 hidden">
            <h3 class="text-lg font-semibold mb-4">Afficher/Masquer les colonnes</h3>
            <div class="flex flex-wrap gap-2">
                <button onclick="toggleColumn('name')" class="column-toggle <?= $columns['name'] ? 'active' : '' ?>">
                    Nom
                </button>
                <button onclick="toggleColumn('sector')" class="column-toggle <?= $columns['sector'] ? 'active' : '' ?>">
                    Secteur
                </button>
                <button onclick="toggleColumn('score')" class="column-toggle <?= $columns['score'] ? 'active' : '' ?>">
                    Score
                </button>
                <button onclick="toggleColumn('odd')" class="column-toggle <?= $columns['odd'] ? 'active' : '' ?>">
                    ODD
                </button>
                <button onclick="toggleColumn('funding')" class="column-toggle <?= $columns['funding'] ? 'active' : '' ?>">
                    Budget
                </button>
                <button onclick="toggleColumn('date')" class="column-toggle <?= $columns['date'] ? 'active' : '' ?>">
                    Date
                </button>
            </div>
        </div>
        
        <!-- Results Summary -->
        <div class="glass rounded-2xl p-4 mb-6">
            <div class="flex justify-between items-center">
                <span class="text-gray-400">
                    <?= $totalProjects ?> projet(s) trouvé(s)
                    <?php if (!empty($search) || !empty($sector) || !empty($odd)): ?>
                        (filtrés)
                    <?php endif; ?>
                </span>
                <span class="text-sm text-gray-500">
                    Page <?= $page ?> sur <?= $totalPages ?>
                </span>
            </div>
        </div>
        
        <!-- Projects Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
            <?php foreach ($pagedProjects as $project): ?>
                <div class="project-card p-6">
                    <!-- Project Header -->
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-white mb-1">
                                <?= htmlspecialchars($project['titre'] ?? 'Projet sans titre') ?>
                            </h3>
                            <p class="text-sm text-gray-400">
                                🏢 <?= htmlspecialchars($project['entreprise_nom'] ?? 'Non associée') ?>
                                <?php if (!empty($project['entreprise_categorie'])): ?>
                                    — <?= htmlspecialchars($project['entreprise_categorie']) ?>
                                <?php endif; ?>
                            </p>
                        </div>
                        
                        <!-- Score Badge -->
                        <div class="score-badge">
                            <?= round($project['score_ia'] ?? 0) ?>/100
                        </div>
                    </div>
                    
                    <!-- Project Description -->
                    <p class="text-sm text-gray-300 mb-4 line-clamp-3">
                        <?= htmlspecialchars(substr($project['description'] ?? '', 0, 120)) ?>...
                    </p>
                    
                    <!-- ODD Tags -->
                    <?php if (!empty($project['sdg_tags']) && $columns['odd']): ?>
                        <div class="flex gap-1 mb-4">
                            <?php 
                            $oddTags = explode(',', $project['sdg_tags']);
                            foreach (array_slice($oddTags, 0, 3) as $odd): ?>
                                <span class="odd-badge"><?= htmlspecialchars(trim($odd)) ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Budget -->
                    <?php if ($columns['funding']): ?>
                        <div class="text-sm text-gray-400 mb-4">
                            💰 Budget: <?= number_format($project['budget_demande'] ?? 0, 0, ',', ' ') ?> TND
                        </div>
                    <?php endif; ?>
                    
                    <!-- Date -->
                    <?php if ($columns['date']): ?>
                        <div class="text-xs text-gray-500 mb-4">
                            📅 <?= date('d/m/Y', strtotime($project['date_soumission'] ?? 'now')) ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Actions -->
                    <div class="flex gap-2">
                        <a href="<?= htmlspecialchars(iv_url(['action'=>'projet_show','id'=>$project['id_projet']]), ENT_QUOTES, 'UTF-8') ?>"
                           class="flex-1 text-center py-2.5 text-xs border border-gray-200 rounded-xl hover:bg-gray-50 font-medium">
                            Voir
                        </a>
                        <a href="<?= htmlspecialchars(iv_url(['action'=>'projet_edit','id'=>$project['id_projet']]), ENT_QUOTES, 'UTF-8') ?>"
                           class="flex-1 text-center py-2.5 text-xs border border-[#1D9E75] text-[#1D9E75] rounded-xl hover:bg-green-50 font-medium">
                            Modifier
                        </a>
                        <a href="<?= htmlspecialchars(iv_url(['action'=>'projet_delete','id'=>$project['id_projet']]), ENT_QUOTES, 'UTF-8') ?>"
                           onclick="return confirm('Supprimer ce projet ?')"
                           class="flex-1 text-center py-2.5 text-xs bg-red-50 text-red-600 rounded-xl hover:bg-red-100 font-medium">
                            Supprimer
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="glass rounded-2xl p-4">
                <div class="flex justify-center items-center gap-2">
                    <?php if ($page > 1): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" 
                           class="pagination-btn">
                            ← Précédent
                        </a>
                    <?php endif; ?>
                    
                    <?php
                    $startPage = max(1, $page - 2);
                    $endPage = min($totalPages, $page + 2);
                    
                    for ($i = $startPage; $i <= $endPage; $i++):
                    ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" 
                           class="pagination-btn <?= $i === $page ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" 
                           class="pagination-btn">
                            Suivant →
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        function toggleColumns() {
            const toggles = document.getElementById('columnToggles');
            toggles.classList.toggle('hidden');
        }
        
        function toggleColumn(column) {
            const columns = JSON.parse(getCookie('project_columns') || '{}');
            columns[column] = !columns[column];
            document.cookie = `project_columns=${JSON.stringify(columns)}; path=/; max-age=365`;
            location.reload();
        }
        
        function getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
        }
        
        function exportCSV() {
            const params = new URLSearchParams(window.location.search);
            params.set('export', 'csv');
            window.location.href = `?${params.toString()}`;
        }
        
        function resetFilters() {
            window.location.href = 'index.php?action=projet_list';
        }
        
        // Auto-submit form on filter change
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const inputs = form.querySelectorAll('input, select');
            
            inputs.forEach(input => {
                input.addEventListener('change', function() {
                    if (input.type === 'text' && input.value.length > 2) {
                        form.submit();
                    } else if (input.type !== 'text') {
                        form.submit();
                    }
                });
            });
        });
    </script>
</body>
</html>
