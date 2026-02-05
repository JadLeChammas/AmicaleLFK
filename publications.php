<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
verifierRole(['admin', 'user', 'membre_d_honneur']); // ✅ pas accessible aux élèves

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="fr">

<?php include 'header.php'; ?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publications</title>
    <link rel="stylesheet" href="css/publications.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
        <button id="newPostButton">Créer une nouvelle publication</button>

        <div id="postFormContainer" style="display: none;">
            <form id="postForm" enctype="multipart/form-data">
                <input type="text" name="title" placeholder="Titre de la publication" required>
                <textarea name="content" placeholder="Écrivez votre publication..." required></textarea>
                <input type="file" name="image">
                <button type="submit">Publier</button>
            </form>
            <div id="postMessage"></div>
        </div>

        <h2>📅 Événements à venir</h2>

        <div class="grid-container">
            <?php
            $result = $conn->query("SELECT * FROM evenements ORDER BY date_evenement DESC");

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="publication-card">';
                    if (!empty($row['image'])) {
                        echo '<img src="' . htmlspecialchars($row['image']) . '" alt="Image événement">';
                    }
                    echo '<h3>' . htmlspecialchars($row['titre']) . '</h3>';
                    echo '<p><strong>Date :</strong> ' . date("d/m/Y", strtotime($row['date_evenement'])) . '</p>';
                    echo '<p>' . nl2br(htmlspecialchars($row['description'])) . '</p>';
                    echo '</div>';
                }
            } else {
                echo '<p>Aucun événement pour le moment.</p>';
            }
            ?>
        </div>

        <h2>📝 Publications des membres</h2>
        <div id="postsContainer" class="grid-container"></div>
    </div>

    <script src="js/publications.js"></script>

    <?php include 'footer.php'; ?>
</body>
</html>
