<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !$_SESSION["is_admin"]){
    header("location: ../login.php");
    exit;
}

require_once "../src/config.php";

// Logic to handle voice deletion with POST request
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])){
    $delete_id = $_POST['delete_id'];
    // First, get the file path to delete the file from the server
    $sql = "SELECT file_path FROM voices WHERE id = ?";
    if($stmt = $conn->prepare($sql)){
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
        $stmt->bind_result($file_path);
        if($stmt->fetch()){
            if(file_exists("../" . $file_path)){
                unlink("../" . $file_path);
            }
        }
        $stmt->close();
    }

    $sql = "DELETE FROM voices WHERE id = ?";
    if($stmt = $conn->prepare($sql)){
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Logic to handle adding a new voice with validation
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['voice_file'])){
    $name = trim($_POST['name']);
    $file_path = '';

    if(isset($_FILES['voice_file']) && $_FILES['voice_file']['error'] == 0){
        $target_dir = "../uploads/";
        $file_name = basename($_FILES["voice_file"]["name"]);
        $target_file = $target_dir . $file_name;
        $file_type = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

        // Sanitize filename
        $file_name = preg_replace("/[^a-zA-Z0-9.\-\_]/", "", $file_name);

        // Check if file is a valid audio file
        $allowed_types = array("mp3", "wav", "ogg");
        if(in_array($file_type, $allowed_types)){
            // Check file size (e.g., max 5MB)
            if ($_FILES["voice_file"]["size"] < 5000000) {
                if(move_uploaded_file($_FILES["voice_file"]["tmp_name"], $target_dir . $file_name)){
                    $file_path = "uploads/" . $file_name;
                }
            }
        }
    }

    if(!empty($name) && !empty($file_path)){
        $sql = "INSERT INTO voices (name, file_path) VALUES (?, ?)";
        if($stmt = $conn->prepare($sql)){
            $stmt->bind_param("ss", $name, $file_path);
            $stmt->execute();
            $stmt->close();
        }
    }
}

$sql = "SELECT id, name, file_path FROM voices";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Voices</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="wrapper">
        <h2>Manage Voices</h2>
        <a href="index.php">Back to Dashboard</a>

        <h3>Add New Voice</h3>
        <form action="voices.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Voice Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Voice File (MP3, WAV, OGG - max 5MB)</label>
                <input type="file" name="voice_file" class="form-control" required>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Add Voice">
            </div>
        </form>

        <h3>All Voices</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>File Path</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['file_path']; ?></td>
                        <td>
                            <form action="voices.php" method="post" onsubmit="return confirm('Are you sure?');">
                                <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                                <input type="submit" value="Delete" class="btn btn-danger">
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No voices found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
