<?php
require_once '../config.php';
require_once '../session.php';

// Function to fetch patient data from the disease_probability table using patient_id
function searchPatientByPatientId($patientId)
{
    global $conn;
    $stmt = $conn->prepare("SELECT dp.patient_id, p.patient_name, GROUP_CONCAT(DISTINCT CONCAT(d.disease_name, ' (', dp.probability, '%)')) AS diagnoses, dp.date_created
                           FROM disease_probability dp
                           INNER JOIN patients p ON dp.patient_id = p.patient_id
                           INNER JOIN diseases d ON dp.disease_id = d.disease_id
                           WHERE dp.patient_id = ?
                           GROUP BY dp.patient_id, dp.date_created");
    $stmt->bind_param("s", $patientId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $searchPatientId = $_POST['search_patient_id'];

    // Search for patient data using patient_id
    $patientData = searchPatientByPatientId($searchPatientId);
}
?>

<!DOCTYPE html>
<html lang="">
<head>
    <title>User Page</title>
    <link rel="stylesheet" type="text/css" href="../style.css">
    <script src="../script.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
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
                <h3>View Diagnosis</h3>
            </div>

            <h2>Search Patients by Patient ID</h2>
            <form action="" method="POST" class="search-form">
                <label for="search_patient_id">Enter Patient ID:</label>
                <input type="text" name="search_patient_id" id="search_patient_id" required>
                <input type="submit" value="Search">
            </form>

            <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($patientData)) : ?>
                <h3>Search Results</h3>
                <table id="PatientData">
                    <thead>
                    <tr>
                        <th>Patient ID</th>
                        <th>Patient Name</th>
                        <th>Diagnoses</th>
                        <th>Date Created</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($patientData as $data) : ?>
                        <tr onclick="window.location.href='viewPatient.php?patient_id=<?php echo $data['patient_id']; ?>&date_created=<?php echo urlencode($data['date_created']); ?>';">
                            <td><?php echo $data['patient_id']; ?></td>
                            <td><?php echo $data['patient_name']; ?></td>
                            <td><?php echo $data['diagnoses']; ?></td>
                            <td><?php echo $data['date_created']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($patientData)) : ?>
                <p>No results found for the given Patient ID.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function () {
        $('#PatientData').DataTable({
            "order": [[3, "desc"]]
        });
    });
</script>
</body>
</html>

