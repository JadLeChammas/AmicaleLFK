<?php
session_start();
include 'config.php'; // Connexion à la BDD via PDO
require_once __DIR__ . '/auth.php';
requireAdmin();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titre = htmlspecialchars($_POST['titre']);
    $description = htmlspecialchars($_POST['description']);
    $date = $_POST['date_evenement']; // Format: YYYY-MM-DD
    $imagePath = null;

    // Vérifie si une image a été uploadée
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads-events/';
        $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $uniqueName = uniqid('event_') . '.' . $extension;
        $uploadFile = $uploadDir . $uniqueName;

        // Crée le dossier s'il n'existe pas
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            $imagePath = $uploadFile;
        }
    }

    // Insère l'événement dans la base
    $stmt = $conn->prepare("INSERT INTO evenements (titre, description, date_evenement, image) VALUES (?, ?, ?, ?)");
    $stmt->execute([$titre, $description, $date, $imagePath]);

    // Redirection après ajout
    header("Location: admin_dashboard.php?success=1");
    exit;
}
?>
