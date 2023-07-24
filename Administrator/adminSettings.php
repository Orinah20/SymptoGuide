<?php
// Include config.php and session.php at the beginning
include '../config.php';
include '../session.php';

// Function to retrieve admin data from the database
function getAdminData($medicalId)
{
    global $conn;

    $stmt = $conn->prepare("SELECT * FROM users WHERE medical_id = ?");
    $stmt->bind_param("i", $medicalId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch the admin data
    $adminData = $result->fetch_assoc();

    // Close the statement, but don't close the connection here
    $stmt->close();

    // Return the admin data
    return $adminData;
}

// Function to update admin data in the database
function updateAdminData($medicalId, $name, $email, $contactNumber, $specialization, $gender)
{
    global $conn;
    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, contact_number = ?, specialization = ?, gender = ? WHERE medical_id = ?");
    $stmt->bind_param("ssssss", $name, $email, $contactNumber, $specialization, $gender, $medicalId);
    $stmt->execute();

    // Close the statement, but don't close the connection here
    $stmt->close();
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page if the user is not logged in
    header('Location: login.php');
    exit;
}

// Retrieve the current values of the administrator from the users table
$medicalId = $_SESSION['user_id']; // Assuming the admin's user ID is stored in the session
$adminData = getAdminData($medicalId);

// Check if the form is submitted for updating admin settings
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $contactNumber = $_POST['contact_number'];
    $specialization = $_POST['specialization'];
    $gender = $_POST['gender'];

    // Update admin data in the database
    updateAdminData($medicalId, $name, $email, $contactNumber, $specialization, $gender);

    // Refresh the admin data
    $adminData = getAdminData($medicalId);
}
?>

<!DOCTYPE html>
<html lang="">
<head>
    <title>Admin Page</title>
    <link rel="stylesheet" type="text/css" href="../styles.css">
    <script src="../script.js"></script>
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
            <h2>Admin Settings</h2>
            <div>
                <form method="POST" action="">
                    <div>
                        <label for="name">Medical Id:</label>
                        <input type="text" name="medical_id" id="medical_id"
                               value="<?php echo $adminData['medical_id']; ?>" readonly>
                    </div>
                    <div>
                        <label for="name">Name:</label>
                        <input type="text" name="name" id="name" value="<?php echo $adminData['name']; ?>">
                    </div>
                    <div>
                        <label for="email">Email:</label>
                        <input type="email" name="email" id="email" value="<?php echo $adminData['email']; ?>">
                    </div>
                    <div>
                        <label for="contact_number">Contact Number:</label>
                        <input type="text" name="contact_number" id="contact_number"
                               value="<?php echo $adminData['contact_number']; ?>">
                    </div>
                    <div>
                        <label for="specialization">Specialization:</label>
                        <input type="text" name="specialization" id="specialization"
                               value="<?php echo $adminData['specialization']; ?>">
                    </div>
                    <div>
                        <label for="gender">Gender:</label>
                        <select name="gender" id="gender">
                            <option value="Male" <?php if ($adminData['gender'] === 'Male') echo 'selected'; ?>>Male
                            </option>
                            <option value="Female" <?php if ($adminData['gender'] === 'Female') echo 'selected'; ?>>
                                Female
                            </option>
                            <option value="Other" <?php if ($adminData['gender'] === 'Other') echo 'selected'; ?>>
                                Other
                            </option>
                        </select>
                    </div>
                    <button type="submit">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>






