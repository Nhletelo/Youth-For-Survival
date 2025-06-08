<?php
session_start();
require_once 'db_conn.php';

// Check if volunteer is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: Login.php");
    exit;
}

$volunteerName = $_SESSION['fullname'] ?? 'Admin';
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Youth For Survival</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
        }
        .sidebar {
            width: 250px;
            background: #2c3e50;
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
            background: #34495e;
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
    <h2>Admin Panel</h2>
    <p>Welcome, <strong><?php echo htmlspecialchars($adminName); ?></strong></p>

    <a href="admin_gallery.php">Manage Gallery</a>
    <a href="manage_news.php">Manage News</a>
    <a href="manage_causes.php">Manage Causes</a>
    <a href="admin_view_donations.php">Manage Donations</a>
    <a href="message.php">Send a Message</a>
    <a href="adminAssign_task.php">Assign Task</a>
    <a href="inbox.php">Inbox</a>
    <a href="logout.php" class="logout-btn">Logout</a>
</div>

<div class="content">
    <h1>Dashboard</h1>
    <p>Welcome to the Admin Dashboard. Use the sidebar on the left to manage different sections of the website:</p>
    <ul>
        <li><strong>Manage Gallery:</strong> Upload, view, and delete pictures in the gallery section.</li>
        <li><strong>Manage News:</strong> Add, update, or remove news articles to keep the community informed.</li>
        <li><strong>Manage Causes:</strong> Create or update causes to promote social impact projects.</li>
        <li><strong>Manage Users:</strong> View and manage registered users and their information.</li>
        <li><strong>Send Messages:</strong> Communicate with users directly by sending them messages.</li>
        <li><strong>Assign Tasks:</strong> Asssign Task to volunteers who registered.</li>
    </ul>
        <li><strong>Logout:</strong> Securely log out of your admin session.</li>
    </ul>
</div>


</body>
</html>