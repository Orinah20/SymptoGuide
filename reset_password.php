<?php
require_once 'config.php';

session_start(); // Start the session

// Check if the user is authenticated (medical ID is stored in the session)
if (!isset($_SESSION['medical_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $medicalId = $_SESSION['medical_id'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Validate form data
    if ($newPassword !== $confirmPassword) {
        echo '<script>alert("Passwords do not match.");</script>';
    } else {
        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update the user's password in the database
        $updateQuery = "UPDATE users SET password = '$hashedPassword' WHERE medical_id = '$medicalId'";
        if (mysqli_query($conn, $updateQuery)) {
            // Password reset successful, remove medical ID from session and redirect to login page
            unset($_SESSION['medical_id']);
            echo '<script>alert("Password reset successful. Please login with your new password.");</script>';
            echo '<script>window.location.href = "login.php";</script>';
            exit();
        } else {
            echo '<script>alert("Failed to reset password. Please try again later.");</script>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
<div class="container">
    <div class="login-form">
        <form action="reset_password.php" method="POST" class="resetPassword">
            <h2>Reset Password</h2>
            <label for="new_password">New Password
                <input type="password" name="new_password" placeholder="New Password" required>
            </label>
            <label for="confirm_password">Confirm Password
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            </label>
            <div>
                <input type="submit" value="Reset Password">
            </div>
            <p><a href="login.php">Back to Login</a></p>
        </form>
    </div>
</div>
</body>
</html>
