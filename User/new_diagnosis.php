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
    <script src="../script.js"></script>
</head>
<body>
<div class="container">
    <div class="container_header">
        <h2>SymptoGuide</h2>
        <div class="content_header-left">
            <div><?php echo $_SESSION['user_name']; ?></div>
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <div class="content">
        <div><p id="session-expire" style="display: none;">Session will expire in: <span id="timer"></span></p></div>
        <div class="content_data-header">
            <a href="user.php" class="back-button">
                <img src="../back-icon.png" alt="Back" height="30px" width="30px" class="back-icon">
            </a>
            <h1>New Diagnosis</h1>
        </div>

        <div>
            <form action="" method="POST" class="new_diagnosis">
                <label for="search_query">Search Patient:</label>
                <input type="text" name="search_query" placeholder="Enter Patient ID" required>
                <button type="submit">Search</button>
            </form>
        </div>

        <?php if ($patientData) : ?>
            <div class="patient_data">
                <div>
                    <h2><u>Patient Details</u></h2>
                    <div>
                        <p><strong>Patient ID:</strong> <?php echo $patientData['patient_id']; ?></p>
                    </div>
                    <div>
                        <p><strong>Name:</strong> <?php echo $patientData['patient_name']; ?></p>
                    </div>
                    <div>
                        <p><strong>Date of Birth:</strong> <?php echo $patientData['date_of_birth']; ?></p>
                    </div>
                    <div>
                        <p><strong>Gender:</strong> <?php echo $patientData['gender']; ?></p>
                    </div>
                    <div>
                        <p><strong>Contact Number:</strong> <?php echo $patientData['contact_number']; ?></p>
                    </div>
                    <div>
                        <p><strong>Address:</strong> <?php echo $patientData['address']; ?></p>
                    </div>

                    <div>
                        <button onclick="openDiagnosisPage()">Use Patient Details</button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div class="create-patient">
            If the patient is not found, <a href="createPatient.php">click here</a> to create a new patient.
        </div>
    </div>
</div>

<script>
    function openDiagnosisPage() {
        // Set the patient ID in the session
        <?php
        $_SESSION['patient_id'] = $patientData['patient_id'];
        $_SESSION['patient_name'] = $patientData['patient_name'];
        ?>

        // Get the patient ID
        var patientId = "<?php echo $_SESSION['patient_id']; ?>";
        // Redirect to diagnosis.php and pass the patient ID as a query parameter
        window.location.href = "diagnosis.php?patient_id=" + patientId;
    }
</script>
</body>
</html>
