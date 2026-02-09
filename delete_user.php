<?php
session_start();

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
requireAdmin(); // 🔐 Admin only
require_once 'admin_log.php'; // 🔥 LOG ADMIN

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

/* Vérification requête */
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST['user_id'])) {
    die("⛔ Requête invalide !");
}

$user_id = (int) $_POST['user_id'];

/* Protection admin principal */
if ($user_id === 1) {
    die("⛔ Impossible de supprimer l'administrateur principal !");
}

/* 🔍 Récupérer email AVANT suppression pour le log */
$stmt = $conn->prepare("SELECT email FROM membres WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    $conn->close();
    header("Location: admin_manage_users.php?error=Utilisateur introuvable");
    exit();
}

/* 🗑️ SUPPRESSION */
$stmt = $conn->prepare("DELETE FROM membres WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {

    /* 🔥 LOG ADMIN */
    logAdminAction(
        $conn,
        $_SESSION['user_id'],
        "Suppression d’un utilisateur",
        "Utilisateur ID #".$user_id." – ".$user['email']
    );

    $stmt->close();
    $conn->close();

    header("Location: admin_manage_users.php?success=Compte supprimé avec succès");
    exit();

} else {
    $stmt->close();
    $conn->close();
    header("Location: admin_manage_users.php?error=Aucun compte supprimé");
    exit();
}
