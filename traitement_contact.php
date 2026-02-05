<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'auth.php';
requireAnyRole();

// Inclure ton fichier de connexion
require_once 'config.php';

// Connexion à la base via PDO (grâce à config.php)
try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    echo "Erreur de connexion à la base de données.";
    exit;
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom     = htmlspecialchars(trim($_POST['nom'] ?? ''));
    $email   = htmlspecialchars(trim($_POST['email'] ?? ''));
    $message = htmlspecialchars(trim($_POST['message'] ?? ''));

    if ($nom && $email && $message) {
        $sql = "INSERT INTO messages_contact (nom, email, message) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);

        if ($stmt->execute([$nom, $email, $message])) {
            http_response_code(200);
            echo "OK";
        } else {
            http_response_code(500);
            echo "Erreur lors de l'enregistrement du message.";
        }
    } else {
        http_response_code(400);
        echo "Tous les champs sont obligatoires.";
    }
} else {
    http_response_code(405);
    echo "Méthode non autorisée.";
}
?>
