<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

require_once "src/config.php";

$user_id = $_SESSION["id"];

// Fetch available voices
$voices_sql = "SELECT id, name FROM voices";
$voices_result = $conn->query($voices_sql);

// Fetch user's generated songs
$songs_sql = "SELECT s.id, v.name as voice_name, s.lyrics, s.file_path, s.created_at FROM songs s JOIN voices v ON s.voice_id = v.id WHERE s.user_id = ? ORDER BY s.created_at DESC";
$songs = [];
if($stmt = $conn->prepare($songs_sql)){
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()){
        $songs[] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AI Music Generator</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div class="wrapper">
        <h2>Generate New Song</h2>
        <p>Welcome, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. <a href="logout.php">Logout</a></p>

        <div class="notice">
            <p><b>Please note:</b> The AI music generation feature is currently a simulation. Submitting the form will generate a placeholder audio file.</p>
        </div>

        <form action="generate.php" method="post">
            <div class="form-group">
                <label>Lyrics</label>
                <textarea name="lyrics" class="form-control" rows="5" required></textarea>
            </div>
            <div class="form-group">
                <label>Voice</label>
                <select name="voice_id" class="form-control" required>
                    <option value="">Select a voice</option>
                    <?php if ($voices_result->num_rows > 0): ?>
                        <?php while($row = $voices_result->fetch_assoc()): ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Generate Song">
            </div>
        </form>

        <h3>Your Generated Songs</h3>
        <?php if(!empty($songs)): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Voice</th>
                        <th>Lyrics</th>
                        <th>File</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($songs as $song): ?>
                        <tr>
                            <td><?php echo $song['voice_name']; ?></td>
                            <td><?php echo substr($song['lyrics'], 0, 50) . '...'; ?></td>
                            <td><a href="<?php echo $song['file_path']; ?>" target="_blank">Listen</a></td>
                            <td><?php echo $song['created_at']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>You haven't generated any songs yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>
