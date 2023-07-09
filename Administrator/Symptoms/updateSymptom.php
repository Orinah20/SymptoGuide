<?php

@include '../../config.php';
@include '../../session.php';

// Check if the symptom ID and other data are provided in the form data
if (isset($_POST['symptom_id'], $_POST['symptom_name'], $_POST['symptom_description'])) {
    $symptomId = $_POST['symptom_id'];
    $symptomName = $_POST['symptom_name'];
    $symptomDescription = $_POST['symptom_description'];

    // Update the symptom data in the database
    $updateQuery = "UPDATE symptoms SET symptom_name = ?, symptom_description = ? WHERE symptom_id = ?";
    $stmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($stmt, "ssi", $symptomName, $symptomDescription, $symptomId);
    mysqli_stmt_execute($stmt);

    // Redirect back to the symptom data page after updating
    header('Location: symptomdata.php');
    exit();
}
?>
