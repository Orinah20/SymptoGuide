<?php
// Including necessary files
@include '../config.php';
@include '../session.php';

// Retrieve the selected symptoms from the query parameter
$selectedSymptoms = isset($_GET['symptoms']) ? explode(',', $_GET['symptoms']) : array();

// Perform disease analysis only if symptoms are selected
if (!empty($selectedSymptoms)) {
    // Analyze disease probabilities based on symptoms
    $diseaseProbabilities = analyzeDiseaseProbabilities($selectedSymptoms);

    // Insert diagnosis information into disease_probability table
    insertDiagnosisIntoHistory($diseaseProbabilities);
} else {
    // No symptoms selected, set empty disease probabilities
    $diseaseProbabilities = array();
}

// Perform disease analysis based on symptoms
function analyzeDiseaseProbabilities($selectedSymptoms)
{
    global $conn;

    // Initialize disease probabilities
    $diseaseProbabilities = array();

    // Prepare the SQL statement to fetch disease symptoms and join with diseases table
    $stmt = $conn->prepare("SELECT ds.disease_id, d.disease_name, ds.symptom_id, d.symptom_count FROM disease_symptoms ds INNER JOIN diseases d ON ds.disease_id = d.disease_id");
    $stmt->execute();
    $result = $stmt->get_result();

    // Iterate over disease symptoms
    while ($row = $result->fetch_assoc()) {
        $diseaseId = $row['disease_id'];
        $diseaseName = $row['disease_name'];
        $symptom = $row['symptom_id'];
        $symptomCount = $row['symptom_count'];

        // Check if the symptom is selected
        if (in_array($symptom, $selectedSymptoms)) {
            // Increase the disease probability
            if (isset($diseaseProbabilities[$diseaseId])) {
                $diseaseProbabilities[$diseaseId]['count']++;
            } else {
                $diseaseProbabilities[$diseaseId] = array(
                    'name' => $diseaseName,
                    'count' => 1,
                    'symptom_count' => $symptomCount
                );
            }
        }
    }

    foreach ($diseaseProbabilities as $diseaseId => $data) {
        $count = $data['count'];
        $symptomCount = $data['symptom_count'];

        // Calculate the probability based on symptom count
        $probability = ($count / $symptomCount) * 100;
        $diseaseProbabilities[$diseaseId]['probability'] = round($probability, 2);
    }

    // Sort disease probabilities in descending order based on probability
    usort($diseaseProbabilities, function ($a, $b) {
        return $b['probability'] <=> $a['probability'];
    });

    // Close the statement
    $stmt->close();

    // Return the disease probabilities
    return $diseaseProbabilities;
}


// Function to insert diagnosis information into disease_probability table
function insertDiagnosisIntoHistory($diseaseProbabilities)
{
    global $conn;

    // Prepare the SQL statement to insert data into the disease_probability table
    $stmt = $conn->prepare("INSERT INTO disease_probability (patient_id, disease_id, probability, date_created, date_modified) VALUES (?, ?, ?, NOW(), NOW())");

    // Get the patient ID from the session or any appropriate method
    $patientId = $_SESSION['patient_id']; // Assuming patient ID is stored in the session

    // Iterate over disease probabilities to insert each diagnosis
    foreach ($diseaseProbabilities as $data) {
        $diseaseName = $data['name'];
        $probability = $data['probability'];

        // Retrieve the disease ID from the diseases table based on the disease name
        $diseaseId = getDiseaseId($diseaseName);

        // Bind the values and execute the query for each diagnosis
        $stmt->bind_param("sid", $patientId, $diseaseId, $probability);
        $stmt->execute();
    }

    // Close the statement
    $stmt->close();
}

// Function to retrieve the disease ID from the diseases table based on the disease name
function getDiseaseId($diseaseName)
{
    global $conn;

    // Prepare the SQL statement
    $stmt = $conn->prepare("SELECT disease_id FROM diseases WHERE disease_name = ?");
    $stmt->bind_param("s", $diseaseName);

    // Execute the query
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    // Fetch the disease ID
    $row = $result->fetch_assoc();
    $diseaseId = $row['disease_id'];

    // Close the statement
    $stmt->close();

    // Return the disease ID
    return $diseaseId;
}

?>

<!DOCTYPE html>
<html lang="">
<head>
    <title>Diagnosis Success</title>
    <link rel="stylesheet" type="text/css" href="../style.css">
    <script src="../script.js"></script>
</head>
<body>
<div class="container">
    <div class="container_header">
        <div style="cursor: pointer; ">
            <h2>
                <a style="text-decoration: none; color: inherit" href="user.php">SymptoGuide</a>
            </h2>
        </div>
        <div class="content_header-left">
            <h3><?php echo $_SESSION['user_name']; ?></h3>
            <a href="../logout.php">
                <button name="logout">Logout</button>
            </a>
        </div>
    </div>

    <div class="content">
        <div><p id="session-expire" style="display: none;">Session will expire in: <span id="timer"></span></p></div>
        <div class="diagnosis-results">
            <h2>Disease Probabilities</h2>
            <table>
                <tr>
                    <th>Disease</th>
                    <th>Probability</th>
                </tr>
                <?php foreach ($diseaseProbabilities as $data) : ?>
                    <tr>
                        <td><?php echo $data['name']; ?></td>
                        <td><?php echo $data['probability']; ?>%</td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <div class="content_data-header">
            <div class="button-container">
                <button class="retry-button" onclick="window.location.href='new_diagnosis.php'">New Diagnosis</button>
                <button class="return-button" onclick="window.location.href='user.php'">Return to User Page</button>
            </div>
        </div>
    </div>
</div>
</body>
</html>


