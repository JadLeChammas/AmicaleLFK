<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
requireAdmin();

if (!isset($_GET['id'])) {
    header("Location: admin_approve_users.php?error=no_id");
    exit();
}

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

$id = intval($_GET['id']);
$sql = "DELETE FROM membres WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: admin_approve_users.php?denied=true");
} else {
    header("Location: admin_approve_users.php?error=delete_fail");
}

$stmt->close();
$conn->close();
exit();
