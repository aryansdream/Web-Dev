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
    die("Connection failed: {$conn->connect_error}");
}

if (isset($_POST['add_hostel'])) {
    $name = $_POST['name'];
    $location = $_POST['location'];
    $type = $_POST['type'];
    $price = $_POST['price'];

    $sql = "INSERT INTO hostels (name, location, type, price) VALUES ('$name', '$location', '$type', '$price')";
    if ($conn->query($sql) === TRUE) {
        header("Location: adminDashboard.php");
        exit();
    } else {
        echo "Error: {$sql}<br>{$conn->error}";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Hostel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .add-container {
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
        input[type="text"], input[type="number"], select {
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
    <div class="add-container">
        <h2>Add Hostel</h2>
        <form action="add_hostel.php" method="post">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name">
            <label for="location">Location:</label>
            <input type="text" id="location" name="location">
            <label for="type">Type:</label>
            <select id="type" name="type">
                <option value="">Any</option>
                <option value="dormitory">Dormitory</option>
                <option value="private room">Private Room</option>
            </select>
            <label for="price">Price:</label>
            <input type="number" id="price" name="price">
            <input type="submit" name="add_hostel" value="Add Hostel">
        </form>
    </div>
</body>
</html>
