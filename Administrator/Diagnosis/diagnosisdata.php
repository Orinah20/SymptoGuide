<?php
// Including necessary files
@include '../../config.php';
@include '../../session.php';

// Function to fetch grouped diagnosis data
function getGroupedDiagnosisData()
{
    global $conn;
    $stmt = $conn->prepare("SELECT dp.patient_id, p.patient_name, GROUP_CONCAT(CONCAT(d.disease_name, ' (', dp.probability, '%)')) AS diagnoses, dp.date_created
                           FROM disease_probability dp
                           INNER JOIN diseases d ON dp.disease_id = d.disease_id
                           INNER JOIN patients p ON dp.patient_id = p.patient_id
                           GROUP BY dp.patient_id, dp.date_created");
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

$diagnosisData = getGroupedDiagnosisData();
?>

<!DOCTYPE html>
<html lang="">
<head>
    <title>Admin Page</title>
    <link rel="stylesheet" type="text/css" href="../../styles.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="../../script.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
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
            <button class="nav-button <?php if ($activePage == 'userdata') echo 'active'; ?>"
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
            <button class="nav-button <?php if ($activePage == 'diagnosisdata') echo 'active'; ?>"
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
                <div class="content-data_user--header">
                    <h2>Diagnosis Data</h2>
                </div>
                <table id="DiagnosisData">
                    <thead>
                    <tr>
                        <th>Patient ID</th>
                        <th>Patient Name</th>
                        <th>Diagnoses</th>
                        <th>Date Created</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($diagnosisData as $data) { ?>
                        <tr onclick="window.location.href='viewDiagnosisData.php?patient_id=<?php echo $data['patient_id']; ?>&date_created=<?php echo urlencode($data['date_created']); ?>';">
                            <td><?php echo $data['patient_id']; ?></td>
                            <td><?php echo $data['patient_name']; ?></td>
                            <td><?php echo $data['diagnoses']; ?></td>
                            <td><?php echo $data['date_created']; ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    // Initialize DataTable
    $(document).ready(function () {
        $('#DiagnosisData').DataTable();
    });
</script>
</body>
</html>
