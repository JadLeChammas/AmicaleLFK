<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
requireAnyRole();

$conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = "SELECT nom, prenom, date_naissance FROM membres WHERE date_naissance IS NOT NULL";
$stmt = $conn->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

$events = [];
$currentYear = date('Y');

foreach ($results as $row) {
    $date = new DateTime($row['date_naissance']);
    $formattedDate = $date->setDate($currentYear, $date->format('m'), $date->format('d'))->format('Y-m-d');

    $events[] = [
        'title' => '🎂 ' . $row['prenom'] . ' ' . $row['nom'],
        'start' => $formattedDate,
        'allDay' => true
    ];
}

header('Content-Type: application/json');
echo json_encode($events);
