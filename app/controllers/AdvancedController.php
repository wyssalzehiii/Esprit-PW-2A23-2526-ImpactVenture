<?php
require_once __DIR__ . '/../models/Projet.php';
require_once __DIR__ . '/../models/FicheEntreprise.php';
require_once __DIR__ . '/../models/MatchingModel.php';
require_once __DIR__ . '/../models/BusinessPlanModel.php';
require_once __DIR__ . '/../models/ViabilityModel.php';
require_once __DIR__ . '/../models/SDGModel.php';
require_once __DIR__ . '/../models/MapModel.php';
require_once __DIR__ . '/../models/ChatbotModel.php';
require_once __DIR__ . '/../models/SentimentModel.php';
require_once __DIR__ . '/../models/BadgeModel.php';
require_once __DIR__ . '/../models/PitchDeckModel.php';

class AdvancedController {

    // ═══════════════ 1. MATCHING ═══════════════
    public function matching() {
        $projetModel = new Projet();
        $matchModel = new MatchingModel();
        $projets = $projetModel->getAll();
        $mentors = []; $investors = []; $selectedProjet = null;

        if (isset($_GET['id_projet'])) {
            $selectedProjet = $projetModel->getById((int)$_GET['id_projet']);
            if ($selectedProjet) {
                $fiche = (new FicheEntreprise())->getById($selectedProjet['id_fiche_entreprise']);
                $selectedProjet['ville'] = $fiche['ville'] ?? 'Tunis';
                $selectedProjet['mots_cles'] = $fiche['mots_cles'] ?? '';
                $mentors = $matchModel->findMentors($selectedProjet);
                $investors = $matchModel->findInvestors($selectedProjet);
            }
        }
        require_once __DIR__ . '/../views/front/Advanced/matching.php';
    }

    // ═══════════════ 2. BUSINESS PLAN ═══════════════
    public function businessPlan() {
        $projetModel = new Projet();
        $bpModel = new BusinessPlanModel();
        $projets = $projetModel->getAll();
        $businessPlan = null; $selectedProjet = null;

        if (isset($_GET['generate']) && isset($_GET['id_projet'])) {
            $selectedProjet = $projetModel->getById((int)$_GET['id_projet']);
            if ($selectedProjet) {
                $data = $bpModel->generate($selectedProjet);
                $businessPlan = $data;
            }
        } elseif (isset($_GET['id_projet'])) {
            $selectedProjet = $projetModel->getById((int)$_GET['id_projet']);
            $existing = $bpModel->getByProjet((int)$_GET['id_projet']);
            if ($existing) $businessPlan = $existing['data'];
        }

        if (isset($_GET['pdf']) && isset($_GET['id_projet'])) {
            $selectedProjet = $projetModel->getById((int)$_GET['id_projet']);
            $existing = $bpModel->getByProjet((int)$_GET['id_projet']);
            if ($existing && $selectedProjet) {
                $html = $bpModel->generatePDF($existing['data'], $selectedProjet);
                echo $html; exit;
            }
        }
        require_once __DIR__ . '/../views/front/Advanced/business_plan.php';
    }

    // ═══════════════ 3. VIABILITY SCORE ═══════════════
    public function viability() {
        $projetModel = new Projet();
        $viabilityModel = new ViabilityModel();
        $projets = $projetModel->getAll();
        $result = null; $selectedProjet = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $selectedProjet = $projetModel->getById((int)$_POST['id_projet']);
            $result = $viabilityModel->calculate($_POST);
        } elseif (isset($_GET['id_projet'])) {
            $selectedProjet = $projetModel->getById((int)$_GET['id_projet']);
        }
        require_once __DIR__ . '/../views/front/Advanced/viability.php';
    }

    // ═══════════════ 4. SDG CLASSIFIER ═══════════════
    public function sdg() {
        $projetModel = new Projet();
        $sdgModel = new SDGModel();
        $projets = $projetModel->getAll();
        $sdgResults = []; $selectedProjet = null; $allSdgs = $sdgModel->getAll();

        if (isset($_GET['id_projet'])) {
            $selectedProjet = $projetModel->getById((int)$_GET['id_projet']);
            if ($selectedProjet) {
                $sdgResults = $sdgModel->analyze($selectedProjet['description'], $selectedProjet['titre']);
                $sdgModel->saveForProjet($selectedProjet['id_projet'], $sdgResults);
            }
        }
        require_once __DIR__ . '/../views/front/Advanced/sdg.php';
    }

    // ═══════════════ 5. MAP ═══════════════
    public function map() {
        $mapModel = new MapModel();
        $entreprises = $mapModel->getAllWithCoords();
        require_once __DIR__ . '/../views/front/Advanced/map.php';
    }

    // ═══════════════ 6. CHATBOT ═══════════════
    public function chatbot() {
        $chatModel = new ChatbotModel();
        $projetModel = new Projet();
        $projets = $projetModel->getAll();
        $history = [];

        $id_projet = isset($_GET['id_projet']) ? (int)$_GET['id_projet'] : null;
        if ($id_projet) $history = $chatModel->getHistory(1, $id_projet);

        require_once __DIR__ . '/../views/front/Advanced/chatbot.php';
    }

    public function chatbotApi() {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);
        $message = $input['message'] ?? '';
        $id_projet = $input['id_projet'] ?? null;

        if (empty($message)) { echo json_encode(['error' => 'Message vide']); exit; }

        $chatModel = new ChatbotModel();
        $response = $chatModel->chat($message, $id_projet);
        echo json_encode(['response' => $response], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ═══════════════ 7. SENTIMENT ═══════════════
    public function sentiment() {
        $projetModel = new Projet();
        $sentimentModel = new SentimentModel();
        $projets = $projetModel->getAll();
        $analysis = null; $selectedProjet = null;

        if (isset($_GET['id_projet'])) {
            $selectedProjet = $projetModel->getById((int)$_GET['id_projet']);
            if ($selectedProjet) {
                $analysis = $sentimentModel->analyze($selectedProjet['description']);
                $pitchScore = $analysis['pitch_quality'] ?? 0;
                $projetModel->query("UPDATE projet SET pitch_score = ?, sentiment_data = ? WHERE id_projet = ?",
                    [$pitchScore, json_encode($analysis), $selectedProjet['id_projet']]);
            }
        }
        require_once __DIR__ . '/../views/front/Advanced/sentiment.php';
    }

    // ═══════════════ 8. BADGES ═══════════════
    public function badges() {
        $badgeModel = new BadgeModel();
        $ficheModel = new FicheEntreprise();
        $fiches = $ficheModel->getAll();
        $allBadges = $badgeModel->getAllBadges();
        $leaderboard = $badgeModel->getLeaderboard();
        $selectedFiche = null; $ficheBadges = []; $progress = null;

        if (isset($_GET['id_fiche'])) {
            $selectedFiche = $ficheModel->getById((int)$_GET['id_fiche']);
            if ($selectedFiche) {
                $badgeModel->checkAndAward($selectedFiche['id']);
                $ficheBadges = $badgeModel->getBadgesForFiche($selectedFiche['id']);
                $progress = $badgeModel->getProgress($selectedFiche['id']);
            }
        }
        // Check all fiches
        foreach ($fiches as $f) { $badgeModel->checkAndAward($f['id']); }
        $leaderboard = $badgeModel->getLeaderboard();

        require_once __DIR__ . '/../views/front/Advanced/badges.php';
    }

    // ═══════════════ 9. CO2 (already exists, enhanced) ═══════════════

    // ═══════════════ 10. PITCH DECK ═══════════════
    public function pitchDeck() {
        $projetModel = new Projet();
        $pdModel = new PitchDeckModel();
        $projets = $projetModel->getAll();
        $slides = null; $selectedProjet = null;

        if (isset($_GET['generate']) && isset($_GET['id_projet'])) {
            $selectedProjet = $projetModel->getById((int)$_GET['id_projet']);
            if ($selectedProjet) $slides = $pdModel->generate($selectedProjet);
        } elseif (isset($_GET['id_projet'])) {
            $selectedProjet = $projetModel->getById((int)$_GET['id_projet']);
            $existing = $pdModel->getByProjet((int)$_GET['id_projet']);
            if ($existing) $slides = $existing['slides_data'];
        }

        if (isset($_GET['export']) && isset($_GET['id_projet'])) {
            $selectedProjet = $projetModel->getById((int)$_GET['id_projet']);
            $existing = $pdModel->getByProjet((int)$_GET['id_projet']);
            if ($existing && $selectedProjet) {
                echo $pdModel->generateHTML($existing['slides_data'], $selectedProjet); exit;
            }
        }
        require_once __DIR__ . '/../views/front/Advanced/pitch_deck.php';
    }

    // ═══════════════ DASHBOARD ═══════════════
    public function dashboard() {
        $projetModel = new Projet();
        $ficheModel = new FicheEntreprise();
        $projets = $projetModel->getAll();
        $fiches = $ficheModel->getAll();
        require_once __DIR__ . '/../views/front/Advanced/dashboard.php';
    }
}
