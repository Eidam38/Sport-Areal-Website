<?php
/**
 * This script handles the user logout process for the Sport Areal website.
 */

function handleLogoutRequest() {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        session_start();
        session_destroy();
        header("Location: main.php");
        exit;
    }
}

handleLogoutRequest();
?>