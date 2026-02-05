<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
requireAdmin(); // ✅ Admin-only access

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

// ✅ Modification ici pour ne sélectionner que les publications avec ID > 10
$sql = "SELECT id, user_id, title, content, image, created_at FROM publications WHERE id > 10";
$result = $conn->query($sql);

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_post_id'])) {
    $post_id = $_POST['delete_post_id'];
    $delete_sql = "DELETE FROM publications WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $post_id);

    if ($stmt->execute()) {
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

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gérer les publications</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="icon" type="image/png" href="/img/lfk.png">
</head>
<body>

<div class="admin-container">
    <a href="admin_dashboard.php" class="back-home">&larr; Retour au tableau de bord</a>

    <div class="admin-box">
        <span class="title">Gestion des publications</span>

        <?= $message ? htmlspecialchars_decode($message) : '' ?>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>ID Utilisateur</th>
                        <th>Titre</th>
                        <th>Contenu</th>
                        <th>Image</th>
                        <th>Date de création</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['user_id']) ?></td>
                            <td><?= htmlspecialchars($row['title']) ?></td>
                            <td><?= htmlspecialchars($row['content']) ?></td>
                            <td>
                                <?php if (!empty($row['image'])): ?>
                                    <img src="uploads/<?= htmlspecialchars($row['image']) ?>" width="50" height="50" alt="Image">
                                <?php else: ?>
                                    Aucune image
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['created_at']) ?></td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="delete_post_id" value="<?= $row['id'] ?>">
                                    <button type="submit" class="btn btn-danger">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucune publication trouvée.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
