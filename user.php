<?php

@include 'config.php';

session_start(); // Start the session

if(!isset($_SESSION['user_name'])){
    header('Location: login.php');
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Medical Officer Landing Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f1f1f1;
            margin: 0;
            padding: 20px;
        }

        h1 {
            color: #333;
        }

        p {
            color: #666;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f5f5f5;
        }

        a {
            color: #333;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<h1>Welcome, <?php echo ($_SESSION['user_name']) ?></h1>
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
</body>
</html>
