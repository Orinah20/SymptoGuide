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

    // Set patient information in session
    $_SESSION['patient_id'] = $patient['patient_id'];
    $_SESSION['patient_name'] = $patient['patient_name'];
    $_SESSION['date_of_birth'] = $patient['date_of_birth'];
    $_SESSION['gender'] = $patient['gender'];
    $_SESSION['contact_number'] = $patient['contact_number'];
    $_SESSION['address'] = $patient['address'];

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
    <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="../script.js"></script>
    <script>
        $(document).ready(function () {
            // Initialize the Select2 plugin
            $('#symptoms').select2();
        });
    </script>
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
            <div class="userSettings"></div>
            <a href="../logout.php">
                <button name="logout">Logout</button>
            </a>
        </div>
    </div>

    <div class="content">
        <div><p id="session-expire" style="display: none;">Session will expire in: <span id="timer"></span></p></div>
        <div class="content_data">
            <div class="content_data-header">
                <div class="content_header-left">
                    <a href="user.php" class="back-button">
                        <img src="../back-icon.png" alt="Back" height="30px" width="30px" class="back-icon">
                    </a>
                    <h2>Diagnosis</h2>
                </div>
            </div>
            <h2>Patient Information</h2>

            <div class="new_diagnosis">
                <div class="patient_details">
                    <div>
                        <h3>Patient ID:</h3>
                        <div><?php echo $_SESSION['patient_id']; ?></div>
                    </div>

                    <div>
                        <h3>Name</h3>
                        <div><?php echo $_SESSION['patient_name']; ?></div>
                    </div>

                    <div>
                        <h3>Date of Birth:</h3>
                        <div><?php echo $_SESSION['date_of_birth']; ?></div>
                    </div>

                    <div>
                        <h3>Gender</h3>
                        <div><?php echo $_SESSION['gender']; ?></div>
                    </div>
                    <div>
                        <h3>Contact Number:</h3>
                        <div><?php echo $_SESSION['contact_number']; ?></div>
                    </div>

                    <div>
                        <h3>Address</h3>
                        <div><?php echo $_SESSION['address']; ?></div>
                    </div>


                </div>

            </div>
            <div class="questionnaire">
                <h2>Symptoms</h2>
                <form action="process_diagnosis.php" method="POST" class="new_diagnosis">
                    <input type="hidden" name="medical_id" value="<?php echo $_SESSION['user_id']; ?>">
                    <input type="hidden" name="patient_id" value="<?php echo $_SESSION['patient_id']; ?>">

                    <label for="symptoms">Select Symptoms:
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
                    </label>

                    <div>
                        <button name="submit" type="submit">Submit</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
</body>
</html>
