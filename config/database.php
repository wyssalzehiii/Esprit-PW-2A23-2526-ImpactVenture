<?php
class Database {
    private $host   = 'localhost';
    private $dbname = 'impactventure_db';   // ← Important : avec _db
    private $user   = 'root';
    private $pass   = '';
    private $pdo;

    public function getConnection() {
        try {
            $this->pdo = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4",
                $this->user, $this->pass
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $this->pdo;
        } catch (PDOException $e) {
            die("Erreur PDO : " . $e->getMessage());
        }
    }
}