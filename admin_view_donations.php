<?php
// Database connection
$host = 'localhost';
$db = 'youth_for_survival';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Delete donation if requested
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM donations WHERE id = $delete_id");
    header("Location: admin_view_donations.php");
    exit;
}

// Handle export to CSV
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=donations.csv');

    $output = fopen("php://output", "w");
    fputcsv($output, ['ID', 'Name', 'Email', 'Donated At']);

    $export_query = $conn->query("SELECT * FROM donations ORDER BY donated_at DESC");
    while ($row = $export_query->fetch_assoc()) {
        fputcsv($output, [$row['id'], $row['name'], $row['email'], $row['donated_at']]);
    }

    fclose($output);
    exit;
}

// Handle search
$search = '';
$where = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $where = "WHERE name LIKE '%$search%' OR email LIKE '%$search%'";
}

// Fetch donations
$sql = "SELECT * FROM donations $where ORDER BY donated_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Donation Requests</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 20px;
        }

        h2 {
            text-align: center;
        }

        form.search-form {
            text-align: center;
            margin-bottom: 20px;
        }

        input[type="text"] {
            padding: 8px;
            width: 250px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button,
        a.export-btn {
            padding: 8px 16px;
            background-color: orange;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 5px;
            text-decoration: none;
        }

        button:hover,
        a.export-btn:hover {
            background-color: darkorange;
        }

        table {
            width: 90%;
            margin: auto;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: orange;
            color: white;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        .action-links a {
            color: red;
            text-decoration: none;
            font-weight: bold;
        }

        .action-links a:hover {
            text-decoration: underline;
        }

        .no-data {
            text-align: center;
            padding: 20px;
        }

        .back {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #555;
        }

        .back:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<h2>Donation Requests</h2>

<form class="search-form" method="GET" action="">
    <input type="text" name="search" placeholder="Search by name or email" value="<?= htmlspecialchars($search) ?>">
    <button type="submit">Search</button>
    <a href="admin_view_donations.php" class="export-btn">Reset</a>
    <a href="?export=csv" class="export-btn">Export CSV</a>
</form>

<?php if ($result->num_rows > 0): ?>
    <table>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Email</th>
            <th>Requested At</th>
            <th>Action</th>
        </tr>
        <?php $count = 1; ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $count++ ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['donated_at']) ?></td>
                <td class="action-links">
                    <a href="?delete_id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this donation?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <div class="no-data">No donation requests found.</div>
<?php endif; ?>

<a class="back" href="admin_dashboard.php">← Back to Dashboard</a>

</body>
</html>

<?php $conn->close(); ?>
