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

// Get user info
if (isset($_SESSION['user_id'])) {
    $user_id = intval($_SESSION['user_id']);
} else {
    $username = $conn->real_escape_string($_SESSION['username']);
    $res = $conn->query("SELECT id FROM users WHERE username='$username' LIMIT 1");
    if ($res && $row = $res->fetch_assoc()) {
        $user_id = $row['id'];
        $_SESSION['user_id'] = $user_id;
    } else {
        echo "User not found.";
        exit();
    }
}

// Handle profile update
$update_msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $new_name = trim($_POST['name']);
    $new_email = trim($_POST['email']);
    $new_contact = trim($_POST['contact']);

    if (!$new_name || !$new_email || !$new_contact) {
        $update_msg = "All fields are required.";
    } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $update_msg = "Invalid email address.";
    } elseif (!preg_match('/^[0-9]{10,15}$/', $new_contact)) {
        $update_msg = "Invalid mobile number.";
    } else {
        $stmt = $conn->prepare("UPDATE users SET name=?, email=?, contact=? WHERE id=?");
        $stmt->bind_param("sssi", $new_name, $new_email, $new_contact, $user_id);
        if ($stmt->execute()) {
            $update_msg = "Profile updated successfully!";
        } else {
            $update_msg = "Error updating profile.";
        }
        $stmt->close();
    }
}

// Refresh user info after update
$user_sql = "SELECT name, username, email, contact FROM users WHERE id = $user_id";
$user_result = $conn->query($user_sql);
$user = $user_result->fetch_assoc();

// Count bookings
$count_sql = "SELECT COUNT(*) as total FROM bookings WHERE user_id = $user_id";
$count_result = $conn->query($count_sql);
$count_row = $count_result->fetch_assoc();
$booking_count = $count_row['total'];

// Password change
$change_msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    // Get current password hash
    $pass_sql = "SELECT password FROM users WHERE id = $user_id";
    $pass_result = $conn->query($pass_sql);
    $pass_row = $pass_result->fetch_assoc();
    $current_hash = $pass_row['password'];

    if (!password_verify($current, $current_hash)) {
        $change_msg = "Current password is incorrect.";
    } elseif ($new !== $confirm) {
        $change_msg = "New passwords do not match.";
    } elseif (strlen($new) < 6) {
        $change_msg = "New password must be at least 6 characters.";
    } else {
        $new_hash = password_hash($new, PASSWORD_DEFAULT);
        $update_sql = "UPDATE users SET password='$new_hash' WHERE id=$user_id";
        if ($conn->query($update_sql) === TRUE) {
            $change_msg = "Password changed successfully!";
        } else {
            $change_msg = "Error updating password.";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; background: #f8f9fa; margin: 0; padding: 0; }
        .container {
            max-width: 420px;
            margin: 80px auto 30px auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            padding: 28px 18px;
        }
        h2 { text-align: center; color: #0078d7; }
        .profile-info { margin-bottom: 24px; }
        .profile-info label { font-weight: bold; display: block; margin-top: 10px; }
        .profile-info span { display: block; margin-top: 2px; color: #333; }
        .bookings-link {
            display: inline-block;
            margin-top: 10px;
            background: #0078d7;
            color: #fff;
            padding: 8px 18px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 1em;
            transition: background 0.2s;
        }
        .bookings-link:hover { background: #0056b3; }
        .edit-profile {
            margin-bottom: 30px;
            padding-bottom: 18px;
            border-bottom: 1px solid #eee;
        }
        .edit-profile label { display: block; margin-top: 10px; }
        .edit-profile input[type="text"], .edit-profile input[type="email"] {
            width: 100%;
            padding: 8px;
            margin-top: 4px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1em;
        }
        .edit-profile input[type="submit"] {
            background: #0078d7;
            color: #fff;
            border: none;
            padding: 10px 0;
            width: 100%;
            border-radius: 4px;
            font-size: 1em;
            cursor: pointer;
            margin-top: 14px;
            transition: background 0.2s;
        }
        .edit-profile input[type="submit"]:hover {
            background: #0056b3;
        }
        .change-password {
            margin-top: 30px;
            padding-top: 18px;
            border-top: 1px solid #eee;
        }
        .change-password label { display: block; margin-top: 10px; }
        .change-password input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-top: 4px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1em;
        }
        .change-password input[type="submit"] {
            background: #0078d7;
            color: #fff;
            border: none;
            padding: 10px 0;
            width: 100%;
            border-radius: 4px;
            font-size: 1em;
            cursor: pointer;
            margin-top: 14px;
            transition: background 0.2s;
        }
        .change-password input[type="submit"]:hover {
            background: #0056b3;
        }
        .msg {
            margin-top: 10px;
            color: #d32f2f;
            text-align: center;
        }
        .msg.success {
            color: #388e3c;
        }
        @media (max-width: 600px) {
            .container { margin: 30px 2% 10px 2%; padding: 12px 2vw; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>User Profile</h2>
        <div class="edit-profile">
            <form method="post" autocomplete="off">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($user['name']); ?>">
                <label for="email">Email ID:</label>
                <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($user['email']); ?>">
                <label for="contact">Contact No.:</label>
                <input type="text" id="contact" name="contact" required pattern="[0-9]{10,15}" maxlength="15" value="<?php echo htmlspecialchars($user['contact']); ?>">
                <input type="submit" name="update_profile" value="Update Profile">
            </form>
            <?php if ($update_msg): ?>
                <div class="msg<?php echo ($update_msg === "Profile updated successfully!") ? ' success' : ''; ?>">
                    <?php echo htmlspecialchars($update_msg); ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="profile-info">
            <label>Username:</label>
            <span><?php echo htmlspecialchars($user['username']); ?></span>
            <label>No. of Bookings:</label>
            <span>
                <?php echo $booking_count; ?>
                <a href="bookings1.php" class="bookings-link">View Bookings</a>
            </span>
        </div>
        <div class="change-password">
            <form method="post" autocomplete="off">
                <label for="current_password">Current Password:</label>
                <input type="password" id="current_password" name="current_password" required>
                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password" required>
                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                <input type="submit" name="change_password" value="Change Password">
            </form>
            <?php if ($change_msg): ?>
                <div class="msg<?php echo ($change_msg === "Password changed successfully!") ? ' success' : ''; ?>">
                    <?php echo htmlspecialchars($change_msg); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>