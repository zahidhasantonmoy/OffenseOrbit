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

// Fetch notifications
$notifications = $conn->query("SELECT * FROM notifications ORDER BY created_at DESC");

// Count unread notifications
$unread_count_result = $conn->query("SELECT COUNT(*) AS unread_count FROM notifications WHERE is_read = 0");
$unread_count = $unread_count_result->fetch_assoc()['unread_count'];

// Clear all notifications
if (isset($_POST['clear_all'])) {
    $conn->query("DELETE FROM notifications");
    echo json_encode(['success' => true]); // Respond with JSON to indicate success
    exit;
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
    $nid = $conn->real_escape_string($_POST['nid']);
    $gender = $conn->real_escape_string($_POST['gender']);
    $dob = $conn->real_escape_string($_POST['date_of_birth']);

    // Check for existing email or NID
    $sql_check = "SELECT * FROM users WHERE email='$email' OR nid='$nid'";
    $result_check = $conn->query($sql_check);

    if ($result_check->num_rows > 0) {
        echo "<script>alert('Email or NID already exists. Please use a different one.');</script>";
    } else {
        // Insert data into the database
        $sql = "INSERT INTO users (name, email, password, phone_number, nid, gender, date_of_birth) 
                VALUES ('$name', '$email', '$password', '$phone', '$nid', '$gender', '$dob')";
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
            background: #2dc970;
            border: none;
            color: white;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .top-bar .search-bar button:hover {
            background: #07822bf7;
        }

        .top-bar-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .top-bar button {
            background: #2dc970;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
        }

        .top-bar button:hover {
            background: #07822bf7;
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
    top: 10px; /* Top position */
    right: 10px; /* Right position */
    font-size: 24px; /* Adjust the size of the icon */
    color: #ff7846; /* Icon color */
    border: none;
    background: transparent; /* Transparent background */
    cursor: pointer;
    z-index: 10; /* Ensure it is on top */
}

.close-btn i {
    font-size: 24px;
}

.close-btn:hover {
    color: red; /* Change color on hover */
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
        .logout-button {
    background: #ff4f4f; /* Red color for logout */
    color: white;
    padding: 8px 15px;
    border: none;
    border-radius: 20px;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.3s;
    text-decoration: none; /* Removes underline from the link */
    display: inline-block; /* Makes the link look like a button */
}

.logout-button:hover {
    background: #e04343; /* Darker red on hover */
}
  .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #f3904f;
        }




.notification-icon {
    position: relative;
    cursor: pointer;
}

.notification-icon img {
    width: 30px;
    height: 30px;
}

.notification-icon span {
    background: #e74c3c;
    color: white;
    border-radius: 50%;
    font-size: 12px;
    padding: 4px;
    position: absolute;
    top: -5px;
    right: -5px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

.dropdown {
    display: none;
    position: fixed;
    top: 60px;
    right: 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
    width: 350px;
    max-height: 400px;
    overflow-y: auto;
    z-index: 1000;
    opacity: 0;
    transform: translateY(-10px);
    transition: all 0.3s ease-in-out;
}

.dropdown.active {
    display: block;
    opacity: 1;
    transform: translateY(0);
}

.dropdown .close-btn {
    background: transparent;
    border: none;
    font-size: 20px;
    color: #e74c3c;
    position: absolute;
    top: 10px;
    right: 10px;
    cursor: pointer;
}

.dropdown h3 {
    margin: 0;
    padding: 15px;
    background: #3498db;
    color: white;
    font-size: 18px;
    text-align: center;
    border-bottom: 1px solid #ddd;
}

.dropdown ul {
    list-style: none;
    margin: 0;
    padding: 15px;
}

.dropdown ul li {
    padding: 10px;
    border-bottom: 1px solid #f0f0f0;
    display: flex;
    flex-direction: column;
}

.dropdown ul li:last-child {
    border-bottom: none;
}

.dropdown ul li .notification-content {
    display: flex;
    flex-direction: column;
}

.dropdown ul li .notification-message {
    font-weight: bold;
    color: #333;
}

.dropdown ul li .notification-time {
    font-size: 12px;
    color: #7f8c8d;
}

.clear-all-btn {
    background: #e74c3c;
    color: white;
    border: none;
    padding: 10px;
    border-radius: 5px;
    width: 100%;
    cursor: pointer;
    font-size: 14px;
    margin: 10px 0;
}

.clear-all-btn:hover {
    background: #c0392b;
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

<div class="notification-icon" onclick="toggleDropdown()">
    <img src="https://cdn-icons-png.flaticon.com/512/1827/1827279.png" alt="Notifications">
    <?php if ($unread_count > 0): ?>
        <span><?php echo $unread_count; ?></span>
    <?php endif; ?>
</div>

<div class="dropdown" id="notificationDropdown">
    <button class="close-btn" onclick="toggleDropdown()">&times;</button>
    <h3>Notifications</h3>
    <ul>
        <?php while ($row = $notifications->fetch_assoc()): ?>
            <li>
                <div class="notification-content">
                    <span class="notification-message"><?php echo $row['message']; ?></span>
                    <span class="notification-time"><?php echo date("d M Y, H:i", strtotime($row['created_at'])); ?></span>
                </div>
            </li>
        <?php endwhile; ?>
    </ul>
    <form method="POST" id="clearAllForm">
    <button type="button" class="clear-all-btn" id="clearAllBtn">Clear All</button>
</form>

</div>



        <div class="top-bar-right">
             <?php if (isset($_SESSION['user'])): ?>
               <a href="?logout=true" class="logout-button">Logout</a>

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
            <button class="welcome-close-btn" onclick="closeWelcome()">
    <i class="fas fa-times"></i>
</button>

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
            <div style="position: relative;">
                <input type="password" id="password" name="password" placeholder="Password" required>
                <i class="fas fa-eye password-toggle" id="toggle-password" onclick="togglePassword()"></i>
            </div>
            <button type="submit" name="login">Login</button>
            <a href="#" style="color: #f3904f; text-decoration: none;">Forgot Password?</a>
        </form>
    </div>
    <div class="panel" id="register-panel" style="display:none;">
        <button class="close-btn" onclick="closePanel()"><i class="fas fa-times"></i></button>
        <h2>Sign Up</h2>
         <h2>Sign Up</h2>
        <form method="POST">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="phone" placeholder="Phone Number" required>
            <input type="text" name="nid" placeholder="National ID (NID)" required>
            <select name="gender" required>
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>
            <input type="date" name="date_of_birth" required>
            <input type="password" name="password" placeholder="Password" required>
           
            <button type="submit" name="register">Sign Up</button>
        </form>
    </div>
</div>

    <script>

document.getElementById('clearAllBtn').addEventListener('click', function () {
    fetch('', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'clear_all=true',
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Clear notifications from the UI
                const notificationList = document.querySelector('.dropdown ul');
                notificationList.innerHTML = '<li>No notifications available.</li>';

                // Update the unread count
                const unreadCount = document.querySelector('.notification-icon span');
                if (unreadCount) {
                    unreadCount.textContent = '0';
                }

                alert('All notifications cleared successfully!');
            } else {
                alert('Failed to clear notifications.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
});




   function toggleDropdown() {
    const dropdown = document.getElementById('notificationDropdown');
    dropdown.classList.toggle('active');
}

// Close dropdown when clicking outside
document.addEventListener('click', function (event) {
    const dropdown = document.getElementById('notificationDropdown');
    const notificationIcon = document.querySelector('.notification-icon');

    // Check if click is outside dropdown or notification icon
    if (!dropdown.contains(event.target) && !notificationIcon.contains(event.target)) {
        dropdown.classList.remove('active');
    }
});


// Show/hide password toggle
    function togglePassword() {
        var passwordField = document.getElementById("password");
        var toggleIcon = document.getElementById("toggle-password");
        
        if (passwordField.type === "password") {
            passwordField.type = "text";
            toggleIcon.classList.remove("fa-eye");
            toggleIcon.classList.add("fa-eye-slash");
        } else {
            passwordField.type = "password";
            toggleIcon.classList.remove("fa-eye-slash");
            toggleIcon.classList.add("fa-eye");
        }
    }



    function showPanel(panel) {
    // Hide all panels initially
    document.getElementById('login-panel').style.display = 'none';
    document.getElementById('register-panel').style.display = 'none';
    document.getElementById('overlay').style.display = 'flex';

    // Show the requested panel
    if (panel === 'login') {
        document.getElementById('login-panel').style.display = 'block';
    } else if (panel === 'register') {
        document.getElementById('register-panel').style.display = 'block';
    }
}

function closePanel() {
    // Hide the overlay and all panels
    document.getElementById('overlay').style.display = 'none';
    document.getElementById('login-panel').style.display = 'none';
    document.getElementById('register-panel').style.display = 'none';
}




function closeWelcome() {
    const welcomeSection = document.getElementById('welcome-section');
    if (welcomeSection) {
        welcomeSection.style.display = 'none';
    }
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