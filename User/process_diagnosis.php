<?php
@include '../config.php';
@include '../session.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the patient ID and medical ID from the session
    $patientId = (string) $_SESSION['patient_id'];
    $medicalId = (string) $_SESSION['user_id'];

    // Get the selected symptoms from the form
    $selectedSymptoms = isset($_POST['symptoms']) ? $_POST['symptoms'] : array();

    // Prepare the SQL statement to insert the symptoms
    $stmt = $conn->prepare("INSERT INTO patient_symptoms (patient_id, symptom_id, medical_id) VALUES (?, ?, ?)");

    // Fetch patient and user data from their respective tables
    $patientData = fetchPatientData($patientId);
    $userData = fetchUserData($medicalId);

    // Iterate over the selected symptoms and bind the parameters
    foreach ($selectedSymptoms as $symptomId) {
        // Bind the parameters and execute the statement
        $stmt->bind_param("sis", $patientData['patient_id'], $symptomId, $userData['medical_id']);
        $stmt->execute();
    }

    // Close the statement
    $stmt->close();

    // Pass the selected symptoms to the success page using a query parameter
    header('Location: diagnosis_success.php?symptoms=' . urlencode(implode(',', $selectedSymptoms)));
    exit();
}

// Function to fetch patient data from the database
function fetchPatientData($patientId)
{
    global $conn;

    // Prepare the SQL statement
    $stmt = $conn->prepare("SELECT * FROM patients WHERE patient_id = ?");
    $stmt->bind_param("s", $patientId);

    // Execute the query
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    // Fetch the patient data
    $patientData = $result->fetch_assoc();

    // Close the statement
    $stmt->close();

    // Return the patient data
    return $patientData;
}

// Function to fetch user data from the database
function fetchUserData($medicalId)
{
    global $conn;

    // Prepare the SQL statement
    $stmt = $conn->prepare("SELECT * FROM users WHERE medical_id = ?");
    $stmt->bind_param("s", $medicalId);

    // Execute the query
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    // Fetch the user data
    $userData = $result->fetch_assoc();

    // Close the statement
    $stmt->close();

    // Return the user data
    return $userData;
}
?>
