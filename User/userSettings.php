<?php

require_once '../config.php';
require_once '../session.php';

// Fetch user details from the database based on user ID
function getUserDetails($userId)
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE medical_id = ?");
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Update user details in the database
function updateUserDetails($userId, $name, $email, $contactNumber)
{
    global $conn;
    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, contact_number = ? WHERE medical_id = ?");
    $stmt->bind_param("ssss", $name, $email, $contactNumber, $userId);
    $stmt->execute();
    $stmt->close();
}

$userId = $_SESSION['user_id'];
$userDetails = getUserDetails($userId);

// Check if the form is submitted for updating user settings
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $contactNumber = $_POST['contact_number'];

    // Update user details in the database
    updateUserDetails($userId, $name, $email, $contactNumber);

    // Refresh the user details
    $userDetails = getUserDetails($userId);
}

// Include your header, navigation, or any other common parts here
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Page</title>
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
        <div><p id="session-expire" style="display: none;">Session will expire in: <span id="timer"></span></p></div>
        <div class="content_data">
            <div class="content_data-header">
                <a href="user.php" class="back-button">
                    <img src="../back-icon.png" alt="Back" height="30px" width="30px" class="back-icon">
                </a>
                <h3>User Settings</h3>
            </div>

            <form method="POST" action="">
                <label for="name">Name:</label>
                <input type="text" name="name" id="name" value="<?php echo $userDetails['name']; ?>">

                <label for="email">Email:</label>
                <input type="email" name="email" id="email" value="<?php echo $userDetails['email']; ?>">

                <label for="contact_number">Contact Number:</label>
                <input type="text" name="contact_number" id="contact_number"
                       value="<?php echo $userDetails['contact_number']; ?>">
                <button type="submit">Update</button>
            </form>
        </div>
    </div>

</div>
</body>
</html>
