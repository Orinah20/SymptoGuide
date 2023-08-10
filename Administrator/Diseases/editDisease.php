<?php
@include '../../config.php';
@include '../../session.php';

// Check if the disease ID is provided in the URL
if (isset($_GET['disease_id'])) {
    $diseaseId = $_GET['disease_id'];

    // Retrieve disease data from the database
    $selectQuery = "SELECT * FROM diseases WHERE disease_id = ?";
    $stmt = mysqli_prepare($conn, $selectQuery);
    mysqli_stmt_bind_param($stmt, "i", $diseaseId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        // Retrieve associated symptoms for the disease
        $symptomsQuery = "SELECT symptom_id FROM disease_symptoms WHERE disease_id = ?";
        $stmt = mysqli_prepare($conn, $symptomsQuery);
        mysqli_stmt_bind_param($stmt, "i", $diseaseId);
        mysqli_stmt_execute($stmt);
        $symptomsResult = mysqli_stmt_get_result($stmt);
        $selectedSymptoms = mysqli_fetch_all($symptomsResult, MYSQLI_ASSOC);
    } else {
        // Redirect to the disease data page if the disease doesn't exist
        header('Location: diseasedata.php');
        exit();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize the form inputs
    $diseaseName = sanitizeInput($_POST['disease_name']);
    $diseaseDescription = sanitizeInput($_POST['disease_description']);

    // Update the disease data in the database
    $updateQuery = "UPDATE diseases SET disease_name = ?, disease_description = ? WHERE disease_id = ?";
    $stmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($stmt, "ssi", $diseaseName, $diseaseDescription, $diseaseId);
    mysqli_stmt_execute($stmt);

    // Delete existing associated symptoms for the disease
    $deleteQuery = "DELETE FROM disease_symptoms WHERE disease_id = ?";
    $stmt = mysqli_prepare($conn, $deleteQuery);
    mysqli_stmt_bind_param($stmt, "i", $diseaseId);
    mysqli_stmt_execute($stmt);

    // Insert the updated associated symptoms for the disease
    if (isset($_POST['symptoms'])) {
        $selectedSymptoms = $_POST['symptoms'];
        foreach ($selectedSymptoms as $symptomId) {
            $insertQuery = "INSERT INTO disease_symptoms (disease_id, symptom_id) VALUES (?, ?)";
            $stmt = mysqli_prepare($conn, $insertQuery);
            mysqli_stmt_bind_param($stmt, "ii", $diseaseId, $symptomId);
            mysqli_stmt_execute($stmt);
        }
    }

    // Redirect back to the disease data page after updating
    header('Location: diseasedata.php');
    exit();
}

function sanitizeInput($input)
{
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input);
    return $input;
}

?>


<!DOCTYPE html>
<html lang="">
<head>
    <title>Admin Page</title>
    <link rel="stylesheet" type="text/css" href="../../styles.css">
    <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            // Initialize the Select2 plugin
            $('#symptoms').select2();
        });
    </script>
</head>
<body>
<div class="container">
    <div class="side_nav">
        <h2>SymptoGuide</h2>
        <div>
            <button class="nav-button <?php if ($activePage == 'dashboard') echo 'active'; ?>"
                    onclick="window.location.href='/SymptoGuide/Administrator/administrator.php' ">Dashboard
            </button>
        </div>
        <div>
            <button class="nav-button <?php if ($activePage == 'userdata') echo 'active'; ?>"
                    onclick="window.location.href='/SymptoGuide/Administrator/Users/userdata.php'">Users
            </button>
        </div>
        <div>
            <button class="nav-button <?php if ($activePage == 'patientdata') echo 'active'; ?>"
                    onclick="window.location.href='/SymptoGuide/Administrator/Patient/patientdata.php'">Patients
            </button>
        </div>
        <div>
            <button class="nav-button <?php if ($activePage == 'diseasedata' || $activePage == 'editDisease' || $activePage == 'addDisease') echo 'active'; ?>"
                    onclick="window.location.href='/SymptoGuide/Administrator/Diseases/diseasedata.php'">Disease
            </button>
        </div>
        <div>
            <button class="nav-button <?php if ($activePage == 'symptomdata') echo 'active'; ?>"
                    onclick="window.location.href='/SymptoGuide/Administrator/Symptoms/symptomdata.php'">Symptom
            </button>
        </div>

        <div>
            <button class="nav-button <?php if ($activePage == 'diagnosis') echo 'active'; ?>"
                    onclick="window.location.href='/SymptoGuide/Administrator/Diagnosis/diagnosisdata.php'">Diagnosis
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
            <button class="nav-button" name="logout" onclick="window.location.href='/SymptoGuide/logout.php'">Logout</button>
        </div>

    </div>
    <div class="content">
        <div class="content_user">
            <div>
                <div><b>Administrator</b></div>
                <div class="content_user-left">
                    <div><b><?php echo($_SESSION['user_name']) ?></b></div>
                </div>
            </div>
        </div>
        <div><p id="session-expire" style="display: none;">Session will expire in: <span id="timer"></span></p></div>
        <!-- ... existing code ... -->
        <div class="content_data">
            <div class="content_data-container">
                <div class="content_data-container--header">
                    <a href="diseasedata.php" class="back-button">
                        <img src="../../back-icon.png" alt="Back" height="30px" width="30px" class="back-icon">
                    </a>
                    <h2>Edit Disease</h2>
                </div>
                <div class="editDisease_form">
                    <form action="updateDisease.php" method="POST" class="editDisease"
                          onsubmit="return confirm('Are you sure you want to continue?')">
                        <label for="disease_id">Disease ID:
                            <input type="text" name="disease_id" value="<?php echo $row['disease_id']; ?>" readonly>
                        </label>

                        <label for="disease_name">Disease Name:
                            <input type="text" name="disease_name" value="<?php echo $row['disease_name']; ?>">
                        </label>

                        <label for="disease_description">Disease Description:
                            <textarea name="disease_description"><?php echo $row['disease_description']; ?></textarea>
                        </label>

                        <label for="symptoms">Select Symptoms:
                            <select name="symptoms[]" multiple id="symptoms">
                                <?php
                                // Retrieve all symptoms
                                $symptomsQuery = "SELECT * FROM symptoms";
                                $symptomsResult = mysqli_query($conn, $symptomsQuery);

                                // Iterate over the symptoms and generate the options
                                while ($symptom = mysqli_fetch_assoc($symptomsResult)) {
                                    $symptomId = $symptom['symptom_id'];
                                    $symptomName = $symptom['symptom_name'];
                                    $isSelected = in_array($symptomId, array_column($selectedSymptoms, 'symptom_id'));

                                    echo '<option value="' . $symptomId . '" ' . ($isSelected ? 'selected' : '') . '>' . $symptomName . '</option>';
                                }
                                ?>
                            </select>
                        </label>

                        <label for="date_created">Date Created:
                            <input type="text" name="date_created" value="<?php echo $row['date_created']; ?>" readonly>
                        </label>

                        <div>
                            <input type="submit" name="update" value="Update" class="edit_disease-update">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

