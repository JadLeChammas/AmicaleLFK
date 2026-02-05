<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
requireAdmin(); // ✅ Admin uniquement

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

// Récupérer les publications en attente
$sql = "SELECT * FROM publications WHERE status = 'pending'";
$result = $conn->query($sql);

if (!$result) {
    die("Erreur SQL : " . $conn->error);
}

$posts = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validation des Publications</title>
    <link rel="stylesheet" href="css/admin1.css">
    <link rel="icon" type="image/png" href="/img/lfk.png">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

    <!-- Lien retour -->
    <div class="back-wrapper">
        <a class="back-home" href="admin_dashboard.php">⬅ Retour au tableau de bord</a>
    </div>

    <div class="container">
        <h2>Validation des Publications</h2>

        <div id="adminPostsContainer">
            <?php if (count($posts) > 0): ?>
                <?php foreach ($posts as $post): ?>
                    <div class="post-item">
                        <h3><?= htmlspecialchars($post['title']) ?></h3>
                        <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
                        <?php if (!empty($post['image'])): ?>
                            <img src="<?= htmlspecialchars($post['image']) ?>" alt="Image du post">
                        <?php endif; ?>
                        <div class="post-actions">
                            <button class="approve-btn" data-id="<?= $post['id'] ?>">Approuver</button>
                            <button class="deny-btn" data-id="<?= $post['id'] ?>">Refuser</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucune publication en attente.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            function handlePostAction(postId, action) {
                $.post("validate_post.php", { id: postId, action: action }, function(response) {
                    try {
                        let result = JSON.parse(response);
                        alert(result.message); 
                        if (result.status === "success") {
                            location.reload(); 
                        }
                    } catch (e) {
                        alert("Erreur lors du traitement.");
                    }
                }).fail(function() {
                    alert("Erreur de connexion au serveur.");
                });
            }

            $(".approve-btn").click(function() {
                let postId = $(this).data("id");
                handlePostAction(postId, "approve");
            });

            $(".deny-btn").click(function() {
                let postId = $(this).data("id");
                handlePostAction(postId, "deny");
            });
        });
    </script>

</body>
</html>
