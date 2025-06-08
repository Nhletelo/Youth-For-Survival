<?php
session_start();
require_once 'db_conn.php';

// Check if volunteer is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'volunteer') {
    header("Location: Login.php");
    exit;
}

$volunteer_id = $_SESSION['user_id'];

// Handle status update (if form submitted)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task_id'], $_POST['task_status'])) {
    $task_id = intval($_POST['task_id']);
    $new_status = $_POST['task_status'];

    // Validate status value
    $valid_statuses = ['pending', 'in_progress', 'completed'];
    if (in_array($new_status, $valid_statuses)) {
        $completed_date_sql = ($new_status === 'completed') ? ", completed_date = NOW()" : ", completed_date = NULL";

        $stmt = $conn->prepare("UPDATE volunteer_tasks SET task_status = ? $completed_date_sql, updated_at = NOW() WHERE id = ? AND volunteer_id = ?");
        $stmt->bind_param("sii", $new_status, $task_id, $volunteer_id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: volunteer_tasks.php");
    exit;
}

// Fetch tasks for this volunteer
$stmt = $conn->prepare("SELECT * FROM volunteer_tasks WHERE volunteer_id = ? ORDER BY due_date ASC");
$stmt->bind_param("i", $volunteer_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Volunteer Tasks</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
        select { padding: 5px; }
        button { padding: 5px 10px; }
        .completed { background-color: #d4edda; }
        .in_progress { background-color: #fff3cd; }
        .pending { background-color: #f8d7da; }
    </style>
</head>
<body>

<h1>My Volunteer Tasks</h1>
<table>
    <thead>
        <tr>
            <th>Task Title</th>
            <th>Description</th>
            <th>Status</th>
            <th>Assigned Date</th>
            <th>Due Date</th>
            <th>Completed Date</th>
            <th>Update Status</th>
        </tr>
    </thead>
    <tbody>
    <?php if ($result->num_rows > 0): ?>
        <?php while ($task = $result->fetch_assoc()): ?>
            <tr class="<?php echo htmlspecialchars($task['task_status']); ?>">
                <td><?php echo htmlspecialchars($task['task_title']); ?></td>
                <td><?php echo nl2br(htmlspecialchars($task['task_description'])); ?></td>
                <td><?php echo ucfirst(htmlspecialchars($task['task_status'])); ?></td>
                <td><?php echo htmlspecialchars($task['assigned_date']); ?></td>
                <td><?php echo htmlspecialchars($task['due_date']); ?></td>
                <td><?php echo htmlspecialchars($task['completed_date'] ?? '-'); ?></td>
                <td>
                    <form method="post" action="volunteer_tasks.php">
                        <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                        <select name="task_status">
                            <option value="pending" <?php if ($task['task_status'] === 'pending') echo 'selected'; ?>>Pending</option>
                            <option value="in_progress" <?php if ($task['task_status'] === 'in_progress') echo 'selected'; ?>>In Progress</option>
                            <option value="completed" <?php if ($task['task_status'] === 'completed') echo 'selected'; ?>>Completed</option>
                        </select>
                        <button type="submit">Update</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="7">No tasks assigned.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

</body>
</html>
