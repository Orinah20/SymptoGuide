<?php
require_once 'config.php';

session_start(); // Start the session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $medicalId = mysqli_real_escape_string($conn, $_POST['medical_id']);
    $securityQuestion = $_POST['security_question'];
    $securityAnswer = $_POST['security_answer'];

    $selectQuery = "SELECT * FROM users WHERE medical_id = '$medicalId'";
    $result = mysqli_query($conn, $selectQuery);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        if ($securityQuestion === $row['security_question'] && $securityAnswer === $row['security_answer']) {
            // Security question and answer match, allow password reset
            $_SESSION['medical_id'] = $row['medical_id'];
            // Store medical ID in session for password reset validation
            header('Location: reset_password.php');
            exit();
        } else {
            $error = 'Incorrect security question or answer!';
        }
    } else {
        $error = 'Invalid medical ID!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Forgot Password</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
<div class="container">
    <div class="login-form">
        <h2>Forgot Password</h2>
        <?php if (isset($error)) { ?>
            <p class="error-message"><?php echo $error; ?></p>
        <?php } ?>
        <form action="forgot_password.php" method="POST">
            <input type="text" name="medical_id" placeholder="Medical ID" style="text-transform: uppercase;" required>
            <select name="security_question" required>
                <option value="">Select a security question</option>
                <option value="mother_maiden_name">What is your mother's maiden name?</option>
                <option value="pet_name">What is your pet's name?</option>
                <option value="first_school">What was the name of your first school?</option>
            </select>
            <input type="text" name="security_answer" placeholder="Security Answer" required>
            <input type="submit" value="Reset Password">
            <p><a href="login.php">Back to Login</a></p>
        </form>
    </div>
</div>
</body>
</html>

