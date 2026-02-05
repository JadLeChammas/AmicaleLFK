<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
requireAnyRole(); // ✅ Protection : tous les rôles connectés

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$erreur = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ancien_mdp = $_POST['ancien_mdp'];
    $nouveau_mdp = $_POST['nouveau_mdp'];
    $confirmer_mdp = $_POST['confirmer_mdp'];

    $sql = "SELECT password FROM membres WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!password_verify($ancien_mdp, $user['password'])) {
        $erreur = "L'ancien mot de passe est incorrect.";
    } elseif ($nouveau_mdp !== $confirmer_mdp) {
        $erreur = "Les nouveaux mots de passe ne correspondent pas.";
    } else {
        $nouveau_mdp_hash = password_hash($nouveau_mdp, PASSWORD_DEFAULT);

        $sql = "UPDATE membres SET password = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $nouveau_mdp_hash, $user_id);

        if ($stmt->execute()) {
            $success = "Mot de passe mis à jour avec succès.";
        } else {
            $erreur = "Une erreur s'est produite. Veuillez réessayer.";
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Changer le mot de passe</title>
    <link rel="stylesheet" href="css/changer_mdp.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/img/lfk.png">
</head>
<body>
<div class="form-wrapper">
    <a href="profil.php" class="back-home">← Retour au profil</a>

    <div class="form-box">
        <h2 class="title">Changer le mot de passe</h2>
        <p class="subtitle">Sécurisez votre compte en mettant à jour votre mot de passe.</p>

        <?php if ($erreur): ?>
            <p class="message error"><?= htmlspecialchars($erreur) ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p class="message success"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <form action="changer_mdp.php" method="post" class="form">
            <div class="form-container">
                <input type="password" class="input" name="ancien_mdp" placeholder="Ancien mot de passe" required>
            </div>

            <div class="form-container">
                <input type="password" class="input" name="nouveau_mdp" placeholder="Nouveau mot de passe" required>
            </div>

            <div class="form-container">
                <input type="password" class="input" name="confirmer_mdp" placeholder="Confirmer le mot de passe" required>
            </div>
            
            <button type="submit">Mettre à jour</button>
        </form>
    </div>
</div>
</body>
</html>
