<?php
@include '../../config.php';
@include '../../session.php';

$diseaseName = "";
$diseaseDescription = "";
$selectedSymptoms = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $diseaseName = sanitizeInput($_POST['disease_name']);
    $diseaseDescription = sanitizeInput($_POST['disease_description']);

    if (isset($_POST['symptoms'])) {
        $selectedSymptoms = $_POST['symptoms'];
    }

    addDisease($diseaseName, $diseaseDescription, $selectedSymptoms);
}

function addDisease($diseaseName, $diseaseDescription, $selectedSymptoms)
{
    global $conn;

    // Prepare the SQL statement to add the disease
    $insertDiseaseQuery = "INSERT INTO diseases (disease_name, disease_description) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $insertDiseaseQuery);
    mysqli_stmt_bind_param($stmt, "ss", $diseaseName, $diseaseDescription);

    // Execute the statement to add the disease
    if (mysqli_stmt_execute($stmt)) {
        $diseaseId = mysqli_insert_id($conn); // Get the auto-generated disease ID

        // Add associated symptoms
        addSymptoms($diseaseId, $selectedSymptoms);

        // Update the symptom count for the disease
        updateSymptomCount($diseaseId);

        // Disease and symptoms added successfully
        header('Location: diseasedata.php');
        exit();
    } else {
        // Error occurred while adding the disease
        echo "Error adding disease: " . mysqli_stmt_error($stmt);
    }

    mysqli_stmt_close($stmt);
}

function addSymptoms($diseaseId, $selectedSymptoms)
{
    global $conn;

    if (!empty($selectedSymptoms)) {
        // Prepare the SQL statement to add disease symptoms
        $insertSymptomsQuery = "INSERT INTO Disease_symptoms (disease_id, symptom_id) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $insertSymptomsQuery);

        // Iterate over the selected symptoms and insert them into the table
        foreach ($selectedSymptoms as $symptomId) {
            // Bind the parameters for each iteration
            mysqli_stmt_bind_param($stmt, "ii", $diseaseId, $symptomId);
            mysqli_stmt_execute($stmt);
        }

        mysqli_stmt_close($stmt);
    }
}

function updateSymptomCount($diseaseId)
{
    global $conn;

    // Retrieve the symptom count for the disease
    $countQuery = "SELECT COUNT(*) AS symptom_count FROM Disease_symptoms WHERE disease_id = ?";
    $stmt = mysqli_prepare($conn, $countQuery);
    mysqli_stmt_bind_param($stmt, "i", $diseaseId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $symptomCount = $row['symptom_count'];

        // Update the symptom count in the diseases table
        $updateCountQuery = "UPDATE diseases SET symptom_count = ? WHERE disease_id = ?";
        $updateCountStmt = mysqli_prepare($conn, $updateCountQuery);
        mysqli_stmt_bind_param($updateCountStmt, "ii", $symptomCount, $diseaseId);
        mysqli_stmt_execute($updateCountStmt);

        if (mysqli_stmt_affected_rows($updateCountStmt) > 0) {
            // Symptom count updated successfully
            mysqli_stmt_close($updateCountStmt);
        } else {
            // Error occurred while updating symptom count
            echo "Error updating symptom count.";
        }
    } else {
        // Error occurred while retrieving symptom count
        // Handle the error as per your application's logic
    }

    mysqli_stmt_close($stmt);
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
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
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
            <button class="nav-button <?php if ($activePage == 'userdata' ) echo 'active'; ?>"
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
            <button class="nav-button" name="logout" onclick="window.location.href='../logout.php'">Logout</button>
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
        <div class="content_data">
            <div class="content_data-header">
                <a href="diseasedata.php" class="back-button">
                    <img src="../../back-icon.png" alt="Back" height="30px" width="30px" class="back-icon">
                </a>
                <h2>Add Disease</h2>
            </div>
            <div class="addDisease_form">
                <form action="" method="POST" class="addDisease">
                    <label for="disease_name">Disease Name:
                        <input type="text" name="disease_name" placeholder="Disease Name" required>
                    </label>

                    <label for="disease_description">Disease Description:
                        <textarea name="disease_description" placeholder="Disease Description" required></textarea>
                    </label>

                    <label for="symptoms">Select Symptoms:
                        <select name="symptoms[]" id="symptoms" multiple required>
                            <?php
                            // Retrieve symptoms from the database
                            $symptomsQuery = "SELECT * FROM symptoms";
                            $symptomsResult = mysqli_query($conn, $symptomsQuery);

                            // Iterate over the symptoms and generate the options
                            while ($symptom = mysqli_fetch_assoc($symptomsResult)) {
                                $symptomId = $symptom['symptom_id'];
                                $symptomName = $symptom['symptom_name'];

                                echo '<option value="' . $symptomId . '">' . $symptomName . '</option>';
                            }
                            ?>
                        </select>
                    </label>

                    <div>
                        <input type="submit" name="add" value="Add">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
