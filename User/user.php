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
    <div class="container_header">
        <h2>SymptoGuide</h2>
        <div class="content_header-left">
        <div><?php echo $_SESSION['user_name']; ?></div>
        <a href="../logout.php">Logout</a>
        </div>
    </div>

    <div class="content">
        <h2>Diagnosis System</h2>
        <p>Explore symptoms and create a new symptom:</p>

        <div class="actions">
            <a href="view_diagnosis.php" class="button">View Diagnosis</a>
            <a href="new_diagnosis.php" class="button">New Diagnosis</a>
        </div>
    </div>


    <!-- Add a span element to display the timer -->
    <p id="session-expire" style="display: none;">Session will expire in: <span id="timer"></span></p>
</div>
</body>
</html>
