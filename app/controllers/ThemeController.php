<?php
require_once __DIR__ . '/../models/Theme.php';

class ThemeController {
    private $model;

    public function __construct() { $this->model = new Theme(); }

    // ── FRONT OFFICE ──────────────────────────────
    public function index() {
        $themes = $this->model->getAll();
        $total  = $this->model->count();
        $avg    = $this->model->avgGreen();
        $totalP = $this->model->totalProjets();
        require_once __DIR__ . '/../views/front/theme/list.php';
    }

    public function create() {
        $errors = [];
        require_once __DIR__ . '/../views/front/theme/create.php';
    }

    public function store() {
        $errors = $this->valider($_POST);
        if (!empty($errors)) {
            require_once __DIR__ . '/../views/front/theme/create.php';
            return;
        }
        $this->model->create($this->nettoyer($_POST));
        header('Location: index.php?action=list&msg=created');
        exit;
    }

    public function edit($id) {
        $errors = [];
        $theme  = $this->model->getById($id);
        if (!$theme) { header('Location: index.php'); exit; }
        require_once __DIR__ . '/../views/front/theme/edit.php';
    }

    public function update($id) {
        $errors = $this->valider($_POST);
        $theme  = $this->model->getById($id);
        if (!empty($errors)) {
            require_once __DIR__ . '/../views/front/theme/edit.php';
            return;
        }
        $this->model->update($id, $this->nettoyer($_POST));
        header('Location: index.php?action=list&msg=updated');
        exit;
    }

    public function delete($id) {
        $this->model->delete($id);
        header('Location: index.php?action=list&msg=deleted');
        exit;
    }

    // ── BACK OFFICE ───────────────────────────────
    public function adminIndex() {
        $themes    = $this->model->getAll();
        $total     = $this->model->count();
        $avgGreen  = $this->model->avgGreen();
        $totalP    = $this->model->totalProjets();
        $topThemes = $this->model->getTopThemes();
        require_once __DIR__ . '/../views/back/theme/list.php';
    }

    public function adminCreate() {
        $errors = [];
        require_once __DIR__ . '/../views/back/theme/create.php';
    }

    public function adminStore() {
        $errors = $this->valider($_POST);
        if (!empty($errors)) {
            require_once __DIR__ . '/../views/back/theme/create.php';
            return;
        }
        $this->model->create($this->nettoyer($_POST));
        header('Location: index.php?action=admin&msg=created');
        exit;
    }

    public function adminEdit($id) {
        $errors = [];
        $theme  = $this->model->getById($id);
        if (!$theme) { header('Location: index.php?action=admin'); exit; }
        require_once __DIR__ . '/../views/back/theme/edit.php';
    }

    public function adminUpdate($id) {
        $errors = $this->valider($_POST);
        $theme  = $this->model->getById($id);
        if (!empty($errors)) {
            require_once __DIR__ . '/../views/back/theme/edit.php';
            return;
        }
        $this->model->update($id, $this->nettoyer($_POST));
        header('Location: index.php?action=admin&msg=updated');
        exit;
    }

    public function adminDelete($id) {
        $this->model->delete($id);
        header('Location: index.php?action=admin&msg=deleted');
        exit;
    }

    public function adminTrending() {
        $topThemes = $this->model->getTopThemes();
        $total     = $this->model->count();
        $avgGreen  = $this->model->avgGreen();
        require_once __DIR__ . '/../views/back/theme/trending.php';
    }

    // ── VALIDATION PHP — pas HTML5 seul ──────────
    private function valider($p) {
        $errors = [];
        if (empty(trim($p['nom']??''))) $errors[] = "Le nom est obligatoire.";
        elseif (strlen(trim($p['nom']))<3) $errors[] = "Le nom doit contenir au moins 3 caractères.";
        elseif (strlen(trim($p['nom']))>150) $errors[] = "Le nom ne peut pas dépasser 150 caractères.";

        if (empty(trim($p['description']??''))) $errors[] = "La description est obligatoire.";
        elseif (strlen(trim($p['description']))<20) $errors[] = "La description doit faire au moins 20 caractères.";

        $cats = ['tech','digital','energie renouvelable','agriculture durable','economie circulaire','mobilite verte','entrepreneuriat','autre'];
        if (empty($p['categorie']??'') || !in_array($p['categorie'],$cats)) $errors[] = "Veuillez sélectionner une catégorie valide.";

        if (empty(trim($p['mots_cles']??''))) $errors[] = "Les mots-clés sont obligatoires.";

        $sg = $p['score_green']??'';
        if ($sg===''||!is_numeric($sg)) $errors[] = "Le score Green doit être un nombre.";
        elseif ((int)$sg<0||(int)$sg>100) $errors[] = "Le score Green doit être entre 0 et 100.";

        return $errors;
    }

    private function nettoyer($p) {
        return [
            'nom'         => htmlspecialchars(trim($p['nom'])),
            'description' => htmlspecialchars(trim($p['description'])),
            'categorie'   => htmlspecialchars(trim($p['categorie'])),
            'mots_cles'   => htmlspecialchars(trim($p['mots_cles'])),
            'score_green' => (int)$p['score_green'],
        ];
    }
}
