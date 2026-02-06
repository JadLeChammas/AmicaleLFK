<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
requireAnyRole(); // ✅ Tous les rôles connectés
include 'header.php';
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>
    <link rel="stylesheet" href="css/contact.css">
</head>

<body>

    <main class="contact">
        <h1>Contact</h1>
        <p>N'hésitez pas à nous contacter via ce formulaire :</p>

        <!-- 🆕 Section d'infos de l'association -->
        <section class="contact-info">
            <p><strong>Email :</strong> <a href="mailto:amicalelyceefrancaisdekoweit@gmail.com">amicalelyceefrancaisdekoweit@gmail.com</a></p>
            <p><strong>Instagram :</strong> <a href="https://www.instagram.com/amicalelfk/" target="_blank">@amicalelfk</a></p>
        </section>

        <!-- Formulaire -->
        <form id="contactForm">
    <label for="nom">Nom :</label>
    <input type="text" id="nom" name="nom" required>

    <label for="email">Email :</label>
    <input type="email" id="email" name="email" required>

    <label for="message">Message :</label>
    <textarea id="message" name="message" rows="6" required></textarea>

    <button type="submit">Envoyer</button>
</form>

<!-- ✅ Zone d'affichage du message -->
<div id="confirmation" style="margin-top: 15px; font-weight: bold;"></div>

    </main>

    <?php include 'footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $('#contactForm').on('submit', function(e) {
    e.preventDefault(); // Empêche l'envoi classique

    $.post('traitement_contact.php', $(this).serialize(), function(data) {
      $('#confirmation').text('✅ Message envoyé avec succès !');
      $('#contactForm')[0].reset();
    }).fail(function() {
      $('#confirmation').text('❌ Une erreur est survenue.');
    });
  });
</script>

</body>
</html>
