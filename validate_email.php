<?php
/**
 * This script validates an email address via an AJAX request for the Sport Areal website.
 */

/**
 * Validates an email address.
 * 
 * @param string $email The email address to validate.
 * @return bool True if the email is valid, false otherwise.
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Handles the email validation request from a POST form submission.
 * 
 * @return void
 */
function handleEmailValidation() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'] ?? '';
        $response = ['valid' => validateEmail($email)];
        echo json_encode($response);
    }
}

handleEmailValidation();
?>
