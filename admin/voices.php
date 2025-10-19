<?php
session_start();
require_once "../src/config.php";
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !$_SESSION["is_admin"]) { header("location: ../login.php"); exit; }

$upload_err = "";

// Handle voice deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $sql = "SELECT file_path FROM voices WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $_POST['delete_id']);
        $stmt->execute();
        $stmt->bind_result($file_path);
        if ($stmt->fetch() && file_exists("../" . $file_path)) { unlink("../" . $file_path); }
        $stmt->close();
    }
    $sql_del = "DELETE FROM voices WHERE id = ?";
    if ($stmt_del = $conn->prepare($sql_del)) {
        $stmt_del->bind_param("i", $_POST['delete_id']);
        $stmt_del->execute();
        $stmt_del->close();
    }
}

// Handle voice addition with validation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['voice_file'])) {
    $name = trim($_POST['name']);
    if (isset($_FILES['voice_file']) && $_FILES['voice_file']['error'] == 0) {
        $allowed_ext = ['mp3', 'wav', 'ogg'];
        $allowed_mime = ['audio/mpeg', 'audio/wav', 'audio/ogg'];
        $file_info = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($file_info, $_FILES['voice_file']['tmp_name']);
        finfo_close($file_info);
        $ext = strtolower(pathinfo($_FILES['voice_file']['name'], PATHINFO_EXTENSION));

        if (in_array($ext, $allowed_ext) && in_array($mime_type, $allowed_mime) && $_FILES['voice_file']['size'] < 104857600) { // 100MB
            $target_dir = "../uploads/";
            $file_name = preg_replace("/[^a-zA-Z0-9.\-\_]/", "", basename($_FILES["voice_file"]["name"]));
            $file_path = "uploads/" . $file_name;
            if (move_uploaded_file($_FILES["voice_file"]["tmp_name"], $target_dir . $file_name)) {
                $sql = "INSERT INTO voices (name, file_path) VALUES (?, ?)";
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("ss", $name, $file_path);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        } else {
            $upload_err = "Invalid file type or size. Allowed: MP3, WAV, OGG under 100MB.";
        }
    } else {
        $upload_err = "File upload error.";
    }
}

$voices = $conn->query("SELECT id, name, file_path FROM voices");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>Manage Voices</title><link rel="stylesheet" href="../style.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo"><a href="../index.php" style="text-decoration:none; color: #fff;">iLoveSong.ai</a></div>
            <a href="index.php">Admin Home</a>
        </nav>
    </header>
    <main>
        <section>
            <h2>Manage Voices</h2>
            <h3>Add Voice</h3>
            <?php if(!empty($upload_err)): ?>
                <div class="notice" style="background-color: #f2dede; border-color: #ebccd1; color: #a94442;"><?php echo $upload_err; ?></div>
            <?php endif; ?>
            <form action="voices.php" method="post" enctype="multipart/form-data" class="generator-form">
                <input type="text" name="name" placeholder="Voice Name" required>
                <input type="file" name="voice_file" required>
                <button type="submit" class="login-btn">Add Voice</button>
            </form>

            <h3>Existing Voices</h3>
            <table style="width:100%; text-align: left;">
                <tr><th>ID</th><th>Name</th><th>File Path</th><th>Action</th></tr>
                <?php while($row = $voices->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['file_path']); ?></td>
                    <td>
                        <form action="voices.php" method="post" onsubmit="return confirm('Are you sure?');">
                            <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" class="login-btn" style="background-color:#d9534f;">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </section>
    </main>
    <footer><p>&copy; 2025 iLoveSong.ai</p></footer>
</body>
</html>
