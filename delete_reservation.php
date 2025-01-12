<?php
/**
 * This script handles the deletion of a reservation for the Sport Areal website.
 */
session_start();

function deleteReservation($reservationToDelete) {
    $reservations = file('Data/reservations.txt', FILE_IGNORE_NEW_LINES); 

    $newReservations = array_filter($reservations, function($line) use ($reservationToDelete) {
        return trim($line) !== trim($reservationToDelete);
    });

    file_put_contents('Data/reservations.txt', implode("\n", $newReservations) . "\n");
}

function handleDeleteReservationRequest() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reservation'])) {
        $reservationToDelete = $_POST['reservation'];
        deleteReservation($reservationToDelete);
        header('Location: profile.php');
        exit();
    }
}

handleDeleteReservationRequest();
?>