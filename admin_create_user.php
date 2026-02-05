<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
requireAdmin(); // ✅ Protection admin uniquement

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

$error = $success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO membres (email, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $email, $password, $role);

    if ($stmt->execute()) {
        $success = "Utilisateur créé avec succès.";
    } else {
        $error = "Erreur lors de la création de l'utilisateur.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un utilisateur</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="icon" type="image/png" href="/img/lfk.png">
</head>
<body>

<div class="admin-container">
    <a href="admin_dashboard.php" class="back-home">← Retour au tableau de bord</a>

    <div class="admin-box">
        <span class="title">Créer un utilisateur</span>
        
        <form action="" method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            
            <select name="role" required>
                <option value="user">Utilisateur</option>
                <option value="admin">Admin</option>
                <option value="eleve">Élève</option>
                <option value="membre_d_honneur">Membre d'honneur</option>
            </select>

            <button type="submit" class="btn">Créer</button>
        </form>

        <?php if ($error): ?>
            <p style="color:red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <?php if ($success): ?>
            <p style="color:green;"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
