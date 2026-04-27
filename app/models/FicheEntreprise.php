<?php
require_once __DIR__ . '/../../config/database.php';

class FicheEntreprise {
    private $pdo;

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->getConnection();
    }

    public function getAll() {
        $stmt = $this->pdo->query("
            SELECT f.*, COUNT(p.id_projet) as nb_projets 
            FROM fiche_entreprise f 
            LEFT JOIN projet p ON p.id_fiche_entreprise = f.id 
            GROUP BY f.id 
            ORDER BY f.created_at DESC
        ");
        return $stmt->fetchAll();
    }

    public function create($data) {
        $stmt = $this->pdo->prepare("
            INSERT INTO fiche_entreprise (nom, description, categorie, mots_cles, score_green)
            VALUES (:nom, :description, :categorie, :mots_cles, :score_green)
        ");
        return $stmt->execute($data);
    }

    public function count() {
        return (int)$this->pdo->query("SELECT COUNT(*) FROM fiche_entreprise")->fetchColumn();
    }

    public function avgGreen() {
        $val = $this->pdo->query("SELECT AVG(score_green) FROM fiche_entreprise")->fetchColumn();
        return round($val ?: 0);
    }

    public function totalProjets() {
        return (int)$this->pdo->query("SELECT COUNT(*) FROM projet")->fetchColumn();
    }
}