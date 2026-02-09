<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'config.php';
require_once 'auth.php';
requireAdmin();

/* Marquer comme lu / non lu */
if (isset($_GET['toggle'])) {
    $id = (int) $_GET['toggle'];
    $stmt = $conn->prepare("
        UPDATE messages_contact 
        SET statut = IF(statut = 'lu', 'non lu', 'lu') 
        WHERE id = :id
    ");
    $stmt->execute([':id' => $id]);
    header("Location: messages.php");
    exit;
}

/* Supprimer message */
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM messages_contact WHERE id = :id");
    $stmt->execute([':id' => $id]);
    header("Location: messages.php");
    exit;
}

/* Récupération messages */
$stmt = $conn->query("SELECT * FROM messages_contact ORDER BY date_envoi DESC");
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Messages de contact</title>
    <link rel="icon" type="image/png" href="/img/lfk.png">

    <!-- CSS ADMIN -->
    <link rel="stylesheet" href="css/admin.css">

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
</head>

<body>

<div class="admin-container">
    <a href="admin_dashboard.php" class="back-home">← Retour au tableau de bord</a>

    <div class="admin-box" style="max-width:1100px;">
        <span class="title">Messages de contact</span>

        <table id="messagesTable" class="display" style="width:100%;">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($messages as $msg): ?>
                <tr>
                    <td><?= htmlspecialchars($msg['nom']) ?></td>
                    <td><?= htmlspecialchars($msg['email']) ?></td>
                    <td><?= nl2br(htmlspecialchars($msg['message'])) ?></td>
                    <td><?= htmlspecialchars($msg['date_envoi']) ?></td>
                    <td>
                        <span style="font-weight:600;color:<?= $msg['statut'] === 'lu' ? 'green' : 'red' ?>">
                            <?= ucfirst($msg['statut']) ?>
                        </span>
                    </td>
                    <td>
                        <a href="?toggle=<?= $msg['id'] ?>">
                            <?= $msg['statut'] === 'lu' ? 'Non lu' : 'Lu' ?>
                        </a>
                        |
                        <a href="?delete=<?= $msg['id'] ?>" style="color:red;"
                           onclick="return confirm('Supprimer ce message ?');">
                            Supprimer
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function () {
    $('#messagesTable').DataTable({
        pageLength: 10,

        // 🔥 FORCER LE LAYOUT (clé du problème)
        dom: '<"dt-top"lf>rt<"dt-bottom"ip>',

        language: {
            lengthMenu: "Afficher _MENU_ entrées",
            zeroRecords: "Aucun message trouvé",
            info: "Affichage de _START_ à _END_ sur _TOTAL_ messages",
            infoEmpty: "Aucun message disponible",
            infoFiltered: "(filtré sur _MAX_ messages)",
            search: "Rechercher :",
            paginate: {
                first: "<<",
                last: ">>",
                next: ">",
                previous: "<"
            }
        },

        columnDefs: [
            { orderable: false, targets: 5 } // Actions
        ]
    });
});
</script>

</body>
</html>
