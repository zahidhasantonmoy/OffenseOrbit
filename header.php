<?php
session_start();

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

// Handle login logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user; // Store user info in session
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            echo "<script>alert('Invalid email or password.');</script>";
        }
    } else {
        echo "<script>alert('Invalid email or password.');</script>";
    }
}

// Handle registration logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($conn->real_escape_string($_POST['password']), PASSWORD_DEFAULT);
    $phone = $conn->real_escape_string($_POST['phone']);
    $address = $conn->real_escape_string($_POST['address']);

    $sql_check_email = "SELECT * FROM users WHERE email='$email'";
    $result_check = $conn->query($sql_check_email);

    if ($result_check->num_rows > 0) {
        echo "<script>alert('Email already exists. Please use a different email.');</script>";
    } else {
        $sql = "INSERT INTO users (name, email, password, role, phone_number, address) 
                VALUES ('$name', '$email', '$password', 'citizen', '$phone', '$address')";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Registration successful! Please login now.');</script>";
        } else {
            echo "<script>alert('Error: " . $conn->error . "');</script>";
        }
    }
}

// Logout logic
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: /");
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OffenseOrbit</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #1c1c28;
            color: #fff;
            margin: 0;
            padding: 0;
        }

        .top-bar {
            background: linear-gradient(90deg, #092931,#3AC8DF,#092931);
            padding: 15px 20px;
            display: flex;
            justify-content: space-evenly;
            align-items: center;
            color: white;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
        }

        .top-bar-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .top-bar .brand {
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: none;
            color: white;
        }

        .top-bar .brand:hover {
            color: #ffd700;
        }

        .top-bar .search-bar {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .top-bar .search-bar input {
            padding: 10px;
            border: none;
            border-radius: 20px;
            outline: none;
            width: 250px;
            transition: all 0.3s;
        }



          .emergency-button {
      position: fixed;
      bottom: 20px;
      left: 20px;
      background-color: red;
      color: white;
      border: none;
      border-radius: 50%;
      width: 60px;
      height: 60px;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      cursor: pointer;
      z-index: 1000;
    }
    .emergency-button:hover {
      background-color: darkred;
    }
    .emergency-icon {
      font-size: 24px;
    }


        .highlight {
            background-color: yellow;
            color: black;
            font-weight: bold;
            padding: 0 2px;
        }

        .popup {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #ff4f4f;
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            font-size: 16px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            animation: fadeInOut 3s ease-in-out;
        }


        .top-bar .search-bar button {
            padding: 10px 15px;
            background: #f3904f;
            border: none;
            color: white;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .top-bar .search-bar button:hover {
            background: #ff7846;
        }

        .top-bar-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .top-bar button {
            background: #f3904f;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
        }

        .top-bar button:hover {
            background: #ff7846;
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            justify-content: center;
            align-items: center;
            z-index: 1001;
        }

        .panel {
            background: white;
            padding: 20px;
            border-radius: 10px;
            width: 400px;
            position: relative;
            text-align: center;
            animation: fadeIn 0.5s ease-in-out;
            z-index: 1002;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .panel input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .panel button {
            width: 100%;
            padding: 10px;
            background: #f3904f;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            transition: all 0.3s;
        }

        .panel button:hover {
            background: #ff7846;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 18px;
            color: #ff7846;
            border: none;
            background: transparent;
            cursor: pointer;
        }

        .close-btn i {
            font-size: 20px;
        }

        .close-btn:hover {
            color: red;
        }

        .welcome-section {
            margin-top: 100px;
            padding: 20px;
            text-align: center;
            position: relative;
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: white;
            animation: hoverEffect 0.3s ease-in-out;
            border-radius: 10px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.5);
        }

        @keyframes hoverEffect {
            0% {
                transform: translateY(10px);
                opacity: 0.5;
            }
            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .welcome-close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: transparent;
            border: none;
            color: #fff;
            font-size: 18px;
            cursor: pointer;
        }

        .welcome-close-btn i {
            font-size: 20px;
        }

        .welcome-close-btn:hover {
            color: red;
        }
    </style>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>


  <button class="emergency-button" onclick="callEmergency()">
    <span class="emergency-icon">🚨</span>
  </button>

  <script>
    // JavaScript function to trigger the emergency call
    function callEmergency() {
      window.location.href = 'tel:999'; // Initiates a phone call to the emergency number
    }
  </script>



    <div class="top-bar">
        <div class="top-bar-left">
            <a href="/" class="brand">OffenseOrbit</a>
           <button onclick="window.location.href='http://offenseorbit.000.pe/others/lost.php'">Lost</button>
<button onclick="window.location.href='http://offenseorbit.000.pe/others/found.php'">Found</button>

            <div class="search-bar">
                <input type="text" id="search" placeholder="Search for text...">
    <button onclick="searchPage()">Search</button>
            </div>
        </div>
        <div class="top-bar-right">
             <?php if (isset($_SESSION['user'])): ?>
                <a href="?logout=true">Logout</a>
                <?php if ($_SESSION['user']['role'] === 'officer'): ?>
                <a href="/dashboard/officer_dashboard.php">
            <button>Officer Dashboard</button>
        </a>
    <?php else: ?>
        <a href="/dashboard/citizen_dashboard.php">
            <button>Citizen Dashboard</button>
        </a>
    <?php endif; ?>
            <?php else: ?>
                <button onclick="showPanel('login')">Login</button>
                <button onclick="showPanel('register')">Sign Up</button>
            <?php endif; ?>
        </div>
    </div>

    <?php if (isset($_SESSION['user'])): ?>
        <div class="welcome-section" id="welcome-section">
            <button class="welcome-close-btn" onclick="closeWelcome()"><i class="fas fa-times"></i></button>
            <h1>Welcome, <?= $_SESSION['user']['name']; ?>!</h1>
            <p>Your role: <?= ucfirst($_SESSION['user']['role']); ?></p>
        </div>
    <?php endif; ?>

    <div class="overlay" id="overlay">
        <div class="panel" id="login-panel">
            <button class="close-btn" onclick="closePanel()"><i class="fas fa-times"></i></button>
            <h2>Login</h2>
            <form method="POST">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login">Login</button>
                <a href="#" style="color: #f3904f; text-decoration: none;">Forgot Password?</a>
            </form>
        </div>
        <div class="panel" id="register-panel" style="display:none;">
            <button class="close-btn" onclick="closePanel()"><i class="fas fa-times"></i></button>
            <h2>Sign Up</h2>
            <form method="POST">
                <input type="text" name="name" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="text" name="phone" placeholder="Phone Number" required>
                <textarea name="address" placeholder="Address" required></textarea>
                <button type="submit" name="register">Sign Up</button>
            </form>
        </div>
    </div>

    <script>
        function showPanel(panel) {
            document.getElementById('overlay').style.display = 'flex';
            document.getElementById(panel + '-panel').style.display = 'block';
        }

        function closePanel() {
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('login-panel').style.display = 'none';
            document.getElementById('register-panel').style.display = 'none';
        }

        function closeWelcome() {
            document.getElementById('welcome-section').style.display = 'none';
        }

    function searchPage() {
    const query = document.getElementById('search').value.trim().toLowerCase();
    const content = document.getElementById('content');

    // Function to recursively remove all highlights
    function removeHighlights(node) {
        if (node.nodeType === 1) { // Element node
            if (node.tagName === 'MARK') {
                const parent = node.parentNode;
                parent.replaceChild(document.createTextNode(node.textContent), node);
                parent.normalize(); // Combine adjacent text nodes
            } else {
                for (let child of node.childNodes) {
                    removeHighlights(child);
                }
            }
        }
    }

    // Remove all existing highlights
    removeHighlights(content);

    if (!query) {
        return; // Exit if no query is provided
    }

    // Highlight matches
    function highlightMatches(node) {
        if (node.nodeType === 3) { // Text node
            const text = node.textContent;
            const index = text.toLowerCase().indexOf(query);
            if (index !== -1) {
                const mark = document.createElement('mark');
                mark.className = 'highlight';
                mark.textContent = text.substring(index, index + query.length);

                const after = document.createTextNode(text.substring(index + query.length));
                const before = document.createTextNode(text.substring(0, index));

                const parent = node.parentNode;
                parent.replaceChild(after, node);
                parent.insertBefore(mark, after);
                parent.insertBefore(before, mark);
            }
        } else if (node.nodeType === 1 && node.tagName !== 'MARK') {
            for (let child of node.childNodes) {
                highlightMatches(child);
            }
        }
    }

    // Apply highlights to the content
    highlightMatches(content);
}


        function showPopup(message) {
            // Create a popup div
            const popup = document.createElement('div');
            popup.className = 'popup';
            popup.textContent = message;

            // Append the popup to the body
            document.body.appendChild(popup);

            // Remove the popup after 3 seconds
            setTimeout(() => {
                popup.remove();
            }, 3000);
        }
    </script>
</body>
</html>
function searchPage() {
    const query = document.getElementById('search').value.trim().toLowerCase();
    const content = document.getElementById('content');

    // Function to recursively remove all highlights
    function removeHighlights(node) {
        if (node.nodeType === 1) { // Element node
            if (node.tagName === 'MARK') {
                const parent = node.parentNode;
                parent.replaceChild(document.createTextNode(node.textContent), node);
                parent.normalize(); // Combine adjacent text nodes
            } else {
                for (let child of node.childNodes) {
                    removeHighlights(child);
                }
            }
        }
    }

    // Remove all existing highlights
    removeHighlights(content);

    if (!query) {
        return; // Exit if no query is provided
    }

    // Highlight matches
    function highlightMatches(node) {
        if (node.nodeType === 3) { // Text node
            const text = node.textContent;
            const index = text.toLowerCase().indexOf(query);
            if (index !== -1) {
                const mark = document.createElement('mark');
                mark.className = 'highlight';
                mark.textContent = text.substring(index, index + query.length);

                const after = document.createTextNode(text.substring(index + query.length));
                const before = document.createTextNode(text.substring(0, index));

                const parent = node.parentNode;
                parent.replaceChild(after, node);
                parent.insertBefore(mark, after);
                parent.insertBefore(before, mark);
            }
        } else if (node.nodeType === 1 && node.tagName !== 'MARK') {
            for (let child of node.childNodes) {
                highlightMatches(child);
            }
        }
    }

    // Apply highlights to the content
    highlightMatches(content);
}


        function showPopup(message) {
            // Create a popup div
            const popup = document.createElement('div');
            popup.className = 'popup';
            popup.textContent = message;

            // Append the popup to the body
            document.body.appendChild(popup);

            // Remove the popup after 3 seconds
            setTimeout(() => {
                popup.remove();
            }, 3000);
        }