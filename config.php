<?php
// Paramètres de connexion à la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'amicalelfk');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    // Connexion à la base de données avec PDO
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Active la gestion des erreurs avec exceptions
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Récupération des données en tableau associatif
        PDO::ATTR_EMULATE_PREPARES => false // Désactive l'émulation des requêtes préparées
    ]);
} catch (PDOException $e) {
    // Message d'erreur caché pour la sécurité, mais stocké dans un log
    error_log("Erreur de connexion à la base de données : " . $e->getMessage());
    die("Une erreur est survenue lors de la connexion à la base de données.");
}
?>
