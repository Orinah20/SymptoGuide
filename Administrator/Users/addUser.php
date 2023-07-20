<?php

@include '../../config.php';
@include '../../session.php';

$userType = "";
$name = "";
$email = "";
$contactNumber = "";
$address = "";
$specialization = "";
$medicalId = "";
$password = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userType = sanitizeInput($_POST['user_type']);
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $contactNumber = sanitizeInput($_POST['contact_number']);
    $address = sanitizeInput($_POST['address']);
    $specialization = sanitizeInput($_POST['specialization']);

    addUser($userType, $name, $email, $contactNumber, $address, $specialization);
}

function addUser($userType, $name, $email, $contactNumber, $address, $specialization)
{
    global $conn, $medicalId, $password;

    // Generate a unique medical ID
    $medicalId = generateMedicalId($conn);

    // Generate a random password and hash it
    $password = generatePassword();
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Generate a security question and answer
    $securityQuestion = generateSecurityQuestion();
    $securityAnswer = generateSecurityAnswer();

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO users (medical_id, user_type, name, email, contact_number, address, specialization, password, security_question, security_answer) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssss", $medicalId, $userType, $name, $email, $contactNumber, $address, $specialization, $hashedPassword, $securityQuestion, $securityAnswer);

    // Execute the statement
    if ($stmt->execute()) {
        // Users added successfully
    } else {
        // Error occurred while adding user
        echo "Error adding user: " . $stmt->error;
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

function generateMedicalId($conn)
{
    $selectQuery = "SELECT MAX(CAST(SUBSTRING(medical_id, 4) AS UNSIGNED)) AS max_id FROM users";
    $result = $conn->query($selectQuery);
    $row = $result->fetch_assoc();
    $lastId = $row['max_id'];
    $nextId = sprintf('%04d', $lastId + 1);
    $medicalId = 'MED' . $nextId;
    return $medicalId;
}

function generatePassword()
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $password = '';
    for ($i = 0; $i < 8; $i++) {
        $randomIndex = mt_rand(0, strlen($characters) - 1);
        $password .= $characters[$randomIndex];
    }
    return $password;
}

function generateSecurityQuestion()
{
    $securityQuestions = array(
        "What is your mother's maiden name?",
        "What is your pet's name?",
        "What was the name of your first school?",
    );
    $randomIndex = mt_rand(0, count($securityQuestions) - 1);
    return $securityQuestions[$randomIndex];
}

function generateSecurityAnswer()
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $answer = '';
    for ($i = 0; $i < 6; $i++) {
        $randomIndex = mt_rand(0, strlen($characters) - 1);
        $answer .= $characters[$randomIndex];
    }
    return $answer;
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
            <button class="nav-button" name="logout" onclick="window.location.href='../logout.php'">Logout</button>
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
            <div class="content_data-header">
                <a href="userdata.php" class="back-button">
                    <img src="../../back-icon.png" alt="Back" height="30px" width="30px" class="back-icon">
                </a>
                <h3>Add User</h3>
            </div>
            <div class="addUser_form">
                <form action="addUser.php" method="POST" class="addUser">
                    <!-- Form fields -->
                    <label for="user_type">User Type:
                        <select name="user_type" required>
                            <option value="" selected disabled>Select user type</option>
                            <option value="administrator">Administrator</option>
                            <option value="user">User</option>
                        </select>
                    </label>

                    <label for="name">Name:
                        <input type="text" name="name" placeholder="Name" required>
                    </label>
                    <label for="email">Email:
                        <input type="email" name="email" placeholder="Email" required>
                    </label>
                    <label for="contact_number">Contact Number:
                        <input type="text" name="contact_number" placeholder="Contact Number" required>
                    </label>
                    <label for="address">Address:
                        <input type="text" name="address" placeholder="Address" required>
                    </label>
                    <label for="specialization">Specialization:
                        <select name="specialization" required>
                            <option value="" selected disabled>Select specialization</option>
                            <option value="Pulmonology">Pulmonology - Specializes in respiratory diseases</option>
                        </select>
                    </label>
                    <div>
                        <input type="submit" name="add" value="Add">
                    </div>
                </form>

                <!-- Details displayed after the form -->
                <div class="form-details">
                   <h3><u>Generated Details appear here</u></h3>
                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        echo "<p>Generated Medical ID: <b> $medicalId </b></p>";
                        echo "<p>Generated Password: <b> $password </b></p>";

                        // Generate a CSV file with user details for download
                        $filename = "user_details.csv";
                        $file = fopen($filename, "w");
                        $csvData = array(
                            $medicalId,
                            $password,
                        );
                        fputcsv($file, $csvData);
                        fclose($file);

                        // Display the download link for the CSV file
                        echo "<p><a href='$filename' download>Download Generated Medical ID and Password</a></p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
