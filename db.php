<?php
$conn = new mysqli("localhost", "root", "", "impactventure_db");
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}
?>