<?php
session_start();
if (!isset($_SESSION['user_id']) && !isset($_SESSION['username'])) {
    header("Location: login.html");
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

// If user_id is not set, get it from username
if (!isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
    $username = $conn->real_escape_string($_SESSION['username']);
    $res = $conn->query("SELECT id FROM users WHERE username='$username' LIMIT 1");
    if ($res && $row = $res->fetch_assoc()) {
        $_SESSION['user_id'] = $row['id'];
    } else {
        echo "User not found.";
        exit();
    }
}

$user_id = intval($_SESSION['user_id']);
$sql = "SELECT b.id, h.name AS hostel_name, h.location, b.checkin_date, b.room_type, b.booking_date
        FROM bookings b
        JOIN hostels h ON b.hostel_id = h.id
        WHERE b.user_id = $user_id
        ORDER BY b.booking_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Bookings</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; background: #f8f9fa; margin: 0; padding: 0; }
        .container {
            max-width: 700px;
            margin: 80px auto 30px auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            padding: 24px 18px;
        }
        h2 { text-align: center; color: #0078d7; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 24px;
            background: #fff;
        }
        th, td {
            padding: 10px 8px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }
        th {
            background: #0078d7;
            color: #fff;
            font-weight: 600;
        }
        tr:last-child td { border-bottom: none; }
        .back-btn {
            display: inline-block;
            background: #0078d7;
            color: #fff;
            padding: 8px 20px;
            border-radius: 4px;
            text-decoration: none;
            margin: 24px 0 0 0;
            font-size: 1em;
            transition: background 0.2s;
        }
        .back-btn:hover { background: #0056b3; }
        @media (max-width: 700px) {
            .container { margin: 30px 2% 10px 2%; padding: 10px 2vw; }
            table, thead, tbody, th, td, tr { display: block; }
            thead tr { display: none; }
            tr { margin-bottom: 1.2rem; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); padding: 0.5rem 0.7rem; }
            td { border: none; position: relative; padding-left: 50%; min-height: 36px; font-size: 1em; }
            td:before {
                position: absolute; left: 12px; top: 10px; width: 45%; white-space: nowrap; font-weight: bold; color: #0078d7;
            }
            td:nth-child(1):before { content: "Hostel"; }
            td:nth-child(2):before { content: "Location"; }
            td:nth-child(3):before { content: "Check-in Date"; }
            td:nth-child(4):before { content: "Room Type"; }
            td:nth-child(5):before { content: "Booking Date"; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>My Bookings</h2>
        <?php if ($result && $result->num_rows > 0) { ?>
        <table>
            <thead>
                <tr>
                    <th>Hostel</th>
                    <th>Location</th>
                    <th>Check-in Date</th>
                    <th>Room Type</th>
                    <th>Booking Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['hostel_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['location']); ?></td>
                    <td><?php echo htmlspecialchars($row['checkin_date']); ?></td>
                    <td><?php echo htmlspecialchars(ucfirst($row['room_type'])); ?></td>
                    <td><?php echo htmlspecialchars($row['booking_date']); ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php } else { ?>
            <p style="text-align:center; color:#888;">You have not made any bookings yet.</p>
        <?php } ?>
        <div style="text-align:center;">
            <a href="dashboard.php" class="back-btn">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>