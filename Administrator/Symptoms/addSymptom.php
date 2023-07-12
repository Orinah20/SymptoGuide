<?php

@include '../../config.php';
@include '../../session.php';

$symptomName = "";
$symptomDescription = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $symptomName = sanitizeInput($_POST['symptom_name']);
    $symptomDescription = sanitizeInput($_POST['symptom_description']);

    addSymptom($symptomName, $symptomDescription);
}

function addSymptom($symptomName, $symptomDescription)
{
    global $conn;

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO symptoms (symptom_name, symptom_description) VALUES (?, ?)");
    $stmt->bind_param("ss", $symptomName, $symptomDescription);

    // Execute the statement
    if ($stmt->execute()) {
        // Symptom added successfully
        header('Location: symptomdata.php');
        exit();
    } else {
        // Error occurred while adding symptom
        echo "Error adding symptom: " . $stmt->error;
    }

    $stmt->close();
}

function sanitizeInput($input)
{
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input);
    return $input;
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
                    <button class="nav-button <?php if ($activePage == 'patientdata' || $activePage == 'editPatient' || $activePage == 'addPatient' ) echo 'active'; ?>"
                            onclick="window.location.href='patientdata.php'">Patients
                    </button>
                </div>
                <div>
                    <button class="nav-button <?php if ($activePage == 'diseasedata' || $activePage == 'editDisease' || $activePage == 'addDisease') echo 'active'; ?>"
                            onclick="window.location.href='../Diseases/diseasedata.php'">Disease
                    </button>
                </div>
                <div>
                    <button class="nav-button <?php if ($activePage == 'symptomdata' || $activePage == 'editSymptom' || $activePage == 'addSymptom') echo 'active'; ?>"
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
                <div><b><?php echo($_SESSION['user_name']) ?></b></div>
            </div>
        </div>
        <div><p id="session-expire" style="display: none;">Session will expire in: <span id="timer"></span></p></div>
        <div class="content_data">
            <div class="content_data-header">
                <a href="symptomdata.php" class="back-button">
                    <img src="../../back-icon.png" alt="Back" height="30px" width="30px" class="back-icon">
                </a>
                <h3>Add Symptom</h3>
            </div>
            <div class="addSymptom_form">
                <form action="" method="POST" class="addSymptom">
                    <label for="symptom_name">Symptom Name:
                        <input type="text" name="symptom_name" placeholder="Symptom Name" required>
                    </label>

                    <label for="symptom_description">Symptom Description:
                        <input type="text" name="symptom_description" placeholder="Symptom Description" required>
                    </label>

                    <div>
                        <input type="submit" name="add" value="Add Symptom">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
