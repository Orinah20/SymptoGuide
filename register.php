<?php
require_once 'config.php';

// Initialize error variable
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data and sanitize inputs
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $contactNumber = sanitizeInput($_POST['contact_number']);
    $address = sanitizeInput($_POST['address']);
    $specialization = $_POST['specialization'];
    $gender = $_POST['gender'];
    $dateOfBirth = $_POST['date_of_birth'];
    $securityQuestion = $_POST['security_question'];
    $securityAnswer = sanitizeInput($_POST['security_answer']);

    // Validate form data
    if (!isValidPasswordFormat($password)) {
        $error = 'Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, and one digit.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        // Check if email already exists using prepared statement
        $selectQuery = "SELECT * FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $selectQuery);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) > 0) {
            $error = 'Email already exists.';
        } else {
            // Generate a unique medical ID
            $medicalId = generateMedicalId();

            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Add the user to the database using prepared statement
            $insertQuery = "INSERT INTO users (medical_id, user_type, name, email, password, contact_number, address, specialization, gender, date_of_birth, security_question, security_answer)
            VALUES (?, 'User' ,?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $insertQuery);
            mysqli_stmt_bind_param($stmt, "sssssssssss", $medicalId, $name, $email, $hashedPassword, $contactNumber, $address, $specialization, $gender, $dateOfBirth, $securityQuestion, $securityAnswer);


            if (mysqli_stmt_execute($stmt)) {
                // Registration successful, redirect to login page
                echo '<script>alert("Registration successful! Your medical ID is: ' . $medicalId . '");</script>';
                echo '<script>alert("' . $medicalId . ' is used when logging in.");</script>';
                echo '<script>window.location.href = "login.php";</script>';
                exit();
            } else {
                $error = 'Failed to register user. Please try again later.';
            }
        }
    }
}

// Function to generate a unique medical ID
// Function to generate a unique and easy-to-memorize medical ID
function generateMedicalId()
{
    global $conn;

    // Get the latest medical ID from the database
    $selectQuery = "SELECT MAX(CAST(SUBSTRING(medical_id, 4) AS UNSIGNED)) AS max_id FROM users";
    $result = mysqli_query($conn, $selectQuery);
    $row = mysqli_fetch_assoc($result);
    $lastId = $row['max_id'];

    // Increment the last medical ID and format it with leading zeros
    $nextId = sprintf('%04d', $lastId + 1);
    $medicalId = 'MED' . $nextId;

    return $medicalId;
}


// Function to sanitize user inputs
function sanitizeInput($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input);
    return $input;
}

// Function to validate password format
function isValidPasswordFormat($password) {
    // Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, and one digit
    $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/';
    return preg_match($pattern, $password);
}
?>
