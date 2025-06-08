
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

    // Get booking info to update available_rooms
    $booking_id = intval($_GET['id']);
    $booking_sql = "SELECT hostel_id FROM bookings WHERE id = $booking_id";
    $booking_result = $conn->query($booking_sql);

    if ($booking_result && $booking_result->num_rows > 0) {
        $booking = $booking_result->fetch_assoc();
        $hostel_id = intval($booking['hostel_id']);

        // Delete booking
        $sql = "DELETE FROM bookings WHERE id = $booking_id";
        if ($conn->query($sql) === TRUE) {
            // Increment available_rooms
            $conn->query("UPDATE hostels SET available_rooms = available_rooms + 1 WHERE id = $hostel_id");
            header("Location: adminDashboard.php");
            exit();
        } else {
            echo "Error deleting booking: " . $conn->error;
        }
    } else {
        header("Location: adminDashboard.php");
        exit();
    }
    $conn->close();
} else {
    header("Location: adminDashboard.php");
    exit();
}