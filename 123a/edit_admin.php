
<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit();
}

$db_host = 'localhost';
$db_username = 'root';
$db_password = '';
$db_name = 'user_data';

$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM admins WHERE id = $id";
    $result = $conn->query($sql);
    $admin = $result->fetch_assoc();
}

if (isset($_POST['edit_admin'])) {
    $id = intval($_POST['id']);
    $username = $_POST['username'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : null;

    if ($password) {
        $sql = "UPDATE admins SET username = '$username', password = '$password' WHERE id = $id";
    } else {
        $sql = "UPDATE admins SET username = '$username' WHERE id = $id";
    }

    if ($conn->query($sql) === TRUE) {
        header("Location: adminDashboard.php");
        exit();
    } else {
        echo "Error updating admin: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .edit-container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            padding: 32px 24px;
            max-width: 400px;
            margin: 40px auto;
        }
        h2 {
            text-align: center;
            color: #2c3e50;
        }
        label {
            display: block;
            margin-bottom: 6px;
            color: #333;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 8px 10px;
            margin-bottom: 18px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1em;
        }
        input[type="submit"] {
            background: #007bff;
            color: #fff;
            border: none;
            padding: 10px 0;
            width: 100%;
            border-radius: 4px;
            font-size: 1em;
            cursor: pointer;
            transition: background 0.2s;
        }
        input[type="submit"]:hover {
            background: #0056b3;
        }
        .note {
            font-size: 0.95em;
            color: #888;
            margin-bottom: 12px;
        }
    </style>
</head>
<body>
    <div class="edit-container">
        <h2>Edit Admin</h2>
        <form action="edit_admin.php?id=<?php echo $admin['id']; ?>" method="post">
            <input type="hidden" name="id" value="<?php echo $admin['id']; ?>">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($admin['username']); ?>">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Leave blank to keep current password">
            <div class="note">Leave password blank if you do not want to change it.</div>
            <input type="submit" name="edit_admin" value="Update Admin">
        </form>
    </div>
</body>
</html>