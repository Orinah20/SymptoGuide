<?php
@include 'config.php';
@include 'session.php';
?>

<!DOCTYPE html>
<html lang="">
<head>
    <title>Medical Officer Landing Page</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <script src="script.js"></script>
</head>
<body>
<h1>Welcome, <?php echo $_SESSION['user_name']; ?>!</h1>
<p>You have successfully logged in as a medical officer.</p>

<h2>Patient List</h2>
<table>
    <thead>
    <tr>
        <th>Name</th>
        <th>Age</th>
        <th>Gender</th>
        <th>Diagnosis</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>John Doe</td>
        <td>30</td>
        <td>Male</td>
        <td>Influenza</td>
    </tr>
    <tr>
        <td>Jane Smith</td>
        <td>25</td>
        <td>Female</td>
        <td>Common Cold</td>
    </tr>
    <!-- Add more patient rows as needed -->
    </tbody>
</table>

<a href="logout.php">Logout</a>

<!-- Add a span element to display the timer -->
<p id="session-expire" style="display: none;">Session will expire in: <span id="timer"></span></p>
</body>
</html>
