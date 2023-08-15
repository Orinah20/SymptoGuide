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
        <div style="cursor: pointer; ">
            <h2>
                <a style="text-decoration: none; color: inherit" href="user.php">SymptoGuide</a>
            </h2>
        </div>
        <div class="content_header-left">
            <h3><?php echo $_SESSION['user_name']; ?></h3>
            <div class="userSettings">
                <a href="userSettings.php">
                    <button name="setting">Settings</button>
                </a>
            </div>
            <a href="../logout.php">
                <button name="logout">Logout</button>
            </a>
        </div>
    </div>

    <div class="content">
        <div class="content_data">
            <div><p id="session-expire" style="display: none;">Session will expire in: <span id="timer"></span></p>
            </div>
            <div class="content_data-header">
                <div class="content_header-left">
                    <h1>Patient Search</h1>
                </div>

                <div class="userSettings">
                    <a href="view_diagnosis.php">
                        <button name="history">History</button>
                    </a>
                </div>
            </div>

            <div>
                <form action="" method="POST" class="new_diagnosis">
                    <label for="search_query">Search Patient:
                        <input type="text" name="search_query" placeholder="Enter Patient ID" required>
                    </label>
                    <div>
                        <button type="submit" name="search">Search</button>
                    </div>
                </form>
            </div>

            <div class="create_patient">
                <a style="color: inherit" href="createPatient.php"> Click here to create a new patient.</a>
            </div>

            <?php if ($patientData) : ?>
            <h2><u>Patient Records</u></h2>
            <div class="new_diagnosis">
                <div class="patient_details">
                    <div>
                        <h3>Patient ID:</h3>
                        <div><?php echo $patientData['patient_id']; ?></div>
                    </div>
                    <div>
                        <h3>Name:</h3>
                        <div><?php echo $patientData['patient_name']; ?></div>
                    </div>
                    <div>
                        <h3>Date of Birth:</h3>
                        <div><?php echo $patientData['date_of_birth']; ?></div>
                    </div>
                    <div>
                        <h3>Gender:</h3>
                        <div><?php echo $patientData['gender']; ?></div>
                    </div>
                    <div>
                        <h3>Contact Number:</h3>
                        <div><?php echo $patientData['contact_number']; ?></div>
                    </div>
                    <div>
                        <h3>Address:</h3>
                        <div><?php echo $patientData['address']; ?></div>
                    </div>

                    <div class="usePatient">
                        <button name="use" onclick="openDiagnosisPage()">Submit Data</button>
                    </div>
                </div>

            </div>

        </div>
        <?php endif; ?>
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
