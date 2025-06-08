<?php
session_start();
require_once 'db_conn.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: Login.php");
    exit;
}

$message = "";

// Fetch all volunteers to populate dropdown/select
$volunteers = [];
$result = $conn->query("SELECT id, fullname, username FROM app_users WHERE role = 'volunteer' ORDER BY fullname");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $volunteers[] = $row;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $volunteer_id = intval($_POST['volunteer_id']);
    $task_title = trim($_POST['task_title']);
    $task_description = trim($_POST['task_description']);
    $due_date = $_POST['due_date'];

    // Basic validation
    if (!$volunteer_id || !$task_title || !$due_date) {
        $message = "Please fill in all required fields.";
    } else {
        $assigned_date = date('Y-m-d');

        $stmt = $conn->prepare("INSERT INTO volunteer_tasks (volunteer_id, task_title, task_description, assigned_date, due_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $volunteer_id, $task_title, $task_description, $assigned_date, $due_date);

        if ($stmt->execute()) {
            $message = "Task assigned successfully.";
        } else {
            $message = "Error assigning task: " . $conn->error;
        }
        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assign Task to Volunteer</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { max-width: 500px; }
        label { display: block; margin-top: 15px; }
        input[type="text"], textarea, select, input[type="date"] {
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
            cursor: pointer;
            border-radius: 4px;
        }
        button:hover { background-color: #1abc9c; }
        .message { margin-top: 15px; color: green; }
        .error { color: red; }
    </style>
</head>
<body>

<h1>Assign Task to Volunteer</h1>

<?php if ($message): ?>
    <p class="<?php echo (strpos($message, 'Error') === false) ? 'message' : 'error'; ?>"><?php echo htmlspecialchars($message); ?></p>
<?php endif; ?>

<form method="post" action="adminAssign_task.php">
    <label for="volunteer_id">Select Volunteer:</label>
    <select name="volunteer_id" id="volunteer_id" required>
        <option value="">-- Select Volunteer --</option>
        <?php foreach ($volunteers as $vol): ?>
            <option value="<?php echo $vol['id']; ?>">
                <?php echo htmlspecialchars($vol['fullname'] . " ({$vol['username']})"); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="task_title">Task Title:</label>
    <input type="text" name="task_title" id="task_title" required>

    <label for="task_description">Task Description:</label>
    <textarea name="task_description" id="task_description" rows="4"></textarea>

    <label for="due_date">Due Date:</label>
    <input type="date" name="due_date" id="due_date" required>

    <button type="submit">Assign Task</button>
</form>

</body>
</html>
