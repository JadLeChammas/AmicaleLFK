<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Plateforme des Alumni du Lycée Français de Koweït">
    <title>Amicale LFK</title>
    <link rel="stylesheet" href="css/accueil.css">
</head>

<body>

<?php include 'header.php'; ?>

<!-- HERO IMAGE -->
<section class="hero">
    <img src="img/lfkback.png" alt="Lycée Français de Koweït">

    <div class="hero-overlay">
        <h1>Alumni LFK</h1>
        <p>Un réseau, une mémoire, une communauté internationale</p>

        <?php if (!isset($_SESSION['user_id'])): ?>
            <div class="hero-actions">
                <a href="signin.php" class="btn-primary">Connexion</a>
                <a href="choix_role.php" class="btn-secondary">Créer un compte</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- INTRO -->
<section class="intro">
    <h2>Bienvenue sur la plateforme des Alumni LFK</h2>
    <p>
        Cette plateforme a pour objectif de réunir les anciens élèves du Lycée Français de Koweït,
        de faciliter les échanges, le partage d’opportunités et de préserver les liens entre générations.
    </p>
</section>

<!-- FEATURES -->
<section class="features">
    <div class="feature">
        <h3>Annuaire Alumni</h3>
        <p>Retrouvez facilement les anciens élèves par promotion, pays ou domaine professionnel.</p>
    </div>

    <div class="feature">
        <h3>Événements & Actualités</h3>
        <p>Restez informé des rencontres, événements et initiatives de l’amicale.</p>
    </div>

    <div class="feature">
        <h3>Ressources & Partenaires</h3>
        <p>Accédez à des contenus exclusifs, opportunités professionnelles et partenariats.</p>
    </div>
</section>

<!-- STATS -->
<section class="stats">
    <div class="stat">
        <strong>+zzz</strong>
        <span>Alumni</span>
    </div>
    <div class="stat">
        <strong>+Promo</strong>
        <span>Promotions</span>
    </div>
    <div class="stat">
        <strong>Pays+</strong>
        <span>Pays représentés</span>
    </div>
</section>

<?php include 'footer.php'; ?>

</body>
</html>
