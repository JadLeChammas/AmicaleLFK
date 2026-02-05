<?php
session_start();
require_once __DIR__ . '/config.php';
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $indicatif = trim($_POST['indicatif']);
    $telephone_local = trim($_POST['telephone_local']);
    $telephone = $indicatif . $telephone_local;
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        $sql = "SELECT id FROM membres WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Cet email est déjà utilisé.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO membres (nom, prenom, email, telephone, password, role) VALUES (?, ?, ?, ?, ?, 'eleve')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $nom, $prenom, $email, $telephone, $hashed_password);

            if ($stmt->execute()) {
                $success = "Compte Élève créé avec succès ! <a href='signin.php'>Se connecter</a>";
            } else {
                $error = "Erreur lors de l'inscription.";
            }
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
    <title>Inscription</title>
    <link rel="icon" type="image/png" href="/img/lfk.png">
    <link rel="stylesheet" href="css/signup.css">
    <script>
        function onlyNumbers(evt) {
            const charCode = evt.which ? evt.which : evt.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                evt.preventDefault();
            }
        }
    </script>
</head>
<body>

<div class="form-wrapper">
    <a href="choix_role.php" class="back-home">← Retour</a>
    <div class="form-box">
        <form class="form" action="signup.php" method="POST">
            <span class="title">Créer un compte</span>
            <?php if ($error): ?>
                <p style="color: red; text-align: center;"> <?php echo htmlspecialchars($error); ?> </p>
            <?php endif; ?>
            <?php if ($success): ?>
                <p style="color: green; text-align: center;"> <?php echo $success; ?> </p>
            <?php endif; ?>
            <div class="form-container">
                <input type="text" name="nom" class="input" placeholder="Nom" required>
                <input type="text" name="prenom" class="input" placeholder="Prénom" required>
                <input type="email" name="email" class="input" placeholder="Email" required>
                <div class="telephone-group">
                    <div class="telephone-row">
                        <select name="indicatif" required class="select-indicatif input">
                        <?php
$indicatifs = [
    ['🇦🇫', '+93'], ['🇦🇱', '+355'], ['🇩🇿', '+213'], ['🇦🇴', '+244'], ['🇦🇷', '+54'], ['🇦🇲', '+374'], ['🇦🇺', '+61'], ['🇦🇹', '+43'], ['🇦🇿', '+994'],
    ['🇧🇭', '+973'], ['🇧🇩', '+880'], ['🇧🇾', '+375'], ['🇧🇪', '+32'], ['🇧🇯', '+229'], ['🇧🇴', '+591'], ['🇧🇦', '+387'], ['🇧🇼', '+267'], ['🇧🇷', '+55'],
    ['🇧🇳', '+673'], ['🇧🇬', '+359'], ['🇰🇭', '+855'], ['🇨🇲', '+237'], ['🇨🇦', '+1'], ['🇨🇫', '+236'], ['🇹🇩', '+235'], ['🇨🇱', '+56'], ['🇨🇳', '+86'],
    ['🇨🇴', '+57'], ['🇨🇷', '+506'], ['🇭🇷', '+385'], ['🇨🇺', '+53'], ['🇨🇾', '+357'], ['🇨🇿', '+420'], ['🇩🇰', '+45'], ['🇩🇯', '+253'], ['🇩🇴', '+1-809'],
    ['🇪🇨', '+593'], ['🇪🇬', '+20'], ['🇸🇻', '+503'], ['🇬🇶', '+240'], ['🇪🇷', '+291'], ['🇪🇪', '+372'], ['🇪🇹', '+251'], ['🇫🇯', '+679'], ['🇫🇮', '+358'],
    ['🇫🇷', '+33'], ['🇬🇦', '+241'], ['🇬🇲', '+220'], ['🇬🇪', '+995'], ['🇩🇪', '+49'], ['🇬🇭', '+233'], ['🇬🇷', '+30'], ['🇬🇹', '+502'], ['🇬🇳', '+224'],
    ['🇭🇹', '+509'], ['🇭🇳', '+504'], ['🇭🇺', '+36'], ['🇮🇸', '+354'], ['🇮🇳', '+91'], ['🇮🇩', '+62'], ['🇮🇷', '+98'], ['🇮🇶', '+964'], ['🇮🇪', '+353'],
    ['🇮🇱', '+972'], ['🇮🇹', '+39'], ['🇯🇲', '+1-876'], ['🇯🇵', '+81'], ['🇯🇴', '+962'], ['🇰🇿', '+7'], ['🇰🇪', '+254'], ['🇰🇼', '+965'], ['🇱🇧', '+961'],
    ['🇱🇷', '+231'], ['🇱🇾', '+218'], ['🇱🇹', '+370'], ['🇱🇺', '+352'], ['🇲🇬', '+261'], ['🇲🇼', '+265'], ['🇲🇾', '+60'], ['🇲🇻', '+960'], ['🇲🇱', '+223'],
    ['🇲🇹', '+356'], ['🇲🇷', '+222'], ['🇲🇺', '+230'], ['🇲🇽', '+52'], ['🇲🇩', '+373'], ['🇲🇳', '+976'], ['🇲🇪', '+382'], ['🇲🇦', '+212'], ['🇲🇿', '+258'],
    ['🇳🇦', '+264'], ['🇳🇵', '+977'], ['🇳🇱', '+31'], ['🇳🇿', '+64'], ['🇳🇮', '+505'], ['🇳🇪', '+227'], ['🇳🇬', '+234'], ['🇰🇵', '+850'], ['🇲🇰', '+389'],
    ['🇳🇴', '+47'], ['🇴🇲', '+968'], ['🇵🇰', '+92'], ['🇵🇸', '+970'], ['🇵🇦', '+507'], ['🇵🇾', '+595'], ['🇵🇪', '+51'], ['🇵🇭', '+63'], ['🇵🇱', '+48'],
    ['🇵🇹', '+351'], ['🇶🇦', '+974'], ['🇷🇴', '+40'], ['🇷🇺', '+7'], ['🇷🇼', '+250'], ['🇸🇦', '+966'], ['🇸🇳', '+221'], ['🇷🇸', '+381'], ['🇸🇨', '+248'],
    ['🇸🇱', '+232'], ['🇸🇬', '+65'], ['🇸🇰', '+421'], ['🇸🇮', '+386'], ['🇿🇦', '+27'], ['🇰🇷', '+82'], ['🇸🇸', '+211'], ['🇪🇸', '+34'], ['🇱🇰', '+94'],
    ['🇸🇩', '+249'], ['🇸🇷', '+597'], ['🇸🇿', '+268'], ['🇸🇪', '+46'], ['🇨🇭', '+41'], ['🇸🇾', '+963'], ['🇹🇼', '+886'], ['🇹🇯', '+992'], ['🇹🇿', '+255'],
    ['🇹🇭', '+66'], ['🇹🇬', '+228'], ['🇹🇳', '+216'], ['🇹🇷', '+90'], ['🇹🇲', '+993'], ['🇺🇬', '+256'], ['🇺🇦', '+380'], ['🇦🇪', '+971'], ['🇬🇧', '+44'],
    ['🇺🇸', '+1'], ['🇺🇾', '+598'], ['🇺🇿', '+998'], ['🇻🇪', '+58'], ['🇻🇳', '+84'], ['🇾🇪', '+967'], ['🇿🇲', '+260'], ['🇿🇼', '+263']
];
foreach ($indicatifs as $item) {
    echo "<option value=\"{$item[1]}\">{$item[0]} {$item[1]}</option>";
}
?>
                

                        </select>
                        <input type="text" name="telephone_local" class="input" placeholder="Numéro" required onkeypress="onlyNumbers(event)" maxlength="15">
                    </div>
                </div>

                <input type="password" name="password" class="input" placeholder="Mot de passe" required>
                <input type="password" name="confirm_password" class="input" placeholder="Confirmer le mot de passe" required>
            </div>
            <button type="submit">S'inscrire</button>
        </form>
        <div class="form-section">
            <p>Déjà un compte ? <a href="signin.php">Se connecter</a></p>
        </div>
    </div>
</div>

</body>
</html>