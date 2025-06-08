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
    $id = $_GET['id'];

    $sql = "SELECT * FROM hostels WHERE id = '$id'";
    $result = $conn->query($sql);
    $hostel = $result->fetch_assoc();
}

if (isset($_POST['edit_hostel'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $location = $_POST['location'];
    $type = $_POST['type'];

    $sql = "UPDATE hostels SET name = '$name', location = '$location', type = '$type' WHERE id = '$id'";
    if ($conn->query($sql) === TRUE) {
        echo "Hostel updated successfully";
        header("Location: admin_dashboard.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Hostel</title>
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
        input[type="text"], select {
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
    </style>
</head>
<body>
    <div class="edit-container">
        <h2>Edit Hostel</h2>
        <form action="" method="post">
            <input type="hidden" name="id" value="<?php echo $hostel['id']; ?>">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo $hostel['name']; ?>">
            <label for="location">Location:</label>
            <input type="text" id="location" name="location" value="<?php echo $hostel['location']; ?>">
            <label for="type">Type:</label>
            <select id="type" name="type">
                <option value="" <?php if($hostel['type'] == '') echo 'selected'; ?>>Any</option>
                <option value="dormitory" <?php if($hostel['type'] == 'dormitory') echo 'selected'; ?>>Dormitory</option>
                <option value="private room" <?php if($hostel['type'] == 'private room') echo 'selected'; ?>>Private Room</option>
            </select>
            <input type="submit" name="edit_hostel" value="Edit Hostel">
        </form>
    </div>
</body>
</html>

