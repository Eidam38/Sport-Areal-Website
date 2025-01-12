<?php

/**
 * This script validates an email address via an AJAX request for the Sport Areal website.
 */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $response = ['valid' => filter_var($email, FILTER_VALIDATE_EMAIL) !== false];
    echo json_encode($response);
}
?>
