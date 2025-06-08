<?php
session_start();
require_once 'db_conn.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit;
}

$fullname = $_SESSION['fullname'] ?? 'User';

// Fetch latest news
$newsStmt = $conn->prepare("SELECT * FROM news ORDER BY created_at DESC LIMIT 5");
$newsStmt->execute();
$newsResult = $newsStmt->get_result();

// Fetch causes
$causesStmt = $conn->prepare("SELECT * FROM causes ORDER BY created_at DESC");
$causesStmt->execute();
$causesResult = $causesStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard - Youth For Survival</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #eef2f3;
            margin: 0;
            padding: 20px;
        }
        .dashboard {
            max-width: 1000px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
        }
        h2, h3 {
            color: #333;
        }
        .logout {
            float: right;
            color: red;
            text-decoration: none;
            font-weight: bold;
        }
        .section {
            margin-top: 30px;
        }
        .item {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #ddd;
        }
        .item h4 {
            margin: 0 0 5px;
            color: #0077cc;
        }
        .item p {
            margin: 5px 0;
        }
        .date {
            font-size: 0.9em;
            color: #777;
        }
    </style>
</head>
<body>

<div class="dashboard">
    <a href="user_logout.php" class="logout">Logout</a>
    <a href="inbox.php">Inbox</a>
    <a href="message.php">Send Message</a>
    <h2>Welcome, <?= htmlspecialchars($fullname) ?>!</h2>

    <div class="section">
        <h3>Recent News</h3>
        <?php if ($newsResult->num_rows > 0): ?>
            <?php while ($news = $newsResult->fetch_assoc()): ?>
                <div class="item">
                    <h4><?= htmlspecialchars($news['title']) ?></h4>
                    <div class="date">Published on <?= date('F j, Y', strtotime($news['created_at'])) ?></div>
                    <p><?= nl2br(htmlspecialchars($news['content'])) ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No recent news available.</p>
        <?php endif; ?>
    </div>

    <div class="section">
        <h3>Ongoing Causes</h3>
        <?php if ($causesResult->num_rows > 0): ?>
            <?php while ($cause = $causesResult->fetch_assoc()): ?>
                <div class="item">
                    <h4><?= htmlspecialchars($cause['title']) ?></h4>
                    <div class="date">Started on <?= date('F j, Y', strtotime($cause['created_at'])) ?></div>
                    <p><?= nl2br(htmlspecialchars($cause['description'])) ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No causes found.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>

<?php
$newsStmt->close();
$causesStmt->close();
$conn->close();
?>
