<?php

require "register.php"

?>

<!DOCTYPE html>
<html lang="">
<head>
    <title>User Registration</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <script src="script.js"></script>
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
                <input type="password" id="password" name="password" required>
                <input type="checkbox" onclick="togglePasswordVisibility('password')"> Show Password
            </label>

        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm Password:
                <input type="password" id="confirm_password" name="confirm_password" required>
                <input type="checkbox" onclick="togglePasswordVisibility('confirm_password')"> Show Password
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
                <select name="specialization" required>
                    <option value="" selected disabled>Select specialization</option>
                    <option value="Pulmonology">Pulmonology - Specializes in respiratory diseases</option>
                </select>
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
                <input type="date" name="date_of_birth" id="date_of_birth" required >
            </label>
        </div>

        <div>
            <label for="security_question">Security Question:
                <select name="security_question" required>
                    <option value="" selected disabled>Select a security question</option>
                    <option value="What is your mother's maiden name?">What is your mother's maiden name?</option>
                    <option value="What is your pet's name?">What is your pet's name?</option>
                    <option value="What was the name of your first school?">What was the name of your first school?</option>
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
