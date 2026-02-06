<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';

requireAnyRole();

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$user_id = $_SESSION['user_id'];

/* --- UPDATE PROFIL --- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $prenom = trim($_POST['prenom']);
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);
    $date_naissance = $_POST['date_naissance'];
    $etablissement = trim($_POST['etablissement']);
    $annee_promotion = intval($_POST['annee_promotion']);

    $stmt = $conn->prepare("
        UPDATE membres 
        SET prenom=?, nom=?, email=?, telephone=?, date_naissance=?, etablissement=?, annee_promotion=?
        WHERE id=?
    ");
    $stmt->bind_param(
        "ssssssii",
        $prenom,
        $nom,
        $email,
        $telephone,
        $date_naissance,
        $etablissement,
        $annee_promotion,
        $user_id
    );
    $stmt->execute();
    $stmt->close();

    header("Location: profil.php?updated=1");
    exit;
}

/* --- FETCH USER --- */
$stmt = $conn->prepare("
    SELECT prenom, nom, email, telephone, date_naissance, etablissement, annee_promotion
    FROM membres WHERE id=?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le profil</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Réutilise ton CSS existant -->
    <link rel="stylesheet" href="css/profil.css">

    <style>
        .form-group {
            margin-bottom: 18px;
        }

        label {
            font-weight: 600;
            font-size: 14px;
            display: block;
            margin-bottom: 6px;
        }

        input {
            width: 100%;
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid #ccc;
            font-size: 15px;
        }

        input:focus {
            outline: none;
            border-color: var(--accent);
        }

        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }

        .form-actions button,
        .form-actions a {
            flex: 1;
            text-align: center;
        }
    </style>
</head>

<body>

<div class="profile-wrapper">

    <a href="profil.php" class="back-home">← Retour au profil</a>

    <div class="profile-card">

        <h2 style="text-align:center; margin-bottom:20px;">
            Modifier les informations
        </h2>

        <form method="POST">

            <div class="form-group">
                <label>Prénom</label>
                <input type="text" name="prenom" required value="<?= htmlspecialchars($user['prenom']) ?>">
            </div>

            <div class="form-group">
                <label>Nom</label>
                <input type="text" name="nom" required value="<?= htmlspecialchars($user['nom']) ?>">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required value="<?= htmlspecialchars($user['email']) ?>">
            </div>

            <div class="form-group">
                <label>Téléphone</label>
                <input type="text" name="telephone" value="<?= htmlspecialchars($user['telephone']) ?>">
            </div>

            <div class="form-group">
                <label>Date de naissance</label>
                <input type="date" name="date_naissance" value="<?= htmlspecialchars($user['date_naissance']) ?>">
            </div>

            <div class="form-group">
                <label>Établissement</label>
                <input type="text" name="etablissement" value="<?= htmlspecialchars($user['etablissement']) ?>">
            </div>

            <div class="form-group">
                <label>Année de promotion</label>
                <input type="number" name="annee_promotion" value="<?= htmlspecialchars($user['annee_promotion']) ?>">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Enregistrer</button>
                <a href="profil.php" class="btn-outline">Annuler</a>
            </div>

        </form>

    </div>
</div>

</body>
</html>
