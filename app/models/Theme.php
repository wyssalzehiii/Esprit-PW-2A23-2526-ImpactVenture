<?php
/**
 * Model Theme — Programmation Orientée Objet + PDO
 * Contient toutes les requêtes SQL pour l'entité Theme
 */
require_once __DIR__ . '/../../config/database.php';

class Theme {
    private $pdo;

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->getConnection();
    }

    // READ ALL
    public function getAll() {
        $stmt = $this->pdo->query(
            "SELECT t.*, COUNT(p.id) as nb_projets
             FROM fiche_entreprise t
             LEFT JOIN projet p ON p.theme_id = t.id
             GROUP BY t.id
             ORDER BY t.created_at DESC"
        );
        return $stmt->fetchAll();
    }

    // READ ONE
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM fiche_entreprise WHERE id = :id");
        $stmt->execute(['id' => (int)$id]);
        return $stmt->fetch();
    }

    // CREATE
    public function create($data) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO fiche_entreprise (nom, description, categorie, mots_cles, score_green, created_at)
             VALUES (:nom, :description, :categorie, :mots_cles, :score_green, NOW())"
        );
        return $stmt->execute([
            'nom'         => $data['nom'],
            'description' => $data['description'],
            'categorie'   => $data['categorie'],
            'mots_cles'   => $data['mots_cles'],
            'score_green' => (int)$data['score_green'],
        ]);
    }

    // UPDATE
    public function update($id, $data) {
        $stmt = $this->pdo->prepare(
            "UPDATE fiche_entreprise SET
                nom         = :nom,
                description = :description,
                categorie   = :categorie,
                mots_cles   = :mots_cles,
                score_green = :score_green
             WHERE id = :id"
        );
        return $stmt->execute([
            'id'          => (int)$id,
            'nom'         => $data['nom'],
            'description' => $data['description'],
            'categorie'   => $data['categorie'],
            'mots_cles'   => $data['mots_cles'],
            'score_green' => (int)$data['score_green'],
        ]);
    }

    // DELETE
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM fiche_entreprise WHERE id = :id");
        return $stmt->execute(['id' => (int)$id]);
    }

    // COUNT total thèmes
    public function count() {
        return (int)$this->pdo->query("SELECT COUNT(*) FROM fiche_entreprise")->fetchColumn();
    }

    // SCORE MOYEN green
    public function avgGreen() {
        return round($this->pdo->query("SELECT AVG(score_green) FROM fiche_entreprise")->fetchColumn());
    }

    // TOTAL PROJETS — méthode manquante ajoutée ✅
    public function totalProjets() {
        return (int)$this->pdo->query("SELECT COUNT(*) FROM projet")->fetchColumn();
    }

    // TOP THÈMES par nombre de projets
    public function getTopThemes($limit = 5) {
        $stmt = $this->pdo->prepare(
            "SELECT t.nom, t.categorie, t.score_green, COUNT(p.id) as nb_projets
             FROM fiche_entreprise t
             LEFT JOIN projet p ON p.theme_id = t.id
             GROUP BY t.id
             ORDER BY nb_projets DESC
             LIMIT :lim"
        );
        $stmt->bindValue(':lim', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}