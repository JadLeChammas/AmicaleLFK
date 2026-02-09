<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<link rel="stylesheet" href="css/header.css">
<link rel="icon" type="image/png" href="img/Alfk.png">

<header class="desktop-header">

    <!-- LEFT -->
    <div class="header-left">
        <a href="index.php" class="logo">
            <img src="img/Alfk.png" alt="Alfk Logo">
        </a>

        <nav class="main-nav">
            <a href="index.php">Accueil</a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="annuaire.php">Annuaire</a>
                <a href="repere.php">Repère</a>
                <a href="calendar.php">Calendrier</a>

                <?php if (in_array($_SESSION['role'], ['user', 'admin', 'membre_d_honneur'])): ?>
                    <a href="publications.php">Publications</a>
                <?php endif; ?>

                <?php if (isset($_SESSION['annee_promotion'])): ?>
                    <a href="promo.php?annee=<?= urlencode($_SESSION['annee_promotion']) ?>">
                        Promo <?= htmlspecialchars($_SESSION['annee_promotion']) ?>
                    </a>
                <?php endif; ?>

                <a href="Partenaire.php">Partenaires</a>
            <?php endif; ?>
        </nav>
    </div>

    <!-- RIGHT -->
    <div class="header-right">
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="user-box">
                <a href="profil.php">
                    <?= htmlspecialchars($_SESSION['user_prenom']) . " " . htmlspecialchars($_SESSION['user_nom']) ?>
                </a>
            </div>

            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="admin_dashboard.php" class="admin-link">
                    Admin Dashboard
                </a>
            <?php endif; ?>

            <a href="logout.php" class="logout-btn">Déconnexion</a>
        <?php else: ?>
            <a href="signin.php" class="btn">Connexion</a>
            <a href="choix_role.php" class="btn">Création de compte</a>
        <?php endif; ?>
    </div>

</header>
