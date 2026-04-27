<?php
require_once __DIR__ . '/../../config/database.php';

class Projet {
    private $pdo;

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->getConnection();
    }

    public function getAll() {
        $stmt = $this->pdo->query("
            SELECT 
                p.id_projet,
                p.titre,
                p.description,
                p.statut,
                p.score_ia,
                p.score_green,
                f.nom as entreprise_nom,
                f.categorie
            FROM projet p
            LEFT JOIN fiche_entreprise f ON p.id_fiche_entreprise = f.id
            ORDER BY p.date_soumission DESC
        ");
        return $stmt->fetchAll();
    }

    public function create($data) {
        $stmt = $this->pdo->prepare("
            INSERT INTO projet (titre, description, id_fiche_entreprise, id_user, statut)
            VALUES (:titre, :description, :id_fiche_entreprise, :id_user, 'soumis')
        ");
        return $stmt->execute([
            'titre'               => trim($data['titre']),
            'description'         => trim($data['description']),
            'id_fiche_entreprise' => (int)$data['id_fiche_entreprise'],
            'id_user'             => 2   // entrepreneur par défaut
        ]);
    }
}