<?php
require_once 'config.php';

session_start(); // Start the session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $medicalId = mysqli_real_escape_string($conn, $_POST['medical_id']);
    $password = $_POST['password'];

    $selectQuery = "SELECT * FROM users WHERE medical_id = '$medicalId'";
    $result = mysqli_query($conn, $selectQuery);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        if (password_verify($password, $row['password'])) {
            if ($row['status'] == 'pending') {
                $error = 'Your account is pending approval.';
            } elseif ($row['status'] == 'banned') {
                $error = 'Your account has been banned.';
            } else {
                // Set the user session data
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_name'] = $row['name'];
                $_SESSION['user_type'] = $row['user_type'];

                // Redirect to the user's page
                if ($row['user_type'] == 'administrator') {
                    header('Location: administrator.php');
                } else {
                    header('Location: user.php');
                }
                exit();
            }
        } else {
            $error = 'Incorrect Medical ID or Password!';
        }
    } else {
        $error = 'Incorrect Medical ID or Password!';
    }

    // Redirect back to the login page with an error message
    header("Location: login.php?error=" . urlencode($error));
    exit();
}
?>

<!DOCTYPE html>
<html lang="">
<head>
    <title>Login Page</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
<div class="container">
    <div class="login-form">
        <h2>Login</h2>
        <form action="login.php" method="POST">

            <label for="medical_id"> Medical ID:
                <input type="text" name="medical_id" placeholder="Medical ID" style="text-transform: uppercase;" required>
            </label>

            <label for="password"> Password:
                <input type="password" name="password" placeholder="Password" required>
            </label>
            <input type="submit" value="Login">
            <p>New User? <a href="registration.php">Register Now</a></p>
            <p><a href="forgot_password.php">Forgot Password?</a></p>
            <?php
            if (isset($_GET['error'])) {
                $error = $_GET['error'];
                echo '<p class="error-message">' . $error . '</p>';
            }
            ?>
        </form>
    </div>
</div>
</body>
</html>
