<?php
require_once '../../config.php';
require_once '../../session.php';

// Function to fetch patient data from the disease_probability table using patient_id
function searchPatientByPatientId($patientId): array
{
    global $conn;
    $stmt = $conn->prepare("SELECT p.*, ps.medical_id, u.name AS medical_name, GROUP_CONCAT(DISTINCT CONCAT(d.disease_name, ' (', dp.probability, '%)')) AS diagnoses, dp.date_created
                           FROM disease_probability dp
                           INNER JOIN patients p ON dp.patient_id = p.patient_id
                           INNER JOIN diseases d ON dp.disease_id = d.disease_id
                           INNER JOIN patient_symptoms ps ON dp.patient_id = ps.patient_id
                           INNER JOIN users u ON ps.medical_id = u.medical_id
                           WHERE dp.patient_id = ?
                           GROUP BY dp.patient_id, dp.date_created");
    $stmt->bind_param("s", $patientId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to fetch medical user data from the users table using medical_id
function searchMedicalUserByMedicalId($medicalId): array
{
    global $conn;
    $stmt = $conn->prepare("SELECT u.*, dp.patient_id, p.patient_name, GROUP_CONCAT(DISTINCT CONCAT(d.disease_name, ' (', dp.probability, '%)')) AS diagnoses, dp.date_created, 
                           GROUP_CONCAT(DISTINCT s2.symptom_name) AS patient_symptoms
                           FROM users u
                           LEFT JOIN patient_symptoms ps ON u.medical_id = ps.medical_id
                           LEFT JOIN disease_probability dp ON ps.patient_id = dp.patient_id
                           LEFT JOIN diseases d ON dp.disease_id = d.disease_id
                           LEFT JOIN patients p ON dp.patient_id = p.patient_id
                           LEFT JOIN symptoms s2 ON ps.symptom_id = s2.symptom_id
                           WHERE u.medical_id = ?
                           GROUP BY u.medical_id, dp.patient_id, dp.date_created");
    $stmt->bind_param("s", $medicalId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $searchId = $_POST['search_id'];

    // Search for patient data using patient_id
    $patientData = searchPatientByPatientId($searchId);

    // If data is not found in patient data, search for medical user data using medical_id
    if (empty($patientData)) {
        $medicalUserData = searchMedicalUserByMedicalId($searchId);
    }
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
            <button class="nav-button <?php if ($activePage == 'diagnosis') echo 'active'; ?>"
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
            <button class="nav-button" name="logout" onclick="window.location.href='/SymptoGuide/logout.php'">Logout
            </button>
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
        <div><p id="session-expire">Session will expire in: <span id="timer"></span></p></div>
        <div class="content-data_user">
            <div class="search">
                <h2>Patients/Medical User Search</h2>
                <form action="" method="POST" class="search-form">
                    <label for="search_id"> Enter Patient ID or Medical ID:
                        <input type="text" name="search_id" id="search_id" required>
                    </label>

                    <div>
                        <input type="submit" name="search" value="Search">
                    </div>
                </form>
            </div>

            <?php if ($_SERVER['REQUEST_METHOD'] === 'POST') : ?>
                <?php if (!empty($patientData)) : ?>
                    <div class="report">
                        <div class="report_header">
                            <h3>Patient Details</h3>
                            <button name="print" onclick="printContent()">Print</button>
                        </div>
                        <form class="medical-user-details-form">
                            <label for="patient_id">Patient ID:
                            <input type="text" id="patient_id" name="patient_id"
                                   value="<?php echo $patientData[0]['patient_id']; ?>" readonly>
                            </label>
                            <label for="patient_name">Patient Name:
                            <input type="text" id="patient_name" name="patient_name"
                                   value="<?php echo $patientData[0]['patient_name']; ?>" readonly>
                            </label>
                            <label for="patient_gender">Gender:
                            <input type="text" id="patient_gender" name="patient_gender"
                                   value="<?php echo $patientData[0]['gender']; ?>" readonly>
                            </label>
                        </form>

                        <h3>Diagnosis Results</h3>
                        <table>
                            <thead>
                            <tr>
                                <th>Medical Id</th>
                                <th>Medical Name</th>
                                <th>Diagnoses</th>
                                <th>Date Created</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($patientData as $data) : ?>
                                <tr>
                                    <td><?php echo $data['medical_id']; ?></td>
                                    <td><?php echo $data['medical_name']; ?></td>
                                    <td><?php echo $data['diagnoses']; ?></td>
                                    <td><?php echo $data['date_created']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php elseif (!empty($medicalUserData)) : ?>
                    <div class="report">
                        <div class="report_header">
                            <h3>Medical User Details</h3>
                            <button name="print" onclick="printContent()">Print</button>
                        </div>
                        <form class="medical-user-details-form">
                            <label for="medical_id">Medical ID:
                                <input type="text" id="medical_id" name="medical_id"
                                       value="<?php echo $medicalUserData[0]['medical_id']; ?>" readonly>
                            </label>
                            <label for="medical_name">Medical Username:
                                <input type="text" id="medical_name" name="medical_name"
                                       value="<?php echo $medicalUserData[0]['name']; ?>" readonly>
                            </label>
                            <label for="medical_certificate">Medical Certificate:
                                <input type="text" id="medical_certificate" name="medical_certificate"
                                       value="<?php echo $medicalUserData[0]['medical_certificate']; ?>" readonly>
                            </label>
                        </form>

                        <h3>Diagnosis Results</h3>
                        <table>
                            <thead>
                            <tr>
                                <th>Patient ID</th>
                                <th>Patient Name</th>
                                <th>Diagnoses</th>
                                <th>Date Created</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($medicalUserData as $data) : ?>
                                <tr>
                                    <td><?php echo $data['patient_id']; ?></td>
                                    <td><?php echo $data['patient_name']; ?></td>
                                    <td><?php echo $data['diagnoses']; ?></td>
                                    <td><?php echo $data['date_created']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else : ?>
                    <p>No results found for the given ID.</p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
