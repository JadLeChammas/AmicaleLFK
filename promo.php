<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
verifierRole(['admin', 'user']); // ✅ Pas d'accès pour eleve

// Vérification de l’année
if (!isset($_GET['annee']) || !is_numeric($_GET['annee'])) {
    die("Année de promotion invalide.");
}

$annee = intval($_GET['annee']);

// Lien WhatsApp par promo (à connecter à une table si besoin)
$whatsapp_links = [
    2022 => "https://chat.whatsapp.com/lienDuGroupe2022",
    2023 => "https://chat.whatsapp.com/lienDuGroupe2023"
];

$whatsapp_link = $whatsapp_links[$annee] ?? null;

// Connexion BDD
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT nom, prenom, email FROM membres WHERE annee_promotion = ?");
$stmt->bind_param("i", $annee);
$stmt->execute();
$result = $stmt->get_result();
$membres = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Promo <?= htmlspecialchars($annee) ?></title>
    <link rel="stylesheet" href="css/promo.css">
    <link rel="icon" type="image/png" href="/img/lfk.png">
</head>
<body>

<?php include 'header.php'; ?>

<div class="container">

    <h1 class="title">Promotion <?= htmlspecialchars($annee) ?></h1>

    <?php if ($whatsapp_link): ?>
        <p class="whatsapp-link">
            🔗 <a href="<?= htmlspecialchars($whatsapp_link) ?>" target="_blank">
                Rejoindre le groupe WhatsApp de la promo <?= htmlspecialchars($annee) ?>
            </a>
        </p>
    <?php else: ?>
        <p class="whatsapp-link">
            🔒 Le lien WhatsApp pour cette promo n’a pas encore été ajouté.
        </p>
    <?php endif; ?>

    <?php if (count($membres) > 0): ?>
        <ul class="liste-membres">
            <?php foreach ($membres as $membre): ?>
                <li>
                    <strong><?= htmlspecialchars($membre['prenom']) . " " . htmlspecialchars($membre['nom']) ?></strong>
                    – <?= htmlspecialchars($membre['email']) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Aucun membre trouvé pour cette promotion.</p>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>

</body>
</html>
