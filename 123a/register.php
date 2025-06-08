<?php
// Handle form submission and registration logic in the same file
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db_host = 'localhost';
    $db_username = 'root';
    $db_password = '';
    $db_name = 'user_data';

    $conn = new mysqli($db_host, $db_username, $db_password, $db_name);
    if ($conn->connect_error) {
        $message = "Database connection failed!";
    } else {
        $name = trim($_POST['name']);
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $contact = trim($_POST['contact']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Basic validation
        if (!$name || !$username || !$email || !$contact || !$password || !$confirm_password) {
            $message = "All fields are required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Invalid email address.";
        } elseif (!preg_match('/^[0-9]{10,15}$/', $contact)) {
            $message = "Invalid mobile number.";
        } elseif ($password !== $confirm_password) {
            $message = "Passwords do not match.";
        } elseif (strlen($password) < 6) {
            $message = "Password must be at least 6 characters.";
        } else {
            // Check if username or email already exists
            $check = $conn->query("SELECT id FROM users WHERE username='$username' OR email='$email'");
            if ($check && $check->num_rows > 0) {
                $message = "Username or Email already exists.";
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (name, username, email, contact, password) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $name, $username, $email, $contact, $hashed);
                if ($stmt->execute()) {
                    $message = "Registration successful! <a href='login.html'>Login here</a>.";
                } else {
                    $message = "Registration failed. Please try again.";
                }
                $stmt->close();
            }
        }
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Signup</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; }
        .container {
            width: 320px;
            margin: 40px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            background: #fff;
        }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; margin-bottom: 7px; }
        .form-group input {
            width: 100%;
            height: 38px;
            padding: 8px;
            font-size: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .btn {
            width: 100%;
            height: 40px;
            padding: 10px;
            font-size: 16px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn:hover { background-color: #3e8e41; }
        .msg {
            margin-bottom: 12px;
            color: #d32f2f;
            text-align: center;
        }
        .msg.success { color: #388e3c; }
        @media (max-width: 500px) {
            .container { width: 98%; padding: 10px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Signup</h2>
        <?php if ($message): ?>
            <div class="msg<?php echo (strpos($message, 'successful') !== false) ? ' success' : ''; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <form action="" method="post" autocomplete="off">
            <div class="form-group">
                <label for="name">Full Name:</label>
                <input type="text" id="name" name="name" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="email">Email ID:</label>
                <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="contact">Mobile No.:</label>
                <input type="tel" id="contact" name="contact" required pattern="[0-9]{10,15}" maxlength="15"
                       inputmode="numeric" autocomplete="off"
                       value="<?php echo isset($_POST['contact']) ? htmlspecialchars($_POST['contact']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn">Signup</button>
        </form>
        <p>Already have an account? <a href="login.html">Login</a></p>
    </div>
</body>
</html>
