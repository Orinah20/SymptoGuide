<?php

global $conn, $activePage;
@include '../../config.php';
@include '../../session.php';

// Retrieve user data from the database
$selectQuery = "SELECT * FROM patients";
$result = mysqli_query($conn, $selectQuery);

?>

<!DOCTYPE html>
<html lang="">
<head>
    <title>Admin Page</title>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
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
        <div><p id="session-expire" style="display: none;">Session will expire in: <span id="timer"></span></p></div>
        <div class="content_data">
            <div class="content-data_user">
                <div class="content-data_user--header">
                    <h2>Patient Data</h2>
                    <div>
                        <a href="addPatient.php">
                            <button name="addPatient">Add Patient</button>
                        </a>
                    </div>
                </div>

                <div class="printButton">
                    <button name="print" onclick="printContent()">Print</button>
                </div>

                <table id="PatientData">
                    <thead>
                    <tr>
                        <th>Patient ID</th>
                        <th>Name</th>
                        <th>Date of Birth</th>
                        <th>Gender</th>
                        <th>Contact Number</th>
                        <th>Address</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<tr onclick="window.location.href=\'editPatient.php?patient_id=' . $row['patient_id'] . '\';">';
                        echo '<td>' . $row['patient_id'] . '</td>';
                        echo '<td>' . $row['patient_name'] . '</td>';
                        echo '<td>' . $row['date_of_birth'] . '</td>';
                        echo '<td>' . $row['gender'] . '</td>';
                        echo '<td>' . $row['contact_number'] . '</td>';
                        echo '<td>' . $row['address'] . '</td>';
                        echo '</tr>';
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function () {
        $('#PatientData').DataTable();
    });
</script>

</body>
</html>


