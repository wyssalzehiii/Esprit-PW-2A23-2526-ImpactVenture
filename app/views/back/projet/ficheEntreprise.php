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
            LEFT JOIN projet p ON p.id_theme = f.id   -- jointure avec Projet
            GROUP BY f.id
            ORDER BY f.created_at DESC
        ");
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM fiche_entreprise WHERE id = :id");
        $stmt->execute(['id' => (int)$id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->pdo->prepare("
            INSERT INTO fiche_entreprise (nom, description, categorie, mots_cles, score_green, created_at)
            VALUES (:nom, :description, :categorie, :mots_cles, :score_green, NOW())
        ");
        return $stmt->execute($data);
    }

    public function update($id, $data) {
        $stmt = $this->pdo->prepare("
            UPDATE fiche_entreprise SET
                nom = :nom, description = :description,
                categorie = :categorie, mots_cles = :mots_cles,
                score_green = :score_green
            WHERE id = :id
        ");
        $data['id'] = (int)$id;
        return $stmt->execute($data);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM fiche_entreprise WHERE id = :id");
        return $stmt->execute(['id' => (int)$id]);
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

    public function getTopFiches($limit = 5) {
        $stmt = $this->pdo->prepare("
            SELECT f.nom, f.categorie, f.score_green, COUNT(p.id_projet) as nb_projets
            FROM fiche_entreprise f
            LEFT JOIN projet p ON p.id_theme = f.id
            GROUP BY f.id
            ORDER BY nb_projets DESC
            LIMIT :lim
        ");
        $stmt->bindValue(':lim', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}