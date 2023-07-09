<?php
require_once 'config.php';

session_start(); // Start the session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $medicalId = sanitizeInput($_POST['medical_id']);
    $password = $_POST['password'];

    $selectQuery = "SELECT * FROM users WHERE medical_id = ?";
    $stmt = mysqli_prepare($conn, $selectQuery);
    mysqli_stmt_bind_param($stmt, "s", $medicalId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        if (password_verify($password, $row['password'])) {

            // Set the user session data
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['name'];
            $_SESSION['user_type'] = $row['user_type'];

            // Redirect to the user's page
            if ($row['user_type'] == 'Administrator') {
                header("Location: /SymptoGuide/Administrator/administrator.php");
            } else {
                header('Location: /SymptoGuide//User/user.php');
            }
            exit();
        }
    } else {
        $error = 'Incorrect Medical ID or Password!';
    }

    // Redirect back to the login page with an error message
    header("Location: login.php?error=" . urlencode($error));
    exit();
}

// Function to sanitize user inputs
function sanitizeInput($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input);
    return $input;
}
?>

<!DOCTYPE html>
<html lang="">
<head>
    <title>Login Page</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <script src="script.js"></script>
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
                <input type="password" name="password" id="password" placeholder="Password" required>
                <input type="checkbox" id="showPassword" onchange="togglePasswordVisibility()">Show Password
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
