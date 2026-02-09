<?php
require_once 'auth.php';
requireAdmin();
require_once 'config.php';

/**
 * Compte sécurisé : si la requête échoue → retourne 0
 */
function safeCount(PDO $conn, string $sql): int {
    try {
        return (int) $conn->query($sql)->fetchColumn();
    } catch (Throwable $e) {
        return 0;
    }
}

/* STATISTIQUES */
$unreadMessages = safeCount(
    $conn,
    "SELECT COUNT(*) FROM messages_contact WHERE TRIM(statut) = 'non lu'"
);

$totalUsers = safeCount(
    $conn,
    "SELECT COUNT(*) FROM membres"
);

$pendingUsers = safeCount(
    $conn,
    "SELECT COUNT(*) FROM membres WHERE is_approved = 0"
);

$pendingPosts = safeCount(
    $conn,
    "SELECT COUNT(*) FROM publications WHERE status = 'pending'"
);

$upcomingEvents = safeCount(
    $conn,
    "SELECT COUNT(*) FROM evenements WHERE date_evenement >= CURDATE()"
);
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
        <span class="admin-badge">Espace sécurisé</span>
    </header>

    <!-- HERO -->
    <section class="admin-hero">
        <h1>Tableau de bord administrateur</h1>
        <p class="admin-hero__subtitle">
            Centralisez vos actions clés pour piloter la plateforme et accompagner la communauté.
        </p>
    </section>

    <!-- STATS -->
    <section class="admin-stats">

        <!-- Messages non lus -->
        <article class="stat-card" onclick="location.href='messages.php'" style="cursor:pointer;">
            <p class="stat-label">Messages non lus</p>
            <p class="stat-value" style="color:<?= $unreadMessages > 0 ? 'red' : 'inherit' ?>">
                <?= $unreadMessages ?>
            </p>
        </article>

        <article class="stat-card">
            <p class="stat-label">Membres inscrits</p>
            <p class="stat-value"><?= $totalUsers ?></p>
        </article>

        <article class="stat-card" onclick="location.href='admin_approve_users.php'" style="cursor:pointer;">
            <p class="stat-label">Comptes à valider</p>
            <p class="stat-value" style="color:<?= $pendingUsers > 0 ? 'red' : 'inherit' ?>">
                <?= $pendingUsers ?>
            </p>

        </article>

        <article class="stat-card">
            <p class="stat-label">Publications en attente</p>
            <p class="stat-value"><?= $pendingPosts ?></p>
        </article>

        <article class="stat-card">
            <p class="stat-label">Événements à venir</p>
            <p class="stat-value"><?= $upcomingEvents ?></p>
        </article>

    </section>

    <!-- PANELS -->
    <main class="admin-panels">

        <!-- Messagerie -->
        <article class="admin-panel">
            <div class="panel-header">
                <span class="panel-icon">✉️</span>
                <div>
                    <h2>Messagerie</h2>
                    <p>Consultez et gérez les messages envoyés par les membres ou visiteurs.</p>
                </div>
            </div>
            <a href="messages.php" class="btn">Ouvrir la messagerie</a>
        </article>

        <!-- Publications -->
        <article class="admin-panel">
            <div class="panel-header">
                <span class="panel-icon">📝</span>
                <div>
                    <h2>Publications</h2>
                    <p>Validez les nouveaux contenus.</p>
                </div>
            </div>
            <a href="admin_posts.php" class="btn">Valider les publications</a>
            <a href="admin_manage_posts.php" class="btn btn-danger">Supprimer une publication</a>
        </article>

        <!-- Événements -->
        <article class="admin-panel">
            <div class="panel-header">
                <span class="panel-icon">📅</span>
                <div>
                    <h2>Événements</h2>
                    <p>Planifiez et gérez les événements.</p>
                </div>
            </div>
            <a href="admin_create_event.php" class="btn">Créer un événement</a>
            <a href="admin_delete_events.php" class="btn btn-danger">Gérer les événements</a>
        </article>

        <!-- Utilisateurs -->
        <article class="admin-panel">
            <div class="panel-header">
                <span class="panel-icon">👥</span>
                <div>
                    <h2>Utilisateurs</h2>
                    <p>Gestion des comptes et rôles.</p>
                </div>
            </div>
            <a href="admin_create_user.php" class="btn">Créer un utilisateur</a>
            <a href="admin_approve_users.php" class="btn">Valider les inscriptions</a>
            <a href="admin_assign_roles.php" class="btn">Attribuer des rôles</a>
            <a href="admin_change_password.php" class="btn">Réinitialiser un mot de passe</a>
            <a href="admin_manage_users.php" class="btn btn-danger">Supprimer un utilisateur</a>
        </article>


        <!-- Admin Logs -->
        <article class="admin-panel">
            <div class="panel-header">
                <span class="panel-icon">📜</span>
                <div>
                    <h2>Logs d'administration</h2>
                    <p>Consultez les actions administratives pour assurer la transparence et la sécurité.</p>
                </div>
            </div>
            <a href="admin_log.php" class="btn">Voir les logs</a>
        </article>

    </main>

</div>

</body>
</html>
