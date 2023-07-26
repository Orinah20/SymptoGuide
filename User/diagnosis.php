<?php
@include '../config.php';
@include '../session.php';

// Check if the patient ID is provided in the query parameter
if (isset($_GET['patient_id'])) {
    $patientId = $_GET['patient_id'];

    // Retrieve patient information from the database
    $stmt = $conn->prepare("SELECT * FROM patients WHERE patient_id = ?");
    $stmt->bind_param("i", $patientId);
    $stmt->execute();
    $result = $stmt->get_result();
    $patient = $result->fetch_assoc();
    $stmt->close();

    // Retrieve respiratory symptoms from the database
    $symptomsQuery = "SELECT * FROM symptoms";
    $symptomsResult = mysqli_query($conn, $symptomsQuery);
}
?>

<!DOCTYPE html>
<html lang="">
<head>
    <title>Diagnosis</title>
    <link rel="stylesheet" type="text/css" href="../style.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="../script.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize the Select2 plugin
            $('#symptoms').select2();
        });
    </script>
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
    <div class="content_data-header">
        <a href="new_diagnosis.php" class="back-button">
            <img src="../back-icon.png" alt="Back" height="30px" width="30px" class="back-icon">
        </a>
        <h1>Diagnosis</h1>
    </div>
    <div class="patient-info">
        <h2>Patient Information</h2>
        <p><strong>Patient ID:</strong> <?php echo $_SESSION['patient_id']; ?></p>
        <p><strong>Name:</strong> <?php echo $_SESSION['patient_name']; ?></p>
    </div>
    <div class="questionnaire">
        <h2>Respiratory Symptoms</h2>
        <form action="process_diagnosis.php" method="POST">
            <input type="hidden" name="medical_id" value="<?php echo $_SESSION['user_id']; ?>">
            <input type="hidden" name="patient_id" value="<?php echo $_SESSION['patient_id']; ?>">

            <label for="symptoms">Select Symptoms:</label>
            <select name="symptoms[]" id="symptoms" multiple required>
                <?php
                // Retrieve symptoms from the database
                $symptomsQuery = "SELECT * FROM symptoms";
                $symptomsResult = mysqli_query($conn, $symptomsQuery);

                // Iterate over the symptoms and generate the options
                while ($symptom = mysqli_fetch_assoc($symptomsResult)) {
                    $symptomId = $symptom['symptom_id'];
                    $symptomName = $symptom['symptom_name'];

                    echo '<option value="' . $symptomId . '">' . $symptomName . '</option>';
                }
                ?>
            </select>

            <button type="submit">Submit</button>
        </form>

    </div>
</div>
</body>
</html>
