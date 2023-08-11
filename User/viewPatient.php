<?php

require_once '../config.php';
require_once '../session.php';

// Check if the patientId parameter is provided in the URL
if (isset($_GET['patientId'])) {
    // Redirect to some error page if patientId is not provided
    header('Location: error_page.php');
    exit;
}

// Function to fetch diagnosis data for the selected patient and date
function getDiagnosisData($patientId, $dateCreated)
{
    global $conn;
    $stmt = $conn->prepare("SELECT dp.patient_id, p.patient_name, GROUP_CONCAT(DISTINCT CONCAT(d.disease_name, ' (', dp.probability, '%)')) AS diagnoses, dp.date_created, 
                           GROUP_CONCAT(DISTINCT s2.symptom_name) AS patient_symptoms, 
                           GROUP_CONCAT(DISTINCT ps.medical_id) AS medical_ids, 
                           GROUP_CONCAT(DISTINCT u.name) AS medical_user_names
                           FROM disease_probability dp
                           INNER JOIN diseases d ON dp.disease_id = d.disease_id
                           INNER JOIN patients p ON dp.patient_id = p.patient_id
                           LEFT JOIN patient_symptoms ps ON dp.patient_id = ps.patient_id
                           LEFT JOIN symptoms s2 ON ps.symptom_id = s2.symptom_id
                           LEFT JOIN users u ON ps.medical_id = u.medical_id
                           WHERE dp.patient_id = ? AND dp.date_created = ?
                           GROUP BY dp.patient_id, dp.date_created");
    $stmt->bind_param("is", $patientId, $dateCreated);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}


// Check if patient_id and date_created are provided in the URL
if (isset($_GET['patient_id']) && isset($_GET['date_created'])) {
    $patientId = $_GET['patient_id'];
    $dateCreated = $_GET['date_created'];

    // Fetch diagnosis data for the selected patient and date
    $diagnosisData = getDiagnosisData($patientId, $dateCreated);
} else {
    // Redirect to diagnosisdata.php if patient_id or date_created is not provided
    header("Location: diagnosisdata.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Page</title>
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
                    <a href="view_diagnosis.php" class="back-button">
                        <img src="../back-icon.png" alt="Back" height="30px" width="30px" class="back-icon">
                    </a>
                    <h3>Patient View</h3>
                </div>
            </div>

            <div class="diagnosis-form">
                <form class="viewDiagnosis">
                    <label for="medical_id">Medical ID:
                        <input type="text" id="medical_id" name="medical_id"
                               value="<?php echo $diagnosisData['medical_ids']; ?>" readonly>
                    </label>

                    <label for="medical_userName">Medical Username:
                        <input type="text" id="medical_userName" name="medical_userName"
                               value="<?php echo $diagnosisData['medical_user_names']; ?>" readonly>
                    </label>

                    <label for="patient_id">Patient ID:
                        <input type="text" id="patient_id" name="patient_id"
                               value="<?php echo $diagnosisData['patient_id']; ?>" readonly>
                    </label>
                    <label for="patient_name">Patient Name:
                        <input type="text" id="patient_name" name="patient_name"
                               value="<?php echo $diagnosisData['patient_name']; ?>" readonly>
                    </label>
                    <label for="diagnoses">Diagnoses:
                        <textarea id="diagnoses" name="patient_symptoms" readonly>
                                <?php echo $diagnosisData['diagnoses']; ?> </textarea>
                    </label>
                    <label for="date_created">Date Created:
                        <input type="text" id="date_created" name="date_created"
                               value="<?php echo $diagnosisData['date_created']; ?>" readonly>
                    </label>

                    <label for="patient_symptoms">Patient Symptoms:
                        <textarea id="patient_symptoms" name="patient_symptoms" readonly>
                                <?php echo $diagnosisData['patient_symptoms']; ?></textarea>
                    </label>
                </form>
            </div>
        </div>
    </div>

</div>
</body>
</html>
