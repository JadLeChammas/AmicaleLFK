<?php
session_start();

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
requireAdmin();

require_once 'admin_log.php'; // 🔥 LOG ADMIN

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin_approve_users.php?error=no_id");
    exit();
}

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

$id = (int) $_GET['id'];

/* 🔍 Récupérer email AVANT suppression pour le log */
$stmt = $conn->prepare("SELECT email FROM membres WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    $conn->close();
    header("Location: admin_approve_users.php?error=user_not_found");
    exit();
}

/* 🗑️ SUPPRESSION */
$stmt = $conn->prepare("DELETE FROM membres WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    /* 🔥 LOG ADMIN (APRÈS SUCCÈS) */
    logAdminAction(
        $conn,
        $_SESSION['user_id'],
        "Refus / suppression d’un compte",
        "Utilisateur ID #".$id." – ".$user['email']
    );

    $stmt->close();
    $conn->close();

    header("Location: admin_approve_users.php?denied=true");
    exit();

} else {
    $stmt->close();
    $conn->close();
    header("Location: admin_approve_users.php?error=delete_fail");
    exit();
}
