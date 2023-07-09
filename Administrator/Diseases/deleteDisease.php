<?php

@include '../../config.php';
@include '../../session.php';

// Check if the disease ID is provided in the form data
if (isset($_POST['disease_id'])) {
    $diseaseId = $_POST['disease_id'];

    // Delete the disease symptoms from the Disease_symptoms table
    $deleteSymptomsQuery = "DELETE FROM Disease_symptoms WHERE disease_id = ?";
    $stmtDeleteSymptoms = mysqli_prepare($conn, $deleteSymptomsQuery);
    mysqli_stmt_bind_param($stmtDeleteSymptoms, "i", $diseaseId);
    mysqli_stmt_execute($stmtDeleteSymptoms);

    // Delete the disease from the diseases table
    $deleteDiseaseQuery = "DELETE FROM diseases WHERE disease_id = ?";
    $stmtDeleteDisease = mysqli_prepare($conn, $deleteDiseaseQuery);
    mysqli_stmt_bind_param($stmtDeleteDisease, "i", $diseaseId);
    mysqli_stmt_execute($stmtDeleteDisease);

    // Redirect back to the disease data page after deletion
    header('Location: diseasedata.php');
    exit();
}
?>
