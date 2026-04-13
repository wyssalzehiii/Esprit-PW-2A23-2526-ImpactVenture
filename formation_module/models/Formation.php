<?php

require_once __DIR__ . '/../config/database.php';

class Formation {
    private $id;
    private $title;
    private $content;
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Getters
    public function getId() { return $this->id; }
    public function getTitle() { return $this->title; }
    public function getContent() { return $this->content; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setTitle($title) { $this->title = $title; }
    public function setContent($content) { $this->content = $content; }

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
        
        $row = $stmt->fetch();
        if ($row) {
            $this->id = $row['id'];
            $this->title = $row['title'];
            $this->content = $row['content'];
            return $this;
        }
        return false;
    }

    public function create() {
        $query = "INSERT INTO formations (title, content) VALUES (:title, :content)";
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':content', $this->content);
        
        if ($stmt->execute()) {
            $this->id = $this->db->lastInsertId();
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE formations SET title = :title, content = :content WHERE id = :id";
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':content', $this->content);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM formations WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
}
?>
