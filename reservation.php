<?php
/**
 * This script handles the creation of a reservation for the Sport Areal website.
 */

/**
 * Creates a reservation and appends it to the reservations file.
 *
 * @param string $username The username (email) of the user.
 * @param string $court The court to reserve.
 * @param string $date The date of the reservation.
 * @param string $time The time of the reservation.
 * @return void
 */
function createReservation($username, $court, $date, $time) {
    $reservationLine = $username . '|' . $court . '|' . $date . '|' . $time . PHP_EOL;
    file_put_contents('Data/reservations.txt', $reservationLine, FILE_APPEND);
}

/**
 * Handles the reservation request from a GET request.
 *
 * @param string $username The username (email) of the user.
 * @param string $court The court to reserve.
 * @param string $date The date of the reservation.
 * @param string $time The time of the reservation.
 * @return void
 */
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