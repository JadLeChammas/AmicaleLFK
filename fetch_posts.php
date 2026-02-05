<?php
session_start();
require_once __DIR__ . '/config.php';
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

require_once 'auth.php';
verifierRole(['admin','user','membre_d_honneur']);

if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}



// Exécuter la requête SQL avec MySQLi
$query = "SELECT p.*, u.prenom, u.nom FROM publications p 
          JOIN membres u ON p.user_id = u.id 
          WHERE p.status = 'approved' 
          ORDER BY p.created_at DESC";

$result = $conn->query($query);

if ($result->num_rows > 0) {
    while ($post = $result->fetch_assoc()) {
        echo '<div class="post-card">';
        if (!empty($post['image'])) {
            echo '<img src="' . htmlspecialchars($post['image']) . '" alt="Image">';
        }
        echo '<div class="post-content">';
        echo '<h3>' . htmlspecialchars($post['title']) . '</h3>';
        echo '<p>' . nl2br(htmlspecialchars($post['content'])) . '</p>';
        echo '<small>Publié par ' . htmlspecialchars($post['prenom']) . ' ' . htmlspecialchars($post['nom']);
        echo '</div>';
        echo '</div>';
    }
} else {
    echo '<p>Aucune publication trouvée.</p>';
}

$conn->close();
?>
