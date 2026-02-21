<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
requireAdmin();
require_once __DIR__ . '/admin_log.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

/* Récupération des publications */
$sql = "SELECT id, user_id, title, content, image, created_at FROM publications WHERE id > 10";
$result = $conn->query($sql);

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_post_id'])) {

    $post_id = (int) $_POST['delete_post_id'];

    /* 🔍 Récupérer titre + image AVANT suppression */
    $stmt = $conn->prepare("SELECT title, image FROM publications WHERE id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $resultPost = $stmt->get_result();
    $post = $resultPost->fetch_assoc();
    $stmt->close();

    if (!$post) {
        $conn->close();
        header("Location: admin_manage_posts.php?error=post_not_found");
        exit();
    }

    /* 🗑️ Suppression DB */
    $stmt = $conn->prepare("DELETE FROM publications WHERE id = ?");
    $stmt->bind_param("i", $post_id);

    if ($stmt->execute()) {

        /* 🧹 (Optionnel) supprimer l'image physique si elle existe */
        if (!empty($post['image'])) {
            $filePath = __DIR__ . "/uploads/" . $post['image']; // adapte si ton dossier est différent
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        /* 🔥 LOG ADMIN (protégé) */
        if (!empty($_SESSION['user_id'])) {
            logAdminAction(
                $conn,
                (int) $_SESSION['user_id'],
                "Suppression d’une publication",
                "Publication ID #".$post_id." – ".$post['title']
            );
        }

        $message = "<p style='color: green; text-align: center;'>Publication supprimée avec succès.</p>";
    } else {
        $message = "<p style='color: red; text-align: center;'>Erreur lors de la suppression.</p>";
    }

    $stmt->close();
    $conn->close();

    header("Refresh: 1; url=admin_manage_posts.php");
    exit();
}

$conn->close();
?>
