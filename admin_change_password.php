<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
requireAdmin();
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}


$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $sql = "UPDATE membres SET password=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $hashed_password, $user_id);

        if ($stmt->execute()) {
            $success = "Mot de passe mis à jour avec succès !";
        } else {
            $error = "Erreur lors de la mise à jour.";
        }
        $stmt->close();
    }
}

$sql = "SELECT id, prenom, nom, email FROM membres";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">

<link rel="icon" type="image/png" href="/img/lfk.png"> <!-- ✅ Favicon -->

<head>
    <meta charset="UTF-8">
    <title>Changer le mot de passe</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>

<div class="admin-container">
    <a href="admin_dashboard.php" class="back-home">← Retour au tableau de bord</a>

    <div class="admin-box">
        <span class="title">Changer le mot de passe d'un utilisateur</span>

        <?php if ($success) echo "<p class='success'>$success</p>"; ?>
        <?php if ($error) echo "<p class='error'>$error</p>"; ?>

        <form action="admin_change_password.php" method="POST">
            <select name="user_id" required>
                <option value="">-- Sélectionner un utilisateur --</option>
                <?php while ($user = $result->fetch_assoc()) : ?>
                    <option value="<?php echo $user['id']; ?>">
                        <?php echo htmlspecialchars($user['prenom'] . " " . $user['nom'] . " (" . $user['email'] . ")"); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <input type="password" name="new_password" class="input" placeholder="Nouveau mot de passe" required>
            <input type="password" name="confirm_password" class="input" placeholder="Confirmer le mot de passe" required>

            <button type="submit" class="btn">Mettre à jour</button>
        </form>
    </div>
</div>

</body>
</html>
