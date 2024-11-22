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

// Fetch Dashboard Metrics
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['fetch_metrics'])) {
    $result = $conn->query("
        SELECT 
            (SELECT COUNT(*) FROM crime_reports) AS total_reports,
            (SELECT COUNT(*) FROM crime_reports WHERE status = 'Pending') AS pending_cases,
            (SELECT COUNT(*) FROM crime_reports WHERE status = 'Resolved') AS resolved_cases
    ");
    echo json_encode($result->fetch_assoc());
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

// Update Case Status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_case_status'])) {
    $case_id = $_POST['case_id'];
    $new_status = $_POST['new_status'];

    if (!empty($case_id) && !empty($new_status)) {
        // Prepare and bind SQL query to update status
        $query = $conn->prepare("
            UPDATE crime_reports 
            SET status = ?, last_updated = NOW() 
            WHERE id = ?
        ");
        $query->bind_param("si", $new_status, $case_id);

        if ($query->execute()) {
            echo json_encode(['success' => true, 'message' => 'Case status updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update case status.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid case ID or status.']);
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
            background-color: #f4f4f9;
        }
        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            color: white;
            height: 100vh;
            position: fixed;
            padding: 20px;
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
        <a href="#" onclick="showFeature('overview')"><i class="fas fa-chart-line"></i> Dashboard Overview</a>
        <a href="#" onclick="showFeature('addCrimeReport')"><i class="fas fa-plus-circle"></i> Add Crime Report</a>
        <a href="#" onclick="showFeature('manageReports')"><i class="fas fa-clipboard-list"></i> Manage Reports</a>
        <a href="#" onclick="showFeature('profile')"><i class="fas fa-user-circle"></i> Profile Management</a>
    </div>

    <div class="content">
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
        <div id="manageReports" class="hidden">
            <h3>Manage Reports</h3>
            <table class="table">
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

        // Fetch and display dashboard metrics
        function loadMetrics() {
            axios.get('?fetch_metrics=true').then(response => {
                document.getElementById('totalReports').textContent = response.data.total_reports;
                document.getElementById('pendingCases').textContent = response.data.pending_cases;
                document.getElementById('resolvedCases').textContent = response.data.resolved_cases;
            }).catch(error => {
                console.error("Error fetching metrics:", error);
            });
        }

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

        // Update status of a case
        function updateStatus(caseId) {
            const newStatus = document.getElementById(`status-${caseId}`).value;
            axios.post("", {
                update_case_status: true,
                case_id: caseId,
                new_status: newStatus
            }).then(response => {
                alert(response.data.message);
                loadReports();
            }).catch(error => {
                console.error(error);
            });
        }

        // Load reports and metrics on page load
        document.addEventListener("DOMContentLoaded", function() {
            loadMetrics(); // Load metrics when the page loads
            loadReports(); // Load the crime reports
        });
    </script>
</body>
</html>
