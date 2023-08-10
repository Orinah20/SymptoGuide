<?php
include '../config.php';
include '../session.php';

function getAdminData($medicalId)
{
    global $conn;
    $query = "SELECT * FROM users WHERE medical_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $medicalId);
    $stmt->execute();
    $result = $stmt->get_result();
    $adminData = $result->fetch_assoc();
    $stmt->close();
    return $adminData;
}

function updateAdminData($medicalId, $name, $email, $contactNumber, $specialization, $gender, $address)
{
    global $conn;
    $query = "UPDATE users SET name = ?, email = ?, contact_number = ?, specialization = ?, gender = ?, address = ? WHERE medical_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssss", $name, $email, $contactNumber, $specialization, $gender, $address, $medicalId);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$medicalId = $_SESSION['user_id'];
$adminData = getAdminData($medicalId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $contactNumber = $_POST['contact_number'];
    $specialization = $_POST['specialization'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];

    $updateSuccess = updateAdminData($medicalId, $name, $email, $contactNumber, $specialization, $gender, $address);

    if ($updateSuccess) {
        $adminData = getAdminData($medicalId);
        echo '<script>alert("Changes made");</script>';
    } else {
        echo "Update failed.";
    }
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
            <div class="content-data_user">
                <div class="content-data_user--header">
                    <h2>Admin Settings</h2>
                        <a href="changePassword.php">
                            <button name="changePassword">Change Password</button>
                        </a>
                </div>
                <form method="POST" action="" class="adminSetting">

                    <label for="name">Medical Id:
                        <input type="text" name="medical_id" id="medical_id"
                               value="<?php echo $adminData['medical_id']; ?>" readonly>
                    </label>

                    <label for="medical_certificate">Medical Certificate:
                        <input type="text" name="medical_certificate" value="<?php echo $adminData['medical_certificate']; ?>" readonly>
                    </label>

                    <label for="name">Name:
                        <input type="text" name="name" id="name" value="<?php echo $adminData['name']; ?>">
                    </label>

                    <label for="email">Email:
                        <input type="email" name="email" id="email" value="<?php echo $adminData['email']; ?>">
                    </label>

                    <label for="contact_number">Contact Number:
                        <input type="text" name="contact_number" id="contact_number"
                               value="<?php echo $adminData['contact_number']; ?>">
                    </label>

                    <label for="specialization">Specialization:
                        <input type="text" name="specialization" id="specialization"
                               value="<?php echo $adminData['specialization']; ?>">
                    </label>

                    <label for="address">Address:
                        <input type="text" name="address" value="<?php echo $adminData['address']; ?>">
                    </label>

                    <label for="gender">Gender:
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
                    </label>

                    <div>
                        <button type="submit" name="update">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>






