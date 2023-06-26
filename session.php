<?php
session_start(); // Start the session

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_name'])) {
    header('Location: login.php');
    exit();
}

// Set a time limit for the session
$sessionExpireTime = 60; // Time limit in seconds (1 hour)

// Check if the session has expired
if (isset($_SESSION['last_activity']) && time() > ($_SESSION['last_activity'] + $sessionExpireTime)) {
    // Session expired, destroy session and redirect to login page
    session_destroy();
    header('Location: login.php');
    exit();
}

// Update last activity time
$_SESSION['last_activity'] = time();

