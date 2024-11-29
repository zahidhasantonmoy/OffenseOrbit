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
    $result = $conn->query("SELECT * FROM crime_reports ORDER BY report_date ASC");
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


// Fetch all notifications
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['fetch_notifications'])) {
    $result = $conn->query("SELECT * FROM notifications ORDER BY created_at DESC");
    $notifications = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($notifications);
    exit;
}

// Clear all notifications
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_notifications'])) {
    $conn->query("DELETE FROM notifications");
    echo json_encode(['success' => true, 'message' => 'All notifications cleared successfully.']);
    exit;
}

// Add a new notification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_notification'])) {
    $message = trim($_POST['message']);
    if (!empty($message)) {
        $stmt = $conn->prepare("INSERT INTO notifications (message) VALUES (?)");
        $stmt->bind_param("s", $message);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Notification added successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add notification.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Notification message cannot be empty.']);
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
        /* Sidebar styles */
.sidebar a {
    color: white;
    text-decoration: none;
    padding: 10px;
    display: flex;
    align-items: center;
    border-radius: 5px;
    transition: background-color 0.3s, transform 0.3s;
}

.sidebar a:hover {
    background-color: #34495e;
    transform: scale(1.05);
}

/* Animated background */
body {
    display: flex;
    margin: 0;
    font-family: Arial, sans-serif;
    background: linear-gradient(-45deg, #1e3c72, #2a5298, #4776e6, #8e44ad);
    background-size: 400% 400%;
    animation: gradientBG 15s ease infinite;
    color: white;
}

/* Background animation keyframes */
@keyframes gradientBG {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* Icon customization */
.sidebar a i {
    margin-right: 10px;
    transition: transform 0.3s;
}

.sidebar a:hover i {
    transform: rotate(20deg);
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
    <a href="#" onclick="showFeature('overview')"><i class="fas fa-chart-bar"></i> Dashboard Overview</a>
    <a href="#" onclick="showFeature('addCrimeReport')"><i class="fas fa-file-alt"></i> Add Report</a>
    <a href="#" onclick="showFeature('manageReports')"><i class="fas fa-tasks"></i> Manage Reports</a>
    <a href="http://offenseorbit.000.pe/others/add_wanted.php"><i class="fas fa-user-secret"></i> Add Wanted</a>

    <a href="http://offenseorbit.000.pe/others/ad_search.php"><i class="fas fa-search"></i> Advance Search</a>
  <a href="http://offenseorbit.000.pe/others/add_criminal.php"><i class="fas fa-user-secret"></i> Add Criminal Data</a>
      <a href="#" onclick="showFeature('notifications')"><i class="fas fa-bell"></i> Notifications</a>
    <a href="http://offenseorbit.000.pe/others/lost.php"><i class="fas fa-question-circle"></i> Lost</a>
    <a href="http://offenseorbit.000.pe/others/found.php"><i class="fas fa-check-circle"></i> Found</a>
  

    <a href="http://offenseorbit.000.pe/others/add_news.php"><i class="fas fa-newspaper"></i> Add News</a>
    <a href="#" onclick="showFeature('profile')"><i class="fas fa-user-cog"></i> Profile</a>
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


<!-- Notifications Management -->
<div id="notifications" class="hidden">
    <h3>Notifications</h3>
    <div class="d-flex justify-content-between mb-3">
        <button class="btn btn-success" onclick="showAddNotificationForm()">Add Notification</button>
        <button class="btn btn-danger" onclick="clearAllNotifications()">Clear All Notifications</button>
    </div>
    <div id="addNotificationForm" class="hidden mb-3">
        <form id="notificationForm">
            <input type="text" class="form-control mb-2" name="message" placeholder="Enter notification message" required>
            <button type="submit" class="btn btn-primary">Submit Notification</button>
            <button type="button" class="btn btn-secondary" onclick="hideAddNotificationForm()">Cancel</button>
        </form>
    </div>
    <ul id="notificationList" class="list-group"></ul>
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

// Show Add Notification Form
function showAddNotificationForm() {
    document.getElementById('addNotificationForm').classList.remove('hidden');
}

// Hide Add Notification Form
function hideAddNotificationForm() {
    document.getElementById('addNotificationForm').classList.add('hidden');
}

// Load Notifications
function loadNotifications() {
    axios.get('?fetch_notifications=true').then(response => {
        const notificationList = document.getElementById('notificationList');
        notificationList.innerHTML = '';
        response.data.forEach(notification => {
            const listItem = document.createElement('li');
            listItem.className = 'list-group-item d-flex justify-content-between align-items-center';
            listItem.innerHTML = `
                <span>${notification.message}</span>
                <small>${new Date(notification.created_at).toLocaleString()}</small>
            `;
            notificationList.appendChild(listItem);
        });
    }).catch(error => {
        console.error('Error loading notifications:', error);
    });
}

// Add a New Notification
document.getElementById('notificationForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('add_notification', true);
    axios.post('', formData).then(response => {
        alert(response.data.message);
        if (response.data.success) {
            hideAddNotificationForm();
            loadNotifications();
        }
    }).catch(error => {
        console.error('Error adding notification:', error);
    });
});

// Clear All Notifications
function clearAllNotifications() {
    axios.post('', { clear_notifications: true }).then(response => {
        alert(response.data.message);
        if (response.data.success) {
            loadNotifications();
        }
    }).catch(error => {
        console.error('Error clearing notifications:', error);
    });
}

// Load Notifications on Page Load
document.addEventListener('DOMContentLoaded', loadNotifications);














    document.querySelectorAll('.sidebar a').forEach(link => {
    link.addEventListener('click', () => {
        document.querySelectorAll('.sidebar a').forEach(el => el.classList.remove('active'));
        link.classList.add('active');
    });
});

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
