<?php
session_start();

// Database connection
$host = "sql302.infinityfree.com";
$username = "if0_37729401";
$password = "xOhEjKEk4uwo6AX";
$database = "if0_37729401_crms";

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['fetch_metrics'])) {
    $result = $conn->query("
        SELECT 
            (SELECT COUNT(*) FROM crime_reports) AS total_reports,
            (SELECT COUNT(*) FROM crime_reports WHERE status = 'Pending') AS pending_cases,
            (SELECT COUNT(*) FROM crime_reports WHERE status = 'Resolved') AS resolved_cases
    ");

    if ($result) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(['total_reports' => 0, 'pending_cases' => 0, 'resolved_cases' => 0]);
    }
    exit;
}

// Fetch All Crime Reports
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['fetch_reports'])) {
    $result = $conn->query("SELECT * FROM crime_reports ORDER BY report_date DESC");
    $reports = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($reports);
    exit;
}

// Insert Crime Report
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addCrimeReport'])) {
    $incident_type = $_POST['incident_type'];
    $incident_description = $_POST['incident_description'];
    $location = $_POST['location'];
    $report_date = $_POST['report_date'];
    $contact_info = $_POST['contact_info'];
    $reporting_officer = $_POST['reporting_officer'];
    $priority_level = $_POST['priority_level'];
    $evidence = $_FILES['evidence']['name'];

    // Move uploaded file to the designated directory
    if ($evidence) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($evidence);
        if (move_uploaded_file($_FILES['evidence']['tmp_name'], $target_file)) {
            $evidence = basename($evidence); // Get the file name
        } else {
            $evidence = NULL; // If file upload fails, set evidence to NULL
        }
    }

    // Prepare and bind SQL query to insert data into crime_reports table
    $query = $conn->prepare("
        INSERT INTO crime_reports 
        (incident_type, incident_description, location, report_date, contact_info, reporting_officer, priority_level, evidence)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $query->bind_param("ssssssss", $incident_type, $incident_description, $location, $report_date, $contact_info, $reporting_officer, $priority_level, $evidence);
    
    if ($query->execute()) {
        echo json_encode(['success' => true, 'message' => 'Crime report submitted successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to submit crime report.']);
    }
    exit;
}

// Update Case Details
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_case'])) {
    $case_id = $_POST['case_id'];
    $incident_type = $_POST['incident_type'];
    $incident_description = $_POST['incident_description'];
    $location = $_POST['location'];
    $status = $_POST['status'];
    $priority_level = $_POST['priority_level'];

    $query = $conn->prepare("
        UPDATE crime_reports
        SET incident_type = ?, incident_description = ?, location = ?, status = ?, priority_level = ?, last_updated = NOW()
        WHERE id = ?
    ");
    $query->bind_param("sssssi", $incident_type, $incident_description, $location, $status, $priority_level, $case_id);

    if ($query->execute()) {
        echo json_encode(['success' => true, 'message' => 'Case updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update case.']);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Officer Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.4.0/axios.min.js"></script>
    <style>
        body {
            display: flex;
            margin: 0;
            font-family: Arial, sans-serif;
            background: url('https://www.transparenttextures.com/patterns/dark-fabric.png'), 
                linear-gradient(to bottom, rgba(0,0,0,0.9), rgba(50,50,50,0.9));
            background-size: cover;
            color: white;
        }

        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            color: white;
            height: 100vh;
            position: fixed;
            padding: 20px;
            overflow-y: auto;
        }

        .sidebar h3 {
            text-align: center;
            color: #ecf0f1;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 10px;
            display: block;
            border-radius: 5px;
        }

        .sidebar a:hover {
            background-color: #34495e;
        }

        .content {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
        }

        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h3>Officer Dashboard</h3>
              <a href="#" onclick="redirectToHomepage()"><i class="fas fa-home"></i> Homepage</a>
        <a href="#" onclick="showFeature('overview')"><i class="fas fa-chart-line"></i> Status</a>
        <a href="#" onclick="showFeature('addCrimeReport')"><i class="fas fa-plus-circle"></i> Add Crime Report</a>
        <a href="#" onclick="showFeature('manageReports')"><i class="fas fa-clipboard-list"></i> Manage Reports</a>
         <a href="http://offenseorbit.000.pe/others/add_wanted.php"><i class="fas fa-user-plus"></i> Add Wanted Person</a>
           <a href="http://offenseorbit.000.pe/others/lost.php"><i class="fas fa-user-plus"></i> Lost Dashboard</a>
         <a href="http://offenseorbit.000.pe/others/found.php"><i class="fas fa-user-plus"></i> Found dashboard </a>
        <a href="#" onclick="showFeature('profile')"><i class="fas fa-user-circle"></i> Profile Management</a>
    </div>

    <div class="content">
        <!-- Homepage -->
        <div id="homepage">
            <h1>Welcome to the Officer Dashboard</h1>
            <p>Navigate through the sidebar to manage reports, add incidents, or view statistics.</p>
        </div>

   
        <!-- Dashboard Overview -->
        <div id="overview" class="hidden">
            <h3>Dashboard Overview</h3>
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5>Total Reports</h5>
                            <p id="totalReports">0</p> <!-- Display Total Reports -->
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5>Pending Cases</h5>
                            <p id="pendingCases">0</p> <!-- Display Pending Cases -->
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5>Resolved Cases</h5>
                            <p id="resolvedCases">0</p> <!-- Display Resolved Cases -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Crime Report -->
        <div id="addCrimeReport" class="hidden">
            <h3>Add Crime Report</h3>
            <form id="addCrimeForm" enctype="multipart/form-data">
                <div class="mb-3">
                    <select class="form-select" name="incident_type" required>
                        <option value="" disabled selected>Select Incident Type</option>
                        <option value="Theft">Theft</option>
                        <option value="Assault">Assault</option>
                        <option value="Fraud">Fraud</option>
                        <option value="Harassment">Harassment</option>
                        <option value="Vandalism">Vandalism</option>
                        <option value="Robbery">Robbery</option>
                    </select>
                </div>
                <div class="mb-3">
                    <textarea class="form-control" name="incident_description" placeholder="Describe the incident..." rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" name="location" placeholder="Location" required>
                </div>
                <div class="mb-3">
                    <input type="date" class="form-control" name="report_date" required>
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" name="contact_info" placeholder="Contact Information" required>
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" name="reporting_officer" placeholder="Reporting Officer" required>
                </div>
                <div class="mb-3">
                    <select class="form-select" name="priority_level" required>
                        <option value="" disabled selected>Select Priority Level</option>
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="evidence" class="form-label">Upload Evidence (optional):</label>
                    <input type="file" class="form-control" name="evidence" id="evidence">
                </div>
                <button type="submit" class="btn btn-primary">Submit Report</button>
            </form>
        </div>

        <!-- Manage Reports -->
        <div id="manageReports">
            <h3>Manage Reports</h3>
            <table class="table table-dark">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="reportsTable"></tbody>
            </table>
        </div>

        <!-- Edit Case Form -->
        <div id="editCaseForm" class="hidden">
            <h3>Edit Case</h3>
            <form id="caseEditForm">
                <input type="hidden" id="editCaseId" name="case_id">
                <div class="mb-3">
                    <input type="text" class="form-control" id="editIncidentType" name="incident_type" placeholder="Incident Type" required>
                </div>
                <div class="mb-3">
                    <textarea class="form-control" id="editIncidentDescription" name="incident_description" placeholder="Description" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" id="editLocation" name="location" placeholder="Location" required>
                </div>
                <div class="mb-3">
                    <select class="form-select" id="editStatus" name="status" required>
                        <option value="Pending">Pending</option>
                        <option value="Under Investigation">Under Investigation</option>
                        <option value="Resolved">Resolved</option>
                    </select>
                </div>
                <div class="mb-3">
                    <select class="form-select" id="editPriorityLevel" name="priority_level" required>
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Update Case</button>
                <button type="button" class="btn btn-secondary" onclick="cancelEdit()">Cancel</button>
            </form>
        </div>

        <!-- Profile Management -->
        <div id="profile" class="hidden">
            <h3>Profile Management</h3>
            <form id="profileForm">
                <div class="mb-3">
                    <input type="text" class="form-control" name="name" placeholder="Full Name" required>
                </div>
                <div class="mb-3">
                    <input type="email" class="form-control" name="email" placeholder="Email Address" required>
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control" name="password" placeholder="New Password" required>
                </div>
                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
        </div>
    </div>

    <script>
        function showFeature(feature) {
            document.querySelectorAll('.content > div').forEach(div => div.classList.add('hidden'));
            document.getElementById(feature).classList.remove('hidden');
        }

    
    function loadMetrics() {
        axios.get('?fetch_metrics=true')
            .then(response => {
                const data = response.data;
                document.getElementById('totalReports').textContent = data.total_reports || 0;
                document.getElementById('pendingCases').textContent = data.pending_cases || 0;
                document.getElementById('resolvedCases').textContent = data.resolved_cases || 0;
            })
            .catch(error => {
                console.error("Error fetching metrics:", error);
                document.getElementById('totalReports').textContent = 'Error';
                document.getElementById('pendingCases').textContent = 'Error';
                document.getElementById('resolvedCases').textContent = 'Error';
            });
    }

    // Call loadMetrics when the page loads
    document.addEventListener('DOMContentLoaded', loadMetrics);


        // Submit Crime Report Form
        document.getElementById("addCrimeForm").addEventListener("submit", function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append("addCrimeReport", true);
            axios.post("", formData).then(response => {
                alert(response.data.message);
                loadReports(); // Reload reports after submitting
            }).catch(error => {
                console.error(error);
            });
        });

        // Load and display all reports
        function loadReports() {
            axios.get('?fetch_reports=true').then(response => {
                const table = document.getElementById('reportsTable');
                table.innerHTML = '';
                response.data.forEach(report => {
                    const row = document.createElement('tr');
                    row.innerHTML = 
                        `<td>${report.id}</td>
                        <td>${report.incident_type}</td>
                        <td>${report.incident_description}</td>
                        <td>
                            <select class="form-select" id="status-${report.id}">
                                <option value="Pending" ${report.status === 'Pending' ? 'selected' : ''}>Pending</option>
                                <option value="Under Investigation" ${report.status === 'Under Investigation' ? 'selected' : ''}>Under Investigation</option>
                                <option value="Resolved" ${report.status === 'Resolved' ? 'selected' : ''}>Resolved</option>
                            </select>
                        </td>
                        <td><button class="btn btn-primary btn-sm" onclick="updateStatus(${report.id})">Update</button></td>`;
                    table.appendChild(row);
                });
            });
        }

          function loadReports() {
            axios.get('?fetch_reports=true').then(response => {
                const table = document.getElementById('reportsTable');
                table.innerHTML = '';
                response.data.forEach(report => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${report.id}</td>
                        <td>${report.incident_type}</td>
                        <td>${report.incident_description}</td>
                        <td>${report.status}</td>
                        <td>
                            <button class="btn btn-primary btn-sm" onclick="editCase(${report.id}, '${report.incident_type}', '${report.incident_description}', '${report.location}', '${report.status}', '${report.priority_level}')">Edit</button>
                        </td>`;
                    table.appendChild(row);
                });
            });
        }

        function editCase(id, incidentType, description, location, status, priorityLevel) {
            document.getElementById('editCaseForm').classList.remove('hidden');
            document.getElementById('manageReports').classList.add('hidden');
            document.getElementById('editCaseId').value = id;
            document.getElementById('editIncidentType').value = incidentType;
            document.getElementById('editIncidentDescription').value = description;
            document.getElementById('editLocation').value = location;
            document.getElementById('editStatus').value = status;
            document.getElementById('editPriorityLevel').value = priorityLevel;
        }

        function cancelEdit() {
            document.getElementById('editCaseForm').classList.add('hidden');
            document.getElementById('manageReports').classList.remove('hidden');
        }

        document.getElementById('caseEditForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('edit_case', true);
            axios.post('', formData).then(response => {
                alert(response.data.message);
                cancelEdit();
                loadReports();
            }).catch(error => {
                console.error(error);
            });
        });

        document.addEventListener('DOMContentLoaded', loadReports)

        
         function redirectToHomepage() {
            window.location.href = "/"; // Change "/" to your desired homepage URL
        }
    </script>
</body>
</html>
