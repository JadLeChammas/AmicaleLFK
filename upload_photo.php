<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
requireAnyRole(); // ✅ Tous les utilisateurs connectés

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES['photo'])) {
    $target_dir = "uploads/";
    $file_tmp_name = $_FILES["photo"]["tmp_name"];
    $original_name = basename($_FILES["photo"]["name"]);
    $file_type = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

    $allowed_types = ["jpg", "jpeg", "png", "gif"];
    if (!in_array($file_type, $allowed_types)) {
        die("Erreur : Seuls les fichiers JPG, JPEG, PNG et GIF sont autorisés.");
    }

    // Renommer le fichier de façon unique pour éviter les conflits
    $new_file_name = uniqid("profil_", true) . '.' . $file_type;
    $file_path = $target_dir . $new_file_name;

    if (move_uploaded_file($file_tmp_name, $file_path)) {
        $sql = "UPDATE membres SET photo_profil = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $new_file_name, $user_id);

        if ($stmt->execute()) {
            header("Location: profil.php");
            exit();
        } else {
            echo "Erreur lors de l'enregistrement de la photo.";
        }

        $stmt->close();
    } else {
        echo "Erreur lors du téléchargement de la photo.";
    }
}

$conn->close();
?>
