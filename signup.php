<?php
include "db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "error: Méthode non autorisée";
    exit;
}

$name     = trim($_POST['name'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$role     = trim($_POST['role'] ?? '');

if (empty($name) || empty($email) || empty($password) || empty($role)) {
    echo "error: Tous les champs sont obligatoires";
    exit;
}

// Hash du mot de passe
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Requête préparée (sécurisée)
$stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $hashedPassword, $role);

if ($stmt->execute()) {
    echo "success";
} else {
    echo "error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>