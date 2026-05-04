<?php

require_once __DIR__ . '/../config/database.php';

class Badge {
    private $id;
    private $nom;
    private $description;
    private $image;
    private $points;
    private $formation_id;
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Getters
    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getDescription() { return $this->description; }
    public function getImage() { return $this->image; }
    public function getPoints() { return $this->points; }
    public function getFormationId() { return $this->formation_id; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setNom($nom) { $this->nom = $nom; }
    public function setDescription($description) { $this->description = $description; }
    public function setImage($image) { $this->image = $image; }
    public function setPoints($points) { $this->points = $points; }
    public function setFormationId($formation_id) { $this->formation_id = $formation_id; }

    // CRUD Methods
    public function findAll() {
        $query = "SELECT badges.*, formations.title as formation_title FROM badges JOIN formations ON badges.formation_id = formations.id ORDER BY badges.id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $query = "SELECT badges.*, formations.title as formation_title FROM badges JOIN formations ON badges.formation_id = formations.id WHERE badges.id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch();
    }

    public function findByFormationId($formation_id) {
        $query = "SELECT * FROM badges WHERE formation_id = :formation_id ORDER BY id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':formation_id', $formation_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function create() {
        $query = "INSERT INTO badges (nom, description, image, points, formation_id) VALUES (:nom, :description, :image, :points, :formation_id)";
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':image', $this->image);
        $stmt->bindParam(':points', $this->points, PDO::PARAM_INT);
        $stmt->bindParam(':formation_id', $this->formation_id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            $this->id = $this->db->lastInsertId();
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE badges SET nom = :nom, description = :description, image = :image, points = :points, formation_id = :formation_id WHERE id = :id";
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':image', $this->image);
        $stmt->bindParam(':points', $this->points, PDO::PARAM_INT);
        $stmt->bindParam(':formation_id', $this->formation_id, PDO::PARAM_INT);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM badges WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function awardBadge($userId, $badgeId) {
        // Check if already awarded
        $query = "SELECT id FROM user_badges WHERE user_id = :user_id AND badge_id = :badge_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':badge_id', $badgeId, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->fetch()) {
            return true; // Already awarded
        }

        $query = "INSERT INTO user_badges (user_id, badge_id) VALUES (:user_id, :badge_id)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':badge_id', $badgeId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getUserBadges($userId) {
        $query = "SELECT user_badges.*, badges.nom, badges.description, badges.image, badges.points, formations.title as formation_title 
                  FROM user_badges 
                  JOIN badges ON user_badges.badge_id = badges.id 
                  JOIN formations ON badges.formation_id = formations.id 
                  WHERE user_badges.user_id = :user_id 
                  ORDER BY user_badges.awarded_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}