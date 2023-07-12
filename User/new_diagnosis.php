<?php
@include '../config.php';
@include '../session.php';

// Variables to store the search query and patient data
$searchQuery = "";
$patientData = null;

// Check if the search form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the search query from the form
    $searchQuery = $_POST['search_query'];

    // Search for the patient in the database
    $searchResult = searchPatient($searchQuery);

    if ($searchResult) {
        // Patient found
        $patientData = $searchResult;

    } else {
        // Redirect to create new patient page
        echo '<script>alert("No patient found with the provided ID. Please create a new patient.");</script>';
    }
}

// Function to search for a patient in the database
function searchPatient($query)
{
    global $conn;

    // Prepare the SQL statement
    $stmt = $conn->prepare("SELECT * FROM patients WHERE patient_id = ?");
    $stmt->bind_param("s", $query);

    // Execute the query
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    // Fetch the patient data
    $patient = $result->fetch_assoc();

    // Close the statement
    $stmt->close();

    // Return the patient data
    return $patient;
}
?>


<!DOCTYPE html>
<html lang="">
<head>
    <title>New Diagnosis</title>
    <link rel="stylesheet" type="text/css" href="../style.css">
</head>
<body>
<div class="container">
    <div class="content_data-header">
        <a href="user.php" class="back-button">
            <img src="../back-icon.png" alt="Back" height="30px" width="30px" class="back-icon">
        </a>
        <h1>New Diagnosis</h1>
    </div>
    <form action="" method="POST">
        <label for="search_query">Search Patient:</label>
        <input type="text" name="search_query" placeholder="Enter Patient ID" required>
        <button type="submit">Search</button>
    </form>

    <?php if ($patientData) : ?>
        <div class="patient-data">
            <h2>Patient Details</h2>
            <p><strong>Patient ID:</strong> <?php echo $patientData['patient_id']; ?></p>
            <p><strong>Name:</strong> <?php echo $patientData['name']; ?></p>
            <p><strong>Date of Birth:</strong> <?php echo $patientData['date_of_birth']; ?></p>
            <p><strong>Gender:</strong> <?php echo $patientData['gender']; ?></p>
            <p><strong>Contact Number:</strong> <?php echo $patientData['contact_number']; ?></p>
            <p><strong>Address:</strong> <?php echo $patientData['address']; ?></p>
            <!-- Display other patient information here -->

            <!-- Button to use patient details -->
            <button onclick="openDiagnosisPage()">Use Patient Details</button>
        </div>
    <?php endif; ?>

    <div class="create-patient">
        <p>If the patient is not found, <a href="createPatient.php">click here</a> to create a new patient.</p>
    </div>
</div>

<script>
    function openDiagnosisPage() {
        // Set the patient ID in the session
        <?php
        $_SESSION['patient_id'] = $patientData['patient_id'];
        $_SESSION['patient_name'] = $patientData['name'];
        ?>

        // Get the patient ID
        var patientId = "<?php echo $_SESSION['patient_id']; ?>";
        // Redirect to diagnosis.php and pass the patient ID as a query parameter
        window.location.href = "diagnosis.php?patient_id=" + patientId;
    }
</script>
</body>
</html>
