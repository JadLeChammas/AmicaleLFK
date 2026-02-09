<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/admin_log.php';

requireAdmin();

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $titre = htmlspecialchars($_POST['titre']);
    $description = htmlspecialchars($_POST['description']);
    $date = $_POST['date_evenement'];
    $imagePath = null;

    // Upload image
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads-events/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $fileName = uniqid('event_') . '.' . $ext;
        $imagePath = $uploadDir . $fileName;

        move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
    }

    // Insert event
    $stmt = $conn->prepare(
        "INSERT INTO evenements (titre, description, date_evenement, image) VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param("ssss", $titre, $description, $date, $imagePath);
    $stmt->execute();

    // ✅ Récupération ID événement (mysqli)
    $eventId = $conn->insert_id;

    // 🧾 LOG ADMIN
    logAdminAction(
        $conn,
        $_SESSION['user_id'],
        "Création d’un événement",
        "Événement ID #$eventId"
    );

    $stmt->close();
    $conn->close();

    header("Location: admin_dashboard.php?event_created=1");
    exit;
}
