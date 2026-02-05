<?php
session_start();
require_once __DIR__ . '/config.php';
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    $sql = "SELECT id FROM membres WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $token = bin2hex(random_bytes(50));

        $sql = "UPDATE membres SET reset_token=? WHERE email=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $token, $email);
        $stmt->execute();

        $reset_link = "http://localhost/lfkalumni/reset_password.php?token=" . $token;

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; 
            $mail->SMTPAuth = true;
            $mail->Username = 'tonemail@gmail.com'; 
            $mail->Password = 'tonmotdepasse'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('tonemail@gmail.com', 'LFK Alumni');
            $mail->addAddress($email);

            $mail->Subject = "Réinitialisation de mot de passe";
            $mail->Body = "Cliquez sur ce lien pour réinitialiser votre mot de passe : $reset_link";
            $mail->send();

            echo "<p style='color: green; text-align: center;'>Un email de réinitialisation a été envoyé.</p>";
        } catch (Exception $e) {
            echo "<p style='color: red; text-align: center;'>Erreur d'envoi de l'email : {$mail->ErrorInfo}</p>";
        }
    } else {
        echo "<p style='color: red; text-align: center;'>Adresse e-mail non trouvée.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<link rel="icon" type="image/png" href="/img/lfk.png"> 

<head>
    <meta charset="UTF-8">
    <title>Mot de passe oublié</title>
    <link rel="stylesheet" href="signin.css">
</head>
<body>
<div class="form-wrapper">
    <a href="signin.php" class="back-home">← Retour à la connexion</a>
    <div class="form-box">
        <form class="form" action="forgot_password.php" method="POST">
            <span class="title">Mot de passe oublié</span>
            <div class="form-container">
                <input type="email" name="email" class="input" placeholder="Entrez votre email" required>
            </div>
            <button type="submit">Envoyer</button>
        </form>
    </div>
</div>
</body>
</html>
