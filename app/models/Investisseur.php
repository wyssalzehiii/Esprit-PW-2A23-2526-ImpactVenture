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
}