<?php
session_start();
require_once 'auth.php';
requireAdmin();
require_once 'config.php';
require_once 'admin_log.php'; // ✅ logger

try {
    // ✅ Connexion PDO (événements)
    $conn = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
        DB_USER,
        DB_PASS
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ✅ Suppression d’un événement
    if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
        $id = (int) $_GET['delete'];

        // 🔍 Récupère infos (titre + image) AVANT suppression (utile pour le log)
        $stmtInfo = $conn->prepare("SELECT titre, image FROM evenements WHERE id = ?");
        $stmtInfo->execute([$id]);
        $eventRow = $stmtInfo->fetch(PDO::FETCH_ASSOC);

        if (!$eventRow) {
            header("Location: admin_delete_events.php?error=not_found");
            exit;
        }

        // 🖼️ Supprime l'image physique si elle existe
        if (!empty($eventRow['image']) && file_exists($eventRow['image'])) {
            unlink($eventRow['image']);
        }

        // 🗑️ Supprime l’événement de la BDD
        $stmt = $conn->prepare("DELETE FROM evenements WHERE id = ?");
        $stmt->execute([$id]);

        // ✅ LOG ADMIN (mysqli séparé)
        $logConn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if (!$logConn->connect_error && isset($_SESSION['user_id'])) {
            logAdminAction(
                $logConn,
                (int) $_SESSION['user_id'],
                "Suppression d’un événement",
                "Événement ID #{$id} – " . $eventRow['titre']
            );
            $logConn->close();
        }

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
        <?php elseif (isset($_GET['error']) && $_GET['error'] === 'not_found'): ?>
            <p style="color: red;">❌ Événement introuvable.</p>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
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
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['titre']) ?></td>
                        <td><?= date("d/m/Y", strtotime($row['date_evenement'])) ?></td>
                        <td>
                            <a href="?delete=<?= (int)$row['id'] ?>" onclick="return confirm('Supprimer cet événement ?')">
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
