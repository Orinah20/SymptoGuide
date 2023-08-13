<?php
require_once '../config.php';
require_once '../session.php';

function getUserDetails($userId)
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE medical_id = ?");
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function updateUserDetails($medicalId, $name, $email, $contactNumber, $specialization, $address, $gender)
{
    global $conn;
    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, contact_number = ?, specialization = ?, address = ?, gender = ? WHERE medical_id = ?");
    $stmt->bind_param("sssssss", $name, $email, $contactNumber, $specialization, $address, $gender, $medicalId);
    $success = $stmt->execute();
    $stmt->close();
    return $success;

}

$medicalId = $_SESSION['user_id'];
$userDetails = getUserDetails($medicalId);

// Check if the form is submitted for updating user settings
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $contactNumber = $_POST['contact_number'];
    $specialization = $_POST['specialization'];
    $address = $_POST['address'];
    $gender = $_POST['gender'];

    // Update user details in the database
    $updateSuccess = updateUserDetails($medicalId, $name, $email, $contactNumber, $specialization, $address, $gender);

    if ($updateSuccess) {

        $_SESSION['user_name'] = $name; // Update the user's name

        $userDetails = getUserDetails($medicalId);
        echo '<script>alert("Changes made");</script>';
    } else {
        echo "Update failed.";
    }

}
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
        <div style="cursor: pointer; ">
            <h2>
                <a style="text-decoration: none; color: inherit" href="data.php">SymptoGuide</a>
            </h2>
        </div>
        <div class="content_header-left">
            <h3><?php echo $_SESSION['user_name']; ?></h3>
            <div class="userSettings"></div>
            <a href="../logout.php">
                <button name="logout">Logout</button>
            </a>
        </div>
    </div>

    <div class="content">
        <div><p id="session-expire" style="display: none;">Session will expire in: <span id="timer"></span></p></div>
        <div class="content_data">
            <div class="content_data-header">
                <div class="content_header-left">
                    <a href="user.php" class="back-button">
                        <img src="../back-icon.png" alt="Back" height="30px" width="30px" class="back-icon">
                    </a>
                    <h2>Settings</h2>
                </div>

                <a href="changeUserPassword.php">
                    <button name="changePassword">Change Password</button>
                </a>
            </div>

            <div class="settingsForm">
                <form method="POST" action="" class="settings">
                    <label for="medical_id">Medical Id:
                        <input type="text" name="medical_id" id="medical_id"
                               value="<?php echo $userDetails['medical_id']; ?>" readonly>
                    </label>

                    <label for="medical_certificate">Medical Certificate:
                        <input type="text" name="medical_certificate"
                               value="<?php echo $userDetails['medical_certificate']; ?>" readonly>
                    </label>

                    <label for="name">Name:
                        <input type="text" name="name" id="name" value="<?php echo $userDetails['name']; ?>">
                    </label>

                    <label for="email">Email:
                        <input type="email" name="email" id="email" value="<?php echo $userDetails['email']; ?>">
                    </label>

                    <label for="contact_number">Contact Number:
                        <input type="text" name="contact_number" id="contact_number"
                               value="<?php echo $userDetails['contact_number']; ?>">
                    </label>

                    <label for="specialization">Specialization:
                        <input type="text" name="specialization" id="specialization"
                               value="<?php echo $userDetails['specialization']; ?>">
                    </label>

                    <label for="address">Address:
                        <input type="text" name="address" value="<?php echo $userDetails['address']; ?>">
                    </label>

                    <label for="gender">Gender:
                        <select name="gender" id="gender">
                            <option value="Male" <?php if ($userDetails['gender'] === 'Male') echo 'selected'; ?>>Male
                            </option>
                            <option value="Female" <?php if ($userDetails['gender'] === 'Female') echo 'selected'; ?>>
                                Female
                            </option>
                            <option value="Other" <?php if ($userDetails['gender'] === 'Other') echo 'selected'; ?>>
                                Other
                            </option>
                        </select>
                    </label>

                    <div>
                        <button name="update" type="submit">Update</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

</div>
</body>
</html>
