<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
requireAdmin();
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

$sql = "SELECT id, password FROM membres";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $id = $row['id'];
    $plain_password = $row['password'];

    if (!password_needs_rehash($plain_password, PASSWORD_DEFAULT)) {
        continue;
    }

    $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);
    
    $update_sql = "UPDATE membres SET password = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $hashed_password, $id);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
echo "Mots de passe hachés avec succès.";
?>
