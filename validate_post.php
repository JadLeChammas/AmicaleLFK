<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
requireAdmin(); // ✅ Protection admin

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Connexion échouée: " . $conn->connect_error]);
    exit();
}

// Traitement de la requête POST
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'], $_POST['action'])) {
    $postId = intval($_POST['id']);
    $action = $_POST['action'];

    if ($action === "approve") {
        $sql = "UPDATE publications SET status = 'approved' WHERE id = ?";
    } elseif ($action === "deny") {
        $sql = "DELETE FROM publications WHERE id = ?";
    } else {
        echo json_encode(["status" => "error", "message" => "Action non valide"]);
        exit();
    }

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Erreur préparation SQL : " . $conn->error]);
        exit();
    }

    $stmt->bind_param("i", $postId);
    $success = $stmt->execute();

    if ($success) {
        echo json_encode(["status" => "success", "message" => "Action effectuée avec succès"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Erreur lors du traitement : " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Requête invalide"]);
}

$conn->close();
?>
