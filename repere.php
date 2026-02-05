<?php
session_start();
require_once __DIR__ . '/auth.php';
requireAnyRole(); // ✅ Tous les utilisateurs connectés
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Carte des anciens élèves</title>
    <link rel="stylesheet" href="css/repere.css"> <!-- ✅ Connexion au fichier CSS renommé -->
</head>

<body>

<?php include 'header.php'; ?>

<!-- Contenu principal -->
<div class="map-section"> <!-- Tu peux renommer cette classe si tu veux, mais elle peut rester -->
  <h1>🌍 Sélectionnez un continent</h1>

  <!-- Boutons de sélection de continent -->
  <div class="map-container">
    <button class="continent-btn" data-continent="Europe">Europe</button>
    <button class="continent-btn" data-continent="Afrique">Afrique</button>
    <button class="continent-btn" data-continent="Asie">Asie</button>
    <button class="continent-btn" data-continent="Amérique du Nord">Amérique du Nord</button>
    <button class="continent-btn" data-continent="Amérique du Sud">Amérique du Sud</button>
    <button class="continent-btn" data-continent="Océanie">Océanie</button>
  </div>

  <!-- Résultats dynamiques -->
  <div id="results">
    <h2>Résultats</h2>
    <div id="output"></div>
  </div>
</div>

<script src="js/repere.js"></script> <!-- ✅ Script renommé -->
<?php include 'footer.php'; ?>
</body>
</html>
