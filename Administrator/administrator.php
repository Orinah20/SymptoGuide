<?php
// Including necessary files
@include '../config.php';
@include '../session.php';

// Function to get the count of users
function getUserCount()
{
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM users");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'];
}

// Function to get the count of patients
function getPatientCount()
{
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM patients");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'];
}

// Function to get the count of diseases
function getDiseaseCount()
{
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM diseases");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'];
}

// Function to get the count of symptoms
function getSymptomCount()
{
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM symptoms");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'];
}

// Function to get the count of diagnoses
function getDiagnosisCount()
{
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM (SELECT DISTINCT patient_id, date_created FROM disease_probability) AS diagnoses");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'];
}

// Function to get the most analyzed disease
// Function to get the most analyzed diseases in descending order of percentage
function getMostAnalyzedDiseases()
{
    global $conn;

    // Prepare the SQL statement to fetch the disease analysis count and name
    $stmt = $conn->prepare("SELECT dp.disease_id, d.disease_name, COUNT(*) AS analysis_count
                            FROM disease_probability dp
                            INNER JOIN diseases d ON dp.disease_id = d.disease_id
                            GROUP BY dp.disease_id, d.disease_name
                            ORDER BY analysis_count DESC");

    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch all disease analysis data into an associative array
    $diseaseAnalysisData = $result->fetch_all(MYSQLI_ASSOC);

    // Calculate the total number of disease analyses
    $totalAnalyses = 0;
    foreach ($diseaseAnalysisData as $diseaseData) {
        $totalAnalyses += $diseaseData['analysis_count'];
    }

    // Calculate the percentage for each disease and sort in descending order
    foreach ($diseaseAnalysisData as &$diseaseData) {
        $percentage = ($diseaseData['analysis_count'] / $totalAnalyses) * 100;
        $diseaseData['percentage'] = round($percentage, 2);
    }

    // Close the statement
    $stmt->close();

    // Return the sorted disease analysis data
    return $diseaseAnalysisData;
}

// Function to get historical analysis count for each disease
function getDiseaseAnalysisHistory($diseaseId)
{
    global $conn;
    $stmt = $conn->prepare("SELECT DATE(date_created) AS analysis_date, COUNT(*) AS analysis_count
                           FROM disease_probability
                           WHERE disease_id = ?
                           GROUP BY DATE(date_created)
                           ORDER BY DATE(date_created)");

    $stmt->bind_param("i", $diseaseId);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = array();
    while ($row = $result->fetch_assoc()) {
        $data[] = array(
            'date' => $row['analysis_date'],
            'count' => $row['analysis_count']
        );
    }
    return $data;
}


?>

<!DOCTYPE html>
<html lang="">
<head>
    <title>Admin Page</title>
    <link rel="stylesheet" type="text/css" href="../styles.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="../script.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.28.3/dist/apexcharts.min.js"></script>
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
        <div><p id="session-expire">Session will expire in: <span id="timer"></span></p></div>
        <div class="content_data">
            <div class="content_data-total">
                <a class="content_manage" href="Users/userdata.php">
                    <div>
                        <h3>Users </h3>
                        <h2><?php echo getUserCount(); ?></h2>
                    </div>
                </a>

                <a class="content_manage" href="Patient/patientdata.php">
                    <div>
                        <h3>Patients</h3>
                        <h2><?php echo getPatientCount(); ?></h2>
                    </div>
                </a>

                <a class="content_manage" href="Diseases/diseasedata.php">
                    <div>
                        <h3>Diseases </h3>
                        <h2><?php echo getDiseaseCount(); ?></h2>
                    </div>
                </a>

                <a class="content_manage" href="Symptoms/symptomdata.php">
                    <div>
                        <h3>Symptoms </h3>
                        <h2><?php echo getSymptomCount(); ?></h2>
                    </div>
                </a>

                <a class="content_manage" href="Diagnosis/diagnosisdata.php">
                    <div>
                        <h3>Diagnosis</h3>
                        <h2><?php echo getDiagnosisCount(); ?></h2>
                    </div>
                </a>
            </div>
            <div id="mostAnalyzedDiseasesTable">
                <h2>Infection Rate:</h2>
                <?php
                $mostAnalyzedDiseases = getMostAnalyzedDiseases();
                foreach ($mostAnalyzedDiseases as $diseaseData) {
                    $diseaseId = $diseaseData['disease_id'];
                    $analysisCount = $diseaseData['analysis_count'];
                    $percentage = $diseaseData['percentage'];
                    $diseaseName = $diseaseData['disease_name'];
                    ?>
                    <div>
                        <div class="disease_name"><?php echo $diseaseName; ?></div>
                        <div class="percentage"><?php echo $percentage; ?>%</div>
                        <div class="analysis-count">Analysis Count: <?php echo $analysisCount; ?></div>
                    </div>
                    <?php
                }
                ?>
            </div>

            <div id="lineChartsContainer">
                <?php
                $mostAnalyzedDiseases = getMostAnalyzedDiseases();
                foreach ($mostAnalyzedDiseases as $diseaseData) {
                    $diseaseId = $diseaseData['disease_id'];
                    $analysisHistory = getDiseaseAnalysisHistory($diseaseId);
                    $labels = array_column($analysisHistory, 'date');
                    $counts = array_column($analysisHistory, 'count');
                    $diseaseName = $diseaseData['disease_name'];
                    ?>
                    <div>
                        <h2><?php echo $diseaseName; ?></h2>
                        <div id="lineChart_<?php echo $diseaseId; ?>"></div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            // Initialize DataTable
            $('#mostAnalyzedTable').DataTable({
                order: [[2, 'desc']] // Sort by the 3rd column (index 2) in descending order
                // Add any other DataTable options you want
            });

            // Your PHP data
            var diseasesData = <?php echo json_encode($mostAnalyzedDiseases); ?>;
            var lineChartContainer = document.getElementById('lineChartsContainer');

            var analysisHistory = <?php echo json_encode(getDiseaseAnalysisHistory($diseaseId)); ?>;
            var labels = analysisHistory.map(function (data) {
                return new Date(data['date']).toLocaleDateString();
            });
            var counts = analysisHistory.map(function (data) {
                return data['count'];
            });

            createLineGraph(diseaseId, labels, counts, mostInfectiousDisease['disease_name']);
        });

        // Get data for each disease and create line graphs
        $(document).ready(function () {
            <?php
            foreach ($mostAnalyzedDiseases as $diseaseData) {
            $diseaseId = $diseaseData['disease_id'];
            $analysisHistory = getDiseaseAnalysisHistory($diseaseId);
            $labels = array_column($analysisHistory, 'date');
            $counts = array_column($analysisHistory, 'count');
            $diseaseName = $diseaseData['disease_name'];
            ?>
            createLineGraph(<?php echo $diseaseId; ?>, <?php echo json_encode($labels); ?>, <?php echo json_encode($counts); ?>, '<?php echo $diseaseName; ?>');
            <?php
            }
            ?>
        });

        // Function to create line graphs for each disease
        function createLineGraph(diseaseId, labels, counts, diseaseName) {
            var options = {
                chart: {
                    type: 'line',
                    height: 150,
                    width: 168,
                },
                series: [{
                    name: diseaseName,
                    data: counts,
                }],
                xaxis: {
                    type: 'datetime',
                    categories: labels,
                    labels: {
                        format: 'MMM dd',
                    },
                },
                yaxis: {
                    title: {
                        text: 'Analysis Count',
                    },
                },
            };
            var chart = new ApexCharts(document.getElementById('lineChart_' + diseaseId), options);
            chart.render();
        }


    </script>
</body>
</html>
