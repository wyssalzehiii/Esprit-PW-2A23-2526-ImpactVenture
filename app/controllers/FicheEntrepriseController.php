<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/scoring_helpers.php';
require_once __DIR__ . '/../models/FicheEntreprise.php';

class FicheEntrepriseController {
    private $pdo;

    public function __construct() {
        $this->pdo = config::getConnexion();
    }

    // ═══════════════════════════════════════════════════════════════════
    //  FRONT OFFICE — CRUD
    // ═══════════════════════════════════════════════════════════════════

    public function index() {
        $fiches = $this->getAllFiches();
        require_once __DIR__ . '/../views/front/FicheEntreprise/list.php';
    }

    public function create() {
        $errors = [];
        require_once __DIR__ . '/../views/front/FicheEntreprise/create.php';
    }

    public function store() {
        $errors = $this->valider($_POST);
        if (!empty($errors)) {
            require_once __DIR__ . '/../views/front/FicheEntreprise/create.php';
            return;
        }

        $fiche = new FicheEntreprise(
            $_POST['nom'] ?? '',
            $_POST['description'] ?? '',
            $_POST['logo'] ?? '',
            $_POST['categorie'] ?? '',
            $_POST['mots_cles'] ?? ''
        );

        $this->addFiche($fiche);
        header('Location: index.php?action=fiche_list&msg=created');
        exit;
    }

    public function generateLogoSuggestionsAjax() {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
            return;
        }

        $nom = trim($_POST['nom'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $categorie = trim($_POST['categorie'] ?? '');
        $motsCles = trim($_POST['mots_cles'] ?? '');

        if ($nom === '' || $description === '') {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'message' => 'Le nom et la description sont requis pour générer un logo IA.'
            ]);
            return;
        }

        $suggestions = $this->buildLogoSuggestions($nom, $description, $categorie, $motsCles);
        echo json_encode(['success' => true, 'suggestions' => $suggestions], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public function edit($id) {
        $fiche = $this->getFicheById($id);
        if (!$fiche) {
            header('Location: index.php?action=fiche_list&msg=notfound');
            exit;
        }
        $errors = [];
        require_once __DIR__ . '/../views/front/FicheEntreprise/edit.php';
    }

    public function update($id) {
        $errors = $this->valider($_POST);
        if (!empty($errors)) {
            $fiche = $this->getFicheById($id);
            require_once __DIR__ . '/../views/front/FicheEntreprise/edit.php';
            return;
        }

        $fiche = new FicheEntreprise(
            $_POST['nom'] ?? '',
            $_POST['description'] ?? '',
            $_POST['logo'] ?? '',
            $_POST['categorie'] ?? '',
            $_POST['mots_cles'] ?? ''
        );
        $fiche->setId($id);

        $this->updateFiche($fiche);
        header('Location: index.php?action=fiche_list&msg=updated');
        exit;
    }

    public function delete($id) {
        $this->deleteFiche($id);
        header('Location: index.php?action=fiche_list&msg=deleted');
        exit;
    }

    // ═══════════════════════════════════════════════════════════════════
    //  BACK OFFICE
    // ═══════════════════════════════════════════════════════════════════

    public function adminIndex() {
        $fiches = $this->getAllFiches();
        
        $total = count($fiches);
        $avgGreen = $total > 0 ? array_sum(array_column($fiches, 'score_green')) / $total : 0;
        
        // Get projet stats
        require_once __DIR__ . '/../controllers/ProjetController.php';
        $projetCtrl = new ProjetController();
        $projets = $projetCtrl->getAllProjets();
        
        $totalP = count($projets);
        $viabilitySum = 0;
        $pitchSum = 0;
        $aiCount = 0;
        foreach($projets as $p) {
            if (!empty($p['viability_score'])) {
                $viabilitySum += $p['viability_score'];
                $pitchSum += $p['pitch_score'] ?? 0;
                $aiCount++;
            }
        }
        $avgViability = $aiCount > 0 ? round($viabilitySum / $aiCount) : 0;
        $avgPitch = $aiCount > 0 ? round($pitchSum / $aiCount) : 0;

        require_once __DIR__ . '/../views/back/ficheentreprise/list.php';
    }

    // ═══════════════════════════════════════════════════════════════════
    //  CRUD SQL (Modèle EspritBookMVC)
    // ═══════════════════════════════════════════════════════════════════

    public function getAllFiches() {
        $stmt = $this->pdo->query("
            SELECT f.*, COUNT(p.id_projet) as nb_projets,
                   AVG(p.score_ia) as avg_score,
                   f.score_green as avg_green_score
            FROM fiche_entreprise f 
            LEFT JOIN projet p ON p.id_fiche_entreprise = f.id 
            GROUP BY f.id 
            ORDER BY f.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFicheById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM fiche_entreprise WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function entrepriseShow($id) {
        if (!$id) {
            header('Location: index.php?action=fiche_list&msg=missing_id');
            exit;
        }

        $entreprise = $this->getFicheById($id);
        if (!$entreprise) {
            header('Location: index.php?action=fiche_list&msg=notfound');
            exit;
        }

        // Get enhanced data for the company
        $stmt = $this->pdo->prepare("
            SELECT f.*, 
                   COUNT(p.id_projet) as nb_projets,
                   AVG(p.score_ia) as avg_score,
                   f.score_green as avg_green_score
            FROM fiche_entreprise f 
            LEFT JOIN projet p ON p.id_fiche_entreprise = f.id 
            WHERE f.id = ?
            GROUP BY f.id
        ");
        $stmt->execute([$id]);
        $entreprise = $stmt->fetch(PDO::FETCH_ASSOC);

        // Get all projects for this company with their scores
        $stmt = $this->pdo->prepare("
            SELECT p.*, 
                   p.date_soumission as score_date,
                   p.score_ia as final_score,
                   p.score_ia as innovation_score,
                   p.score_ia as sustainability_score,
                   p.score_ia as market_score,
                   p.score_ia as risk_score
            FROM projet p 
            WHERE p.id_fiche_entreprise = ?
            ORDER BY p.date_soumission DESC
        ");
        $stmt->execute([$id]);
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get ODD data from entreprise
        $entreprise_odds = [];
        
        // Try multiple methods to extract ODD data
        if (!empty($entreprise['mots_cles'])) {
            // Extract ODD numbers from mots_cles
            preg_match_all('/ODD\s*(\d+)/i', $entreprise['mots_cles'], $matches);
            if (!empty($matches[1])) {
                $entreprise_odds = array_map(function($match) {
                    $oddNumber = trim($match);
                    $oddData = [
                        'numero' => $oddNumber,
                        'titre' => "ODD $oddNumber",
                        'description' => "Objectif de Développement Durable $oddNumber",
                        'color' => $this->getOddColor($oddNumber)
                    ];
                    return $oddData;
                }, $matches[1]);
            }
        }
        
        // If still no ODDs found, add some default ones based on company category
        if (empty($entreprise_odds)) {
            $default_odds = [];
            $categorie = strtolower($entreprise['categorie'] ?? '');
            
            // Add ODDs based on company category
            if (strpos($categorie, 'tech') !== false || strpos($categorie, 'fintech') !== false) {
                $default_odds = [
                    ['numero' => '8', 'titre' => 'Travail décent et croissance économique', 'description' => 'Promouvoir une croissance économique partagée', 'color' => '#A21942'],
                    ['numero' => '9', 'titre' => 'Industrie, Innovation et Infrastructure', 'description' => 'Bâtir une infrastructure résiliente', 'color' => '#BD1340']
                ];
            } elseif (strpos($categorie, 'agriculture') !== false) {
                $default_odds = [
                    ['numero' => '2', 'titre' => 'Faim zéro', 'description' => 'Éliminer la faim et assurer la sécurité alimentaire', 'color' => '#DDA63A'],
                    ['numero' => '1', 'titre' => 'Pas de pauvreté', 'description' => 'Éradiquer la pauvreté sous toutes ses formes', 'color' => '#E5243B']
                ];
            } elseif (strpos($categorie, 'sante') !== false || strpos($categorie, 'health') !== false) {
                $default_odds = [
                    ['numero' => '3', 'titre' => 'Bonne santé et bien-être', 'description' => 'Permettre à tous de vivre en bonne santé', 'color' => '#4C9F38']
                ];
            } elseif (strpos($categorie, 'education') !== false) {
                $default_odds = [
                    ['numero' => '4', 'titre' => 'Éducation de qualité', 'description' => 'Assurer une éducation inclusive et de qualité', 'color' => '#C5192D']
                ];
            }
            
            $entreprise_odds = array_merge($entreprise_odds, $default_odds);
        }

        // Get matched mentors
        $mentors = [];
        if (!empty($projects)) {
            $firstProject = $projects[0];
            $mentors = $this->decodeJsonList($firstProject['mentor_suggestions'] ?? null);
            $mentors = array_slice($mentors, 0, 3);
        }

        require_once __DIR__ . '/../views/front/FicheEntreprise/show.php';
    }

    public function addFiche(FicheEntreprise $fiche) {
        $score_green = $this->calculateGreenScore($fiche->getDescription());
        $logo = !empty($fiche->getLogo()) ? $fiche->getLogo() : $this->generateLogoPath($fiche->getNom());

        $sql = "INSERT INTO fiche_entreprise (nom, description, logo, categorie, mots_cles, score_green, created_at) 
                VALUES (:nom, :description, :logo, :categorie, :mots_cles, :score_green, NOW())";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nom' => $fiche->getNom(),
            ':description' => $fiche->getDescription(),
            ':logo' => $logo,
            ':categorie' => $fiche->getCategorie(),
            ':mots_cles' => $fiche->getMotsCles(),
            ':score_green' => $score_green
        ]);
    }

    public function updateFiche(FicheEntreprise $fiche) {
        $score_green = $this->calculateGreenScore($fiche->getDescription());
        $logo = !empty($fiche->getLogo()) ? $fiche->getLogo() : $this->generateLogoPath($fiche->getNom());

        $sql = "UPDATE fiche_entreprise SET 
                nom = :nom, description = :description, logo = :logo, categorie = :categorie, 
                mots_cles = :mots_cles, score_green = :score_green
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nom' => $fiche->getNom(),
            ':description' => $fiche->getDescription(),
            ':logo' => $logo,
            ':categorie' => $fiche->getCategorie(),
            ':mots_cles' => $fiche->getMotsCles(),
            ':score_green' => $score_green,
            ':id' => $fiche->getId()
        ]);
    }

    public function deleteFiche($id) {
        $stmt = $this->pdo->prepare("DELETE FROM fiche_entreprise WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }

    public function countFiches() {
        return (int)$this->pdo->query("SELECT COUNT(*) FROM fiche_entreprise")->fetchColumn();
    }

    public function avgGreen() {
        $val = $this->pdo->query("SELECT AVG(score_green) FROM fiche_entreprise")->fetchColumn();
        return round($val ?: 0);
    }

    public function totalProjets() {
        return (int)$this->pdo->query("SELECT COUNT(*) FROM projet")->fetchColumn();
    }

    // ═══════════════════════════════════════════════════════════════════
    //  MÉTIER AVANCÉ — GREEN SCORE
    // ═══════════════════════════════════════════════════════════════════

    private function calculateGreenScore($description) {
        $adj = iv_text_scoring_adjustments($description ?? '');
        $lc = mb_strtolower($description ?? '', 'UTF-8');
        $hits = iv_keyword_bonus_once_each($lc, iv_green_keyword_set());
        $score = 52 + ($hits * 6) + $adj['length_penalty'] + $adj['structure_bonus'] + $adj['diversity_bonus'];
        return (int) min(98, max(38, $score));
    }

    private function generateLogoPath($nom) {
        if (empty($nom)) return null;
        $safe = preg_replace('/[^a-z0-9]/i', '-', strtolower(trim($nom)));
        return "logos/" . $safe . ".png";
    }

    /**
     * Logos vectoriels générés côté serveur (fiables, pas de dépendance réseau).
     * Fichiers courts dans uploads/logos/ → compatibles avec logo VARCHAR(500).
     */
    private function buildLogoSuggestions($nom, $description, $categorie, $motsCles) {
        $uploadDir = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'logos';
        if (!is_dir($uploadDir) && !@mkdir($uploadDir, 0755, true)) {
            return [
                [
                    'id' => 'logo_1',
                    'label' => 'Proposition 1',
                    'url' => $this->buildLogoDataUrlFallback($nom, $categorie, 0)
                ],
                [
                    'id' => 'logo_2',
                    'label' => 'Proposition 2',
                    'url' => $this->buildLogoDataUrlFallback($nom, $categorie, 1)
                ],
                [
                    'id' => 'logo_3',
                    'label' => 'Proposition 3',
                    'url' => $this->buildLogoDataUrlFallback($nom, $categorie, 2)
                ],
            ];
        }

        $palette = $this->inferLogoPalette($description, $categorie, $motsCles);
        $subtitle = $this->logoSubtitle($categorie, $motsCles);
        $displayName = $this->truncateForLogoText($nom, 34);

        $token = bin2hex(random_bytes(4));
        $builders = [
            fn () => $this->renderLogoSvgDiamondWordmark($displayName, $subtitle, $palette),
            fn () => $this->renderLogoSvgGrowthMark($displayName, $subtitle, $palette),
            fn () => $this->renderLogoSvgSealMark($displayName, $subtitle, $palette, $nom),
        ];

        $suggestions = [];
        foreach ($builders as $i => $builder) {
            $svg = $builder();
            $file = 'logo_' . $token . '_' . $i . '.svg';
            $fullPath = $uploadDir . DIRECTORY_SEPARATOR . $file;
            if (@file_put_contents($fullPath, $svg) === false) {
                $url = $this->buildLogoDataUrlFallback($nom, $categorie, $i);
            } else {
                $url = 'uploads/logos/' . $file;
            }
            $suggestions[] = [
                'id' => 'logo_' . ($i + 1),
                'label' => 'Proposition ' . ($i + 1),
                'url' => $url
            ];
        }

        return $suggestions;
    }

    private function svgEsc($s) {
        return htmlspecialchars((string) $s, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    private function truncateForLogoText($s, $maxChars) {
        $s = trim(preg_replace('/\s+/', ' ', $s));
        if (function_exists('mb_strlen') && function_exists('mb_substr')) {
            if (mb_strlen($s, 'UTF-8') <= $maxChars) {
                return $s;
            }
            return mb_substr($s, 0, $maxChars - 1, 'UTF-8') . '…';
        }
        return strlen($s) <= $maxChars ? $s : (substr($s, 0, $maxChars - 1) . '…');
    }

    private function upperLogo($s) {
        if (function_exists('mb_strtoupper')) {
            return mb_strtoupper($s, 'UTF-8');
        }
        return strtoupper($s);
    }

    private function logoSubtitle($categorie, $motsCles) {
        $t = trim($categorie);
        if ($t !== '') {
            return $this->truncateForLogoText($this->upperLogo($t), 28);
        }
        $t = trim($motsCles);
        if ($t !== '') {
            $w = explode(',', $t);
            $w = array_map('trim', $w);
            return $this->truncateForLogoText($this->upperLogo(implode(' · ', array_slice($w, 0, 2))), 28);
        }
        return 'ENTERPRISE';
    }

    private function inferLogoPalette($description, $categorie, $motsCles) {
        $t = strtolower($description . ' ' . $categorie . ' ' . $motsCles);

        if (preg_match('/agri|agriculture|bio|eco|durable|green|nature|ferme|organic|plante|for[eê]t/', $t)) {
            return [
                'primary' => '#14532d',
                'mid' => '#16a34a',
                'light' => '#4ade80',
                'accent' => '#84cc16',
                'bg' => '#f0fdf4',
                'ink' => '#052e16'
            ];
        }
        if (preg_match('/tech|ia|digital|software|data|cloud|robot|code|dev/', $t)) {
            return [
                'primary' => '#1e3a8a',
                'mid' => '#4338ca',
                'light' => '#6366f1',
                'accent' => '#38bdf8',
                'bg' => '#f8fafc',
                'ink' => '#0f172a'
            ];
        }
        if (preg_match('/sante|health|medical|care|clinic/', $t)) {
            return [
                'primary' => '#0e7490',
                'mid' => '#0d9488',
                'light' => '#2dd4bf',
                'accent' => '#bae6fd',
                'bg' => '#f0fdfa',
                'ink' => '#134e4a'
            ];
        }
        if (preg_match('/energie|solar|solaire|renewable|wind|co2|power/', $t)) {
            return [
                'primary' => '#0c4a6e',
                'mid' => '#0284c7',
                'light' => '#38bdf8',
                'accent' => '#f59e0b',
                'bg' => '#f0f9ff',
                'ink' => '#082f49'
            ];
        }

        return [
            'primary' => '#0f2942',
            'mid' => '#1e3a5f',
            'light' => '#2563eb',
            'accent' => '#ea580c',
            'bg' => '#f8fafc',
            'ink' => '#0f172a'
        ];
    }

    private function brandInitials($nom) {
        $nom = trim($nom);
        if ($nom === '') {
            return 'IV';
        }
        $parts = preg_split('/\s+/u', $nom);
        $out = '';
        foreach ($parts as $p) {
            if ($p === '') {
                continue;
            }
            $first = function_exists('mb_substr') ? mb_substr($p, 0, 1, 'UTF-8') : substr($p, 0, 1);
            if (preg_match('/\p{L}/u', $first)) {
                $out .= $this->upperLogo($first);
            }
            $len = function_exists('mb_strlen') ? mb_strlen($out, 'UTF-8') : strlen($out);
            if ($len >= 2) {
                break;
            }
        }
        $len = function_exists('mb_strlen') ? mb_strlen($out, 'UTF-8') : strlen($out);
        if ($len < 2) {
            $two = function_exists('mb_substr') ? mb_substr($nom, 0, 2, 'UTF-8') : substr($nom, 0, 2);
            $out = $this->upperLogo($two);
        }
        return $out ?: 'IV';
    }

    /** Losange + gradient + wordmark (style corporate « Atlantic »). */
    private function renderLogoSvgDiamondWordmark($name, $subtitle, array $p) {
        $n = $this->svgEsc($name);
        $s = $this->svgEsc($subtitle);

        return <<<SVG
<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="512" height="512">
  <defs>
    <linearGradient id="dmk" x1="0%" y1="0%" x2="0%" y2="100%">
      <stop offset="0%" style="stop-color:{$p['light']}"/>
      <stop offset="100%" style="stop-color:{$p['primary']}"/>
    </linearGradient>
    <filter id="sh" x="-20%" y="-20%" width="140%" height="140%">
      <feDropShadow dx="0" dy="3" stdDeviation="4" flood-opacity="0.18"/>
    </filter>
  </defs>
  <rect width="512" height="512" fill="{$p['bg']}"/>
  <g filter="url(#sh)">
    <path d="M 118 256 L 178 156 L 258 156 L 318 256 L 258 356 L 178 356 Z" fill="url(#dmk)"/>
    <path d="M 188 216 Q 228 236 268 216" stroke="#ffffff" stroke-width="8" fill="none" stroke-linecap="round" opacity="0.95"/>
    <path d="M 198 276 Q 238 296 278 276" stroke="#ffffff" stroke-width="5" fill="none" stroke-linecap="round" opacity="0.85"/>
  </g>
  <text x="348" y="230" font-family="Segoe UI, Tahoma, Arial, sans-serif" font-size="28" font-weight="700" fill="{$p['light']}">{$n}</text>
  <text x="348" y="272" font-family="Segoe UI, Tahoma, Arial, sans-serif" font-size="13" font-weight="600" fill="{$p['mid']}" letter-spacing="0.12em">{$s}</text>
  <rect x="348" y="288" width="140" height="3" fill="{$p['accent']}" rx="1"/>
</svg>
SVG;
    }

    /** Barres + swoosh (croissance / impact). */
    private function renderLogoSvgGrowthMark($name, $subtitle, array $p) {
        $n = $this->svgEsc($name);
        $s = $this->svgEsc($subtitle);
        $light = $p['light'];
        $mid = $p['mid'];
        $primary = $p['primary'];
        $accent = $p['accent'];

        return <<<SVG
<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="512" height="512">
  <defs>
    <linearGradient id="bar1" x1="0" x2="0" y1="0" y2="1">
      <stop offset="0%" style="stop-color:{$light}"/><stop offset="100%" style="stop-color:{$primary}"/>
    </linearGradient>
    <linearGradient id="bar2" x1="0" x2="0" y1="0" y2="1">
      <stop offset="0%" style="stop-color:{$light}"/><stop offset="100%" style="stop-color:{$mid}"/>
    </linearGradient>
    <linearGradient id="bar3" x1="0" x2="0" y1="0" y2="1">
      <stop offset="0%" style="stop-color:{$light}"/><stop offset="100%" style="stop-color:{$mid}"/>
    </linearGradient>
  </defs>
  <rect width="512" height="512" fill="{$p['bg']}"/>
  <path d="M 48 380 Q 120 200 280 120 Q 400 80 480 48" stroke="{$primary}" stroke-width="6" fill="none" opacity="0.35"/>
  <path d="M 128 360 L 158 220 L 198 220 L 228 360 Z" fill="url(#bar1)"/>
  <path d="M 248 360 L 278 160 L 318 160 L 348 360 Z" fill="url(#bar2)"/>
  <path d="M 368 360 L 398 120 L 438 120 L 468 360 Z" fill="url(#bar3)"/>
  <path d="M 100 400 Q 200 280 420 340" stroke="{$accent}" stroke-width="12" fill="none" stroke-linecap="round"/>
  <path d="M 112 392 Q 210 300 410 352" stroke="{$primary}" stroke-width="5" fill="none" opacity="0.45"/>
  <text x="256" y="430" text-anchor="middle" font-family="Segoe UI, Tahoma, Arial, sans-serif" font-size="26" font-weight="700" fill="{$p['ink']}">{$n}</text>
  <text x="256" y="465" text-anchor="middle" font-family="Segoe UI, Tahoma, Arial, sans-serif" font-size="12" font-weight="600" fill="{$mid}" letter-spacing="0.14em">{$s}</text>
</svg>
SVG;
    }

    /** Sceau circulaire + initiales (identité premium). */
    private function renderLogoSvgSealMark($name, $subtitle, array $p, $nomOriginal) {
        $n = $this->svgEsc($name);
        $s = $this->svgEsc($subtitle);
        $ini = $this->svgEsc($this->brandInitials($nomOriginal));
        $primary = $p['primary'];
        $light = $p['light'];

        return <<<SVG
<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="512" height="512">
  <rect width="512" height="512" fill="{$p['bg']}"/>
  <circle cx="256" cy="220" r="150" fill="none" stroke="{$primary}" stroke-width="10"/>
  <circle cx="256" cy="220" r="130" fill="none" stroke="{$light}" stroke-width="2" stroke-dasharray="6 8" opacity="0.85"/>
  <circle cx="256" cy="220" r="112" fill="#ffffff" stroke="{$light}" stroke-width="1" opacity="0.95"/>
  <path d="M 200 180 L 256 130 L 312 180 L 292 250 L 220 250 Z" fill="{$light}" opacity="0.9"/>
  <circle cx="256" cy="210" r="18" fill="{$primary}"/>
  <text x="256" y="230" text-anchor="middle" font-family="Segoe UI, Tahoma, Arial, sans-serif" font-size="72" font-weight="800" fill="{$primary}">{$ini}</text>
  <text x="256" y="390" text-anchor="middle" font-family="Segoe UI, Tahoma, Arial, sans-serif" font-size="22" font-weight="700" fill="{$primary}">{$n}</text>
  <text x="256" y="428" text-anchor="middle" font-family="Segoe UI, Tahoma, Arial, sans-serif" font-size="11" font-weight="600" fill="{$light}" letter-spacing="0.16em">{$s}</text>
</svg>
SVG;
    }

    /** Fallback inline si écriture disque impossible (reste &lt; ~2 Ko). */
    private function buildLogoDataUrlFallback($nom, $categorie, $index) {
        $palette = $this->inferLogoPalette('', $categorie, '');
        $subtitle = $this->logoSubtitle($categorie, '');
        $displayName = $this->truncateForLogoText($nom, 34);
        $builders = [
            fn () => $this->renderLogoSvgDiamondWordmark($displayName, $subtitle, $palette),
            fn () => $this->renderLogoSvgGrowthMark($displayName, $subtitle, $palette),
            fn () => $this->renderLogoSvgSealMark($displayName, $subtitle, $palette, $nom),
        ];
        $svg = $builders[$index]();
        $b64 = base64_encode($svg);

        return 'data:image/svg+xml;base64,' . $b64;
    }

    // ═══════════════════════════════════════════════════════════════════
    //  MÉTIER AVANCÉ — BADGES (intégré de BadgeModel)
    // ═══════════════════════════════════════════════════════════════════

    private function awardBadge($id_fiche, $badge_name) {
        $fiche = $this->getFicheById($id_fiche);
        if (!$fiche) return false;

        $current = !empty($fiche['badges']) ? explode(',', $fiche['badges']) : [];
        if (in_array($badge_name, $current)) return false;

        $current[] = $badge_name;
        $new_badges = implode(',', $current);

        $stmt = $this->pdo->prepare("UPDATE fiche_entreprise SET badges = :badges WHERE id = :id");
        $stmt->execute([':badges' => $new_badges, ':id' => $id_fiche]);
        return true;
    }

    private function checkGreenBadges() {
        $fiches = $this->getAllFiches();
        foreach ($fiches as $f) {
            if (!empty($f['score_green']) && $f['score_green'] >= 80) {
                $this->awardBadge($f['id'], 'Eco Warrior');
            }
        }
    }

    public function getAllBadges() {
        return $this->pdo->query("SELECT * FROM badge ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBadgesForFiche($id_fiche) {
        $stmt = $this->pdo->prepare("
            SELECT b.*, ub.awarded_at FROM badge b 
            INNER JOIN user_badges ub ON ub.id_badge = b.id 
            WHERE ub.id_fiche_entreprise = :id ORDER BY ub.awarded_at DESC
        ");
        $stmt->execute([':id' => $id_fiche]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function awardBadgeById($id_fiche, $id_badge) {
        try {
            $stmt = $this->pdo->prepare("INSERT IGNORE INTO user_badges (id_fiche_entreprise, id_badge) VALUES (:fiche, :badge)");
            $stmt->execute([':fiche' => $id_fiche, ':badge' => $id_badge]);
            return true;
        } catch (Exception $e) { return false; }
    }

    public function checkAndAwardBadges($id_fiche) {
        $fiche = $this->getFicheById($id_fiche);
        if (!$fiche) return [];

        $badges = $this->getAllBadges();
        $awarded = [];

        $stmt = $this->pdo->prepare("SELECT * FROM projet WHERE id_fiche_entreprise = :id");
        $stmt->execute([':id' => $id_fiche]);
        $projets = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $projetCount = count($projets);
        $hasAccepted = false; $maxScoreIA = 0; $maxViability = 0; $maxPitch = 0; $sdgCount = 0;

        $stmtCO2 = $this->pdo->prepare("SELECT COUNT(*) FROM co2_calculation WHERE id_fiche_entreprise = :id");
        $stmtCO2->execute([':id' => $id_fiche]);
        $hasCO2 = $stmtCO2->fetchColumn();

        foreach ($projets as $p) {
            if ($p['statut'] === 'accepté') $hasAccepted = true;
            if (($p['score_ia'] ?? 0) > $maxScoreIA) $maxScoreIA = $p['score_ia'];
            if (($p['viability_score'] ?? 0) > $maxViability) $maxViability = $p['viability_score'];
            if (($p['pitch_score'] ?? 0) > $maxPitch) $maxPitch = $p['pitch_score'];
            $tags = array_filter(explode(',', $p['sdg_tags'] ?? ''));
            if (count($tags) > $sdgCount) $sdgCount = count($tags);
        }

        foreach ($badges as $b) {
            $shouldAward = false;
            switch ($b['condition_type']) {
                case 'green_score': $shouldAward = ($fiche['score_green'] ?? 0) >= $b['condition_value']; break;
                case 'pitch_score': $shouldAward = $maxPitch >= $b['condition_value']; break;
                case 'project_count': $shouldAward = $projetCount >= $b['condition_value']; break;
                case 'project_accepted': $shouldAward = $hasAccepted; break;
                case 'score_ia': $shouldAward = $maxScoreIA >= $b['condition_value']; break;
                case 'sdg_count': $shouldAward = $sdgCount >= $b['condition_value']; break;
                case 'viability_score': $shouldAward = $maxViability >= $b['condition_value']; break;
                case 'co2_calculated': $shouldAward = $hasCO2 > 0; break;
            }
            if ($shouldAward) {
                $this->awardBadgeById($id_fiche, $b['id']);
                $awarded[] = $b;
            }
        }
        return $awarded;
    }

    public function getLeaderboard() {
        return $this->pdo->query("
            SELECT f.id, f.nom, f.score_green, COUNT(ub.id) as badge_count
            FROM fiche_entreprise f 
            LEFT JOIN user_badges ub ON ub.id_fiche_entreprise = f.id
            GROUP BY f.id ORDER BY badge_count DESC, f.score_green DESC LIMIT 10
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBadgeProgress($id_fiche) {
        $total = (int)$this->pdo->query("SELECT COUNT(*) FROM badge")->fetchColumn();
        $stmtE = $this->pdo->prepare("SELECT COUNT(*) FROM user_badges WHERE id_fiche_entreprise = :id");
        $stmtE->execute([':id' => $id_fiche]);
        $earned = (int)$stmtE->fetchColumn();
        return ['total' => $total, 'earned' => $earned, 'percent' => $total > 0 ? round(($earned / $total) * 100) : 0];
    }

    public function badges() {
        $fiches = $this->getAllFiches();
        $allBadges = $this->getAllBadges();
        $leaderboard = $this->getLeaderboard();
        $selectedFiche = null; $ficheBadges = []; $progress = null;

        if (isset($_GET['id_fiche'])) {
            $selectedFiche = $this->getFicheById((int)$_GET['id_fiche']);
            if ($selectedFiche) {
                $this->checkAndAwardBadges($selectedFiche['id']);
                $ficheBadges = $this->getBadgesForFiche($selectedFiche['id']);
                $progress = $this->getBadgeProgress($selectedFiche['id']);
            }
        }
        // Check all fiches
        foreach ($fiches as $f) { $this->checkAndAwardBadges($f['id']); }
        $leaderboard = $this->getLeaderboard();

        require_once __DIR__ . '/../views/front/Advanced/badges.php';
    }

    // ═══════════════════════════════════════════════════════════════════
    //  MÉTIER AVANCÉ — MAP (intégré de MapModel)
    // ═══════════════════════════════════════════════════════════════════

    public function getAllWithCoords() {
        return $this->pdo->query("
            SELECT f.*, COUNT(p.id_projet) as nb_projets,
                   (SELECT p2.id_projet FROM projet p2 WHERE p2.id_fiche_entreprise = f.id 
                    ORDER BY p2.date_soumission DESC, p2.id_projet DESC LIMIT 1) AS dernier_projet_id
            FROM fiche_entreprise f 
            LEFT JOIN projet p ON p.id_fiche_entreprise = f.id 
            WHERE f.latitude IS NOT NULL AND f.longitude IS NOT NULL
            GROUP BY f.id ORDER BY f.created_at DESC
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateCoords($id, $lat, $lng) {
        $stmt = $this->pdo->prepare("UPDATE fiche_entreprise SET latitude = :lat, longitude = :lng WHERE id = :id");
        $stmt->execute([':lat' => $lat, ':lng' => $lng, ':id' => $id]);
    }

    public function geocode($ville) {
        $villes = [
            'tunis'=>[36.8065,10.1815],'sfax'=>[34.7406,10.7603],
            'sousse'=>[35.8256,10.6084],'monastir'=>[35.7643,10.8113],
            'bizerte'=>[37.2744,9.8739],'gabes'=>[33.8815,10.0982],
            'kairouan'=>[35.6781,10.0963],'gafsa'=>[34.4250,8.7842],
            'nabeul'=>[36.4561,10.7376],'ariana'=>[36.8625,10.1956],
            'ben arous'=>[36.7533,10.2283],'manouba'=>[36.8101,10.0956],
            'medenine'=>[33.3399,10.5055],'tozeur'=>[33.9197,8.1336],
            'hammamet'=>[36.4000,10.6167],'djerba'=>[33.8076,10.8451]
        ];
        $v = strtolower(trim($ville));
        return $villes[$v] ?? [36.8065, 10.1815];
    }

    public function map() {
        $entreprises = $this->getAllWithCoords();
        require_once __DIR__ . '/../views/front/Advanced/map.php';
    }

    // ═══════════════════════════════════════════════════════════════════
    //  MÉTIER AVANCÉ — CO2 (intégré de CO2Model)
    // ═══════════════════════════════════════════════════════════════════

    private $co2Coefficients = [
        'transport' => 0.192,
        'energie'   => 0.475,
        'dechets'   => 2.5,
        'eau'       => 0.344
    ];

    public function calculateCO2($data) {
        $co2 = [
            'transport' => ($data['transport_km'] ?? 0) * $this->co2Coefficients['transport'],
            'energie'   => ($data['energie_kwh'] ?? 0) * $this->co2Coefficients['energie'],
            'dechets'   => ($data['dechets_kg'] ?? 0) * $this->co2Coefficients['dechets'],
            'eau'       => ($data['eau_m3'] ?? 0) * $this->co2Coefficients['eau']
        ];

        $total = array_sum($co2);

        // Sauvegarde
        $stmt = $this->pdo->prepare("
            INSERT INTO co2_calculation 
            (id_user, id_fiche_entreprise, transport_km, energie_kwh, dechets_kg, eau_m3, total_co2_kg)
            VALUES (:user, :fiche, :transport, :energie, :dechets, :eau, :total)
        ");
        $stmt->execute([
            ':user' => 1,
            ':fiche' => $data['id_fiche_entreprise'] ?? null,
            ':transport' => $data['transport_km'] ?? 0,
            ':energie' => $data['energie_kwh'] ?? 0,
            ':dechets' => $data['dechets_kg'] ?? 0,
            ':eau' => $data['eau_m3'] ?? 0,
            ':total' => $total
        ]);

        return [
            'details' => $co2,
            'total'   => round($total, 2),
            'equivalence' => $this->getCO2Equivalence($total)
        ];
    }

    private function getCO2Equivalence($kg) {
        if ($kg > 10000) return "🌳 " . round($kg/250) . " arbres à planter par an";
        if ($kg > 1000)  return "🚗 " . round($kg/192) . " trajets Tunis-Paris en voiture";
        return "💡 Équivalent de " . round($kg/0.475) . " kWh d'électricité";
    }

    public function getCO2History($id_user = 1) {
        $stmt = $this->pdo->prepare("
            SELECT c.*, f.nom as entreprise_nom 
            FROM co2_calculation c 
            LEFT JOIN fiche_entreprise f ON c.id_fiche_entreprise = f.id
            WHERE c.id_user = :user
            ORDER BY c.created_at DESC LIMIT 10
        ");
        $stmt->execute([':user' => $id_user]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function co2Simulator() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $t = floatval($_POST['transport'] ?? 0);
            $e = floatval($_POST['energie'] ?? 0);
            $d = floatval($_POST['dechets'] ?? 0);
            $r = ($t * 0.192) + ($e * 0.475) + ($d * 2.5);
            $result = ['total' => round($r, 1)];
        }
        $history = [];
        require_once __DIR__ . '/../views/front/CO2/simulator.php';
    }

    // ═══════════════════════════════════════════════════════════════════
    //  VALIDATION
    // ═══════════════════════════════════════════════════════════════════

    private function valider($p) {
        $errors = [];
        if (empty(trim($p['nom'] ?? ''))) $errors[] = "Le nom est obligatoire.";
        if (strlen(trim($p['nom'] ?? '')) < 2) $errors[] = "Le nom doit avoir au moins 2 caractères.";
        if (empty(trim($p['description'] ?? ''))) $errors[] = "La description est obligatoire.";
        if (strlen(trim($p['description'] ?? '')) < 20) $errors[] = "La description doit avoir au moins 20 caractères.";
        if (empty($p['categorie'] ?? '')) $errors[] = "La catégorie est obligatoire.";
        return $errors;
    }

    /** @param mixed $json */
    private function decodeJsonList($json)
    {
        if (!$json) {
            return [];
        }
        $a = json_decode($json, true);
        return is_array($a) ? $a : [];
    }

    private function getOddColor($oddNumber) {
        $colors = [
            1 => '#E5243B', 2 => '#DDA63A', 3 => '#4C9F38', 4 => '#C5192D',
            5 => '#FF3A21', 6 => '#26BDE2', 7 => '#FCC30B', 8 => '#A21942',
            9 => '#BD1340', 10 => '#3F51B5', 11 => '#8B1537', 12 => '#E91E63',
            13 => '#3F51B5', 14 => '#8B1537', 15 => '#FF9800', 16 => '#795548',
            17 => '#8B1537'
        ];
        return $colors[$oddNumber] ?? '#666666';
    }
}
?>