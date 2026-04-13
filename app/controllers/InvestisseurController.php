<?php
require_once __DIR__ . '/../models/Investisseur.php';

class InvestisseurController {

    private $model;

    public function __construct() {
        $this->model = new Investisseur();
    }

    // Main page: list of all impact investors
    public function index() {
        $investisseurs = $this->model->getAll();
        require_once __DIR__ . '/../views/front/financement/list.php';
    }

    // Submit a funding request
    public function submit() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fiche_id        = $_POST['fiche_entreprise_id'] ?? null;
            $investisseur_id = $_POST['investisseur_id'] ?? null;
            $message         = trim($_POST['message'] ?? '');

            if ($fiche_id && $investisseur_id && $message) {
                $success = $this->model->submitDemande($fiche_id, $investisseur_id, $message);
                if ($success) {
                    header("Location: ?action=financement&success=1");
                    exit;
                }
            }
        }
        // If something fails, go back to the page
        header("Location: ?action=financement");
        exit;
    }
}