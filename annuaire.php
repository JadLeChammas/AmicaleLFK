<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
requireAnyRole(); // ✅ Tous les rôles connectés

// PDO utilisé ici, on vérifie d’abord l'objet PDO
if (!isset($conn) || !$conn) {
    die(json_encode(["status" => "error", "message" => "Erreur de connexion à la base de données."]));
}

// Récupérer les membres
$query = "SELECT id, nom, prenom, email, etablissement, date_naissance, annee_promotion 
          FROM membres 
          WHERE id > 1000
          ORDER BY LOWER(nom) ASC";

try {
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur lors de la récupération des membres : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">

<?php include 'header.php'; ?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Annuaire</title>
    <link rel="stylesheet" href="css/annuaire.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
</head>
<body>

    <h2>Annuaire</h2>

    <table id="annuaireTable" class="display">
        <thead>
            <tr>
                <th>Id</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Date de Naissance</th>
                <th>Email</th>
                <th>Année de Promotion</th>
                <th>Établissement</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['id'] ?? '') ?></td>
                    <td><?= htmlspecialchars($user['nom'] ?? '') ?></td>
                    <td><?= htmlspecialchars($user['prenom'] ?? '') ?></td>
                    <td><?= htmlspecialchars($user['date_naissance'] ?? '') ?></td>
                    <td><?= htmlspecialchars($user['email'] ?? '') ?></td>
                    <td><?= htmlspecialchars($user['annee_promotion'] ?? '') ?></td>
                    <td><?= htmlspecialchars($user['etablissement'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
        $(document).ready(function() {
            $('#annuaireTable').DataTable({
    "pageLength": 25,
    "order": [[1, "asc"]],
                "language": {
                    "lengthMenu": "Afficher _MENU_ entrées",
                    "zeroRecords": "Aucun résultat trouvé",
                    "info": "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
                    "infoEmpty": "Aucune entrée disponible",
                    "infoFiltered": "(filtré sur _MAX_ entrées au total)",
                    "search": "Filtrer :",
                    "paginate": {
                        "first": "<<",
                        "last": ">>",
                        "next": ">",
                        "previous": "<"
                    }
                }
            });
        });
    </script>

    <?php include 'footer.php'; ?>

</body>
</html>
