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
            <a href="userSettings.php">Settings</a>
            <div><?php echo $_SESSION['user_name']; ?></div>
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <div class="content">
        <div><p id="session-expire" style="display: none;">Session will expire in: <span id="timer"></span></p></div>
        <div class="content_home">
            <div class="actions">
                <a href="new_diagnosis.php" class="link" id="newDiagnosis">
                    <h1>New Diagnosis</h1>
                    <div>
                        Initiate the process of creating a new diagnosis. Begin the process that leads to uncovering
                        insights into their health, pinpointing possible conditions, and paving the way for effective
                        treatments. Your action here sets the course for improved health outcomes.
                    </div>
                </a>

                <a href="view_diagnosis.php" class="link" id="history">
                    <h1>History</h1>
                    <div>
                        Investigate the medical history of patients' diagnoses. Dive into the medical history of
                        patients' diagnoses and uncover valuable insights into their health journeys. By exploring their
                        diagnostic records, you gain a deeper understanding of their conditions, contributing to better
                        healthcare decisions and enhanced well-being.
                    </div>
                </a>
            </div>
        </div>
    </div>

    <p id="session-expire" style="display: none;">Session will expire in: <span id="timer"></span></p>

</div>
</body>
</html>
