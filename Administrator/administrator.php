<?php

@include '../config.php';
@include '../session.php';


?>

<!DOCTYPE html>
<html lang="">
<head>
    <title>Admin Page</title>
    <link rel="stylesheet" type="text/css" href="../styles.css">
    <script src="../script.js"></script>
</head>
<body>
<div class="container">
    <div class="side_nav">
        <div class="side_nav">
            <div class="side_nav-data">
                <h2>SymptoGuide</h2>
                <div>
                    <button class="nav-button <?php if ($activePage == 'dashboard') echo 'active'; ?>"
                            onclick="window.location.href='/SymptoGuide/Administrator/administrator.php' ">Dashboard
                    </button>
                </div>
                <div>
                    <button class="nav-button <?php if ($activePage == 'userdata' || $activePage == 'editUser' || $activePage == 'addUser') echo 'active'; ?>"
                     onclick="window.location.href='/SymptoGuide/Administrator/Users/userdata.php'">Users
                    </button>
                </div>
                <div>
                    <button class="nav-button <?php if ($activePage == 'patientdata') echo 'active'; ?>"
                            onclick="window.location.href='/SymptoGuide/Administrator/Patient/patientdata.php'">Patients
                    </button>
                </div>
                <div>
                    <button class="nav-button <?php if ($activePage == 'diseasedata') echo 'active'; ?>"
                            onclick="window.location.href='/SymptoGuide/Administrator/Diseases/diseasedata.php'">Disease
                    </button>
                </div>
                <div>
                    <button class="nav-button <?php if ($activePage == 'symptomdata') echo 'active'; ?>"
                            onclick="window.location.href='/SymptoGuide/Administrator/Symptoms/symptomdata.php'">Symptom
                    </button>
                </div>
                <div>
                    <button class="nav-button <?php if ($activePage == 'reports') echo 'active'; ?>"
                            onclick="window.location.href='/SymptoGuide/Administrator/Reports/reports.php'">Reports
                    </button>
                </div>
                <div>
                    <button class="nav-button <?php if ($activePage == 'adminSettings') echo 'active'; ?>"
                            onclick="window.location.href='/SymptoGuide/Administrator/adminSettings.php'">Settings
                    </button>
                </div>
                <div>
                    <button class="nav-button" onclick="window.location.href='../logout.php'">Logout</button>
                </div>
            </div>
        </div>

    </div>
    <div class="content">
        <div class="content_user">
            <div><b>Administrator</b></div>
            <div class="content_user-left">
                <div><a href="../Administrator/adminSettings.php">Settings</a></div>
                <div><b><?php echo($_SESSION['user_name']) ?></b></div>
            </div>
        </div>
        <div><p id="session-expire" style="display: none;">Session will expire in: <span id="timer"></span></p></div>
        <div class="content_data">
            <a class="content_manage" href="Users/userdata.php">
                <h3>Manage Users</h3>
                <ul>
                    <li>Add User</li>
                    <li>Edit User</li>
                    <li>Delete User</li>
                </ul>
            </a>

            <a class="content_manage" href="Patient/patientdata.php">
                <h3>Manage Patients</h3>
                <ul>
                    <li>Add User</li>
                    <li>Edit User</li>
                    <li>Delete User</li>
                </ul>
            </a>

            <a class="content_manage" href="Diseases/diseasedata.php">
                <h3>Manage Diseases</h3>
                <ul>
                    <li>Add User</li>
                    <li>Edit User</li>
                    <li>Delete User</li>
                </ul>
            </a>

            <a class="content_manage" href="Symptoms/symptomdata.php">
                <h3>Manage Symptoms</h3>
                <ul>
                    <li>Add User</li>
                    <li>Edit User</li>
                    <li>Delete User</li>
                </ul>
            </a>

            <a class="content_manage" href="Reports/reports.php">
                <h3>Reports</h3>
                <ul>
                    <li>Generate Report</li>
                    <li>View Reports</li>
                </ul>
            </a>
        </div>
    </div>
</div>
</body>
</html>
