<?php
/**
 * This script validates an email address via an AJAX request for the Sport Areal website.
 */

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function handleEmailValidation() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'] ?? '';
        $response = ['valid' => validateEmail($email)];
        echo json_encode($response);
    }
}

handleEmailValidation();
?>
