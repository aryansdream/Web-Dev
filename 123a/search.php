<?php
// Connect to database
$conn = new mysqli(
    $db_host = 'localhost',
    $db_username = 'root',
    $db_password = '',
    $db_name = 'user_data'
);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get search parameters
$location = $_GET['location'];
$type = $_GET['type'];

// Build SQL query
$sql = "SELECT * FROM hostels WHERE 1=1";

if (!empty($location)) {
    $sql .= " AND location LIKE '%$location%'";
}

if (!empty($type)) {
    $sql .= " AND type = '$type'";
}

// Execute query
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hostel Search Results</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .results-table {
            width: 95%;
            margin: 30px auto;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border-radius: 8px;
            overflow: hidden;
            font-size: 1rem;
        }
        .results-table th, .results-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }
        .results-table th {
            background: #007bff;
            color: #fff;
            font-weight: 600;
        }
        .results-table tr:last-child td {
            border-bottom: none;
        }
        .book-btn {
            margin-left: 10px;
            background: #007bff;
            color: #fff;
            padding: 6px 14px;
            border-radius: 4px;
            text-decoration: none;
            transition: background 0.2s;
            font-size: 1em;
            display: inline-block;
        }
        .book-btn:hover {
            background: #0056b3;
        }
        .back-btn {
            display: inline-block;
            background: #6c757d;
            color: #fff;
            padding: 8px 20px;
            border-radius: 4px;
            text-decoration: none;
            margin: 30px auto 0 auto;
            font-size: 1em;
            transition: background 0.2s;
        }
        .back-btn:hover {
            background: #495057;
        }
        @media (max-width: 700px) {
            .results-table, .results-table thead, .results-table tbody, .results-table th, .results-table td, .results-table tr {
                display: block;
            }
            .results-table thead tr {
                display: none;
            }
            .results-table tr {
                margin-bottom: 1.2rem;
                background: #fff;
                border-radius: 8px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.06);
                padding: 0.5rem 0.7rem;
            }
            .results-table td {
                border: none;
                position: relative;
                padding-left: 50%;
                min-height: 36px;
                font-size: 1em;
            }
            .results-table td:before {
                position: absolute;
                left: 12px;
                top: 10px;
                width: 45%;
                white-space: nowrap;
                font-weight: bold;
                color: #007bff;
            }
            .results-table td:nth-child(1):before { content: "Name"; }
            .results-table td:nth-child(2):before { content: "Location"; }
            .results-table td:nth-child(3):before { content: "Type"; }
            .results-table td:nth-child(4):before { content: "Price"; }
            .results-table td:nth-child(5):before { content: "Available Rooms"; }
            .results-table td:nth-child(6):before { content: "Action"; }
            .book-btn, .back-btn {
                width: 100%;
                margin: 10px 0 0 0;
                font-size: 1.05em;
            }
        }
    </style>
</head>
<body>

<?php
echo '<table class="results-table">';
echo '<thead><tr>
        <th>Name</th>
        <th>Location</th>
        <th>Type</th>
        <th>Price</th>
        <th>Available Rooms</th>
        <th>Action</th>
      </tr></thead><tbody>';
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['name']) . '</td>';
        echo '<td>' . htmlspecialchars($row['location']) . '</td>';
        echo '<td>' . htmlspecialchars($row['type']) . '</td>';
        echo '<td style="color:#28a745;">₹' . htmlspecialchars($row['price']) . '</td>';
        echo '<td>' . htmlspecialchars($row['available_rooms']) . '</td>';
        echo '<td><a href="book_hostel.php?id=' . $row['id'] . '" class="book-btn">Book</a></td>';
        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="6" style="text-align:center;">No results found.</td></tr>';
}
echo '</tbody></table>';

// Back to Dashboard button
echo '<div style="text-align:center;">
        <a href="dashboard.php" class="back-btn">Back to Dashboard</a>
      </div>';

// Close connection
$conn->close();
?>

</body>
</html>
