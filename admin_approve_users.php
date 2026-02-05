<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
requireAdmin();

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

$sql = "SELECT id, nom, prenom, email, telephone, date_naissance, annee_promotion, 
               preuve_scolarite, etablissement, ville, role 
        FROM membres 
        WHERE is_approved = 0";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Approbation des utilisateurs</title>
    <link rel="stylesheet" href="css/admin_approve_users.css">
    <link rel="icon" type="image/png" href="/img/lfk.png">
</head>
<body>

<div class="admin-container">
    <a href="admin_dashboard.php" class="back-link">← Retour au tableau de bord</a>

    <div class="admin-box">
        <span class="title">Utilisateurs en attente d'approbation</span>

        <?php if (isset($_GET['approved']) && $_GET['approved'] === 'true'): ?>
            <p class="success">✅ Utilisateur approuvé avec succès.</p>
        <?php elseif (isset($_GET['denied']) && $_GET['denied'] === 'true'): ?>
            <p class="success">🗑️ Utilisateur supprimé avec succès.</p>
        <?php elseif (isset($_GET['error'])): ?>
            <p class="error">❌ Une erreur est survenue.</p>
        <?php endif; ?>

        <?php if ($result->num_rows > 0): ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Date de naissance</th>
                            <th>Année de promo</th>
                            <th>Preuve</th>
                            <th>Établissement</th>
                            <th>Ville travail</th>
                            <th>Rôle</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['nom']) ?: '<i>NULL</i>' ?></td>
                                <td><?= htmlspecialchars($row['prenom']) ?: '<i>NULL</i>' ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= htmlspecialchars($row['telephone']) ?: '<i>NULL</i>' ?></td>
                                <td><?= htmlspecialchars($row['date_naissance']) ?: '<i>NULL</i>' ?></td>
                                <td><?= htmlspecialchars($row['annee_promotion']) ?: '<i>NULL</i>' ?></td>
                                <td>
                                    <?php if (!empty($row['preuve_scolarite'])): ?>
                                        <a href="<?= htmlspecialchars($row['preuve_scolarite']) ?>" target="_blank">📄 Voir</a>
                                    <?php else: ?>
                                        <i>NULL</i>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($row['etablissement']) ?: '<i>NULL</i>' ?></td>
                                <td><?= htmlspecialchars($row['ville']) ?: '<i>NULL</i>' ?></td>
                                <td class="role-badge" data-role="<?= htmlspecialchars($row['role']) ?>"></td>
                                <td class="actions-cell">
                                    <a class="validate-link" href="approve_user.php?id=<?= $row['id'] ?>">Approuver</a>
                                    <a class="deny-link" href="deny_user.php?id=<?= $row['id'] ?>" onclick="return confirm('Confirmer la suppression de cet utilisateur ?')">Refuser</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>Aucun utilisateur en attente.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>

<?php $conn->close(); ?>
