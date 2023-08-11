<?php
require_once '../config.php';
require_once '../session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword !== $confirmPassword) {
        echo '<script>alert("Passwords do not match.");</script>';
    } else {
        $medicalId = $_SESSION['user_id'];

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
<html lang="en">
<head>
    <title>User Page</title>
    <link rel="stylesheet" type="text/css" href="../style.css">
    <script src="../script.js"></script>
</head>
<body>
<div class="container">
    <div class="container_header">
        <div style="cursor: pointer;">
            <h2>
                <a style="text-decoration: none; color: inherit" href="user.php">SymptoGuide</a>
            </h2>
        </div>
        <div class="content_header-left">
            <h3><?php echo $_SESSION['user_name']; ?></h3>
            <a href="../logout.php">
                <button name="logout">Logout</button>
            </a>
        </div>
    </div>

    <div class="content">
        <div><p id="session-expire" style="display: none;">Session will expire in: <span id="timer"></span></p></div>
        <div class="content_data">


            <div class="settingsForm">
                <form action="" method="POST" class="resetPassword">
                    <span class="content_header-left">
                        <a href="userSettings.php" class="back-button">
                            <img src="../back-icon.png" alt="Back" height="30px" width="30px" class="back-icon">
                        </a>
                        <h2>Change Password</h2>
                    </span>
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
