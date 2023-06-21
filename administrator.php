<?php

@include 'config.php';

session_start(); // Start the session

if(!isset($_SESSION['user_name'])){
    header('Location: login.php');
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Page</title>
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

        ul {
            list-style-type: none;
            padding: 0;
            margin-bottom: 20px;
        }

        li {
            margin-bottom: 10px;
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
<h1>Welcome,<?php echo ($_SESSION['user_name']) ?>
</h1>
<p>You have successfully logged in as an admin.</p>

<h2>Manage Users</h2>
<ul>
    <li><a href="add_user.php">Add User</a></li>
    <li><a href="edit_user.php">Edit User</a></li>
    <li><a href="delete_user.php">Delete User</a></li>
</ul>

<h2>Reports</h2>
<ul>
    <li><a href="generate_report.php">Generate Report</a></li>
    <li><a href="view_reports.php">View Reports</a></li>
</ul>

<a href="logout.php">Logout</a>
</body>
</html>



