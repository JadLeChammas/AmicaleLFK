<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
requireAnyRole(); // accès sécurisé à tous les rôles connectés
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nos Partenaires</title>
    <link rel="stylesheet" href="css/partenaire.css">
    <link rel="icon" type="image/png" href="img/lfk.png">
</head>
<body>

<?php include 'header.php'; ?>

<h2 class="titre-page">Nos Partenaires</h2>

<div class="sponsors-container">
    <!-- Sponsor 1 -->
    <a href="https://www.lfkoweit.edu.kw" target="_blank" class="sponsor-card">
        <img src="img/lfklogo.png" alt="Logo LFK">
        <h3>Lycée Français du Koweit</h3>
    </a>

    <!-- Sponsor 2 -->
    <a href="https://kw.ambafrance.org/-Cooperation-et-Action-culturelle-" target="_blank" class="sponsor-card">
        <img src="img/scac.png" alt="Logo scac">
        <h3>Le Service de coopération et d'action culturelle</h3>
    </a>

    <!-- Sponsor 3 -->
    <a href="https://kw.ambafrance.org/-Cooperation-et-Action-culturelle-" target="_blank" class="sponsor-card">
        <img src="img/scac.png" alt="Logo scac">
        <h3>Le Service de coopération et d'action culturelle</h3>
    </a>

    <!-- Sponsor 4 -->
    <a href="https://kw.ambafrance.org/-Cooperation-et-Action-culturelle-" target="_blank" class="sponsor-card">
        <img src="img/scac.png" alt="Logo scac">
        <h3>Le Service de coopération et d'action culturelle</h3>
    </a>
</div>

<?php include 'footer.php'; ?>

</body>
</html>
