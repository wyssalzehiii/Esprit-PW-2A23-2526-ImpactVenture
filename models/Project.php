<?php

require_once __DIR__ . '/../config/database.php';

class Project {
    private $id;
    private $userId;
    private $title;
    private $category;
    private $description;
    private $linkedToPath;
    private $createdAt;
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Getters
    public function getId() { return $this->id; }
    public function getUserId() { return $this->userId; }
    public function getTitle() { return $this->title; }
    public function getCategory() { return $this->category; }
    public function getDescription() { return $this->description; }
    public function getLinkedToPath() { return $this->linkedToPath; }
    public function getCreatedAt() { return $this->createdAt; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setUserId($userId) { $this->userId = $userId; }
    public function setTitle($title) { $this->title = $title; }
    public function setCategory($category) { $this->category = $category; }
    public function setDescription($description) { $this->description = $description; }
    public function setLinkedToPath($linkedToPath) { $this->linkedToPath = $linkedToPath; }
    public function setCreatedAt($createdAt) { $this->createdAt = $createdAt; }

    // CRUD methods
    public function findAllByUser($userId) {
        $query = "SELECT * FROM projects WHERE user_id = :userId ORDER BY created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $query = "SELECT * FROM projects WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function create() {
        $query = "INSERT INTO projects (user_id, title, category, description, linked_to_path) VALUES (:userId, :title, :category, :description, :linkedToPath)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $this->userId, PDO::PARAM_INT);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':category', $this->category);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':linkedToPath', $this->linkedToPath, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function update() {
        $query = "UPDATE projects SET title = :title, category = :category, description = :description, linked_to_path = :linkedToPath WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':category', $this->category);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':linkedToPath', $this->linkedToPath, PDO::PARAM_INT);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM projects WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function toggleLinkedToPath() {
        $query = "UPDATE projects SET linked_to_path = NOT linked_to_path WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>