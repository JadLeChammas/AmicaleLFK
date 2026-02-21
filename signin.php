<?php
session_start();
require_once __DIR__ . '/config.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT id, nom, prenom, email, password, role, is_approved, annee_promotion
            FROM membres
            WHERE email = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (!password_verify($password, $row['password'])) {
            $error = "Mot de passe incorrect.";
        } elseif ((int)$row['is_approved'] === 0) {
            $error = "Votre compte n'a pas encore été approuvé par un administrateur.";
        } else {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_nom'] = $row['nom'];
            $_SESSION['user_prenom'] = $row['prenom'];
            $_SESSION['user_email'] = $row['email'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['annee_promotion'] = $row['annee_promotion'];

            if ($row['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit();
        }
    } else {
        $error = "Utilisateur introuvable.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="icon" type="image/png" href="/img/lfk.png">
    <link rel="stylesheet" href="css/signin.css">
</head>
<body>

<div class="form-wrapper">
    <a href="index.php" class="back-home">← Retour à l'accueil</a>

    <div class="form-box">
        <form class="form" action="signin.php" method="POST">

            <span class="title">Connexion</span>

            <?php if (!empty($error)): ?>
                <p style="color:red; text-align:center;">
                    <?= htmlspecialchars($error) ?>
                </p>
            <?php endif; ?>

            <div class="form-container">
                <input
                    type="email"
                    name="email"
                    class="input"
                    placeholder="Adresse E-mail"
                    required
                >

                <input
                    type="password"
                    name="password"
                    class="input"
                    placeholder="Mot de passe"
                    required
                >
            </div>

            <a href="forgot_password.php" class="forgot-password">
                Mot de passe oublié ?
            </a>

            <button type="submit">Connexion</button>

            <div class="divider">OU</div>

            <div class="register">
                Vous n’avez pas de compte ?
                <a href="choix_role.php">Créer un compte</a>
            </div>

        </form>
    </div>
</div>

</body>
</html>
