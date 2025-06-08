<?php
session_start();
require_once 'db_conn.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch messages where logged-in user is the receiver
$stmt = $conn->prepare("
    SELECT m.id, m.subject, m.body, m.sent_at, m.is_read, 
           u.fullname AS sender_name, u.username AS sender_username
    FROM messages m
    JOIN app_users u ON m.sender_id = u.id
    WHERE m.receiver_id = ?
    ORDER BY m.sent_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Inbox</title>
<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { border-collapse: collapse; width: 100%; max-width: 900px; }
    th, td { padding: 10px; border: 1px solid #ccc; }
    th { background-color: #2c3e50; color: white; }
    tr.unread { font-weight: bold; background-color: #eef9ff; }
    .container { max-width: 900px; margin: auto; }
    .message-body { white-space: pre-wrap; }
</style>
</head>
<body>

<div class="container">
    <h1>Your Inbox</h1>

    <?php if (empty($messages)): ?>
        <p>No messages found.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>From</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Sent At</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages as $msg): ?>
                    <tr class="<?php echo $msg['is_read'] ? '' : 'unread'; ?>">
                        <td><?php echo htmlspecialchars($msg['sender_name'] . " ({$msg['sender_username']})"); ?></td>
                        <td><?php echo htmlspecialchars($msg['subject']); ?></td>
                        <td class="message-body"><?php echo nl2br(htmlspecialchars($msg['body'])); ?></td>
                        <td><?php echo htmlspecialchars($msg['sent_at']); ?></td>
                        <td><?php echo $msg['is_read'] ? 'Read' : 'Unread'; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>
