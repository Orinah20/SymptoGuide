<?php
session_start(); // Start the session

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_name'])) {
    header('Location: login.php');
    exit();
}

// Get the current filename and remove the extension
$currentFile = basename($_SERVER['PHP_SELF'], '.php');

// Define an array of valid pages
$validPages = array('dashboard', 'userdata', 'addUser', 'editUser', 'patientdata', 'diseasedata', 'symptomdata', 'reports', 'adminSettings', 'logout');

// Check if the current page is valid
$activePage = (in_array($currentFile, $validPages)) ? $currentFile : 'dashboard';
