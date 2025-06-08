
<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit();
}

if (isset($_GET['id'])) {
    $db_host = 'localhost';
    $db_username = 'root';
    $db_password = '';
    $db_name = 'user_data';

    $conn = new mysqli($db_host, $db_username, $db_password, $db_name);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $id = intval($_GET['id']);
    $sql = "DELETE FROM hostels WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        header("Location: adminDashboard.php");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
    $conn->close();
} else {
    header("Location: adminDashboard.php");
    exit();
}