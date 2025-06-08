<?php
session_start();

// Connect to database
$conn = new mysqli('localhost', 'root', '', 'youth_for_survival');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle image upload
if (isset($_POST['submit'])) {
    if (!empty($_FILES['image']['name'])) {
        $caption = $conn->real_escape_string($_POST['caption']);
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $filename = basename($_FILES['image']['name']);
        $targetFile = $targetDir . time() . "_" . $filename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $stmt = $conn->prepare("INSERT INTO gallery (image_path, caption, created_at) VALUES (?, ?, NOW())");
            $stmt->bind_param("ss", $targetFile, $caption);
            $stmt->execute();
            $stmt->close();
            header("Location: admin_gallery.php?success=uploaded");
            exit;
        } else {
            $error = "Failed to upload image.";
        }
    } else {
        $error = "Please select an image to upload.";
    }
}

// Handle delete action
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $res = $conn->query("SELECT image_path FROM gallery WHERE id = $id");
    if ($res && $res->num_rows > 0) {
        $img = $res->fetch_assoc();
        $file_path = $img['image_path'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        $conn->query("DELETE FROM gallery WHERE id = $id");
        header("Location: admin_gallery.php?success=deleted");
        exit;
    }
}

// Fetch all images
$images = $conn->query("SELECT * FROM gallery ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Gallery - Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            margin: 0;
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .message {
            color: green;
            text-align: center;
            margin-bottom: 20px;
        }
        .error {
            color: red;
            text-align: center;
            margin-bottom: 20px;
        }
        .upload-form {
            max-width: 500px;
            margin: 0 auto 30px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .upload-form input[type="file"],
        .upload-form input[type="text"],
        .upload-form input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
        }
        .gallery-container {
            max-width: 1000px;
            margin: 0 auto;
        }
        .image-card {
            display: flex;
            align-items: center;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 15px;
            overflow: hidden;
        }
        .image-card img {
            width: 150px;
            height: 100px;
            object-fit: cover;
            flex-shrink: 0;
        }
        .image-details {
            padding: 15px;
            flex-grow: 1;
        }
        .image-details p {
            margin: 5px 0;
        }
        .actions {
            padding: 15px;
        }
        .actions a {
            text-decoration: none;
            background: #ff4d4d;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            font-size: 14px;
            transition: background 0.3s;
        }
        .actions a:hover {
            background: #e60000;
        }
    </style>
</head>
<body>

<h2>Admin - Manage Gallery</h2>

<?php if (isset($_GET['success']) && $_GET['success'] === 'deleted'): ?>
    <p class="message">Image deleted successfully!</p>
<?php elseif (isset($_GET['success']) && $_GET['success'] === 'uploaded'): ?>
    <p class="message">Image uploaded successfully!</p>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<div style="text-align: center; margin-top: 10px;">
    <a href="admin_dashboard.php" style="
        text-decoration: none;
        background-color: #007bff;
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        font-weight: bold;
        display: inline-block;
        transition: background 0.3s;">
        ← Back to Dashboard
    </a>
</div>


<div class="upload-form">
    <h3>Upload New Image</h3>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="image" required>
        <input type="text" name="caption" placeholder="Enter caption (optional)">
        <input type="submit" name="submit" value="Upload">
    </form>
</div>

<div class="gallery-container">
    <?php if ($images->num_rows > 0): ?>
        <?php while ($row = $images->fetch_assoc()): ?>
            <div class="image-card">
                <img src="<?= htmlspecialchars($row['image_path']) ?>" alt="Gallery Image">
                <div class="image-details">
                    <p><strong>ID:</strong> <?= $row['id'] ?></p>
                    <p><strong>Caption:</strong> <?= htmlspecialchars($row['caption']) ?></p>
                    <p><strong>Uploaded:</strong> <?= $row['created_at'] ?></p>
                </div>
                <div class="actions">
                    <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this image?')">Delete</a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="text-align:center;">No images in the gallery.</p>
    <?php endif; ?>
</div>

</body>
</html>
