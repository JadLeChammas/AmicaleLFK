<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Choix du type de compte</title>
    <link rel="stylesheet" href="css/choix_role.css">
</head>
<body>

<!-- Lien retour centré au-dessus de la boîte -->
<div class="back-home-wrapper">
    <a href="index.php" class="back-home">← Retour à l'accueil</a>
</div>

<!-- Boîte du formulaire -->
<div class="form-wrapper">
    <div class="form-box">
        <form class="form" method="GET" action="">
            <span class="title">Quel type de compte voulez-vous créer ?</span>
            <div class="form-container">
                <button type="submit" formaction="signup.php">Alumni LFK</button>
                <button type="submit" formaction="signup_eleve.php">Élève LFK</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
