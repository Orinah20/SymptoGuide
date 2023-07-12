<?php

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
        <div class="side_nav">
            <div class="side_nav-data">
                <h2>SymptoGuide</h2>
                <div>
                    <button class="nav-button <?php if ($activePage == 'dashboard') echo 'active'; ?>"
                            onclick="window.location.href='../administrator.php'">
                        Dashboard
                    </button>
                </div>
                <div>
                    <button class="nav-button <?php if ($activePage == 'userdata' || $activePage == 'editUser' || $activePage == 'addUser') echo 'active'; ?>"
                            onclick="window.location.href='../Users/userdata.php'">Users
                    </button>
                </div>
                <div>
                    <button class="nav-button <?php if ($activePage == 'patientdata' || $activePage == 'editPatient' || $activePage == 'addPatient' ) echo 'active'; ?>"
                            onclick="window.location.href='patientdata.php'">Patients
                    </button>
                </div>
                <div>
                    <button class="nav-button <?php if ($activePage == 'diseasedata') echo 'active'; ?>"
                            onclick="window.location.href='../Diseases/diseasedata.php'">Disease
                    </button>
                </div>
                <div>
                    <button class="nav-button <?php if ($activePage == 'symptomdata') echo 'active'; ?>"
                            onclick="window.location.href='../Symptoms/symptomdata.php'">Symptom
                    </button>
                </div>
                <div>
                    <button class="nav-button <?php if ($activePage == 'reports') echo 'active'; ?>"
                            onclick="window.location.href='../Reports/reports.php'">Reports
                    </button>
                </div>
                <div>
                    <button class="nav-button <?php if ($activePage == 'adminSettings') echo 'active'; ?>"
                            onclick="window.location.href='../adminSettings.php'">Settings
                    </button>
                </div>
                <div>
                    <button class="nav-button" onclick="window.location.href='../../logout.php'">Logout</button>
                </div>
            </div>
        </div>

    </div>
    <div class="content">
        <div class="content_user">
            <div><b>Administrator</b></div>
            <div class="content_user-left">
                <div><a href="../adminSettings.php">Settings</a></div>
                <div><b><?php echo($_SESSION['user_name']) ?></b></div>
            </div>
        </div>
        <div><p id="session-expire" style="display: none;">Session will expire in: <span id="timer"></span></p></div>
        <div class="content_data">
            <div class="content-data_user">
                <div class="content-data_user--header">
                    <h2>Patient Data</h2>
                    <div>
                        <a href="addPatient.php">
                            <button>Add Patient</button>
                        </a>
                    </div>
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
                        echo '<td>' . $row['name'] . '</td>';
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
    <script src="../../search.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#PatientData').DataTable();
        });
    </script>

</body>
</html>


