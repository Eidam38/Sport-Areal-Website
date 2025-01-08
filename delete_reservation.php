<?php
session_start();

$reservationToDelete = $_POST['reservation'];
$reservations = file('Data/reservations.txt', FILE_IGNORE_NEW_LINES); 

// Filter out the reservation that needs to be deleted
$newReservations = array_filter($reservations, function($line) use ($reservationToDelete) {
    return trim($line) !== trim($reservationToDelete);
});

file_put_contents('Data/reservations.txt', implode("\n", $newReservations) . "\n"); // Write the updated reservations back to the file
header('Location: profile.php');
exit();