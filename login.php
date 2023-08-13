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
            $_SESSION['user_id'] = $row['medical_id'];
            $_SESSION['user_name'] = $row['name'];
            $_SESSION['user_type'] = $row['user_type'];

            // Redirect to the user's page
            if ($row['user_type'] == 'Administrator') {
                header("Location: /SymptoGuide/Administrator/administrator.php");
            } else {
                header('Location: /SymptoGuide/User/user.php');
            }
            exit();
        } else {
            // Password error
            $error = 'Incorrect Medical ID or Password!';
            header("Location: login.php?error=" . urlencode($error) . "&password_error=1");
            exit();
        }
    } else {
        // Medical ID error
        $error = 'Incorrect Medical ID or Password!';
        header("Location: login.php?error=" . urlencode($error) . "&medical_id_error=1");
        exit();
    }
}

// Function to sanitize user inputs
function sanitizeInput($input)
{
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
    <h1 class="logo_identifier">SymptoGuide - Web Based Diagnosis System</h1>
    <div class="login-form">
        <form action="login.php" method="POST" class="login">
            <h2>Login</h2>
            <label for="medical_id"> Medical ID:
                <input type="text" name="medical_id" placeholder="Medical ID" style="text-transform: uppercase;"
                       required>
            </label>

            <label for="password"> Password:
                <input type="password" name="password" id="password" placeholder="Password" required>
                <div class="showPassword">
                    <label for="show_password">
                    <input type="checkbox" name="show_password" id="showPassword" onchange="togglePasswordVisibility()">
                        Show Password
                    </label>
                </div>
            </label>

            <div>
                <input type="submit" name="login" value="Login">
            </div>

            <p>New User? <a href="registration.php">Register Now</a></p>
            <p><a href="forgot_password.php">Forgot Password?</a></p>

            <?php
            if (isset($_GET['password_error'])) {
                echo '<script>alert("Incorrect Medical ID or Password!");</script>';
            } elseif (isset($_GET['medical_id_error'])) {
                echo '<script>alert("Incorrect Medical ID or Password!");</script>';
            } elseif (isset($_GET['error'])) {
                $error = $_GET['error'];
                echo '<script>alert("' . $error . '");</script>';
            }
            ?>
        </form>
    </div>
</div>
</body>
</html>
