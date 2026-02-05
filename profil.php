<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
requireAnyRole(); // ✅ Tous les utilisateurs connectés

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT nom, prenom, email, telephone, date_naissance, annee_promotion, etablissement, photo_profil 
        FROM membres 
        WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$conn->close();

$photo_profil = !empty($user['photo_profil']) ? "uploads/" . htmlspecialchars($user['photo_profil']) : "default-avatar.png";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil - <?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></title>
    <link rel="stylesheet" href="css/profil.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/img/lfk.png">
</head>
<body>

<div class="profile-container">
    <a href="index.php" class="back-home">← Retour à l'accueil</a>

    <div class="profile-card">
        <div class="profile-header">
            <label for="upload-photo" class="profile-avatar-label">
                <img src="<?= $photo_profil ?>" alt="Avatar" class="profile-avatar">
            </label>

            <h2><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></h2>

            <form action="upload_photo.php" method="POST" enctype="multipart/form-data">
                <input type="file" id="upload-photo" name="photo" hidden>
                <button type="button" id="trigger-upload">Changer la photo</button>
                <button type="submit" id="upload-btn" style="display: none;">Téléverser</button>
            </form>
        </div>

        <div class="profile-info">
            <div class="info-item">
                <span class="icon">📧</span>
                <div>
                    <h4>Email</h4>
                    <p><?= htmlspecialchars($user['email']) ?></p>
                </div>
            </div>

            <div class="info-item">
                <span class="icon">🎓</span>
                <div>
                    <h4>Établissement</h4>
                    <p><?= htmlspecialchars($user['etablissement']) ?></p>
                </div>
            </div>

            <div class="info-item">
                <span class="icon">📅</span>
                <div>
                    <h4>Date de naissance</h4>
                    <p><?= htmlspecialchars($user['date_naissance']) ?></p>
                </div>
            </div>

            <div class="info-item">
                <span class="icon">📞</span>
                <div>
                    <h4>Téléphone</h4>
                    <p><?= htmlspecialchars($user['telephone']) ?></p>
                </div>
            </div>

            <div class="info-item">
                <span class="icon">🎓</span>
                <div>
                    <h4>Promotion</h4>
                    <p><?= htmlspecialchars($user['annee_promotion']) ?></p>
                </div>
            </div>
        </div>

        <form action="changer_mdp.php" method="get">
            <button type="submit" class="btn-change-password">Changer le mot de passe</button>
        </form>
    </div>
</div>

<script>
    document.getElementById('trigger-upload').addEventListener('click', function() {
        document.getElementById('upload-photo').click();
    });

    document.getElementById('upload-photo').addEventListener('change', function() {
        document.getElementById('upload-btn').style.display = 'block';
    });
</script>

</body>
</html>
