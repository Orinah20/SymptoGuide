<?php

@include '../../config.php';
@include '../../session.php';

// Check if the user ID and user type are provided in the form data
if (isset($_POST['medical_id']) && isset($_POST['user_type'])) {
    $medicalId = $_POST['medical_id'];
    $userType = $_POST['user_type'];

    // Update the user type in the database
    $updateQuery = "UPDATE users SET user_type = ? WHERE medical_id = ?";
    $stmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($stmt, "ss", $userType, $medicalId);
    mysqli_stmt_execute($stmt);

    // Redirect back to the user data page after updating
    header('Location: userdata.php');
    exit();
}
?>
