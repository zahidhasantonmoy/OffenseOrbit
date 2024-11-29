<?php
$host = "sql302.infinityfree.com";
$username = "if0_37729401";
$password = "xOhEjKEk4uwo6AX";
$database = "if0_37729401_crms";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Add User
if (isset($_POST['add_user'])) {
    $name = $_POST['name'];
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $_POST['role'];
    $phone_number = $_POST['phone_number'];
    $nid = $_POST['nid'];
    $security_question = $_POST['security_question'];
    $address = $_POST['address'];
    $occupation = $_POST['occupation'];

    $stmt = $conn->prepare("INSERT INTO users (name, date_of_birth, gender, email, password, role, phone_number, nid, security_question, address, occupation) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssss", $name, $date_of_birth, $gender, $email, $password, $role, $phone_number, $nid, $security_question, $address, $occupation);
    $stmt->execute();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Handle Update User
if (isset($_POST['update_user'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $phone_number = $_POST['phone_number'];
    $nid = $_POST['nid'];
    $security_question = $_POST['security_question'];
    $address = $_POST['address'];
    $occupation = $_POST['occupation'];

    $stmt = $conn->prepare("UPDATE users SET name=?, date_of_birth=?, gender=?, email=?, role=?, phone_number=?, nid=?, security_question=?, address=?, occupation=? WHERE id=?");
    $stmt->bind_param("ssssssssssi", $name, $date_of_birth, $gender, $email, $role, $phone_number, $nid, $security_question, $address, $occupation, $id);
    $stmt->execute();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Fetch Users
$users = $conn->query("SELECT * FROM users");

// Delete User
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM users WHERE id=$id");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Fetch User for Edit
$edit_user = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = $conn->query("SELECT * FROM users WHERE id=$id");
    $edit_user = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
            height: 100vh;
        }
        .sidebar {
            width: 250px;
            background: #2c3e50;
            color: white;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            background: #34495e;
            text-align: center;
        }
        .sidebar a:hover {
            background: #1abc9c;
        }
        .content {
            flex: 1;
            padding: 20px;
            background: #ecf0f1;
            overflow-y: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        table th {
            background: #34495e;
            color: white;
        }
        input, select, textarea, button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background: #3498db;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background: #2980b9;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <a href="?">View All Users</a>
        <a href="?add">Add User</a>
    </div>
    <div class="content">
        <?php if (isset($_GET['add']) || isset($edit_user)): ?>
            <h2><?php echo isset($edit_user) ? 'Edit User' : 'Add New User'; ?></h2>
            <form method="post">
                <?php if (isset($edit_user)): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_user['id']; ?>">
                <?php endif; ?>
                <input type="text" name="name" placeholder="Name" value="<?php echo $edit_user['name'] ?? ''; ?>" required>
                <input type="date" name="date_of_birth" value="<?php echo $edit_user['date_of_birth'] ?? ''; ?>">
                <select name="gender">
                    <option value="">Select Gender</option>
                    <option value="Male" <?php echo (isset($edit_user) && $edit_user['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?php echo (isset($edit_user) && $edit_user['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                    <option value="Other" <?php echo (isset($edit_user) && $edit_user['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                </select>
                <input type="email" name="email" placeholder="Email" value="<?php echo $edit_user['email'] ?? ''; ?>" required>
                <input type="text" name="phone_number" placeholder="Phone Number" value="<?php echo $edit_user['phone_number'] ?? ''; ?>">
                <input type="text" name="nid" placeholder="NID" value="<?php echo $edit_user['nid'] ?? ''; ?>" required>
                <textarea name="security_question" placeholder="Security Question"><?php echo $edit_user['security_question'] ?? ''; ?></textarea>
                <textarea name="address" placeholder="Address"><?php echo $edit_user['address'] ?? ''; ?></textarea>
                <input type="text" name="occupation" placeholder="Occupation" value="<?php echo $edit_user['occupation'] ?? ''; ?>">
                <select name="role">
                    <option value="citizen" <?php echo (isset($edit_user) && $edit_user['role'] == 'citizen') ? 'selected' : ''; ?>>Citizen</option>
                    <option value="officer" <?php echo (isset($edit_user) && $edit_user['role'] == 'officer') ? 'selected' : ''; ?>>Officer</option>
                </select>
                <button type="submit" name="<?php echo isset($edit_user) ? 'update_user' : 'add_user'; ?>">
                    <?php echo isset($edit_user) ? 'Update User' : 'Add User'; ?>
                </button>
            </form>
        <?php else: ?>
            <h2>All Users</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>NID</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $users->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['phone_number']; ?></td>
                            <td><?php echo $row['nid']; ?></td>
                            <td><?php echo $row['role']; ?></td>
                            <td>
                                <a href="?edit=<?php echo $row['id']; ?>">Edit</a>
                                <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
