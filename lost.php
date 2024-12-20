<?php
// Database credentials
$host = "sql302.infinityfree.com";
$username = "if0_37729401";
$password = "xOhEjKEk4uwo6AX";
$database = "if0_37729401_crms";

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$search_results = [];
$message = "";

// Handle form submission for reporting lost items
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["report_item"])) {
    $name = $conn->real_escape_string($_POST["name"]);
    $contact = $conn->real_escape_string($_POST["contact"]);
    $item = $conn->real_escape_string($_POST["item"]);
    $description = $conn->real_escape_string($_POST["description"]);
    $date_lost = $conn->real_escape_string($_POST["date_lost"]);
    $location = $conn->real_escape_string($_POST["location"]);
    $category = $conn->real_escape_string($_POST["category"]);
    $extra_field1 = isset($_POST["extra_field1"]) ? $conn->real_escape_string($_POST["extra_field1"]) : null;
    $extra_field2 = isset($_POST["extra_field2"]) ? $conn->real_escape_string($_POST["extra_field2"]) : null;

    $sql = "INSERT INTO lost_items (name, contact, item, description, date_lost, location, category, extra_field1, extra_field2)
            VALUES ('$name', '$contact', '$item', '$description', '$date_lost', '$location', '$category', '$extra_field1', '$extra_field2')";

    if ($conn->query($sql) === TRUE) {
        $message = "Item reported successfully.";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Initialize variables
$intelligent_matches = [];

// Intelligent Match Logic
$sql = "
    SELECT 
        l.name AS lost_name,
        f.name AS found_name,
        l.contact AS lost_contact,
        f.contact AS found_contact,
        l.item AS lost_item,
        f.item AS found_item,
        l.description AS lost_description,
        f.description AS found_description,
        l.date_lost,
        f.date_found,
        l.location AS lost_location,
        f.location AS found_location,
        l.category AS lost_category,
        f.category AS found_category,
        f.image_path AS found_image,
        (
            (l.item = f.item) * 3 +
            (l.location = f.location) * 2 +
            (l.category = f.category) * 2 +
            (l.description LIKE CONCAT('%', f.description, '%')) * 1 +
            (f.description LIKE CONCAT('%', l.description, '%')) * 1
        ) AS match_score
    FROM lost_items l
    INNER JOIN found_items f
    ON (
        (l.item = f.item OR l.location = f.location OR l.category = f.category OR 
         l.description LIKE CONCAT('%', f.description, '%') OR f.description LIKE CONCAT('%', l.description, '%'))
    )
    HAVING match_score >= 5
    ORDER BY match_score DESC
";

$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $intelligent_matches[] = $row;
    }
}





// Handle form submission for searching lost items
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["search_items"])) {
    $search_query = $conn->real_escape_string($_POST["search_query"]);

    $sql = "SELECT * FROM lost_items WHERE item LIKE '%$search_query%' OR location LIKE '%$search_query%' OR description LIKE '%$search_query%'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $search_results[] = $row;
        }
    } else {
        $message = "No matching items found.";
    }
}

$view_reports = [];
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["view_reports"])) {
    $user_contact = $conn->real_escape_string($_POST["user_contact"]);

    $sql = "SELECT * FROM lost_items WHERE contact = '$user_contact'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $view_reports[] = $row;
        }
    } else {
        $message = "No reports found for the given contact information.";
    }
}



?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offense Orbit Lost </title>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            display: flex;
            height: 100vh;
            overflow: hidden;
            background: linear-gradient(-45deg, #6a11cb, #2575fc, #ff6a00, #f4d03f);
            background-size: 400% 400%;
            animation: gradientAnimation 15s ease infinite;
        }

        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            padding: 20px;
            height: 100%;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px 15px;
            margin: 10px 0;
            font-size: 18px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s ease-in-out;
        }

        .sidebar a:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(255, 255, 255, 0.2);
        }

        .sidebar a img {
            width: 24px;
            height: 24px;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
            font-size: 36px;
            animation: fadeIn 2s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-container, .search-container, .intelligent-match-container,.results-container,.notifications-container {
            display: none;
            animation: slideUp 0.5s ease;
        }



        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-group {
            margin-bottom: 15px;
            animation: fadeIn 1s ease;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        input, select, textarea, button {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            background: #2575fc;
            color: white;
            border: none;
            cursor: pointer;
            transition: background 0.3s ease-in-out, transform 0.2s;
        }

        button:hover {
            background: #6a11cb;
            transform: translateY(-2px);
        }

        .results-container, .notifications-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .result-item, .notification-item {
            margin-bottom: 15px;
            padding: 15px;
            background: rgba(0, 0, 0, 0.05);
            border-radius: 5px;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .result-item:hover, .notification-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        }

        .result-item h3, .notification-item h3 {
            color: #2575fc;
            font-size: 18px;
        }

        .result-item p, .notification-item p {
            color: #555;
            margin: 5px 0;
        }

        /* Additional style to prevent notification overlap */
        .notification-item p {
            word-wrap: break-word;
            word-break: break-word;
            white-space: pre-wrap;
        }

         table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #bdc3c7;
            padding: 10px;
            text-align: left;
        }
        table th {
            background: #34495e;
            color: white;
        }
        .message {
            color: green;
            font-weight: bold;
            text-align: center;
            margin-bottom: 15px;
        }

          .close-button:hover {
            background: #c0392b;
        }
        .hidden {
            display: none;
        }



         .match-block {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        background: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 10px;
        margin-bottom: 20px;
        padding: 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .match-info {
        flex: 1;
        margin-right: 20px;
    }
    .match-info h3 {
        color: #333;
        margin-bottom: 10px;
    }
    .match-info p {
        margin: 5px 0;
        color: #555;
    }
    .match-image {
        flex: 0 0 auto;
        text-align: center;
    }
    .match-image img {
        max-width: 150px;
        border-radius: 10px;
        border: 1px solid #ddd;
    }
    </style>
    <script>
        function handleCategoryChange() {
            const category = document.getElementById("category").value;
            const extraFields = document.getElementById("extra-fields");

            if (category === "electronics") {
                extraFields.innerHTML = `
                    <label for="extra_field1">Device Brand:</label>
                    <input type="text" id="extra_field1" name="extra_field1">
                    <label for="extra_field2">Serial Number:</label>
                    <input type="text" id="extra_field2" name="extra_field2">
                `;
            } else if (category === "documents") {
                extraFields.innerHTML = `
                    <label for="extra_field1">Document Type:</label>
                    <input type="text" id="extra_field1" name="extra_field1">
                    <label for="extra_field2">Issuer:</label>
                    <input type="text" id="extra_field2" name="extra_field2">
                `;
            } else {
                extraFields.innerHTML = ``;
            }
        }
          function closeResults() {
            document.getElementById("results-section").classList.add("hidden");
        }
         
        

    </script>
</head>
<body>
    <div class="sidebar">
        <a href="/">
            <img src="https://img.icons8.com/3d-fluency/94/home.png" alt="Home"><i class="fas fa-home"></i> Homepage
        </a>
        <a href="#" onclick="showSection('form-container')">
            <img width="50" height="50" src="https://img.icons8.com/cute-clipart/50/add-file.png" alt="add-file"/><i class="fas fa-file-alt"></i> Report Lost Item
        </a>
        <a href="#" onclick="showSection('search-container')">
            <img width="50" height="50" src="https://img.icons8.com/papercut/50/search.png" alt="search"/> <i class="fas fa-search"></i> Search Lost Items
        </a>
        <a href="#" onclick="showSection('results-container')">
        <img width="50" height="50" src="https://img.icons8.com/fluency/50/view.png" alt="view"/>  <i class="fas fa-search"></i>View All Reports
        </a>
        <a href="#" onclick="showSection('intelligent-match-container')">
    <img width="50" height="50" src="https://img.icons8.com/fluency/50/idea.png" alt="intelligent-match"/>
    <i class="fas fa-brain"></i> Intelligent Match
</a>

          <a href="#" onclick="showSection('notification')">
          <img width="64" height="64" src="https://img.icons8.com/nolan/64/appointment-reminders.png" alt="appointment-reminders"/>
        <i class="fas fa-bell"></i> Notifications
    </a>
    <a href="#" onclick="showSection('contact-container')">
    <img width="94" height="94" src="https://img.icons8.com/3d-fluency/94/phone-disconnected.png" alt="phone-disconnected"/>
        <i class="fas fa-phone"></i> Contact
    </a>
    </div>
    <div class="main-content">
        <h1 id="page-title">Welcome to the Lost Portal</h1>


      
         <!-- Display Message -->
        <?php if (!empty($message)) : ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <!-- Report Lost Item Section -->
        <div id="form-container" class="form-container">
           <h2>Report a Lost Item</h2>
            <form method="POST">
                <label for="name">Your Name:</label>
                <input type="text" id="name" name="name" required>
                <label for="contact">Contact Information:</label>
                <input type="text" id="contact" name="contact" required>
                <label for="item">Item Name:</label>
                <input type="text" id="item" name="item" required>
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="4" required></textarea>
                <label for="date_lost">Date Lost:</label>
                <input type="date" id="date_lost" name="date_lost" required>
                <label for="location">Location Lost:</label>
                <input type="text" id="location" name="location" required>
                <label for="category">Category:</label>
                <select id="category" name="category" onchange="handleCategoryChange()" required>
                    <option value="">Select a category</option>
                    <option value="electronics">Electronics</option>
                    <option value="documents">Documents</option>
                    <option value="others">Others</option>
                </select>
                <div id="extra-fields"></div>
                <button type="submit" name="report_item">Submit</button>
            </form>
        </div>

        <!-- Search Lost Items Section -->
        <div id="search-container" class="search-container">
            <h2>Search Lost Items</h2>
            <form method="POST">
                <label for="search_query">Search Query:</label>
                <input type="text" id="search_query" name="search_query" required>
                <button type="submit" name="search_items">Search</button>
            </form>
        </div>

        <!-- Display Search Results -->
        <?php if (!empty($search_results)) : ?>
            <div class="results-section">
                <h2>Search Results</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Item</th>
                            <th>Description</th>
                            <th>Date Lost</th>
                            <th>Location</th>
                            <th>Category</th>
                            <th>Extra Field 1</th>
                            <th>Extra Field 2</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($search_results as $result) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($result["name"]); ?></td>
                                <td><?php echo htmlspecialchars($result["contact"]); ?></td>
                                <td><?php echo htmlspecialchars($result["item"]); ?></td>
                                <td><?php echo htmlspecialchars($result["description"]); ?></td>
                                <td><?php echo htmlspecialchars($result["date_lost"]); ?></td>
                                <td><?php echo htmlspecialchars($result["location"]); ?></td>
                                <td><?php echo htmlspecialchars($result["category"]); ?></td>
                                <td><?php echo htmlspecialchars($result["extra_field1"]); ?></td>
                                <td><?php echo htmlspecialchars($result["extra_field2"]); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>


 <div id="intelligent-match-container" class="results-container">
    <h2>Intelligent Match Results</h2>
    <?php if (!empty($intelligent_matches)) : ?>
        <?php foreach ($intelligent_matches as $match) : ?>
            <div class="match-block">
                <div class="match-info">
                    <h3>Matched Item: <?php echo htmlspecialchars($match["lost_item"]); ?></h3>
                    <p><strong>Lost by:</strong> <?php echo htmlspecialchars($match["lost_name"]); ?></p>
                    <p><strong>Contact (Lost):</strong> <?php echo htmlspecialchars($match["lost_contact"]); ?></p>
                    <p><strong>Description (Lost):</strong> <?php echo htmlspecialchars($match["lost_description"]); ?></p>
                    <p><strong>Date Lost:</strong> <?php echo htmlspecialchars($match["date_lost"]); ?></p>
                    <p><strong>Location (Lost):</strong> <?php echo htmlspecialchars($match["lost_location"]); ?></p>
                    <p><strong>Found by:</strong> <?php echo htmlspecialchars($match["found_name"]); ?></p>
                    <p><strong>Contact (Found):</strong> <?php echo htmlspecialchars($match["found_contact"]); ?></p>
                    <p><strong>Description (Found):</strong> <?php echo htmlspecialchars($match["found_description"]); ?></p>
                    <p><strong>Date Found:</strong> <?php echo htmlspecialchars($match["date_found"]); ?></p>
                    <p><strong>Location (Found):</strong> <?php echo htmlspecialchars($match["found_location"]); ?></p>
                    <p><strong>Category:</strong> <?php echo htmlspecialchars($match["lost_category"]); ?></p>
                </div>
                <div class="match-image">
                    <?php if (!empty($match["found_image"])) : ?>
                        <img src="<?php echo htmlspecialchars($match["found_image"]); ?>" alt="Found Item Image" style="width: 150px; height: auto;">
                    <?php else : ?>
                        <p>No Image Available</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else : ?>
        <p>No intelligent matches found.</p>
    <?php endif; ?>
</div>




        <div id="results-container" class="results-container">
            <h2>View All Reports</h2>
            <form method="POST">
                <label for="user_contact">Enter Your Contact Information:</label>
                <input type="text" id="user_contact" name="user_contact" required>
                <button type="submit" name="view_reports">View My Reports</button>
            </form>
        </div>

        

        <!-- Display User Reports -->
        <?php if (!empty($view_reports)) : ?>
            <div class="results-section" id="results-section">
                <button class="close-button" onclick="closeResults()">Close</button>
                <h2>Your Submitted Reports</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Item</th>
                            <th>Description</th>
                            <th>Date Lost</th>
                            <th>Location</th>
                            <th>Category</th>
                            <th>Extra Field 1</th>
                            <th>Extra Field 2</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($view_reports as $report) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($report["name"]); ?></td>
                                <td><?php echo htmlspecialchars($report["contact"]); ?></td>
                                <td><?php echo htmlspecialchars($report["item"]); ?></td>
                                <td><?php echo htmlspecialchars($report["description"]); ?></td>
                                <td><?php echo htmlspecialchars($report["date_lost"]); ?></td>
                                <td><?php echo htmlspecialchars($report["location"]); ?></td>
                                <td><?php echo htmlspecialchars($report["category"]); ?></td>
                                <td><?php echo htmlspecialchars($report["extra_field1"]); ?></td>
                                <td><?php echo htmlspecialchars($report["extra_field2"]); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>





    <div id="notification" class="form-container hidden">
    <h1>No Notifications </h1>
    
</div>
   


<div id="contact-container" class="form-container hidden">
    <h2>Emergency Contacts in Bangladesh</h2>
    <ul>
        <li><strong>Police:</strong> 999</li>
        <li><strong>Fire Service:</strong> 999</li>
        <li><strong>Ambulance:</strong> 999</li>
        <li><strong>National Helpline:</strong> 109</li>
        <li><strong>Women and Child Abuse Prevention:</strong> 10921</li>
    </ul>
</div>




       </div>

    <script>



   

        const reports = [];
        const notifications = [];

        function showSection(sectionId) {
            document.querySelectorAll('.form-container, .search-container, .results-container, .notifications-container, .intelligent-match-container' ).forEach(section => {
                section.style.display = 'none';
            });
            document.getElementById(sectionId).style.display = 'block';
            
        }

        function updateFields() {
            const type = document.getElementById('type').value;
            const customFields = document.getElementById('custom-fields');
            customFields.innerHTML = '';

            if (type === 'phone') {
                customFields.innerHTML = `
                    <div class="form-group">
                        <label for="imei">IMEI Number:</label>
                        <input type="text" id="imei" placeholder="Enter IMEI number">
                    </div>
                    <div class="form-group">
                        <label for="color">Phone Color:</label>
                        <input type="text" id="color" placeholder="Enter phone color">
                    </div>`;
            } else if (type === 'car') {
                customFields.innerHTML = `
                    <div class="form-group">
                        <label for="registration">Registration Number:</label>
                        <input type="text" id="registration" placeholder="Enter car registration number">
                    </div>
                    <div class="form-group">
                        <label for="model">Car Model:</label>
                        <input type="text" id="model" placeholder="Enter car model">
                    </div>
                    <div class="form-group">
                        <label for="color">Car Color:</label>
                        <input type="text" id="color" placeholder="Enter car color">
                    </div>`;
            }
        }

        function submitReport() {
            const name = document.getElementById('name').value || 'Anonymous';
            const type = document.getElementById('type').value;
            const description = document.getElementById('description').value;
            const location = document.getElementById('location').value;
            const contact = document.getElementById('contact').value;

            if (!description || !location || !contact) {
                alert('Please fill in all required fields.');
                return;
            }

            const customData = {};
            if (type === 'phone') {
                customData.imei = document.getElementById('imei')?.value || '';
                customData.color = document.getElementById('color')?.value || '';
            } else if (type === 'car') {
                customData.registration = document.getElementById('registration')?.value || '';
                customData.model = document.getElementById('model')?.value || '';
                customData.color = document.getElementById('color')?.value || '';
            }

            const report = {
                name,
                type,
                description,
                location,
                contact,
                ...customData,
            };

            reports.push(report);
            notifications.push(`New ${type} report submitted: "${description}"`);
            alert('Report submitted successfully!');
            document.getElementById('lost-form').reset();
            document.getElementById('custom-fields').innerHTML = '';
        }

        function loadNotifications() {
            const notificationsContainer = document.getElementById('notifications');
            notificationsContainer.innerHTML = '';

            if (notifications.length === 0) {
                notificationsContainer.innerHTML = '<p>No new notifications.</p>';
                return;
            }

            notifications.forEach(note => {
                const notificationItem = document.createElement('div');
                notificationItem.className = 'notification-item';
                notificationItem.innerHTML = `<p>${note}</p>`;
                notificationsContainer.appendChild(notificationItem);
            });
        }
    </script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
