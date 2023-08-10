<?php

@include '../../config.php';
@include '../../session.php';

// Check if the user ID is provided in the URL
if (isset($_GET['patient_id'])) {
    $patientId = $_GET['patient_id'];

    // Retrieve user data from the database
    $selectQuery = "SELECT * FROM patients WHERE patient_id = ?";
    $stmt = mysqli_prepare($conn, $selectQuery);
    mysqli_stmt_bind_param($stmt, "s", $patientId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
    } else {
        // Redirect to the user data page if the user doesn't exist
        header('Location: patientdata.php');
        exit();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userType = $_POST['user_type'];

    // Update the user type in the database
    $updateQuery = "UPDATE users SET user_type = ? WHERE medical_id = ?";
    $stmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($stmt, "ss", $userType, $medicalId);
    mysqli_stmt_execute($stmt);

    // Redirect back to the user data page after updating
    header('Location: userdata.php');
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
            <button class="nav-button <?php if ($activePage == 'userdata') echo 'active'; ?>"
                    onclick="window.location.href='/SymptoGuide/Administrator/Users/userdata.php'">Users
            </button>
        </div>
        <div>
            <button class="nav-button <?php if ($activePage == 'patientdata' || $activePage == 'editPatient' || $activePage == 'addPatient') echo 'active'; ?>"
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
            <div class="content_data-container">
                <div class="content_data-container--header">
                    <a href="patientdata.php" class="back-button">
                        <img src="../../back-icon.png" alt="Back" height="30px" width="30px" class="back-icon">
                    </a>
                    <h2>Edit Patient</h2>
                </div>
                <form action="updatePatient.php" method="POST" class="editPatient"
                      onsubmit="return confirm('Are you sure you want to continue?')">
                    <label for="patient_id">Patient ID:
                        <input type="text" name="patient_id" value="<?php echo $row['patient_id']; ?>" readonly>
                    </label>

                    <label for="patient_name">Patient Name:
                        <input type="text" name="patient_name" value="<?php echo $row['patient_name']; ?>">
                    </label>

                    <label for="date_of_birth">Date of Birth:
                        <input type="date" name="date_of_birth" placeholder="Date of Birth"
                               value="<?php echo $row['date_of_birth']; ?>" required
                               max="<?php echo date('Y-m-d'); ?> ">
                    </label>

                    <label for="gender">Gender:
                        <select name="gender" required>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </label>

                    <label for="contact_number">Contact Number:
                        <input type="text" name="contact_number" value="<?php echo $row['contact_number']; ?>">
                    </label>

                    <label for="address">Address:
                        <input type="text" name="address" value="<?php echo $row['address']; ?>">
                    </label>

                    <label for="date_created">Date Created:
                        <input type="text" name="date_created" value="<?php echo $row['date_created']; ?>" readonly>
                    </label>

                    <div>
                        <input type="submit" name="update" value="Update" class="edit_user-update">
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

