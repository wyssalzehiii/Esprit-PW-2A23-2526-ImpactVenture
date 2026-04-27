<?php
require_once __DIR__ . '/../models/Projet.php';
require_once __DIR__ . '/../models/FicheEntreprise.php';

class ProjetController {
    private $model;

    public function __construct() {
        $this->model = new Projet();
    }

    // FRONT OFFICE
    public function index() {
        $projets = $this->model->getAll();
        require_once __DIR__ . '/../views/front/projet/list.php';
    }

    public function create() {
        $fiches = (new FicheEntreprise())->getAll(); // Select des entreprises
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
        $_POST['id_user'] = 2; // À remplacer par session plus tard
        $this->model->create($_POST);
        header('Location: index.php?action=projet_list&msg=created');
        exit;
    }

    private function valider($p) {
        $errors = [];
        if (empty(trim($p['titre']??''))) $errors[] = "Le titre du projet est obligatoire.";
        if (empty(trim($p['description']??''))) $errors[] = "La description du projet est obligatoire.";
        if (empty($p['id_fiche_entreprise']??'') || !is_numeric($p['id_fiche_entreprise'])) 
            $errors[] = "Veuillez sélectionner une fiche entreprise.";
        return $errors;
    }
}