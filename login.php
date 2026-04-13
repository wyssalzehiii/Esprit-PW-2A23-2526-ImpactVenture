<?php
include "db.php";

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    echo "error: Email et mot de passe requis";
    exit;
}

$stmt = $conn->prepare("SELECT password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 1) {
    $stmt->bind_result($hashedPassword);
    $stmt->fetch();
    
    if (password_verify($password, $hashedPassword)) {
        echo "success";
    } else {
        echo "error: Mot de passe incorrect";
    }
} else {
    echo "error: Email non trouvé";
}

$stmt->close();
$conn->close();
?>