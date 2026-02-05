<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'config.php';
require_once 'auth.php';
requireAdmin();

// Marquer comme lu/non lu
if (isset($_GET['toggle'])) {
    $id = intval($_GET['toggle']);
    $stmt = $conn->prepare("UPDATE messages_contact SET statut = IF(statut = 'lu', 'non lu', 'lu') WHERE id = :id");
    $stmt->execute([':id' => $id]);
    header("Location: messages.php?updated=1");
    exit();
}

// Supprimer un message
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM messages_contact WHERE id = :id");
    $stmt->execute([':id' => $id]);
    header("Location: messages.php?deleted=1");
    exit();
}

// Récupération des messages
$messages = [];
$result = $conn->query("SELECT * FROM messages_contact ORDER BY date_envoi DESC");
$messages = $result->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Messages de Contact</title>
    <link rel="stylesheet" href="css/messages.css"> <!-- Ton CSS local -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
</head>

<body>

<?php include 'header.php'; ?>

<main style="max-width: 1100px; margin: auto; padding: 40px;">
    <h2>Messages de Contact</h2>

    <?php if (isset($_GET['deleted'])): ?>
        <p style="color: green;">Message supprimé avec succès.</p>
    <?php elseif (isset($_GET['updated'])): ?>
        <p style="color: green;">Statut du message mis à jour.</p>
    <?php endif; ?>

    <table id="messagesTable" class="display">
        <thead>
            <tr>
                <th>ID</th>
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
                    <td><?= htmlspecialchars($msg['id']) ?></td>
                    <td><?= htmlspecialchars($msg['nom']) ?></td>
                    <td><?= htmlspecialchars($msg['email']) ?></td>
                    <td><?= nl2br(htmlspecialchars($msg['message'])) ?></td>
                    <td><?= htmlspecialchars($msg['date_envoi']) ?></td>
                    <td>
                        <span style="color: <?= $msg['statut'] === 'lu' ? 'green' : 'red' ?>;">
                            <?= ucfirst($msg['statut']) ?>
                        </span>
                    </td>
                    <td>
                        <a href="messages.php?toggle=<?= $msg['id'] ?>" style="margin-right: 10px;">
                            <?= $msg['statut'] === 'lu' ? 'Marquer comme non lu' : 'Marquer comme lu' ?>
                        </a>
                        <a href="messages.php?delete=<?= $msg['id'] ?>" onclick="return confirm('Supprimer ce message ?');" style="color: red;">
                            Supprimer
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>

<script>
    $(document).ready(function () {
        $('#messagesTable').DataTable({
            "pageLength": 10,
            "language": {
                "lengthMenu": "Afficher _MENU_ entrées",
                "zeroRecords": "Aucun message trouvé",
                "info": "Affichage de _START_ à _END_ sur _TOTAL_ messages",
                "infoEmpty": "Aucun message disponible",
                "infoFiltered": "(filtré sur _MAX_ messages)",
                "search": "Rechercher :",
                "paginate": {
                    "first": "<<",
                    "last": ">>",
                    "next": ">",
                    "previous": "<"
                }
            },
            "columnDefs": [
                { "orderable": false, "targets": 6 }
            ]
        });
    });
</script>

<?php include 'footer.php'; ?>

</body>
</html>
