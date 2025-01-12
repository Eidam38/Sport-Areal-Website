<?php
/**
 * This script handles the deletion of a reservation for the Sport Areal website.
 */
session_start();

$reservationToDelete = $_POST['reservation'];
$reservations = file('Data/reservations.txt', FILE_IGNORE_NEW_LINES); 

$newReservations = array_filter($reservations, function($line) use ($reservationToDelete) {
    return trim($line) !== trim($reservationToDelete);
});

file_put_contents('Data/reservations.txt', implode("\n", $newReservations) . "\n");
header('Location: profile.php');
exit();
?>