<?php

@include '../../config.php';
@include '../../session.php';

// Retrieve user data from the database
$selectQuery = "SELECT * FROM diseases";
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
                <button class="nav-button" name="logout" onclick="window.location.href='../logout.php'">Logout</button>
            </div>

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
                    <h2>Disease Data</h2>
                    <div>
                        <a href="addDisease.php">
                            <button name="addDisease">Add Disease</button>
                        </a>
                    </div>
                </div>
                <table id="DiseaseData">
                    <thead>
                    <tr>
                        <th>Disease ID</th>
                        <th>Disease Name</th>
                        <th>Disease Description</th>
                        <th>Symptom Count</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<tr onclick="window.location.href=\'editDisease.php?disease_id=' . $row['disease_id'] . '\';">';
                        echo '<td>' . $row['disease_id'] . '</td>';
                        echo '<td>' . $row['disease_name'] . '</td>';
                        echo '<td>' . $row['disease_description'] . '</td>';
                        echo '<td>' . $row['symptom_count'] . '</td>';
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
        $('#DiseaseData').DataTable();
    });
</script>

</body>
</html>


