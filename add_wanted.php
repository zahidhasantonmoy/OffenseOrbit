<?php
// Database connection
$host = "sql302.infinityfree.com";
$username = "if0_37729401";
$password = "xOhEjKEk4uwo6AX";
$database = "if0_37729401_crms";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Variables for edit functionality
$id = null;
$name = '';
$crime = '';
$description = '';
$last_known_location = '';
$reward = '';
$image_path = '';

// Handle Edit Request (GET Method)
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $sql = "SELECT * FROM wanted_list WHERE id=$id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $name = $row['name'];
        $crime = $row['crime'];
        $description = $row['description'];
        $last_known_location = $row['last_known_location'];
        $reward = $row['reward'];
        $image_path = $row['image_path'];
    }
}

// Handle Delete Request
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $sql = "DELETE FROM wanted_list WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        echo "<p class='success-message'>Wanted list entry deleted successfully.</p>";
    } else {
        echo "<p class='error-message'>Error: " . $conn->error . "</p>";
    }
}

// Handle Form Submission for Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;
    $name = htmlspecialchars($_POST['name']);
    $crime = htmlspecialchars($_POST['crime']);
    $description = htmlspecialchars($_POST['description']);
    $last_known_location = htmlspecialchars($_POST['last_known_location']);
    $reward = htmlspecialchars($_POST['reward']);
    $image_path = isset($_POST['existing_image']) ? $_POST['existing_image'] : null;

    // Handle Image Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        $image_path = $upload_dir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
    }

    if ($id) {
        // Update record
        $sql = "UPDATE wanted_list 
                SET name='$name', crime='$crime', description='$description', 
                    last_known_location='$last_known_location', reward='$reward', image_path='$image_path' 
                WHERE id=$id";

        if ($conn->query($sql) === TRUE) {
            echo "<p class='success-message'>Wanted list entry updated successfully.</p>";
        } else {
            echo "<p class='error-message'>Error: " . $conn->error . "</p>";
        }
    } else {
        // Insert new record
        $sql = "INSERT INTO wanted_list (name, crime, image_path, description, last_known_location, reward) 
                VALUES ('$name', '$crime', '$image_path', '$description', '$last_known_location', '$reward')";

        if ($conn->query($sql) === TRUE) {
            echo "<p class='success-message'>Wanted list entry added successfully.</p>";
        } else {
            echo "<p class='error-message'>Error: " . $conn->error . "</p>";
        }
    }
}

// Retrieve all entries for display
$sql = "SELECT * FROM wanted_list";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Wanted List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #3498db, #9b59b6);
            color: white;
            margin: 0;
            padding: 0;
            display: flex;
        }

       
        /* Gradient Sidebar */
        .sidebar {
            width: 250px;
            background: linear-gradient(135deg, #3498db, #9b59b6);
            color: white;
            height: 100vh;
            position: fixed;
            padding: 20px 0;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .sidebar a {
            display: block;
            color: white;
            padding: 15px 20px;
            text-decoration: none;
            font-size: 1.1em;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            transition: background-color 0.3s, padding-left 0.3s;
        }

        .sidebar a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            padding-left: 30px;
        }

        .sidebar a i {
            margin-right: 10px;
            font-size: 1.2em;
        }

        .container {
            flex: 1;
            padding: 20px;
            margin: 20px auto;
            max-width: 600px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
            color: #333;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-size: 1.1rem;
            font-weight: bold;
        }

        input, textarea, button {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 1rem;
        }

        textarea {
            resize: none;
            height: 100px;
        }

        button {
            background: #3498db;
            color: white;
            border: none;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.3s ease;
        }

        button:hover {
            background: #2980b9;
            transform: scale(1.05);
        }

        .success-message, .error-message {
            text-align: center;
            font-weight: bold;
            margin: 10px 0;
        }

        .success-message {
            color: #2ecc71;
        }

        .error-message {
            color: #e74c3c;
        }

        .animated-form {
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Menu</h2>
        <a href="/">Go to Homepage</a>
        <a href="/dashboard/officer_dashboard.php">Dashboard</a>
        <a href="?">Add Wanted</a>
        <a href="?view">View Wanted List</a>
    </div>
    <div class="container">
        <?php if (isset($_GET['edit']) || !isset($_GET['view'])): ?>
            <h1><?= $id ? "Edit Wanted Person" : "Add Wanted Person" ?></h1>
            <form method="POST" enctype="multipart/form-data" class="animated-form">
                <input type="hidden" name="id" value="<?= $id ?>">
                <input type="hidden" name="existing_image" value="<?= $image_path ?>">
                
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required value="<?= $name ?>">

                <label for="crime">Crime:</label>
                <input type="text" id="crime" name="crime" required value="<?= $crime ?>">

                <label for="description">Description:</label>
                <textarea id="description" name="description" required><?= $description ?></textarea>

                <label for="last_known_location">Last Known Location:</label>
                <input type="text" id="last_known_location" name="last_known_location" required value="<?= $last_known_location ?>">

                <label for="reward">Reward (in USD):</label>
                <input type="number" id="reward" name="reward" required value="<?= $reward ?>">

                <label for="image">Upload Image:</label>
                <?php if ($image_path): ?>
                    <img src="<?= $image_path ?>" alt="Current Image" style="width: 100px; height: auto;"><br>
                <?php endif; ?>
                <input type="file" id="image" name="image" accept="image/*">

                <button type="submit"><?= $id ? "Update" : "Add" ?></button>
            </form>
        <?php elseif (isset($_GET['view'])): ?>
            <h1>Wanted List</h1>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Crime</th>
                        <th>Description</th>
                        <th>Last Known Location</th>
                        <th>Reward</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= $row['name'] ?></td>
                                <td><?= $row['crime'] ?></td>
                                <td><?= $row['description'] ?></td>
                                <td><?= $row['last_known_location'] ?></td>
                                <td>$<?= $row['reward'] ?></td>
                                <td><img src="<?= $row['image_path'] ?>" alt="Image" style="width: 100px; height: auto;"></td>
                                <td>
                                    <a href="?edit=<?= $row['id'] ?>">Edit</a> |
                                    <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this entry?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">No entries found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
