<?php
@include '../config.php';
@include '../session.php';
?>

<!DOCTYPE html>
<html lang="">
<head>
    <title>Diagnosis System - User Landing Page</title>
    <link rel="stylesheet" type="text/css" href="../style.css">
    <script src="../script.js"></script>
</head>
<body>
<div class="container">
    <h1>Welcome, <?php echo $_SESSION['user_name']; ?>!</h1>
    <p>You have successfully logged in as a user.</p>

    <div class="content">
        <h2>Diagnosis System</h2>
        <p>Explore symptoms and create a new symptom:</p>

        <div class="actions">
            <a href="view_diagnosis.php" class="button">View Diagnosis</a>
            <a href="new_diagnosis.php" class="button">New Diagnosis</a>
        </div>
    </div>

    <div class="footer">
        <a href="../logout.php">Logout</a>
    </div>

    <!-- Add a span element to display the timer -->
    <p id="session-expire" style="display: none;">Session will expire in: <span id="timer"></span></p>
</div>
</body>
</html>
