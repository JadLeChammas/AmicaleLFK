<?php
session_start();
require_once __DIR__ . '/config.php';

require_once 'auth.php';
verifierRole(['admin']);

// Vérifie que l'ID est bien fourni et valide
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: admin_approve_users.php?error=invalid_id');
    exit;
}

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

$id = (int) $_GET['id'];

// Mise à jour du champ is_approved
$stmt = $conn->prepare("UPDATE membres SET is_approved = 1 WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

// Fermeture
$stmt->close();
$conn->close();

// Redirection avec message de succès
header("Location: admin_approve_users.php?approved=true");
exit;
