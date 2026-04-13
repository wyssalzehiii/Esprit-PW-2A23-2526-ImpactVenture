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
    // Show investor detailed profile
    public function profile($id) {
        if (!$id) {
            header("Location: ?action=financement");
            exit;
        }

        $investisseur = $this->model->getById($id);

        if (!$investisseur) {
            header("Location: ?action=financement");
            exit;
        }

        require_once __DIR__ . '/../views/front/financement/profile.php';
    }
    // Mes Demandes de Financement (Entrepreneur view)
    public function mesDemandes() {
        $fiche_id = 1;                    // TODO: later we'll use real user session
        $demandes = $this->model->getDemandesByFiche($fiche_id);

        require_once __DIR__ . '/../views/front/financement/mes_demandes.php';
    }
    public function deleteDemande() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->model->deleteDemande($id);
        }
        header("Location: ?action=mes_demandes");
        exit;
    }
    // ================= ADMIN INVESTISSEURS =================

// List all investors (admin)
    public function adminList() {
        $investisseurs = $this->model->getAll();
        require_once __DIR__ . '/../views/back/financement/list.php';
    }

// Add investor
    public function addInvestisseur() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->create($_POST);
            header("Location: ?action=admin_financement");
            exit;
        }

        require_once __DIR__ . '/../views/back/financement/create.php';
    }

// Delete investor
    public function deleteInvestisseur() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->model->delete($id);
        }

        header("Location: ?action=admin_financement");
        exit;
    }


// ================= ADMIN DEMANDES =================

// Show all demandes
    public function adminDemandes() {
        $demandes = $this->model->getAllDemandes();
        require_once __DIR__ . '/../views/back/financement/demandes.php';
    }

// Update status (accept/refuse)
    public function updateDemandeStatus() {
        $id = $_GET['id'] ?? null;
        $status = $_GET['status'] ?? null;

        if ($id && in_array($status, ['accepte', 'refuse'])) {
            $this->model->updateDemandeStatus($id, $status);
        }

        header("Location: ?action=admin_demandes");
        exit;
    }
    public function adminCreate() {
        require_once __DIR__ . '/../views/back/investisseur/create.php';
    }
    public function adminStore() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $data = [
                'nom' => $_POST['nom'] ?? '',
                'organisation' => $_POST['organisation'] ?? null,
                'secteur_focus' => $_POST['secteur_focus'] ?? null,
                'montant_min' => $_POST['montant_min'] ?? null,
                'montant_max' => $_POST['montant_max'] ?? null,
                'description' => $_POST['description'] ?? null,
                'photo' => $_POST['photo'] ?? null,
                'linkedin' => $_POST['linkedin'] ?? null,
            ];

            $this->model->createInvestisseur($data);
        }

        header("Location: index.php?action=admin_investisseur_list");
        exit;
    }

}