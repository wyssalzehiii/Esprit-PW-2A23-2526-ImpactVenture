<?php
class Database {
    private $host = 'localhost';
    private $dbname = 'impactventure_db';
    private $user = 'root';
    private $pass = '';
    private $pdo;

    public function getConnection() {
        try {
            $this->pdo = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4",
                $this->user, 
                $this->pass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            return $this->pdo;
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }
}