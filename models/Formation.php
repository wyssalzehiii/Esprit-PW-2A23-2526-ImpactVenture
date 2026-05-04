<?php

require_once __DIR__ . '/../config/database.php';

class Formation {
    private $id;
    private $title;
    private $content;
    private $categorie;
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Getters
    public function getId() { return $this->id; }
    public function getTitle() { return $this->title; }
    public function getContent() { return $this->content; }
    public function getCategorie() { return $this->categorie; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setTitle($title) { $this->title = $title; }
    public function setContent($content) { $this->content = $content; }
    public function setCategorie($categorie) { $this->categorie = $categorie; }

    // CRUD Methods
    public function findAll() {
        $query = "SELECT * FROM formations ORDER BY id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $query = "SELECT * FROM formations WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch();
    }

    public function create() {
        $query = "INSERT INTO formations (title, content, categorie) VALUES (:title, :content, :categorie)";
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':content', $this->content);
        $stmt->bindParam(':categorie', $this->categorie);
        
        if ($stmt->execute()) {
            $this->id = $this->db->lastInsertId();
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE formations SET title = :title, content = :content, categorie = :categorie WHERE id = :id";
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':content', $this->content);
        $stmt->bindParam(':categorie', $this->categorie);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM formations WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function findByUserProject($userId) {
        // Join with projects table, only linked projects
        $query = "SELECT DISTINCT formations.* FROM formations
                  JOIN projects ON formations.categorie = projects.category
                  WHERE projects.user_id = :userId AND projects.linked_to_path = 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
