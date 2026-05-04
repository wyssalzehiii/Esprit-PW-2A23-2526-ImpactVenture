<?php
require_once __DIR__ . '/../models/FicheEntreprise.php';

class FicheEntrepriseController {
    private $model;

    public function __construct() {
        $this->model = new FicheEntreprise();
    }

    public function index() {
        $fiches = $this->model->getAll();
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
        $this->model->create($_POST);
        header('Location: index.php?action=fiche_list&msg=created');
        exit;
    }

    public function edit($id) {
        $fiche = $this->model->getById($id);
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
            $fiche = $this->model->getById($id);
            require_once __DIR__ . '/../views/front/FicheEntreprise/edit.php';
            return;
        }
        $this->model->update($id, $_POST);
        header('Location: index.php?action=fiche_list&msg=updated');
        exit;
    }

    public function delete($id) {
        $this->model->delete($id);
        header('Location: index.php?action=fiche_list&msg=deleted');
        exit;
    }

    public function adminIndex() {
        $fiches = $this->model->getAll();
        
        // Advanced Statistics
        $total = count($fiches);
        $avgGreen = $total > 0 ? array_sum(array_column($fiches, 'score_green')) / $total : 0;
        
        require_once __DIR__ . '/../models/Projet.php';
        $projetModel = new Projet();
        $projets = $projetModel->getAll();
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

    private function valider($p) {
        $errors = [];
        if (empty(trim($p['nom'] ?? ''))) $errors[] = "Le nom est obligatoire.";
        if (strlen(trim($p['nom'] ?? '')) < 2) $errors[] = "Le nom doit avoir au moins 2 caractères.";
        if (empty(trim($p['description'] ?? ''))) $errors[] = "La description est obligatoire.";
        if (strlen(trim($p['description'] ?? '')) < 20) $errors[] = "La description doit avoir au moins 20 caractères.";
        if (empty($p['categorie'] ?? '')) $errors[] = "La catégorie est obligatoire.";
        return $errors;
    }
}