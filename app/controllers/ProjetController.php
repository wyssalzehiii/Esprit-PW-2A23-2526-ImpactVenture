<?php
require_once __DIR__ . '/../models/Projet.php';
require_once __DIR__ . '/../models/FicheEntreprise.php';

class ProjetController {
    private $model;

    public function __construct() {
        $this->model = new Projet();
    }

    // ===================== FRONT OFFICE =====================

    public function index() {
        $projets = $this->model->getAll();
        require_once __DIR__ . '/../views/front/Projet/list.php';
    }

    public function create() {
        $fiches = (new FicheEntreprise())->getAll();
        $errors = [];
        require_once __DIR__ . '/../views/front/Projet/create.php';
    }

    public function store() {
        $errors = $this->valider($_POST);
        if (!empty($errors)) {
            $fiches = (new FicheEntreprise())->getAll();
            require_once __DIR__ . '/../views/front/Projet/create.php';
            return;
        }
        $_POST['id_user'] = 1;
        $id_projet = $this->model->create($_POST);

        // --- AUTOMATISATION IA ---
        // 1. Analyse NLP / Sentiment
        require_once __DIR__ . '/../models/SentimentModel.php';
        $sentimentModel = new SentimentModel();
        $analysis = $sentimentModel->analyze($_POST['description']);
        $pitchScore = $analysis['pitch_quality'] ?? 0;
        $this->model->query("UPDATE projet SET pitch_score = ?, sentiment_data = ? WHERE id_projet = ?",
            [$pitchScore, json_encode($analysis), $id_projet]);

        // 2. Classification ODD (SDG)
        require_once __DIR__ . '/../models/SDGModel.php';
        $sdgModel = new SDGModel();
        $sdgResults = $sdgModel->analyze($_POST['description'], $_POST['titre']);
        $sdgModel->saveForProjet($id_projet, $sdgResults);

        // Rediriger vers la page du projet qui contient tous les outils avancés (Matching, BP, Viabilité...)
        header('Location: index.php?action=projet_show&id=' . $id_projet . '&msg=created_with_ai');
        exit;
    }

    public function edit($id) {
        $projet = $this->model->getById($id);
        if (!$projet) {
            header('Location: index.php?action=projet_list&msg=notfound');
            exit;
        }
        $fiches = (new FicheEntreprise())->getAll();
        $errors = [];
        require_once __DIR__ . '/../views/front/Projet/edit.php';
    }

    public function update($id) {
        $errors = $this->valider($_POST);
        if (!empty($errors)) {
            $projet = $this->model->getById($id);
            $fiches = (new FicheEntreprise())->getAll();
            require_once __DIR__ . '/../views/front/Projet/edit.php';
            return;
        }
        $this->model->update($id, $_POST);
        header('Location: index.php?action=projet_list&msg=updated');
        exit;
    }

    public function delete($id) {
        $this->model->delete($id);
        header('Location: index.php?action=projet_list&msg=deleted');
        exit;
    }

    public function show($id) {
        $projet = $this->model->getById($id);
        if (!$projet) {
            header('Location: index.php?action=projet_list');
            exit;
        }

        // --- Fetch AI Data for Evaluator ---
        // 1. ODDs (SDG)
        require_once __DIR__ . '/../models/SDGModel.php';
        $sdgModel = new SDGModel();
        $sdgs = $sdgModel->getForProjet($id);

        // 2. Matching
        require_once __DIR__ . '/../models/MatchingModel.php';
        $matchingModel = new MatchingModel();
        $mentors = $matchingModel->matchMentors($projet);
        $investors = $matchingModel->matchInvestors($projet);

        // 3. Sentiment & Pitch
        $sentimentData = $projet['sentiment_data'] ? json_decode($projet['sentiment_data'], true) : null;
        $pitchScore = $projet['pitch_score'] ?? 0;
        
        // 4. Score Green (from Entreprise)
        $scoreGreen = $projet['score_green'] ?? 0;

        require_once __DIR__ . '/../views/front/Projet/show.php';
    }

    // ===================== BACK OFFICE =====================

    public function adminIndex() {
        $projets = $this->model->getAll();
        $stats   = $this->model->getStats();
        require_once __DIR__ . '/../views/back/projet/list.php';
    }

    public function adminEdit($id) {
        $projet = $this->model->getById($id);
        $fiches = (new FicheEntreprise())->getAll();
        $errors = [];
        require_once __DIR__ . '/../views/back/projet/edit.php';
    }

    public function adminUpdate($id) {
        $errors = $this->valider($_POST);
        if (!empty($errors)) {
            $projet = $this->model->getById($id);
            $fiches = (new FicheEntreprise())->getAll();
            require_once __DIR__ . '/../views/back/projet/edit.php';
            return;
        }
        $this->model->update($id, $_POST);
        header('Location: index.php?action=admin_projet&msg=updated');
        exit;
    }

    public function adminDelete($id) {
        $this->model->delete($id);
        header('Location: index.php?action=admin_projet&msg=deleted');
        exit;
    }

    public function trending() {
        $fiches   = (new FicheEntreprise())->getTopFiches(5);
        $total    = $this->model->count();
        $stats    = $this->model->getStats();
        require_once __DIR__ . '/../views/back/projet/trending.php';
    }
    public function co2Simulator() {
    $fiches = (new FicheEntreprise())->getAll();
    $co2Model = new CO2Model();
    $history = $co2Model->getHistory();
    $result = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $result = $co2Model->calculate($_POST);
    }

    require_once __DIR__ . '/../views/front/CO2/simulator.php';
    }

    public function analyzeAjax() {
        header('Content-Type: application/json');
        $desc = $_POST['description'] ?? '';
        $titre = $_POST['titre'] ?? '';

        require_once __DIR__ . '/../models/SentimentModel.php';
        $sentimentModel = new SentimentModel();
        $sentiment = $sentimentModel->analyze($desc);

        require_once __DIR__ . '/../models/SDGModel.php';
        $sdgModel = new SDGModel();
        $sdgs = $sdgModel->analyze($desc, $titre);

        $viability = min(100, strlen($desc) > 50 ? 85 : 50 + rand(0, 20));

        echo json_encode([
            'pitch_score' => $sentiment['pitch_quality'] ?? 0,
            'sdgs' => array_map(function($s){ return $s['nom']; }, $sdgs),
            'viability' => $viability
        ]);
        exit;
    }


    // ===================== VALIDATION =====================

    private function valider($p) {
        $errors = [];
        if (empty(trim($p['titre'] ?? '')))                     $errors[] = "Le titre est obligatoire.";
        if (strlen(trim($p['titre'] ?? '')) < 3)               $errors[] = "Le titre doit avoir au moins 3 caractères.";
        if (empty(trim($p['description'] ?? '')))               $errors[] = "La description est obligatoire.";
        if (strlen(trim($p['description'] ?? '')) < 20)        $errors[] = "La description doit avoir au moins 20 caractères.";
        if (empty($p['id_fiche_entreprise'] ?? ''))             $errors[] = "Veuillez sélectionner une entreprise.";
        return $errors;
    }
}
