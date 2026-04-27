<?php
require_once __DIR__ . '/../models/FicheEntreprise.php';

class FicheEntrepriseController {
    private $model;

    public function __construct() {
        $this->model = new FicheEntreprise();
    }

    // FRONT
    public function index() {
        $fiches = $this->model->getAll();
        require_once __DIR__ . '/../views/front/ficheentreprise/list.php';
    }

    public function create() {
        $errors = [];
        require_once __DIR__ . '/../views/front/ficheentreprise/create.php';
    }

    public function store() {
        $errors = $this->valider($_POST);
        if (!empty($errors)) {
            require_once __DIR__ . '/../views/front/ficheentreprise/create.php';
            return;
        }
        $this->model->create($this->nettoyer($_POST));
        header('Location: index.php?action=fiche_list&msg=created');
        exit;
    }

    // BACK OFFICE
    public function adminIndex() {
        $fiches = $this->model->getAll();
        $total = $this->model->count();
        $avgGreen = $this->model->avgGreen();
        $totalP = $this->model->totalProjets();
        require_once __DIR__ . '/../views/back/ficheentreprise/list.php';
    }

    private function valider($p) {
        $errors = [];
        if (empty(trim($p['nom'] ?? ''))) $errors[] = "Le nom est obligatoire.";
        if (empty(trim($p['description'] ?? ''))) $errors[] = "La description est obligatoire.";
        if (empty($p['categorie'] ?? '')) $errors[] = "La catégorie est obligatoire.";
        return $errors;
    }

    private function nettoyer($p) {
        return [
            'nom'         => htmlspecialchars(trim($p['nom'])),
            'description' => htmlspecialchars(trim($p['description'])),
            'categorie'   => htmlspecialchars(trim($p['categorie'])),
            'mots_cles'   => htmlspecialchars(trim($p['mots_cles'] ?? '')),
            'score_green' => (int)($p['score_green'] ?? 50)
        ];
    }
}