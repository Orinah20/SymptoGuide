<?php

@include '../../config.php';
@include '../../session.php';

$name = "";
$dateOfBirth = "";
$gender = "";
$contactNumber = "";
$address = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name']);
    $dateOfBirth = sanitizeInput($_POST['date_of_birth']);
    $gender = sanitizeInput($_POST['gender']);
    $contactNumber = sanitizeInput($_POST['contact_number']);
    $address = sanitizeInput($_POST['address']);

    addPatient($name, $dateOfBirth, $gender, $contactNumber, $address);
}

function addPatient($name, $dateOfBirth, $gender, $contactNumber, $address): void
{
    global $conn;

    // Generate a unique patient ID
    $patientId = generatePatientId($conn);

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO patients (patient_id, name, date_of_birth, gender, contact_number, address) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $patientId, $name, $dateOfBirth, $gender, $contactNumber, $address);

    // Execute the statement
    if ($stmt->execute()) {
        // Patient added successfully
        header('Location: patientdata.php');
        exit();
    } else {
        // Error occurred while adding patient
        echo "Error adding patient: " . $stmt->error;
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

function generatePatientId($conn)
{
    $selectQuery = "SELECT MAX(CAST(SUBSTRING(patient_id, 4) AS UNSIGNED)) AS max_id FROM patients";
    $result = $conn->query($selectQuery);
    $row = $result->fetch_assoc();
    $lastId = $row['max_id'];
    $nextId = sprintf('%04d', $lastId + 1);
    $patientId = 'PAT' . $nextId;
    return $patientId;
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
                <div><b><?php echo($_SESSION['user_name']) ?></b></div>
            </div>
        </div>
        <div><p id="session-expire" style="display: none;">Session will expire in: <span id="timer"></span></p></div>
        <div class="content_data">
            <div class="content_data-header">
                <a href="patientdata.php" class="back-button">
                    <img src="../../back-icon.png" alt="Back" height="30px" width="30px" class="back-icon">
                </a>
                <h3>Add Patient</h3>
            </div>
            <div class="addUser_form">
                <form action="" method="POST" class="addUser">
                    <label for="name">Name:
                        <input type="text" name="name" placeholder="Name" required>
                    </label>
                    <label for="dob">Date of Birth:
                        <input type="date" name="date_of_birth" placeholder="Date of Birth" required>
                    </label>
                    <label for="gender">Gender:
                        <select name="gender" required>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </label>
                    <label for="contact">Contact:
                        <input type="text" name="contact_number" placeholder="contact" required>
                    </label>
                    <label for="address">Address:
                        <input type="text" name="address" placeholder="Address" required>
                    </label>

                    <div>
                        <input type="submit" name="add" value="Add">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
