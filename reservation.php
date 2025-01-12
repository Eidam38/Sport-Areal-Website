<?php
/**
 * This script handles the creation of a reservation for the Sport Areal website.
 */

function createReservation($username, $court, $date, $time) {
    $reservationLine = $username . '|' . $court . '|' . $date . '|' . $time . PHP_EOL;
    file_put_contents('Data/reservations.txt', $reservationLine, FILE_APPEND);
}

function handleReservationRequest() {
    session_start();
    if (isset($_GET['court'], $_GET['date'], $_GET['time']) && isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
        $court = $_GET['court'];
        $date = $_GET['date'];
        $time = $_GET['time'];

        createReservation($username, $court, $date, $time);

        header('Location: main.php?reservation=success');
        exit;
    }
}

handleReservationRequest();
?>