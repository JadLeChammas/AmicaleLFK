<?php
session_start();
require_once __DIR__ . '/auth.php';
requireAnyRole();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Repère – Carte des anciens</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/repere.css">
</head>

<body>

<?php include 'header.php'; ?>

<main class="repere-page">

    <!-- HERO -->
    <section class="repere-hero">
        <h1>🌍 Repère des anciens</h1>
        <p>
            Explorez la communauté ALFK à travers le monde.
            Sélectionnez un continent pour découvrir pays, villes et membres.
        </p>
    </section>

    <!-- CONTINENTS -->
    <section class="repere-continents">
        <h2>Sélectionnez un continent</h2>

        <div class="continent-grid">
            <button class="continent-btn" data-continent="Europe">Europe</button>
            <button class="continent-btn" data-continent="Afrique">Afrique</button>
            <button class="continent-btn" data-continent="Asie">Asie</button>
            <button class="continent-btn" data-continent="Amérique du Nord">Amérique du Nord</button>
            <button class="continent-btn" data-continent="Amérique du Sud">Amérique du Sud</button>
            <button class="continent-btn" data-continent="Océanie">Océanie</button>
        </div>
    </section>

    <!-- RESULTATS -->
    <section class="repere-results">
        <h2>Résultats</h2>
        <div id="output">
            <p class="placeholder">
                Sélectionnez un continent pour afficher les résultats.
            </p>
        </div>
    </section>

</main>

<script src="js/repere.js"></script>
<?php include 'footer.php'; ?>

</body>
</html>
