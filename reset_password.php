<?php
session_start();
require_once __DIR__ . '/config.php';
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

if (!isset($_GET['token'])) {
    die("Token manquant !");
}

$token = $_GET['token'];

$sql = "SELECT * FROM membres WHERE reset_token=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Token invalide ou expiré !");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        echo "<p style='color: red; text-align: center;'>Les mots de passe ne correspondent pas.</p>";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

        $sql = "UPDATE membres SET password=?, reset_token=NULL WHERE reset_token=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $hashed_password, $token);
        $stmt->execute();

        echo "<p style='color: green; text-align: center;'>Mot de passe mis à jour avec succès !</p>";
        echo "<p style='text-align: center;'><a href='signin.php'>Se connecter</a></p>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<link rel="icon" type="image/png" href="/img/lfk.png"> 
<head>
    <meta charset="UTF-8">
    <title>Réinitialisation du mot de passe</title>
    <link rel="stylesheet" href="signin.css">
</head>
<body>
<div class="form-wrapper">
    <div class="form-box">
        <form class="form" action="" method="POST">
            <span class="title">Réinitialisation du mot de passe</span>
            <div class="form-container">
                <input type="password" name="password" class="input" placeholder="Nouveau mot de passe" required>
                <input type="password" name="confirm_password" class="input" placeholder="Confirmer le mot de passe" required>
            </div>
            <button type="submit">Réinitialiser</button>
        </form>
    </div>
</div>
</body>
</html>
