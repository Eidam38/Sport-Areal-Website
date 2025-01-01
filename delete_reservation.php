<?php
session_start();

if (!isset($_SESSION['username']) || !isset($_POST['reservation'])) {
    header('Location: profile.php');
    exit();
}

$reservationToDelete = $_POST['reservation'];
$reservations = file('reservation.txt', FILE_IGNORE_NEW_LINES);
$newReservations = array_filter($reservations, function($line) use ($reservationToDelete) {
    return trim($line) !== trim($reservationToDelete);
});

file_put_contents('reservation.txt', implode("\n", $newReservations) . "\n");
header('Location: profile.php');
exit();