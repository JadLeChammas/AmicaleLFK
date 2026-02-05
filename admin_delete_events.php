<?php
require_once 'auth.php';
requireAdmin();
require_once 'config.php';

try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Suppression d’un événement
    if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
        $id = (int) $_GET['delete'];

        // Récupère le chemin de l'image si elle existe
        $stmtImage = $conn->prepare("SELECT image FROM evenements WHERE id = ?");
        $stmtImage->execute([$id]);
        $imageRow = $stmtImage->fetch(PDO::FETCH_ASSOC);

        // Supprime l'image physique si elle existe
        if ($imageRow && !empty($imageRow['image']) && file_exists($imageRow['image'])) {
            unlink($imageRow['image']);
        }

        // Supprime l’événement de la BDD
        $stmt = $conn->prepare("DELETE FROM evenements WHERE id = ?");
        $stmt->execute([$id]);

        header("Location: admin_delete_events.php?success=1");
        exit;
    }
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Supprimer des événements</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>

<div class="admin-container">
    <a href="admin_dashboard.php" class="back-home">← Retour au dashboard</a>

    <div class="admin-box">
        <h2>🗑️ Supprimer un événement</h2>

        <?php if (isset($_GET['success'])): ?>
            <p style="color: green;">✅ Événement supprimé avec succès.</p>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $conn->query("SELECT id, titre, date_evenement FROM evenements ORDER BY date_evenement DESC");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
                ?>
                    <tr>
                        <td><?= htmlspecialchars($row['titre']) ?></td>
                        <td><?= date("d/m/Y", strtotime($row['date_evenement'])) ?></td>
                        <td>
                            <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Supprimer cet événement ?')">
                                ❌ Supprimer
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
