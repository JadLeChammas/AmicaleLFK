<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
requireAdmin(); // ✅ Protection propre pour les admins

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

$error = $success = "";

// Exclure soi-même de la liste
$users = $conn->query("SELECT id, email, role FROM membres WHERE id != " . $_SESSION['user_id']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['role'];

    $stmt = $conn->prepare("UPDATE membres SET role = ? WHERE id = ?");
    $stmt->bind_param("si", $new_role, $user_id);

    if ($stmt->execute()) {
        $success = "Rôle mis à jour avec succès.";
    } else {
        $error = "Erreur lors de la mise à jour du rôle.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Attribuer des rôles</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="icon" type="image/png" href="/img/lfk.png"> <!-- ✅ Favicon -->
</head>
<body>

<div class="admin-container">
    <a href="admin_dashboard.php" class="back-home">← Retour au tableau de bord</a>

    <div class="admin-box">
        <span class="title">Attribuer des rôles</span>
        
        <form action="" method="POST">
            <select name="user_id" required>
                <?php while ($row = $users->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['email']) ?> (<?= htmlspecialchars($row['role']) ?>)</option>
                <?php endwhile; ?>
            </select>

            <select name="role" required>
                <option value="user">Utilisateur</option>
                <option value="admin">Admin</option>
                <option value="eleve">Élève</option>
                <option value="membre_d_honneur">Membre d'honneur</option>
            </select>

            <button type="submit" class="btn">Mettre à jour</button>
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
