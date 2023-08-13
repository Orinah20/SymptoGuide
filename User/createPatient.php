<?php
require_once '../config.php';
require_once '../session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    // Retrieve form data
    $patientName = $_POST['patient_name'];
    $dateOfBirth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $contactNumber = $_POST['contact_number'];
    $address = $_POST['address'];

    // Generate the next patient_id
    $patientId = generatePatientId();

    // Prepare the SQL statement to insert data into the patients table
    $stmt = $conn->prepare("INSERT INTO patients (patient_id, patient_name, date_of_birth, gender, contact_number, address) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $patientId, $patientName, $dateOfBirth, $gender, $contactNumber, $address);


    // Execute the query
    if ($stmt->execute()) {
        // Patient added successfully
        $alertMessage = "Patient added successfully. Patient ID: " . $patientId;
        echo "<script>alert('$alertMessage');</script>";
        $stmt->close();

        header('Location: diagnosis.php');
        exit();
    } else {
        // Failed to add patient
        $stmt->close();
        echo '<script>alert("Failed to add patient. Please try again.");</script>';
    }

}

// Function to generate the next patient_id
function generatePatientId()
{
    global $conn;

    // Get the last patient_id from the patients table
    $stmt = $conn->prepare("SELECT patient_id FROM patients ORDER BY patient_id DESC LIMIT 1");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Generate the next patient_id
    $lastPatientId = $row ? $row['patient_id'] : 'PAT0000';
    $nextPatientNumber = intval(substr($lastPatientId, 3)) + 1;
    $nextPatientId = 'PAT' . sprintf('%04d', $nextPatientNumber);

    // Close the statement
    $stmt->close();

    return $nextPatientId;
}

?>

<!DOCTYPE html>
<html lang="">
<head>
    <title>Admin Page</title>
    <link rel="stylesheet" type="text/css" href="../style.css">
    <script src="../script.js"></script>
</head>
<body>
<div class="container">
    <div class="container_header">
        <div style="cursor: pointer; ">
            <h2>
                <a style="text-decoration: none; color: inherit" href="user.php">SymptoGuide</a>
            </h2>
        </div>
        <div class="content_header-left">
            <h3><?php echo $_SESSION['user_name']; ?></h3>
            <div class="userSettings"></div>
            <a href="../logout.php">
                <button name="logout">Logout</button>
            </a>
        </div>
    </div>

    <div class="content">
        <div><p id="session-expire" style="display: none;">Session will expire in: <span id="timer"></span></p></div>
        <div class="content_data">
            <div class="content_data-header">
                <div class="content_header-left">
                    <a href="user.php" class="back-button">
                        <img src="../back-icon.png" alt="Back" height="30px" width="30px" class="back-icon">
                    </a>
                    <h2>Add Patient</h2>
                </div>
            </div>
            <div class="addUser_form">
                <form action="" method="POST" class="addUser">
                    <label for="patient_name">Name:
                        <input type="text" name="patient_name" placeholder="Patient Name" required>
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
                        <input type="text" name="contact_number" placeholder="Contact" required>
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
