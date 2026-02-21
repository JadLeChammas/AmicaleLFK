<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
requireAdmin();
require_once __DIR__ . '/admin_log.php'; // ✅ LOGGER

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin_approve_users.php?error=no_id");
    exit();
}

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

$id = (int) $_GET['id'];

/* 🔍 Optionnel mais PRO : récupérer l'email avant validation */
$stmtInfo = $conn->prepare("SELECT email FROM membres WHERE id = ?");
$stmtInfo->bind_param("i", $id);
$stmtInfo->execute();
$stmtInfo->bind_result($email);
$stmtInfo->fetch();
$stmtInfo->close();

/* ✅ Validation du compte */
$stmt = $conn->prepare("UPDATE membres SET is_approved = 1 WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    /* 📝 LOG ADMIN */
    if (isset($_SESSION['user_id'])) {
        logAdminAction(
            $conn,
            (int) $_SESSION['user_id'],
            "Validation d’un compte",
            "Utilisateur ID #$id" . ($email ? " ($email)" : "")
        );
    }

    header("Location: admin_approve_users.php?approved=true");
} else {
    header("Location: admin_approve_users.php?error=approve_fail");
}

$stmt->close();
$conn->close();
exit();
