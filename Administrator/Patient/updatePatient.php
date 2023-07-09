<?php

@include '../../config.php';
@include '../../session.php';

// Check if the patient ID and other data are provided in the form data
if (isset($_POST['patient_id'], $_POST['name'], $_POST['date_of_birth'], $_POST['gender'], $_POST['contact_number'], $_POST['address'])) {
    $patientId = $_POST['patient_id'];
    $name = $_POST['name'];
    $dateOfBirth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $contactNumber = $_POST['contact_number'];
    $address = $_POST['address'];

    // Update the patient data in the database
    $updateQuery = "UPDATE patients SET name = ?, date_of_birth = ?, gender = ?, contact_number = ?, address = ? WHERE patient_id = ?";
    $stmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($stmt, "ssssss", $name, $dateOfBirth, $gender, $contactNumber, $address, $patientId);
    mysqli_stmt_execute($stmt);

    // Redirect back to the user data page after updating
    header('Location: patientdata.php');
    exit();
}
?>
