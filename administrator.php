<?php

@include 'config.php';
@include  'session.php';
?>

<!DOCTYPE html>
<html lang="">
<head>
    <title>Admin Page</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <script src="script.js"></script>
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

<p id="session-expire" style="display: none;">Session will expire in: <span id="timer"></span></p>

</body>
</html>



