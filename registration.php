<?php
require_once 'config.php';

// Initialize error variable
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $medicalId = $_POST['medical_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $userType = $_POST['user_type'];
    $contactNumber = $_POST['contact_number'];
    $address = $_POST['address'];
    $specialization = $_POST['specialization'];
    $gender = $_POST['gender'];
    $dateOfBirth = $_POST['date_of_birth'];
    $securityQuestion = $_POST['security_question'];
    $securityAnswer = $_POST['security_answer'];

    // Validate form data
    if ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        // Check if medical ID or email already exists
        $selectQuery = "SELECT * FROM users WHERE medical_id = '$medicalId' OR email = '$email'";
        $result = mysqli_query($conn, $selectQuery);
        if (mysqli_num_rows($result) > 0) {
            $error = 'Medical ID or Email already exists.';
        } else {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Add the user to the database
            $insertQuery = "INSERT INTO users (medical_id, name, email, password, user_type, status, contact_number, address, specialization, gender, date_of_birth, security_question, security_answer)
            VALUES ('$medicalId', '$name', '$email', '$hashedPassword', '$userType', 'pending', '$contactNumber', '$address', '$specialization', '$gender', '$dateOfBirth', '$securityQuestion', '$securityAnswer')";

            if (mysqli_query($conn, $insertQuery)) {
                // Registration successful, redirect to login page
                header('Location: login.php');
                exit();
            } else {
                $error = 'Failed to register user. Please try again later.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="">
<head>
    <title>User Registration</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
<div class="container">
    <h2>User Registration</h2>
    <?php if ($error !== ''): ?>
        <!-- Display error message if any -->
        <p class="error-message"><?php echo $error; ?></p>
    <?php endif; ?>
    <form action="registration.php" method="POST">
        <div class="form-group">
            <label for="medical_id">Medical ID:
                <input type="text" name="medical_id" style="text-transform: uppercase;" required>
            </label>
        </div>
        <div class="form-group">
            <label for="name">Name:
                <input type="text" name="name" required>
            </label>
        </div>
        <div class="form-group">
            <label for="email">Email:
                <input type="email" name="email" required>
            </label>
        </div>
        <div class="form-group">
            <label for="password">Password:
                <input type="password" name="password" required>
            </label>
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm Password:
                <input type="password" name="confirm_password" required>
            </label>
        </div>
        <div class="form-group">
            <label for="user_type">User Type:
                <select name="user_type" required>
                    <option value="administrator">Administrator</option>
                    <option value="user">User</option>
                </select>
            </label>
        </div>
        <div class="form-group">
            <label for="contact_number">Contact Number:
                <input type="text" name="contact_number" required>
            </label>
        </div>
        <div class="form-group">
            <label for="address">Address:
                <input type="text" name="address" required>
            </label>
        </div>
        <div class="form-group">
            <label for="specialization">Specialization:
                <input type="text" name="specialization" required>
            </label>
        </div>
        <div class="form-group">
            <label for="gender">Gender:
                <select name="gender" required>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
            </label>
        </div>
        <div class="form-group">
            <label for="date_of_birth">Date of Birth:
                <input type="date" name="date_of_birth" required>
            </label>
        </div>

        <div>
            <label for="security_question">Security Question:
            <select name="security_question" required>
                <option value="" selected disabled>Select a security question</option>
                <option value="mother_maiden_name">What is your mother's maiden name?</option>
                <option value="pet_name">What is your pet's name?</option>
                <option value="first_school">What was the name of your first school?</option>
            </select>

            </label>
            <label for="security_question">Security Question:
                <input type="text" name="security_answer" placeholder="Security Answer" required>
            </label>
        </div>

        <button type="submit">Register</button>
        <p>Already a user? <a href="login.php">Login Now</a></p>
    </form>
</div>
</body>
</html>
