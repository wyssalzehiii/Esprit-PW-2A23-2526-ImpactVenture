<?php
require_once __DIR__ . '/../../config/database.php';

class Investisseur {
    private $pdo;

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->getConnection();
    }

    // Get all investors
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM investisseurs ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    // Get one investor
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM investisseurs WHERE id = :id");
        $stmt->execute(['id' => (int)$id]);
        return $stmt->fetch();
    }

    // Submit funding request
    public function submitDemande($fiche_entreprise_id, $investisseur_id, $message, $montant_demande = null, $currency = 'TND', $phone = null) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO demandes_financement 
             (fiche_entreprise_id, investisseur_id, message, montant_demande, currency, phone, statut, date_demande)
             VALUES (:fiche_id, :inv_id, :message, :montant, :currency, :phone, 'en_attente', NOW())"
        );

        return $stmt->execute([
            'fiche_id'   => (int)$fiche_entreprise_id,
            'inv_id'     => (int)$investisseur_id,
            'message'    => $message,
            'montant'    => $montant_demande,
            'currency'   => $currency,
            'phone'      => $phone
        ]);
    }

    // Get all funding requests for a specific fiche_entreprise
    public function getDemandesByFiche($fiche_entreprise_id) {
        $stmt = $this->pdo->prepare(
            "SELECT d.*, i.nom as investisseur_nom, i.organisation
             FROM demandes_financement d
             JOIN investisseurs i ON i.id = d.investisseur_id
             WHERE d.fiche_entreprise_id = :fiche_id
             ORDER BY d.date_demande DESC"
        );
        $stmt->execute(['fiche_id' => (int)$fiche_entreprise_id]);
        return $stmt->fetchAll();
    }

    public function deleteDemande($demande_id) {
        $stmt = $this->pdo->prepare("DELETE FROM demandes_financement WHERE id = :id");
        return $stmt->execute(['id' => (int)$demande_id]);
    }

    // Create investor
    public function createInvestisseur($data) {
        $stmt = $this->pdo->prepare("
            INSERT INTO investisseurs
            (nom, organisation, secteur_focus, montant_min, montant_max, description, photo, linkedin)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['nom'],
            $data['organisation'],
            $data['secteur_focus'],
            $data['montant_min'],
            $data['montant_max'],
            $data['description'],
            $data['photo'],
            $data['linkedin']
        ]);
    }

    // Delete investor
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM investisseurs WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Get all demandes (admin)
    public function getAllDemandes() {
        $sql = "SELECT d.*, i.nom as investisseur_nom
                FROM demandes_financement d
                JOIN investisseurs i ON d.investisseur_id = i.id
                ORDER BY d.date_demande DESC";
        return $this->pdo->query($sql)->fetchAll();
    }

    // Update investor
    public function updateInvestisseur($id, $data) {
        $stmt = $this->pdo->prepare("
            UPDATE investisseurs SET
                nom           = ?,
                organisation  = ?,
                secteur_focus = ?,
                montant_min   = ?,
                montant_max   = ?,
                description   = ?,
                photo         = ?,
                linkedin      = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['nom'],
            $data['organisation'],
            $data['secteur_focus'],
            $data['montant_min'],
            $data['montant_max'],
            $data['description'],
            $data['photo'],
            $data['linkedin'],
            $id
        ]);
    }

    // Get single demande by ID
    public function getDemandeById($id) {
        $stmt = $this->pdo->prepare("
            SELECT d.*, i.nom AS investisseur_nom
            FROM demandes_financement d
            JOIN investisseurs i ON i.id = d.investisseur_id
            WHERE d.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update demande message
    public function updateDemande($id, $message) {
        $stmt = $this->pdo->prepare("
            UPDATE demandes_financement
            SET message = ?
            WHERE id = ?
        ");
        return $stmt->execute([$message, $id]);
    }

    // Create investor (legacy alias — same as createInvestisseur)
    public function create($data) {
        $stmt = $this->pdo->prepare("
            INSERT INTO investisseurs
            (nom, organisation, secteur_focus, montant_min, montant_max, description, photo, linkedin)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['nom'],
            $data['organisation'],
            $data['secteur_focus'],
            $data['montant_min'],
            $data['montant_max'],
            $data['description'],
            $data['photo'],
            $data['linkedin']
        ]);
    }

    // Get all demandes received by a specific investor (Espace Investisseur)
    public function getDemandesByInvestisseur($investisseur_id) {
        $stmt = $this->pdo->prepare("
            SELECT d.*, i.nom AS investisseur_nom, i.organisation
            FROM demandes_financement d
            JOIN investisseurs i ON i.id = d.investisseur_id
            WHERE d.investisseur_id = ?
            ORDER BY d.date_demande DESC
        ");
        $stmt->execute([$investisseur_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Accept or refuse a demande with optional refusal reason
    public function updateDemandeStatus($id, $status, $motif = null) {
        $stmt = $this->pdo->prepare("
        UPDATE demandes_financement
        SET statut = ?, motif_refus = ?
        WHERE id = ?
    ");
        return $stmt->execute([$status, $motif, $id]);
    }
    public function fundDemande($id, $montant) {
        $stmt = $this->pdo->prepare("
        UPDATE demandes_financement
        SET montant_investi = ?, date_financement = NOW()
        WHERE id = ? AND statut = 'accepte'
    ");
        return $stmt->execute([$montant, $id]);
    }
    public function getTotalInvested($investisseur_id) {
        $stmt = $this->pdo->prepare("
        SELECT SUM(montant_investi) as total
        FROM demandes_financement
        WHERE investisseur_id = ? AND montant_investi IS NOT NULL
    ");
        $stmt->execute([$investisseur_id]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res['total'] ?? 0;
    }
}