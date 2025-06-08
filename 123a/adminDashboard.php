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

// Fetch hostels
$hostels = array();
$sql = "SELECT * FROM hostels";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $hostels[] = $row;
}

// Update available rooms if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_rooms'])) {
    $hostel_id = intval($_POST['hostel_id']);
    $available_rooms = intval($_POST['available_rooms']);
    $conn->query("UPDATE hostels SET available_rooms = $available_rooms WHERE id = $hostel_id");
    // Refresh data
    header("Location: adminDashboard.php");
    exit();
}

// Fetch admins
$admins = array();
$sql = "SELECT * FROM admins";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $admins[] = $row;
}

// Fetch users
$users = array();
$sql = "SELECT * FROM users";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

// Fetch bookings with user and hostel info
$bookings = array();
$sql = "SELECT b.id, u.username, h.name AS hostel_name, b.checkin_date, b.room_type 
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN hostels h ON b.hostel_id = h.id
        ORDER BY b.id DESC";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f9fa;
            margin: 0;
            padding: 24px;
        }
        h2, h3 {
            color: #2c3e50;
            text-align: center;
        }
        p {
            text-align: center;
        }
        table {
            width: 90%;
            margin: 24px auto;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 12px 16px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }
        th {
            background: #007bff;
            color: #fff;
            font-weight: 600;
        }
        tr:last-child td {
            border-bottom: none;
        }
        a {
            color: #007bff;
            text-decoration: none;
            margin-right: 10px;
            transition: color 0.2s;
        }
        a:hover {
            color: #0056b3;
            text-decoration: underline;
        }
        .action-links a {
            margin-right: 8px;
        }
        .add-links {
            text-align: center;
            margin-top: 32px;
        }
        .add-links a {
            display: inline-block;
            background: #007bff;
            color: #fff;
            padding: 10px 22px;
            border-radius: 4px;
            margin: 0 10px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.2s;
        }
        .add-links a:hover {
            background: #0056b3;
        }
        .update-form {
            display: inline-block;
            margin: 0;
        }
        .update-form input[type="number"] {
            width: 60px;
            padding: 4px 6px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1em;
        }
        .update-form input[type="submit"] {
            background: #28a745;
            color: #fff;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 1em;
            cursor: pointer;
            margin-left: 6px;
        }
        .update-form input[type="submit"]:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <h2>Admin Dashboard</h2>
    <p>Welcome, <?php echo $_SESSION['admin_username']; ?></p>

    <h3>Hostel Details</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Location</th>
            <th>Type</th>
            <th>Available Rooms</th>
            <th>Action</th>
        </tr>
        <?php foreach ($hostels as $hostel) { ?>
        <tr>
            <td><?php echo $hostel['id']; ?></td>
            <td><?php echo $hostel['name']; ?></td>
            <td><?php echo $hostel['location']; ?></td>
            <td><?php echo $hostel['type']; ?></td>
            <td>
                <form class="update-form" method="post" action="adminDashboard.php">
                    <input type="hidden" name="hostel_id" value="<?php echo $hostel['id']; ?>">
                    <input type="number" name="available_rooms" min="0" value="<?php echo isset($hostel['available_rooms']) ? $hostel['available_rooms'] : 0; ?>">
                    <input type="submit" name="update_rooms" value="Update">
                </form>
            </td>
            <td class="action-links">
                <a href="edit_hostel.php?id=<?php echo $hostel['id']; ?>">Edit</a>
                <a href="delete_hostel.php?id=<?php echo $hostel['id']; ?>">Delete</a>
            </td>
        </tr>
        <?php } ?>
    </table>

    <h3>Bookings</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Hostel</th>
            <th>Check-in Date</th>
            <th>Room Type</th>
            <th>Action</th>
        </tr>
        <?php foreach ($bookings as $booking) { ?>
        <tr>
            <td><?php echo $booking['id']; ?></td>
            <td><?php echo htmlspecialchars($booking['username']); ?></td>
            <td><?php echo htmlspecialchars($booking['hostel_name']); ?></td>
            <td><?php echo htmlspecialchars($booking['checkin_date']); ?></td>
            <td><?php echo htmlspecialchars($booking['room_type']); ?></td>
            <td class="action-links">
                <a href="delete_booking.php?id=<?php echo $booking['id']; ?>">Delete</a>
            </td>
        </tr>
        <?php } ?>
    </table>

    <h3>Admins</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Action</th>
        </tr>
        <?php foreach ($admins as $admin) { ?>
        <tr>
            <td><?php echo $admin['id']; ?></td>
            <td><?php echo $admin['username']; ?></td>
            <td class="action-links">
                <a href="edit_admin.php?id=<?php echo $admin['id']; ?>">Edit</a>
                <a href="delete_admin.php?id=<?php echo $admin['id']; ?>">Delete</a>
            </td>
        </tr>
        <?php } ?>
    </table>

    <h3>Users</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Action</th>
        </tr>
        <?php foreach ($users as $user) { ?>
        <tr>
            <td><?php echo $user['id']; ?></td>
            <td><?php echo $user['username']; ?></td>
            <td><?php echo $user['email']; ?></td>
            <td class="action-links">
                <a href="delete_user.php?id=<?php echo $user['id']; ?>">Delete</a>
            </td>
        </tr>
        <?php } ?>
    </table>

    <div class="add-links">
        <a href="add_hostel.php">Add Hostel</a>
        <a href="add_admin.php">Add Admin</a>
        <a href="index.html" style="background:#6c757d;">Return to Home</a>
    </div>
</body>
</html>
