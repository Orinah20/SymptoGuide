<?php
// Including necessary files
require_once '../../config.php';
require_once '../../session.php';

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
<html lang="">
<head>
    <title>Admin Page</title>
    <link rel="stylesheet" type="text/css" href="../../styles.css">
    <script src="../../script.js"></script>
</head>
<body>
<div class="container">
    <div class="side_nav">
        <h2>SymptoGuide</h2>
        <div>
            <button class="nav-button <?php if ($activePage == 'dashboard') echo 'active'; ?>"
                    onclick="window.location.href='/SymptoGuide/Administrator/administrator.php' ">Dashboard
            </button>
        </div>
        <div>
            <button class="nav-button <?php if ($activePage == 'userdata' || $activePage == 'editUser' || $activePage == 'addUser') echo 'active'; ?>"
                    onclick="window.location.href='/SymptoGuide/Administrator/Users/userdata.php'">Users
            </button>
        </div>
        <div>
            <button class="nav-button <?php if ($activePage == 'patientdata') echo 'active'; ?>"
                    onclick="window.location.href='/SymptoGuide/Administrator/Patient/patientdata.php'">Patients
            </button>
        </div>
        <div>
            <button class="nav-button <?php if ($activePage == 'diseasedata') echo 'active'; ?>"
                    onclick="window.location.href='/SymptoGuide/Administrator/Diseases/diseasedata.php'">Disease
            </button>
        </div>
        <div>
            <button class="nav-button <?php if ($activePage == 'symptomdata') echo 'active'; ?>"
                    onclick="window.location.href='/SymptoGuide/Administrator/Symptoms/symptomdata.php'">Symptom
            </button>
        </div>

        <div>
            <button class="nav-button <?php if ($activePage == 'diagnosisdata' || $activePage == 'viewDiagnosisData') echo 'active'; ?>"
                    onclick="window.location.href='/SymptoGuide/Administrator/Diagnosis/diagnosisdata.php'">Diagnosis
            </button>
        </div>

        <div>
            <button class="nav-button <?php if ($activePage == 'reports') echo 'active'; ?>"
                    onclick="window.location.href='/SymptoGuide/Administrator/Reports/reports.php'">Reports
            </button>
        </div>

        <div>
            <button class="nav-button <?php if ($activePage == 'adminSettings') echo 'active'; ?>"
                    onclick="window.location.href='/SymptoGuide/Administrator/adminSettings.php'">Settings
            </button>
        </div>
        <div>
            <button class="nav-button" name="logout" onclick="window.location.href='/SymptoGuide/logout.php'">Logout</button>
        </div>

    </div>
    <div class="content">
        <div class="content_user">
            <div>
                <div><b>Administrator</b></div>
                <div class="content_user-left">
                    <div><b><?php echo($_SESSION['user_name']) ?></b></div>
                </div>
            </div>
        </div>
        <div><p id="session-expire" style="display: none;">Session will expire in: <span id="timer"></span></p></div>

        <div class="content_data">
            <div class="content-data_user">
                <div class="content_data-container--header">
                    <a href="diagnosisdata.php" class="back-button">
                        <img src="../../back-icon.png" alt="Back" height="30px" width="30px" class="back-icon">
                    </a>
                    <h2>Diagnosis View</h2>
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
                            <input type="text" id="diagnoses" name="diagnoses"
                                   value="<?php echo $diagnosisData['diagnoses']; ?>" readonly>
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
</div>
</body>
</html>
