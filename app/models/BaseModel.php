<?php
abstract class BaseModel {
    protected $pdo;

    public function __construct() {
        require_once __DIR__ . '/../../config/database.php';
        $db = new Database();
        $this->pdo = $db->getConnection();
    }

    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}