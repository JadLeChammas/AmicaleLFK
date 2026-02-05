<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
requireAdmin(); // 🔐 Admin only

// Connexion à la BDD
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

// Vérifie que la requête est bien POST avec un ID valide
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);

    if ($user_id === 1) {
        die("⛔ Impossible de supprimer l'administrateur principal !");
    }

    // Préparation et exécution
    $stmt = $conn->prepare("DELETE FROM membres WHERE id = ?");
    if (!$stmt) {
        die("Erreur SQL : " . $conn->error);
    }

    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        header("Location: admin_manage_users.php?success=Compte supprimé avec succès");
    } else {
        header("Location: admin_manage_users.php?error=Aucun compte supprimé");
    }

    $stmt->close();
    $conn->close();
    exit();
} else {
    die("⛔ Requête invalide !");
}
?>
