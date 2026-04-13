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
    public function submitDemande($fiche_entreprise_id, $investisseur_id, $message) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO demandes_financement (fiche_entreprise_id, investisseur_id, message) 
             VALUES (:fiche_id, :inv_id, :message)"
        );
        return $stmt->execute([
            'fiche_id' => (int)$fiche_entreprise_id,
            'inv_id'   => (int)$investisseur_id,
            'message'  => $message
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
        $dbObj = new Database();
        $db = $dbObj->getConnection();

        $sql = "INSERT INTO investisseurs 
    (nom, organisation, secteur_focus, montant_min, montant_max, description, photo, linkedin)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $db->prepare($sql);
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
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM investisseurs WHERE id = ?");
        return $stmt->execute([$id]);
    }

// Get all demandes (admin)
    public function getAllDemandes() {
        $dbObj = new Database();
        $db = $dbObj->getConnection();

        $sql = "SELECT d.*, i.nom as investisseur_nom 
            FROM demandes_financement d
            JOIN investisseurs i ON d.investisseur_id = i.id
            ORDER BY d.date_demande DESC";

        return $db->query($sql)->fetchAll();
    }

// Update demande status
    public function updateDemandeStatus($id, $status) {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE demandes_financement SET statut = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }
}