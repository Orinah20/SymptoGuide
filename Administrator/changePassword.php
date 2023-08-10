<?php
global $conn, $activePage;
include '../config.php';
include '../session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $medicalId = $_SESSION['user_id'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Validate form data
    if ($newPassword !== $confirmPassword) {
        echo '<script>alert("Passwords do not match.");</script>';
    } else {
        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update the user's password in the database
        $updateQuery = "UPDATE users SET password = ? WHERE medical_id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("ss", $hashedPassword, $medicalId);

        if ($stmt->execute()) {
            echo '<script>alert("Password change successful. Use the new password on the next login.");</script>';
        } else {
            echo '<script>alert("Failed to change password. Please try again later.");</script>';
        }

        $stmt->close();
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
            <button class="nav-button <?php if ($activePage == 'adminSettings' || $activePage == 'changePassword') echo 'active'; ?>"
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
                <div class="content_data-container--header">
                    <a href="adminSettings.php" class="back-button">
                        <img src="../back-icon.png" alt="Back" height="30px" width="30px" class="back-icon">
                    </a>
                    <h2>Reset Password</h2>
                </div>

                <form action="" method="POST" class="resetPassword">
                    <label for="new_password">New Password
                        <input type="password" name="new_password" placeholder="New Password" required>
                    </label>
                    <label for="confirm_password">Confirm Password
                        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                    </label>
                    <div>
                        <input type="submit" name="reset" value="Reset Password">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
