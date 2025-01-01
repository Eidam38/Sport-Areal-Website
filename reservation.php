<?php
session_start();
// ...existing code...

if (isset($_GET['court'], $_GET['date'], $_GET['time']) && isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $court = $_GET['court'];
    $date = $_GET['date'];
    $time = $_GET['time'];

    $reservationLine = $username . '|' . $court . '|' . $date . '|' . $time . PHP_EOL;
    file_put_contents('reservation.txt', $reservationLine, FILE_APPEND);

    header('Location: main.php?reservation=success');
    exit;
}

// ...existing code...