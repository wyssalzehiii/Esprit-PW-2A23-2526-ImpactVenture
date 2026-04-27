<?php
require_once __DIR__ . '/../models/FicheEntreprise.php';

class FicheEntrepriseController {
    private $model;

    public function __construct() {
        $this->model = new FicheEntreprise();
    }

    // FRONT OFFICE - Liste des fiches entreprises
    public function index() {
        $fiches = $this->model->getAll();
        $total = $this->model->count();
        $avg = $this->model->avgGreen();
        $totalP = $this->model->totalProjets();
        require_once __DIR__ . '/../views/front/ficheentreprise/list.php';
    }

    // Formulaire création fiche entreprise
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
        $data = $this->nettoyer($_POST);
        $this->model->create($data);
        header('Location: index.php?action=fiche_list&msg=created');
        exit;
    }

    private function valider($p) {
        $errors = [];
        if (empty(trim($p['nom']??''))) $errors[] = "Le nom est obligatoire.";
        if (empty(trim($p['description']??''))) $errors[] = "La description est obligatoire.";
        if (empty($p['categorie']??'')) $errors[] = "La catégorie est obligatoire.";
        return $errors;
    }

    private function nettoyer($p) {
        return [
            'nom' => htmlspecialchars(trim($p['nom'])),
            'description' => htmlspecialchars(trim($p['description'])),
            'categorie' => htmlspecialchars(trim($p['categorie'])),
            'mots_cles' => htmlspecialchars(trim($p['mots_cles'] ?? '')),
            'score_green' => (int)($p['score_green'] ?? 0)
        ];
    }
}