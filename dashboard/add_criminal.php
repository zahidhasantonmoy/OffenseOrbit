<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
$host = "sql302.infinityfree.com";
$username = "if0_37729401";
$password = "xOhEjKEk4uwo6AX";
$database = "if0_37729401_crms";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission for adding data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $nid_no = htmlspecialchars($_POST['nid_no']);
    $phone_no = htmlspecialchars($_POST['phone_no']);
    $home_district = htmlspecialchars($_POST['home_district']);
    $crime_format = htmlspecialchars($_POST['crime_format']);
    $crime_count = (int)$_POST['crime_count'];
    $wanted_status = htmlspecialchars($_POST['wanted_status']);
    $case_id = htmlspecialchars($_POST['case_id']);

    $query = "INSERT INTO criminal_data (name, nid_no, phone_no, home_district, crime_format, crime_count, wanted_status, case_id) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssiis", $name, $nid_no, $phone_no, $home_district, $crime_format, $crime_count, $wanted_status, $case_id);

    if ($stmt->execute()) {
        $success_message = "Data added successfully!";
    } else {
        $error_message = "Error adding data: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Data</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <style>
        /* Include the styles from your Advanced Search page */
        /* Sidebar and other styles copied for consistency */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #ff9a9e, #fad0c4, #fbc2eb, #a18cd1, #fad0c4);
            background-size: 300% 300%;
            animation: gradientShift 10s ease infinite;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .sidebar {
            width: 250px;
            background: linear-gradient(135deg, #3498db, #9b59b6);
            color: white;
            height: 100vh;
            position: fixed;
            padding: 20px 10px;
            transition: width 0.3s ease;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <button onclick="toggleSidebar()">☰</button>
    <h2>Menu</h2>
    <a href="/"><i class="fas fa-home"></i> <span>Homepage</span></a>
    <a href="/officer_dashboard.php"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a>
</div>

<div class="main-content">
    <div class="container">
        <h1>Add Data</h1>
        <?php if (!empty($success_message)) : ?>
            <div class="alert alert-success"><?= $success_message; ?></div>
        <?php elseif (!empty($error_message)) : ?>
            <div class="alert alert-danger"><?= $error_message; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="nid_no" class="form-label">NID</label>
                <input type="text" class="form-control" id="nid_no" name="nid_no" required>
            </div>
            <div class="mb-3">
                <label for="phone_no" class="form-label">Phone</label>
                <input type="text" class="form-control" id="phone_no" name="phone_no" required>
            </div>
            <div class="mb-3">
                <label for="home_district" class="form-label">Home District</label>
                <input type="text" class="form-control" id="home_district" name="home_district" required>
            </div>
            <div class="mb-3">
                <label for="crime_format" class="form-label">Crime Format</label>
                <input type="text" class="form-control" id="crime_format" name="crime_format" required>
            </div>
            <div class="mb-3">
                <label for="crime_count" class="form-label">Crime Count</label>
                <input type="number" class="form-control" id="crime_count" name="crime_count" required>
            </div>
            <div class="mb-3">
                <label for="wanted_status" class="form-label">Wanted Status</label>
                <input type="text" class="form-control" id="wanted_status" name="wanted_status" required>
            </div>
            <div class="mb-3">
                <label for="case_id" class="form-label">Case ID</label>
                <input type="text" class="form-control" id="case_id" name="case_id" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Data</button>
        </form>
    </div>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.querySelector(".sidebar");
        const mainContent = document.querySelector(".main-content");
        sidebar.classList.toggle("collapsed");
        mainContent.classList.toggle("collapsed");
    }
</script>

</body>
</html>
