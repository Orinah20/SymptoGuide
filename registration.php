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
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $insertQuery);
            $userType = 'User';
            mysqli_stmt_bind_param($stmt, "ssssssssssss", $medicalId, $userType, $name, $email, $hashedPassword, $contactNumber, $address, $specialization, $gender, $dateOfBirth, $securityQuestion, $securityAnswer);

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
function sanitizeInput($input)
{
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input);
    return $input;
}

// Function to validate password format
function isValidPasswordFormat($password)
{
    // Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, and one digit
    $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/';
    return preg_match($pattern, $password);
}

?>

<!DOCTYPE html>
<html lang="">
<head>
    <title>Registration Page</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <script src="script.js"></script>
</head>
<body>
<div class="container">
    <div class="registration-form">
        <form action="registration.php" method="POST" class="registration">
            <h2>Registration</h2>

            <label for="name">Name:
                <input type="text" name="name" placeholder="Name" required>
            </label>

            <label for="email">Email:
                <input type="email" name="email" placeholder="Email" required>
            </label>

            <label for="password">Password:
                <input type="password" name="password" placeholder="Password" required>
            </label>

            <label for="confirm_password">Confirm Password:
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            </label>

            <label for="contact_number">Contact Number:
                <input type="text" name="contact_number" pattern="[0-9]+" placeholder="Contact Number" required>
            </label>

            <label for="address">Address:
                <input type="text" name="address" placeholder="Address" required>
            </label>

            <label for="specialization">Specialization:
                <select name="specialization" required>
                    <option value="">Select a Specialization</option>
                    <option value="Pulmonology">Pulmonology - Respiratory Diseases</option>
                </select>
            </label>


            <label for="gender">Gender:
                <select name="gender" required>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
            </label>

            <label for="date_of_birth">Date of Birth:
                <input type="date" name="date_of_birth" placeholder="Date of Birth"
                       max="<?php echo date('Y-m-d', strtotime('-22 years')); ?>" required>
            </label>

            <label for="security_question">Security Question:
                <select name="security_question" required>
                    <option value="">Select a Security Question</option>
                    <option value="What is your mother's maiden name?">What is your mother's maiden name?</option>
                    <option value="What is the name of your first pet?">What is the name of your first pet?</option>
                    <option value="What was the name of your first school?">What was the name of your first school?
                    </option>
                    <!-- Add more options as needed -->
                </select>
            </label>

            <label for="security_answer">Security Answer:
                <input type="text" name="security_answer" placeholder="Security Answer" required>
            </label>

            <div>
                <input type="submit" value="Register">
            </div>

            <p>Already a user? <a href="login.php">Login Now</a></p>

            <?php
            if (!empty($error)) {
                echo '<script>alert("' . $error . '");</script>';
            }
            ?>
        </form>
    </div>
</div>
</body>
</html>
