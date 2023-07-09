<?php

@include '../../config.php';
@include '../../session.php';

// Check if the user ID is provided in the URL
if (isset($_GET['medical_id'])) {
    $medicalId = $_GET['medical_id'];

    // Retrieve user data from the database
    $selectQuery = "SELECT * FROM users WHERE medical_id = ?";
    $stmt = mysqli_prepare($conn, $selectQuery);
    mysqli_stmt_bind_param($stmt, "s", $medicalId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
    } else {
        // Redirect to the user data page if the user doesn't exist
        header('Location: userdata.php');
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
                            onclick="window.location.href='userdata.php'">Users
                    </button>
                </div>
                <div>
                    <button class="nav-button <?php if ($activePage == 'patientdata' || $activePage == 'editPatient' || $activePage == 'addPatient' ) echo 'active'; ?>"
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
                    <a href="userdata.php" class="back-button">
                        <img src="../../back-icon.png" alt="Back" height="30px" width="30px" class="back-icon">
                    </a>
                    <h2>Edit User</h2>
                </div>
                <form action="updateUser.php" method="POST" class="edit_user"
                      onsubmit="return confirm('Are you sure you want to continue?')">
                    <label for="medical_id">Medical ID:
                        <input type="text" name="medical_id" value="<?php echo $row['medical_id']; ?>" readonly>
                    </label>

                    <label for="name">Name:
                        <input type="text" name="name" value="<?php echo $row['name']; ?>" readonly>
                    </label>

                    <label for="email">Email Address:
                        <input type="text" name="email" value="<?php echo $row['email']; ?>" readonly>
                    </label>

                    <label for="contact_number">Contact Number:
                        <input type="text" name="contact_number" value="<?php echo $row['contact_number']; ?>" readonly>
                    </label>

                    <label for="address">Address:
                        <input type="text" name="address" value="<?php echo $row['address']; ?>" readonly>
                    </label>

                    <label for="specialization">Specialization:
                        <input type="text" name="specialization" value="<?php echo $row['specialization']; ?>" readonly>
                    </label>

                    <label for="user_type">User Type:
                        <select name="user_type">
                            <option value="Administrator" <?php if ($row['user_type'] == 'Administrator') echo 'selected'; ?>>
                                Administrator
                            </option>
                            <option value="User" <?php if ($row['user_type'] == 'Users') echo 'selected'; ?>>User
                            </option>
                        </select>
                    </label>
                    <input type="submit" name="update" value="Update" class="edit_user-update">
                </form>

                <form action="deleteUser.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
                    <input type="hidden" name="medical_id" value="<?php echo $row['medical_id']; ?>">
                    <input type="submit" name="delete" value="Delete User" class="edit_user-delete">
                </form>

            </div>
        </div>
    </div>
</div>

