<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
requireAdmin(); // ✅ Admin-only access
require_once 'admin_log.php'; // 🔥 LOG ADMIN

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

    /* 🔍 Récupérer le titre AVANT suppression pour le log */
    $stmt = $conn->prepare("SELECT title FROM publications WHERE id = ?");
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

    /* 🗑️ SUPPRESSION */
    $delete_sql = "DELETE FROM publications WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $post_id);

    if ($stmt->execute()) {

        /* 🔥 LOG ADMIN */
        logAdminAction(
            $conn,
            $_SESSION['user_id'],
            "Suppression d’une publication",
            "Publication ID #".$post_id." – ".$post['title']
        );

        $message = "<p style='color: green; text-align: center;'>Publication supprimée avec succès.</p>";
    } else {
        $message = "<p style='color: red; text-align: center;'>Erreur lors de la suppression.</p>";
    }

    $stmt->close();

    header("Refresh: 1; url=admin_manage_posts.php");
    exit();
}

$conn->close();
?>
