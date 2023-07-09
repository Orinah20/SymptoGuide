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
    $stmt = $conn->prepare("INSERT INTO diseases (disease_name, disease_description) VALUES (?, ?)");
    $stmt->bind_param("ss", $diseaseName, $diseaseDescription);

    // Execute the statement to add the disease
    if ($stmt->execute()) {
        $diseaseId = $stmt->insert_id; // Get the auto-generated disease ID
        $stmt->close();

        // Add associated symptoms
        addSymptoms($diseaseId, $selectedSymptoms);

        // Update the symptom count for the disease
        updateSymptomCount($diseaseId);

        // Disease and symptoms added successfully
        header('Location: diseasedata.php');
        exit();
    } else {
        // Error occurred while adding the disease
        echo "Error adding disease: " . $stmt->error;
    }

    $stmt->close();
}

function addSymptoms($diseaseId, $selectedSymptoms)
{
    global $conn;

    if (!empty($selectedSymptoms)) {
        // Prepare the SQL statement to add disease symptoms
        $stmt = $conn->prepare("INSERT INTO Disease_symptoms (disease_id, symptom_id) VALUES (?, ?)");

        // Iterate over the selected symptoms and insert them into the table
        foreach ($selectedSymptoms as $symptomId) {
            // Bind the parameters for each iteration
            $stmt->bind_param("ii", $diseaseId, $symptomId);
            $stmt->execute();
        }

        $stmt->close();
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
        $stmt = mysqli_prepare($conn, $updateCountQuery);
        mysqli_stmt_bind_param($stmt, "ii", $symptomCount, $diseaseId);
        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);
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
    <script src="https://cdn.jsdelivr.net/npm/jquery/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-multiselect@3.0.4/dist/js/jquery.multi-select.min.js"></script>
    <link rel="stylesheet" type="text/css"
          href="https://cdn.jsdelivr.net/npm/jquery-multiselect@3.0.4/dist/css/multi-select.min.css">
    <script>
        $(document).ready(function () {
            // Initialize the multi-select plugin
            $('#symptoms').multiSelect({
                selectableHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='Search'>",
                selectionHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='Search'>",
                afterInit: function (ms) {
                    var that = this,
                        $selectableSearch = that.$selectableUl.prev(),
                        $selectionSearch = that.$selectionUl.prev(),
                        selectableSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selectable:not(.ms-selected)',
                        selectionSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selection.ms-selected';

                    that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
                        .on('keydown', function (e) {
                            if (e.which === 40) {
                                that.$selectableUl.focus();
                                return false;
                            }
                        });

                    that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
                        .on('keydown', function (e) {
                            if (e.which === 40) {
                                that.$selectionUl.focus();
                                return false;
                            }
                        });
                },
                afterSelect: function () {
                    this.qs1.cache();
                    this.qs2.cache();
                },
                afterDeselect: function () {
                    this.qs1.cache();
                    this.qs2.cache();
                }
            });
        });
    </script>
</head>
<body>
<div class="container">
    <div class="side_nav">
        <div class="side_nav">
            <div class="side_nav-data">
                <h2>SymptoGuide</h2>
                <div>
                    <button class="nav-button <?php if ($activePage == 'dashboard') echo 'active'; ?>"
                            onclick="window.location.href='../administrator.php'">
                        Dashboard
                    </button>
                </div>
                <div>
                    <button class="nav-button <?php if ($activePage == 'userdata' || $activePage == 'editUser' || $activePage == 'addUser') echo 'active'; ?>"
                            onclick="window.location.href='../Users/userdata.php'">Users
                    </button>
                </div>
                <div>
                    <button class="nav-button <?php if ($activePage == 'patientdata' || $activePage == 'editPatient' || $activePage == 'addPatient') echo 'active'; ?>"
                            onclick="window.location.href='../Patient/patientdata.php'">Patients
                    </button>
                </div>
                <div>
                    <button class="nav-button <?php if ($activePage == 'diseasedata' || $activePage == 'editDisease' || $activePage == 'addDisease') echo 'active'; ?>"
                            onclick="window.location.href='diseasedata.php'">Disease
                    </button>
                </div>
                <div>
                    <button class="nav-button <?php if ($activePage == 'symptomdata' || $activePage == 'editSymptom' || $activePage == 'addSymptom') echo 'active'; ?>"
                            onclick="window.location.href='../Symptoms/symptomdata.php'">Symptom
                    </button>
                </div>
                <div>
                    <button class="nav-button <?php if ($activePage == 'reports') echo 'active'; ?>"
                            onclick="window.location.href='../Reports/reports.php'">Reports
                    </button>
                </div>
                <div>
                    <button class="nav-button <?php if ($activePage == 'adminSettings') echo 'active'; ?>"
                            onclick="window.location.href='../adminSettings.php'">Settings
                    </button>
                </div>
                <div>
                    <button class="nav-button" onclick="window.location.href='../../logout.php'">Logout</button>
                </div>
            </div>
        </div>

    </div>
    <div class="content">
        <div class="content_user">
            <div><b>Administrator</b></div>
            <div class="content_user-left">
                <div><b><?php echo($_SESSION['user_name']) ?></b></div>
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
                    <label for="disease_name">Disease Name:</label>
                    <input type="text" name="disease_name" placeholder="Disease Name" required>

                    <label for="disease_description">Disease Description:</label>
                    <textarea name="disease_description" placeholder="Disease Description" required></textarea>

                    <label for="symptoms">Select Symptoms:</label>
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
                    <div>
                        <input type="submit" name="add" value="Add">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
</body>
</html>
