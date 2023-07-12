<?php
@include '../../config.php';
@include '../../session.php';

// Check if the symptom ID is provided in the URL
if (isset($_GET['symptom_id'])) {
    $symptomId = $_GET['symptom_id'];

    // Retrieve symptom data from the database
    $selectQuery = "SELECT * FROM Symptoms WHERE symptom_id = ?";
    $stmt = mysqli_prepare($conn, $selectQuery);
    mysqli_stmt_bind_param($stmt, "i", $symptomId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
    } else {
        // Redirect to the symptom data page if the symptom doesn't exist
        header('Location: symptomdata.php');
        exit();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize the form inputs
    $symptomName = sanitizeInput($_POST['symptom_name']);
    $symptomDescription = sanitizeInput($_POST['symptom_description']);

    // Update the symptom data in the database
    $updateQuery = "UPDATE Symptoms SET symptom_name = ?, symptom_description = ? WHERE symptom_id = ?";
    $stmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($stmt, "ssi", $symptomName, $symptomDescription, $symptomId);
    mysqli_stmt_execute($stmt);

    // Redirect back to the symptom data page after updating
    header('Location: symptomdata.php');
    exit();
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
                    <button class="nav-button <?php if ($activePage == 'patientdata' || $activePage == 'editPatient' || $activePage == 'addPatient') echo 'active'; ?>"
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
            <div class="content_data-container">
                <div class="content_data-container--header">
                    <a href="symptomdata.php" class="back-button">
                        <img src="../../back-icon.png" alt="Back" height="30px" width="30px" class="back-icon">
                    </a>
                    <h2>Edit Symptom</h2>
                </div>
                <div>
                    <form action="updateSymptom.php" method="POST" class="edit_symptom"
                          onsubmit="return confirm('Are you sure you want to continue?')">
                        <label for="symptom_id">Symptom ID:
                            <input type="text" name="symptom_id" value="<?php echo $row['symptom_id']; ?>" readonly>
                        </label>

                        <label for="symptom_name">Symptom Name:
                            <input type="text" name="symptom_name" value="<?php echo $row['symptom_name']; ?>">
                        </label>

                        <label for="symptom_description">Symptom Description:
                            <textarea name="symptom_description"><?php echo $row['symptom_description']; ?></textarea>
                        </label>

                        <div>
                            <input type="submit" name="update" value="Update" class="edit_symptom-update">
                        </div>
                    </form>

                    <form action="deleteSymptom.php" method="POST"
                          onsubmit="return confirm('Are you sure you want to delete this symptom?');">
                        <input type="hidden" name="symptom_id" value="<?php echo $row['symptom_id']; ?>">
                        <input type="submit" name="delete" value="Delete Symptom" class="edit_symptom-delete">
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

