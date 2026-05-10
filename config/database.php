<?php
class config
{
    private static $pdo = null;

    public static function getConnexion()
    {
        if (!isset(self::$pdo)) {
            $username   = "root";
            $password   = "";
            $dbname     = "impactventure_db";

            $hosts = ["127.0.0.1", "localhost"];
            $ports = [3306, 3307];
            $errors = [];

            foreach ($hosts as $host) {
                foreach ($ports as $port) {
                    try {
                        self::$pdo = new PDO(
                            "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4",
                            $username,
                            $password,
                            [
                                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                                PDO::ATTR_TIMEOUT => 3
                            ]
                        );
                        return self::$pdo;
                    } catch (Exception $e) {
                        $errors[] = "{$host}:{$port} -> " . $e->getMessage();
                    }
                }
            }

            die(
                "Erreur : connexion MySQL impossible. Verifiez que MySQL est demarre dans XAMPP "
                . "et que la base '{$dbname}' existe. Details: " . implode(" | ", $errors)
            );
        }
        return self::$pdo;
    }
}
?>