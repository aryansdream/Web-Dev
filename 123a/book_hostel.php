<?php
session_start();

$db_host = 'localhost';
$db_username = 'root';
$db_password = '';
$db_name = 'user_data';

$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$message = "";
$available_rooms = 0;
$available_types = ['dormitory', 'private room']; // default

if (isset($_GET['id'])) {
    $hostel_id = intval($_GET['id']);
    $user_id = intval($_SESSION['user_id']);

    // Fetch available rooms and type for this hostel
    $hostel_sql = "SELECT available_rooms, type FROM hostels WHERE id = $hostel_id";
    $hostel_result = $conn->query($hostel_sql);
    if ($hostel_result && $hostel_result->num_rows > 0) {
        $hostel = $hostel_result->fetch_assoc();
        $available_rooms = intval($hostel['available_rooms']);
        // If type is 'any', allow both, else only the available type
        if (strtolower($hostel['type']) === 'any') {
            $available_types = ['dormitory', 'private room'];
        } else {
            $available_types = [strtolower($hostel['type'])];
        }
    }

    // If a room_type is passed via GET (from search.php), pre-select it in the form
    $selected_room_type = '';
    if (isset($_GET['room_type']) && in_array(strtolower($_GET['room_type']), $available_types)) {
        $selected_room_type = strtolower($_GET['room_type']);
    }

    // If form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_hostel'])) {
        $checkin_date = $_POST['checkin_date'];
        $room_type = $_POST['room_type'];

        // Check if rooms are available
        if ($available_rooms <= 0) {
            $message = "No rooms available for booking.";
        } elseif (!in_array($room_type, $available_types)) {
            $message = "Selected room type is not available for this hostel.";
        } else {
            // Optional: Check if already booked for this hostel, date, and room type
            $check = $conn->query("SELECT * FROM bookings WHERE user_id = $user_id AND hostel_id = $hostel_id AND checkin_date = '$checkin_date' AND room_type = '$room_type'");
            if ($check && $check->num_rows > 0) {
                $message = "You have already booked this hostel for the selected date and room type.";
            } else {
                $sql = "INSERT INTO bookings (user_id, hostel_id, booking_date, checkin_date, room_type) VALUES ($user_id, $hostel_id, NOW(), '$checkin_date', '$room_type')";
                if ($conn->query($sql) === TRUE) {
                    // Decrement available rooms
                    $conn->query("UPDATE hostels SET available_rooms = available_rooms - 1 WHERE id = $hostel_id");
                    $message = "Booking successful!";
                    $available_rooms--;
                } else {
                    $message = "Error: " . $conn->error;
                }
            }
        }
    }
} else {
    $message = "Invalid hostel selection.";
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Hostel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .book-container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            padding: 32px 24px;
            max-width: 400px;
            margin: 60px auto;
            text-align: center;
        }
        .book-container a {
            display: inline-block;
            margin-top: 18px;
            background: #007bff;
            color: #fff;
            padding: 10px 22px;
            border-radius: 4px;
            text-decoration: none;
            transition: background 0.2s;
        }
        .book-container a:hover {
            background: #0056b3;
        }
        .message {
            font-size: 1.1em;
            color: #2c3e50;
            margin-bottom: 18px;
        }
        form {
            margin-top: 18px;
        }
        label {
            display: block;
            margin-bottom: 6px;
            color: #333;
            text-align: left;
        }
        input[type="date"], select {
            width: 100%;
            padding: 8px 10px;
            margin-bottom: 16px;
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
        .available-rooms {
            font-size: 1.05em;
            color: #28a745;
            margin-bottom: 12px;
        }
    </style>
</head>
<body>
    <div class="book-container">
        <?php if (!empty($message)) { ?>
            <div class="message"><?php echo $message; ?></div>
            <a href="search.php">Back to Search</a>
        <?php } else { ?>
            <h2>Book Hostel</h2>
            <div class="available-rooms">Available Rooms: <?php echo $available_rooms; ?></div>
            <form method="post">
                <label for="checkin_date">Check-in Date:</label>
                <input type="date" id="checkin_date" name="checkin_date" required>
                <label for="room_type">Room Type:</label>
                <select id="room_type" name="room_type" required>
                    <option value="">Select Type</option>
                    <option value="dormitory">Dormitory</option>
                    <option value="private room">private room</option>
                </select>
                <input type="submit" name="book_hostel" value="Book Now" <?php if ($available_rooms <= 0) echo 'disabled'; ?>>
            </form>
            <a href="search.php">Back to Search</a>
        <?php } ?>
    </div>
</body>
</html>