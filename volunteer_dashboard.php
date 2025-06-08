<?php
session_start();
require_once 'db_conn.php';

// Check if volunteer is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'volunteer') {
    header("Location: Login.php");
    exit;
}

$volunteerName = $_SESSION['fullname'] ?? 'Volunteer';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Volunteer Dashboard - Youth For Survival</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
        }
        .sidebar {
            width: 250px;
            background: #2980b9;
            color: #fff;
            height: 100vh;
            padding: 20px;
            box-sizing: border-box;
            position: fixed;
        }
        .sidebar h2 {
            margin-bottom: 30px;
            font-size: 24px;
            text-align: center;
        }
        .sidebar a {
            display: block;
            color: #fff;
            text-decoration: none;
            padding: 10px 15px;
            margin-bottom: 10px;
            background: #3498db;
            border-radius: 4px;
        }
        .sidebar a:hover {
            background: #1abc9c;
        }
        .content {
            margin-left: 250px;
            padding: 30px;
            flex: 1;
        }
        .logout-btn {
            display: inline-block;
            margin-top: 20px;
            background: #e74c3c;
            padding: 10px 15px;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .logout-btn:hover {
            background: #c0392b;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Volunteer Panel</h2>
    <p>Welcome, <strong><?php echo htmlspecialchars($volunteerName); ?></strong></p>

    <a href="volunteer_tasks.php">My Tasks</a>
    
    <a href="message.php">Send a Message</a>
    <a href="inbox.php">Inbox</a>
    <a href="logout.php" class="logout-btn">Logout</a>
</div>

<div class="content">
    <h1>Dashboard</h1>
    <p>Welcome to your Volunteer Dashboard. Use the sidebar to manage your tasks, communicate with the team.</p>
    <ul>
        <li><strong>My Tasks:</strong> View and update your assigned volunteer tasks.</li>
        
        <li><strong>Logout:</strong> Securely log out of your session.</li>
    </ul>
</div>

</body>
</html>
