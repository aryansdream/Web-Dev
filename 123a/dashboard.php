<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0; padding: 0;
            background: #f8f9fa;
        }
        .navbar {
            background-color: #4CAF50;
            padding: 10px;
            text-align: center;
            position: fixed;
            top: 0;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 100;
        }
        .navbar a {
            color: #fff;
            text-decoration: none;
            margin: 0 10px;
            padding: 8px 14px;
            border-radius: 4px;
            font-size: 1rem;
            background: #388e3c;
            transition: background 0.2s;
            display: inline-block;
        }
        .navbar a:hover {
            background: #2e7031;
            color: #fff;
        }
        .navbar .logo {
            font-size: 20px;
            font-weight: bold;
            color: #fff;
            background: none;
            margin-right: 10px;
        }
        .container {
            width: 90%;
            max-width: 600px;
            margin: 90px auto 30px auto;
            padding: 18px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        }
        .header {
            background-color: #4CAF50;
            color: #fff;
            padding: 10px;
            text-align: center;
            border-radius: 6px 6px 0 0;
        }
        .content {
            padding: 20px;
            margin-top: 0;
        }
        .btn {
            background-color: #4CAF50;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }
        .btn:hover {
            background-color: #3e8e41;
        }
        .search-bar {
            padding: 10px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .search-bar input[type="text"], .search-bar select {
            width: 100%;
            max-width: 320px;
            padding: 10px;
            font-size: 1rem;
            margin-bottom: 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .search-bar input[type="submit"] {
            width: 100%;
            max-width: 320px;
            padding: 10px;
            font-size: 1rem;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .search-bar input[type="submit"]:hover {
            background-color: #3e8e41;
        }
        @media (max-width: 700px) {
            .navbar {
                flex-direction: column;
                align-items: flex-start;
                padding: 8px 4px;
            }
            .navbar .logo {
                font-size: 1.1rem !important;
                margin-bottom: 6px;
            }
            .navbar a {
                margin: 4px 0 !important;
                font-size: 1rem !important;
                width: 100%;
                text-align: left;
            }
            .container {
                width: 98% !important;
                margin: 80px auto 20px auto !important;
                padding: 8px !important;
            }
            .header {
                font-size: 1.1rem;
                padding: 8px !important;
            }
            .content {
                padding: 10px !important;
                font-size: 1rem;
            }
            .search-bar input[type="text"], .search-bar select, .search-bar input[type="submit"] {
                width: 100% !important;
                font-size: 1rem !important;
            }
        }
        @media (max-width: 500px) {
            .container {
                width: 100% !important;
                margin: 70px auto 10px auto !important;
                padding: 4px !important;
            }
            .header {
                font-size: 1rem;
                padding: 6px !important;
            }
            .content {
                padding: 6px !important;
                font-size: 0.98rem;
            }
            .navbar .logo {
                font-size: 1rem !important;
            }
            .navbar a {
                font-size: 0.98rem !important;
            }
            .search-bar input[type="text"], .search-bar select, .search-bar input[type="submit"] {
                font-size: 0.98rem !important;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo">BookYourHostel!</div>
        <div>
            <a href="#">Home</a>
            <a href="bookings1.php">Bookings</a>
            <a href="userprofile.php">Profile</a>
            <a href="index.html">Logout</a>
        </div>
    </div>

    <div class="container">
        <div class="header">
            <h2>Search Hostels</h2>
        </div>
        <div class="content">
            <h3>Welcome, <?php echo $_SESSION['username']; ?>!</h3>
            <p>This is your dashboard. You can view your account information, update your profile, or logout.</p>
            <form class="search-bar" action="search.php" method="get">
                <label for="location">Location:</label>
                <input type="text" id="location" name="location" placeholder="Enter location">
                <label for="type">Type:</label>
                <select id="type" name="type">
                    <option value="">Any</option>
                    <option value="dormitory">Dormitory</option>
                    <option value="private room">Private Room</option>
                </select>
                <input type="submit" value="Search">
            </form>
            <button class="btn" onclick="location.href='index.html'">Logout</button>
        </div>
    </div>
</body>
</html>
