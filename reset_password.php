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
        $error = 'Passwords do not match.';
    } else {
        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update the user's password in the database
        $updateQuery = "UPDATE users SET password = '$hashedPassword' WHERE medical_id = '$medicalId'";
        if (mysqli_query($conn, $updateQuery)) {
            // Password reset successful, remove medical ID from session and redirect to login page
            unset($_SESSION['medical_id']);
            header('Location: login.php');
            exit();
        } else {
            $error = 'Failed to reset password. Please try again later.';
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
        <h2>Reset Password</h2>
        <?php if (isset($error)) { ?>
            <p class="error-message"><?php echo $error; ?></p>
        <?php } ?>
        <form action="reset_password.php" method="POST">
            <input type="password" name="new_password" placeholder="New Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <input type="submit" value="Reset Password">
        </form>
    </div>
</div>
</body>
</html>
