<?php
require_once __DIR__ . '/../models/Projet.php';
require_once __DIR__ . '/../models/FicheEntreprise.php';

class ProjetController {
    private $model;

    public function __construct() {
        $this->model = new Projet();
    }

    public function index() {
        $projets = $this->model->getAll();
        require_once __DIR__ . '/../views/front/projet/list.php';
    }

    public function create() {
        $fiches = (new FicheEntreprise())->getAll();
        $errors = [];
        require_once __DIR__ . '/../views/front/projet/create.php';
    }

    public function store() {
        $errors = $this->valider($_POST);
        if (!empty($errors)) {
            $fiches = (new FicheEntreprise())->getAll();
            require_once __DIR__ . '/../views/front/projet/create.php';
            return;
        }
        $_POST['id_user'] = 2;
        $this->model->create($_POST);
        header('Location: index.php?action=projet_list&msg=created');
        exit;
    }

    private function valider($p) {
        $errors = [];
        if (empty(trim($p['titre'] ?? ''))) $errors[] = "Le titre est obligatoire.";
        if (empty(trim($p['description'] ?? ''))) $errors[] = "La description est obligatoire.";
        if (empty($p['id_fiche_entreprise'] ?? '')) $errors[] = "Veuillez sélectionner une entreprise.";
        return $errors;
    }
}