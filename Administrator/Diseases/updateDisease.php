<?php

@include '../../config.php';
@include '../../session.php';

// Check if the disease ID and other data are provided in the form data
if (isset($_POST['disease_id'], $_POST['disease_name'], $_POST['disease_description'], $_POST['symptoms'])) {
    $diseaseId = $_POST['disease_id'];
    $diseaseName = $_POST['disease_name'];
    $diseaseDescription = $_POST['disease_description'];
    $selectedSymptoms = $_POST['symptoms'];

    // Update the disease data in the database
    $updateQuery = "UPDATE diseases SET disease_name = ?, disease_description = ? WHERE disease_id = ?";
    $stmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($stmt, "ssi", $diseaseName, $diseaseDescription, $diseaseId);
    mysqli_stmt_execute($stmt);

    // Delete existing disease symptoms for the given disease ID
    $deleteQuery = "DELETE FROM Disease_symptoms WHERE disease_id = ?";
    $stmt = mysqli_prepare($conn, $deleteQuery);
    mysqli_stmt_bind_param($stmt, "i", $diseaseId);
    mysqli_stmt_execute($stmt);

    // Insert new disease symptoms for the given disease ID and selected symptoms
    $insertQuery = "INSERT INTO Disease_symptoms (disease_id, symptom_id) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $insertQuery);
    mysqli_stmt_bind_param($stmt, "ii", $diseaseId, $symptomId);

    // Iterate over the selected symptoms and execute the insert query for each symptom
    foreach ($selectedSymptoms as $symptomId) {
        mysqli_stmt_execute($stmt);
    }

    // Update the symptom count for the disease
    updateSymptomCount($diseaseId);

    // Redirect back to the disease data page after updating
    header('Location: diseasedata.php');
    exit();
}

function updateSymptomCount($diseaseId)
{
    global $conn;

    // Count the number of symptoms for the given disease ID
    $countQuery = "SELECT COUNT(*) AS symptom_count FROM Disease_symptoms WHERE disease_id = ?";
    $stmt = mysqli_prepare($conn, $countQuery);
    mysqli_stmt_bind_param($stmt, "i", $diseaseId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $symptomCount = $row['symptom_count'];

        // Update the symptom count in the diseases table
        $updateCountQuery = "UPDATE diseases SET symptom_count = ? WHERE disease_id = ?";
        $stmt = mysqli_prepare($conn, $updateCountQuery);
        mysqli_stmt_bind_param($stmt, "ii", $symptomCount, $diseaseId);
        mysqli_stmt_execute($stmt);
    }

    mysqli_stmt_close($stmt);
}
?>
