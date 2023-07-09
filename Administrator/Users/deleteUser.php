<?php

@include '../../config.php';
@include '../../session.php';

// Check if the user ID is provided in the form data
if (isset($_POST['medical_id'])) {
    $medicalId = $_POST['medical_id'];

    // Delete the user from the database
    $deleteQuery = "DELETE FROM users WHERE medical_id = ?";
    $stmt = mysqli_prepare($conn, $deleteQuery);
    mysqli_stmt_bind_param($stmt, "s", $medicalId);
    mysqli_stmt_execute($stmt);

    // Redirect back to the user data page after deletion
    header('Location: userdata.php');
    exit();
}

