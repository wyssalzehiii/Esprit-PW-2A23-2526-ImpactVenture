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
            $montant         = $_POST['montant_demande'] ?? null;
            $currency        = $_POST['currency'] ?? 'TND';
            $phone           = trim($_POST['phone'] ?? '');

            if ($fiche_id && $investisseur_id && $message) {
                $success = $this->model->submitDemande(
                    $fiche_id,
                    $investisseur_id,
                    $message,
                    $montant,
                    $currency,
                    $phone
                );

                if ($success) {
                    header("Location: ?action=financement&success=1");
                    exit;
                }
            }
        }
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
    public function adminEdit($id) {
        $investisseur = $this->model->getById($id);
        require_once __DIR__ . '/../views/back/investisseur/edit.php';
    }
    public function adminUpdate($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $data = [
                'nom' => $_POST['nom'],
                'organisation' => $_POST['organisation'],
                'secteur_focus' => $_POST['secteur_focus'],
                'montant_min' => $_POST['montant_min'],
                'montant_max' => $_POST['montant_max'],
                'description' => $_POST['description'],
                'photo' => $_POST['photo'],
                'linkedin' => $_POST['linkedin'],
            ];

            $this->model->updateInvestisseur($id, $data);
        }

        header("Location: index.php?action=admin_investisseur_list");
        exit;
    }
    public function editDemande($id) {
        if (!$id) {
            header("Location: index.php?action=mes_demandes");
            exit;
        }

        $demande = $this->model->getDemandeById($id);

        if (!$demande) {
            header("Location: index.php?action=mes_demandes");
            exit;
        }

        require __DIR__ . '/../views/front/financement/edit_demande.php';
    }
    public function updateDemande($id) {
        if (!$id) {
            header("Location: index.php?action=mes_demandes");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $message = trim($_POST['message'] ?? '');

            if (!empty($message)) {
                $this->model->updateDemande($id, $message);
            }
        }

        header("Location: index.php?action=mes_demandes");
        exit;
    }
    public function getDemandeById($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("
        SELECT d.*, i.nom AS investisseur_nom
        FROM demandes_financement d
        JOIN investisseurs i ON i.id = d.investisseur_id
        WHERE d.id = ?
    ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
// ================= ESPACE INVESTISSEUR =================

    /**
     * Dashboard for the logged-in investor.
     * TODO: Replace hardcoded $investisseur_id = 1 with $_SESSION['investisseur_id']
     */
    public function espaceInvestisseur() {
        $investisseur_id = 1; // TODO: use session

        $investisseur = $this->model->getById($investisseur_id);

        if (!$investisseur) {
            header("Location: index.php?action=financement");
            exit;
        }

        // All demandes sent to this investor
        $demandes    = $this->model->getDemandesByInvestisseur($investisseur_id);

        // Split into pending vs portfolio (accepted)
        $en_attente  = array_filter($demandes, fn($d) => $d['statut'] === 'en_attente');
        $acceptees   = array_filter($demandes, fn($d) => $d['statut'] === 'accepte');
        $refusees    = array_filter($demandes, fn($d) => $d['statut'] === 'refuse');

        // Stats
        $total       = count($demandes);
        $nb_accepte  = count($acceptees);
        $nb_attente  = count($en_attente);
        $taux        = $total > 0 ? round(($nb_accepte / $total) * 100) : 0;
        $total_invested = $this->model->getTotalInvested($investisseur_id);

        require_once __DIR__ . '/../views/front/financement/espace_investisseur.php';
    }

    /**
     * Accept or refuse a demande, with optional motif.
     * TODO: Verify the demande actually belongs to the logged-in investor before acting.
     */
    public function investisseurAction() {
        $id     = $_GET['id']     ?? null;
        $status = $_GET['status'] ?? null;


        if ($id && in_array($status, ['accepte', 'refuse'])) {
            $motif = ($status === 'refuse') ? trim($_POST['motif'] ?? '') : null;

            $success = $this->model->updateDemandeStatus($id, $status, $motif);

            if ($success) {
                require_once __DIR__ . '/../helpers/TwilioHelper.php';
                $twilio = new TwilioHelper();
                $sent = $twilio->sendNotification($status, $motif);

                if ($sent) {
                    $_SESSION['flash_success'] = "Demande mise à jour + SMS envoyé à votre numéro.";
                } else {
                    $_SESSION['flash_error'] = "Demande mise à jour, mais SMS non envoyé (erreur Twilio).";
                }
            }
        }

        header("Location: index.php?action=espace_investisseur");
        exit;
    }
    public function fundDemande() {
        $id = $_GET['id'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
            $montant = $_POST['montant'] ?? null;

            if ($montant && is_numeric($montant)) {
                $this->model->fundDemande($id, $montant);
            }
        }

        header("Location: index.php?action=espace_investisseur");
        exit;
    }
}