<?php

@include '../../config.php';
@include '../../session.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reportType = $_POST['report_type'];

    // Generate and display the selected report type
    if ($reportType === 'users') {
        generateUserReport();
    } elseif ($reportType === 'patients') {
        generatePatientReport();
    } elseif ($reportType === 'diseases') {
        generateDiseaseReport();
    }
}

// Function to generate and display the user report
function generateUserReport()
{
    global $conn;

    $userQuery = "SELECT * FROM users";
    $userResult = mysqli_query($conn, $userQuery);

    echo "<h3>User Reports</h3>";
    displayReports($userResult);
}

// Function to generate and display the patient report
function generatePatientReport()
{
    global $conn;

    $criteria = isset($_POST['criteria']) ? $_POST['criteria'] : null; // Get the selected criteria

    $patientQuery = "SELECT * FROM patients";
    if ($criteria !== null) {
        $patientQuery .= " WHERE $criteria";
    }

    $patientResult = mysqli_query($conn, $patientQuery);

    echo "<h3>Patient Reports</h3>";
    displayReports($patientResult);
}

// Function to generate and display the disease report
function generateDiseaseReport()
{
    global $conn;

    $diseaseQuery = "SELECT * FROM diseases";
    $diseaseResult = mysqli_query($conn, $diseaseQuery);

    echo "<h3>Disease Reports</h3>";
    displayReports($diseaseResult);
}

// Function to display reports in a table
function displayReports($result)
{
    if (mysqli_num_rows($result) > 0) {
        echo "<table>";
        $row = mysqli_fetch_assoc($result);
        $header = array_keys($row);
        echo "<tr>";
        foreach ($header as $key) {
            echo "<th>$key</th>";
        }
        echo "</tr>";
        mysqli_data_seek($result, 0);
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>$value</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No data found.";
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
                    <button class="nav-button <?php if ($activePage == 'patientdata') echo 'active'; ?>"
                            onclick="window.location.href='../Patient/patientdata.php'">Patients
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
                            onclick="window.location.href='reports.php'">Reports
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
        <div><p id```
            <div class="content_data">
                <h2>Generate Reports</h2>
                <form action="" method="POST" class="report-form">
                    <label for="report_type">Report Type:</label>
                    <select name="report_type" id="report_type" onchange="showCriteria()">
                        <option value="">Select Report Type</option>
                        <option value="users">Users</option>
                        <option value="patients">Patients</option>
                        <option value="diseases">Diseases</option>
                    </select>
                    <div id="criteria_section" style="display: none;">
                        <label for="criteria">Criteria:</label>
                        <select name="criteria" id="criteria">
                            <option value="">Select Criteria</option>
                            <?php if ($reportType === 'patients') : ?>
                                <option value="age > 30">Age > 30</option>
                                <option value="gender = 'Male'">Male</option>
                                <option value="gender = 'Female'">Female</option>
                                <option value="status = 'Active'">Active Patients</option>
                                <option value="status = 'Inactive'">Inactive Patients</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <input type="submit" value="Generate Report">
                </form>
            </div>
        </div>
    </div>
</body>
</html>
