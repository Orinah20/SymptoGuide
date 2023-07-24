<?php
@include '../config.php';
@include '../session.php';

// Retrieve the diagnosis data with distinct patient_id and report_date
$stmt = $conn->prepare("SELECT dp.patient_id, p.patient_name, GROUP_CONCAT(CONCAT(d.disease_name, ' (', dp.probability, '%)')) AS diagnoses, dp.date_created
                        FROM disease_probability dp
                        INNER JOIN diseases d ON dp.disease_id = d.disease_id
                        INNER JOIN patients p ON dp.patient_id = p.patient_id
                        GROUP BY dp.patient_id, dp.date_created");
$stmt->execute();
$result = $stmt->get_result();

// Fetch the data into an associative array
$diagnosisData = $result->fetch_all(MYSQLI_ASSOC);

// Close the statement
$stmt->close();
?>

<!DOCTYPE html>
<html lang="">
<head>
    <title>View Diagnosis</title>
    <link rel="stylesheet" type="text/css" href="../style.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <script src="../script.js"></script>
    <style>
        #diagnosisTable tbody tr:hover {
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header_position">
        <div class="container_header">
            <h2>SymptoGuide</h2>
            <div class="content_header-left">
                <div><?php echo $_SESSION['user_name']; ?></div>
                <a href="../logout.php">Logout</a>
            </div>
        </div>
    </div>

    <div class="content">
        <div><p id="session-expire" style="display: none;">Session will expire in: <span id="timer"></span></p></div>
        <div class="content_data">
            <div class="content_data-header">
                <a href="user.php" class="back-button">
                    <img src="../back-icon.png" alt="Back" height="30px" width="30px" class="back-icon">
                </a>
                <h3>View Diagnosis</h3>
            </div>
            <div class="diagnosis-table">
                <table id="diagnosisTable">
                    <thead>
                    <tr>
                        <th>Patient ID</th>
                        <th>Patient Name</th>
                        <th>Diagnoses</th>
                        <th>Report Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($diagnosisData as $data) : ?>
                        <tr>
                            <td><?php echo $data['patient_id']; ?></td>
                            <td><?php echo $data['patient_name']; ?></td>
                            <td><?php echo $data['diagnoses']; ?></td>
                            <td><?php echo date('F j, Y, g:i A', strtotime($data['date_created'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function () {
        $('#diagnosisTable').DataTable();
    });
</script>

</body>
</html>



<?php
require_once '../../config.php';
require_once '../../session.php';

// Function to fetch patient data from the disease_probability table using patient_id
function searchPatientByPatientId($patientId)
{
    global $conn;
    $stmt = $conn->prepare("SELECT dp.patient_id, p.patient_name, GROUP_CONCAT(DISTINCT CONCAT(d.disease_name, ' (', dp.probability, '%)')) AS diagnoses, dp.date_created
                           FROM disease_probability dp
                           INNER JOIN patients p ON dp.patient_id = p.patient_id
                           INNER JOIN diseases d ON dp.disease_id = d.disease_id
                           WHERE dp.patient_id = ?
                           GROUP BY dp.patient_id, dp.date_created");
    $stmt->bind_param("s", $patientId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $searchPatientId = $_POST['search_patient_id'];

    // Search for patient data using patient_id
    $patientData = searchPatientByPatientId($searchPatientId);
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
        <div class="content">
            <div class="content_data">
                <h2>Search Patients by Patient ID</h2>
                <form action="" method="POST" class="search-form">
                    <label for="search_patient_id">Enter Patient ID:</label>
                    <input type="text" name="search_patient_id" id="search_patient_id" required>
                    <input type="submit" value="Search">
                </form>

                <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($patientData)) : ?>
                    <h3>Search Results</h3>
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
                        <?php foreach ($patientData as $data) : ?>
                            <tr>
                                <td><?php echo $data['patient_id']; ?></td>
                                <td><?php echo $data['patient_name']; ?></td>
                                <td><?php echo $data['diagnoses']; ?></td>
                                <td><?php echo $data['date_created']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($patientData)) : ?>
                    <p>No results found for the given Patient ID.</p>
                <?php endif; ?>
            </div>
        </div>    </div>
</div>
</body>
</html>

