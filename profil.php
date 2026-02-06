<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';

requireAnyRole();

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$user_id = $_SESSION['user_id'];

/* Récupération utilisateur */
$stmt = $conn->prepare("
    SELECT nom, prenom, email, telephone, date_naissance,
           annee_promotion, etablissement, photo_profil, sexe
    FROM membres
    WHERE id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conn->close();

/* LOGIQUE AVATAR */
if (!empty($user['photo_profil'])) {
    $avatar = "uploads/" . htmlspecialchars($user['photo_profil']) . "?v=" . time();
} else {
    if ($user['sexe'] === 'F') {
        $avatar = "uploads/avatar-femme.webp";
    } else {
        $avatar = "uploads/avatar-homme.webp";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil — <?= htmlspecialchars($user['prenom'].' '.$user['nom']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/profil.css">
</head>

<body>

<div class="profile-wrapper">

    <a href="index.php" class="back-home">← Retour à l'accueil</a>

    <div class="profile-card">

        <!-- HEADER -->
        <div class="profile-header">

            <!-- AVATAR -->
            <div class="avatar-wrapper">
                <img src="<?= $avatar ?>" alt="Photo de profil">
            </div>

            <h2><?= htmlspecialchars($user['prenom'].' '.$user['nom']) ?></h2>

            <!-- ACTIONS PHOTO -->
            <div class="photo-actions">

                <button type="button" id="toggle-photo-actions" class="btn-outline">
                    Changer la photo
                </button>

                <div class="photo-edit-buttons" id="photo-edit-buttons">

                    <!-- Upload -->
                    <form action="upload_photo.php" method="POST" enctype="multipart/form-data">
                        <input type="file" name="photo" id="upload-photo" accept="image/*" hidden>

                        <button type="button" id="choose-photo" class="btn-primary">
                            Téléverser une photo
                        </button>

                        <button type="submit" id="confirm-upload" class="btn-primary" style="display:none;">
                            Valider
                        </button>
                    </form>

                    <!-- Suppression -->
                    <form action="supprimer_photo.php" method="POST">
                        <button type="submit" class="btn-danger-outline">
                            Supprimer la photo
                        </button>
                    </form>

                </div>
            </div>
        </div>

        <!-- INFOS -->
        <div class="profile-info-grid">

            <div class="info-card">
                <span>📧</span>
                <div>
                    <small>Email</small>
                    <p><?= htmlspecialchars($user['email']) ?></p>
                </div>
            </div>

            <div class="info-card">
                <span>🎓</span>
                <div>
                    <small>Établissement</small>
                    <p><?= htmlspecialchars($user['etablissement']) ?></p>
                </div>
            </div>

            <div class="info-card">
                <span>📅</span>
                <div>
                    <small>Date de naissance</small>
                    <p><?= htmlspecialchars($user['date_naissance']) ?></p>
                </div>
            </div>

            <div class="info-card">
                <span>📞</span>
                <div>
                    <small>Téléphone</small>
                    <p><?= htmlspecialchars($user['telephone']) ?></p>
                </div>
            </div>

            <div class="info-card full">
                <span>🎓</span>
                <div>
                    <small>Promotion</small>
                    <p><?= htmlspecialchars($user['annee_promotion']) ?></p>
                </div>
            </div>
        </div>

        <!-- ACTIONS PROFIL -->
        <div class="profile-actions">
            <a href="modifier_profil.php" class="btn-outline">
                Modifier les informations
            </a>
            <a href="changer_mdp.php" class="btn-danger">
                Changer le mot de passe
            </a>
        </div>

    </div>
</div>

<!-- JS -->
<script>
const toggleBtn = document.getElementById('toggle-photo-actions');
const actionsBox = document.getElementById('photo-edit-buttons');
const chooseBtn = document.getElementById('choose-photo');
const fileInput = document.getElementById('upload-photo');
const confirmBtn = document.getElementById('confirm-upload');

toggleBtn.addEventListener('click', () => {
    actionsBox.style.display =
        actionsBox.style.display === 'flex' ? 'none' : 'flex';
});

chooseBtn.addEventListener('click', () => {
    fileInput.click();
});

fileInput.addEventListener('change', () => {
    if (fileInput.files.length > 0) {
        confirmBtn.style.display = 'inline-block';
    }
});
</script>

</body>
</html>
