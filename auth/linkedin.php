<?php
/**
 *  This script is calling by LinkedIn after a user attempts to login and
 *  authenticate our application.
 */

require_once("../include/iwa_auth.php");

// Ensure all required parameters are set.
if (!isset($_GET['code']) || !isset($_GET['state'])) {
    if (isset($_GET['error']))
        $_SESSION['error'] = $_GET['error'];
    if (isset($_GET['error_description']))
        $_SESSION['error_description'] = $_GET['error_description'];
    header("Location: /index.php");
    exit(0);
}

// Ensure the state parameters matches the one that was set.
$state = $_GET['state'];
if ($state != linkedin_auth_state()) {
    $_SESSION['error'] = "state_invalid";
    $_SESSION['error_description'] = "Missing or invalid auth state key.";
    header("Location: /index.php");
    exit(0);
}

// All is well.
linkedin_authenticate($_GET['code']);
header("Location: /application.php");
exit(0);