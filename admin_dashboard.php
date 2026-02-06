<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'auth.php';
requireAdmin();
require_once 'config.php';

/*
 * $conn doit être une instance PDO
 */

$stats = [
    'total_users'     => 0,
    'pending_users'   => 0,
    'pending_posts'   => 0,
    'upcoming_events' => 0,
];

$statsError = null;

try {
    $stats['total_users'] = (int) $conn
        ->query("SELECT COUNT(*) FROM membres")
        ->fetchColumn();

    $stats['pending_users'] = (int) $conn
        ->query("SELECT COUNT(*) FROM membres WHERE is_approved = 0")
        ->fetchColumn();

    $stats['pending_posts'] = (int) $conn
        ->query("SELECT COUNT(*) FROM publications WHERE status = 'pending'")
        ->fetchColumn();

    $stats['upcoming_events'] = (int) $conn
        ->query("SELECT COUNT(*) FROM evenements WHERE date_evenement >= CURDATE()")
        ->fetchColumn();

} catch (Throwable $e) {
    $statsError = "Impossible de récupérer les statistiques pour le moment.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord Administrateur</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/img/lfk.png">
    <link rel="stylesheet" href="css/admin_dashboard.css">
</head>

<body>

<div class="admin-dashboard">

    <!-- HEADER -->
    <header class="admin-header">
        <a href="index.php" class="back-home">← Retour à l'accueil</a>
        <div class="admin-header__meta">
            <span class="admin-badge">Espace sécurisé</span>
        </div>
    </header>

    <!-- HERO -->
    <section class="admin-hero">
        <h1>Tableau de bord administrateur</h1>
        <p class="admin-hero__subtitle">
            Centralisez vos actions clés pour piloter la plateforme et accompagner la communauté.
        </p>
    </section>

    <!-- STATS -->
    <?php if ($statsError): ?>
        <div class="admin-alert" role="alert">
            <?= htmlspecialchars($statsError) ?>
        </div>
    <?php else: ?>
        <section class="admin-stats" aria-label="Statistiques clés">
            <article class="stat-card">
                <p class="stat-label">Membres inscrits</p>
                <p class="stat-value"><?= number_format($stats['total_users'], 0, ',', ' ') ?></p>
            </article>

            <article class="stat-card">
                <p class="stat-label">Comptes à valider</p>
                <p class="stat-value"><?= number_format($stats['pending_users'], 0, ',', ' ') ?></p>
            </article>

            <article class="stat-card">
                <p class="stat-label">Publications en attente</p>
                <p class="stat-value"><?= number_format($stats['pending_posts'], 0, ',', ' ') ?></p>
            </article>

            <article class="stat-card">
                <p class="stat-label">Événements à venir</p>
                <p class="stat-value"><?= number_format($stats['upcoming_events'], 0, ',', ' ') ?></p>
            </article>
        </section>
    <?php endif; ?>

    <!-- PANELS -->
    <main class="admin-panels" aria-label="Actions d'administration">

        <!-- Messagerie -->
        <article class="admin-panel">
            <div class="panel-header">
                <span class="panel-icon">✉️</span>
                <div>
                    <h2>Messagerie</h2>
                    <p>Consultez et gérez les messages envoyés par les membres ou visiteurs.</p>
                </div>
            </div>
            <div class="panel-actions">
                <a href="messages.php" class="btn">Ouvrir la messagerie</a>
            </div>
        </article>

        <!-- Publications -->
        <article class="admin-panel">
            <div class="panel-header">
                <span class="panel-icon">📝</span>
                <div>
                    <h2>Publications</h2>
                    <p>Validez les nouveaux contenus et gardez le fil d’actualité pertinent.</p>
                </div>
            </div>
            <div class="panel-actions">
                <a href="admin_posts.php" class="btn">Valider les publications</a>
                <a href="admin_manage_posts.php" class="btn btn-danger">Supprimer une publication</a>
            </div>
        </article>

        <!-- Événements -->
        <article class="admin-panel">
            <div class="panel-header">
                <span class="panel-icon">📅</span>
                <div>
                    <h2>Événements</h2>
                    <p>Planifiez, modifiez et animez les événements communautaires.</p>
                </div>
            </div>
            <div class="panel-actions">
                <a href="admin_create_event.php" class="btn">Créer un événement</a>
                <a href="admin_delete_events.php" class="btn btn-danger">Gérer les événements</a>
            </div>
        </article>

        <!-- Utilisateurs -->
        <article class="admin-panel">
            <div class="panel-header">
                <span class="panel-icon">👥</span>
                <div>
                    <h2>Utilisateurs</h2>
                    <p>Gérez les comptes, rôles et accès des utilisateurs.</p>
                </div>
            </div>
            <div class="panel-actions">
                <a href="admin_create_user.php" class="btn">Créer un utilisateur</a>
                <a href="admin_approve_users.php" class="btn">Valider les inscriptions</a>
                <a href="admin_assign_roles.php" class="btn">Attribuer des rôles</a>
                <a href="admin_change_password.php" class="btn">Réinitialiser un mot de passe</a>
                <a href="admin_manage_users.php" class="btn btn-danger">Supprimer un utilisateur</a>
            </div>
        </article>

    </main>
</div>

</body>
</html>
