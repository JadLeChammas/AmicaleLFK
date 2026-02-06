<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'config.php';
include 'auth.php';
requireAnyRole();

$events = [];
$currentYear = date("Y");
$futureYear = $currentYear + 1000; // Génère les anniversaires jusqu’à +1000 ans

// 🎂 Anniversaires des membres
$sql = "SELECT nom, prenom, date_naissance FROM membres WHERE date_naissance IS NOT NULL AND id >= 1000";
$stmt = $conn->query($sql);

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $birthDate = new DateTime($row['date_naissance']);
    $birthYear = (int)$birthDate->format("Y");
    $monthDay = $birthDate->format("m-d");

    for ($year = $birthYear; $year <= $futureYear; $year++) {
        $eventDate = $year . '-' . $monthDay;

        $events[] = [
            'title' => '🎉 ' . $row['prenom'] . ' ' . $row['nom'],
            'start' => $eventDate,
            'allDay' => true,
            'color' => '#f39c12'
        ];
    }
}

// 📌 Événements personnalisés créés par l’admin
$sqlEvents = "SELECT titre, description, date_evenement FROM evenements";
$stmtEvents = $conn->query($sqlEvents);

while ($row = $stmtEvents->fetch(PDO::FETCH_ASSOC)) {
    $events[] = [
        'title' => '📌 ' . $row['titre'],
        'start' => $row['date_evenement'],
        'allDay' => true,
        'color' => '#2980b9',
        'description' => $row['description']
    ];
}

// Envoi des événements au JS
echo "<script>var events = " . json_encode($events) . ";</script>";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Calendrier</title>

    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">

    <!-- FullCalendar JS - Version globale -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales/fr.js"></script>

    <!-- Favicon -->
    <link rel="icon" href="img/lfk.png">

    <!-- Style du calendrier et titre -->
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .titre-page {
            text-align: center;
            font-size: 32px;
            margin-top: 30px;
            margin-bottom: 20px;
            font-weight: bold;
            color: #2c3e50;
        }

        #calendar {
            max-width: 900px;
            min-height: 600px;
            margin: 0 auto 50px auto;
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: block;
        }

        .fc .fc-toolbar-title {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?> <!-- Ton header -->

<h1 class="titre-page">Calendrier</h1>

<div id="calendar"></div>

<?php include 'footer.php'; ?> <!-- Ton footer -->

<!-- Initialisation du calendrier -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        initialDate: new Date(),
        locale: 'fr',
        showNonCurrentDates: false,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,dayGridYear'
        },
        events: events,
        eventDisplay: 'block',
        eventClick: function(info) {
            // Affiche un popup avec le titre et la description si dispo
            var description = info.event.extendedProps.description || "Aucune description";
            alert(info.event.title + "\n\n" + description);
        }
    });

    calendar.render();
});
</script>

</body>
</html>
