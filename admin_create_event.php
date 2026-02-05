<?php
require_once 'auth.php';
requireAdmin();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un événement</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>

<div class="admin-container">
    <a href="admin_dashboard.php" class="back-home">← Retour au dashboard</a>

    <div class="admin-box">
        <h2>Créer un événement personnalisé</h2>

        <form action="ajouter_evenement.php" method="POST" enctype="multipart/form-data" class="form-group">
            <label for="titre">Titre :</label>
            <input type="text" name="titre" id="titre" required>

            <label for="description">Description :</label>
            <textarea name="description" id="description" required></textarea>

            <label for="date_evenement">Date :</label>
            <input type="date" name="date_evenement" id="date_evenement" required>

            <label for="image">Image (optionnelle) :</label>
            <input type="file" name="image" id="image" accept="image/*">

            <button type="submit" class="btn">Créer l'événement</button>
        </form>
    </div>
</div>

</body>
</html>
