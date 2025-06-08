<?php
session_start();
require_once 'db_conn.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit;
}

$message = "";
$user_id = $_SESSION['user_id'];

// Fetch all users except current user for receiver list
$users = [];
$result = $conn->query("SELECT id, fullname, username FROM app_users WHERE id != $user_id ORDER BY fullname");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $receiver_id = intval($_POST['receiver_id']);
    $subject = trim($_POST['subject']);
    $body = trim($_POST['body']);

    if (!$receiver_id || !$subject || !$body) {
        $message = "Please fill in all fields.";
    } else {
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, subject, body) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $user_id, $receiver_id, $subject, $body);

        if ($stmt->execute()) {
            $message = "Message sent successfully.";
        } else {
            $message = "Error sending message: " . $conn->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Send Message</title>
<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    form { max-width: 500px; }
    label { display: block; margin-top: 15px; }
    select, input[type="text"], textarea {
        width: 100%;
        padding: 8px;
        box-sizing: border-box;
        margin-top: 5px;
    }
    button {
        margin-top: 20px;
        padding: 10px 15px;
        background-color: #2c3e50;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    button:hover { background-color: #1abc9c; }
    .message { margin-top: 15px; color: green; }
    .error { color: red; }
</style>
</head>
<body>

<h1>Send a Message</h1>

<?php if ($message): ?>
    <p class="<?php echo (strpos($message, 'Error') === false) ? 'message' : 'error'; ?>"><?php echo htmlspecialchars($message); ?></p>
<?php endif; ?>

<form method="post" action="message.php">
    <label for="receiver_id">Select Recipient:</label>
    <select name="receiver_id" id="receiver_id" required>
        <option value="">-- Select User --</option>
        <?php foreach ($users as $user): ?>
            <option value="<?php echo $user['id']; ?>">
                <?php echo htmlspecialchars($user['fullname'] . " ({$user['username']})"); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="subject">Subject:</label>
    <input type="text" id="subject" name="subject" required />

    <label for="body">Message:</label>
    <textarea id="body" name="body" rows="5" required></textarea>

    <button type="submit">Send Message</button>
</form>

</body>
</html>
