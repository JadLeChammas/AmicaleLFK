<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
requireAnyRole(); // Tous les rôles connectés

if (!isset($conn) || !$conn) {
    die("Erreur de connexion à la base de données.");
}

/* Récupération des membres */
$query = "
    SELECT 
        id,
        nom,
        prenom,
        date_naissance,
        email,
        annee_promotion,
        etablissement,
        pays,
        ville
    FROM membres
    WHERE id > 1000
    ORDER BY LOWER(nom) ASC
";

$stmt = $conn->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<?php include 'header.php'; ?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Annuaire des membres</title>

    <link rel="stylesheet" href="css/annuaire.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
</head>

<body>

<h2>Annuaire des membres</h2>

<div class="annuaire-wrapper">
    <table id="annuaireTable" class="display">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Date de naissance</th>
                <th>Email</th>
                <th>Promotion</th>
                <th>Établissement</th>
                <th>Pays</th>
                <th>Ville</th>
            </tr>
        </thead>

        <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['id']) ?></td>
                <td><?= htmlspecialchars($user['nom']) ?></td>
                <td><?= htmlspecialchars($user['prenom']) ?></td>
                <td><?= htmlspecialchars($user['date_naissance']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['annee_promotion']) ?></td>
                <td><?= htmlspecialchars($user['etablissement']) ?></td>
                <td><?= htmlspecialchars($user['pays'] ?? '-') ?></td>
                <td><?= htmlspecialchars($user['ville'] ?? '-') ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
$(document).ready(function () {
    $('#annuaireTable').DataTable({
        pageLength: 25,
        order: [[1, 'asc']],
        responsive: true,
        language: {
            lengthMenu: "Afficher _MENU_ entrées",
            zeroRecords: "Aucun résultat trouvé",
            info: "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
            infoEmpty: "Aucune entrée disponible",
            infoFiltered: "(filtré sur _MAX_ entrées)",
            search: "Filtrer :",
            paginate: {
                first: "<<",
                last: ">>",
                next: ">",
                previous: "<"
            }
        }
    });
});
</script>

<?php include 'footer.php'; ?>

</body>
</html>
