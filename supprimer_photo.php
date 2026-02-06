<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';

requireAnyRole();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: profil.php");
    exit;
}

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$user_id = $_SESSION['user_id'];

/* 1️⃣ Récupérer la photo */
$stmt = $conn->prepare("SELECT photo_profil FROM membres WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($photo);
$stmt->fetch();
$stmt->close();

/* 2️⃣ Supprimer le fichier */
if (!empty($photo)) {
    $filePath = __DIR__ . "/uploads/" . $photo;

    if (file_exists($filePath)) {
        unlink($filePath);
    }
}

/* 3️⃣ Mettre à NULL en base */
$stmt = $conn->prepare("UPDATE membres SET photo_profil = NULL WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->close();

$conn->close();

/* 4️⃣ Retour */
header("Location: profil.php?photo_deleted=1");
exit;