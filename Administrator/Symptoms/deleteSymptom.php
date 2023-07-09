<?php

@include '../../config.php';
@include '../../session.php';

// Check if the symptom ID is provided in the form data
if (isset($_POST['symptom_id'])) {
    $symptomId = $_POST['symptom_id'];

    // Delete the symptom from the database
    $deleteQuery = "DELETE FROM symptoms WHERE symptom_id = ?";
    $stmt = mysqli_prepare($conn, $deleteQuery);
    mysqli_stmt_bind_param($stmt, "i", $symptomId);
    mysqli_stmt_execute($stmt);

    // Redirect back to the symptom data page after deletion
    header('Location: symptomdata.php');
    exit();
}
?>
