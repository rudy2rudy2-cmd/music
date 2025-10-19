<?php
error_reporting(0);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db_server = $_POST['db_server'];
    $db_username = $_POST['db_username'];
    $db_password = $_POST['db_password'];
    $db_name = $_POST['db_name'];
    $admin_username = $_POST['admin_username'];
    $admin_password = $_POST['admin_password'];
    $admin_email = $_POST['admin_email'];

    // Create config file
    $config_content = "<?php\n";
    $config_content .= "define('DB_SERVER', '$db_server');\n";
    $config_content .= "define('DB_USERNAME', '$db_username');\n";
    $config_content .= "define('DB_PASSWORD', '$db_password');\n";
    $config_content .= "define('DB_NAME', '$db_name');\n\n";
    $config_content .= "\$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);\n\n";
    $config_content .= "if(\$conn === false){\n";
    $config_content .= "    die('ERROR: Could not connect. ' . \$conn->connect_error);\n";
    $config_content .= "}\n?>";
    file_put_contents('src/config.php', $config_content);

    // Create database and tables
    $conn = new mysqli($db_server, $db_username, $db_password);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $conn->query("CREATE DATABASE IF NOT EXISTS $db_name");
    $conn->select_db($db_name);
    $sql = file_get_contents('database.sql');
    $conn->multi_query($sql);
    do { if ($res = $conn->store_result()) { $res->free(); } } while ($conn->more_results() && $conn->next_result());

    // Insert voices
    $sql_voices = file_get_contents('update_voices.sql');
    $conn->multi_query($sql_voices);
    do { if ($res = $conn->store_result()) { $res->free(); } } while ($conn->more_results() && $conn->next_result());

    // Create admin user
    $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, password, email, is_admin) VALUES (?, ?, ?, 1)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sss", $admin_username, $hashed_password, $admin_email);
        $stmt->execute();
        $stmt->close();
    }
    $conn->close();

    echo "Installation complete! You can now delete this file and log in with your admin account.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Installation</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="wrapper">
        <h2>Installation</h2>
        <form action="install.php" method="post" class="generator-form">
            <h3>Database Details</h3>
            <div class="form-group">
                <label>DB Server</label>
                <input type="text" name="db_server" value="127.0.0.1" required>
            </div>
            <div class="form-group">
                <label>DB Username</label>
                <input type="text" name="db_username" value="root" required>
            </div>
            <div class="form-group">
                <label>DB Password</label>
                <input type="password" name="db_password">
            </div>
            <div class="form-group">
                <label>DB Name</label>
                <input type="text" name="db_name" value="ai_music_platform" required>
            </div>
            <h3>Admin User</h3>
            <div class="form-group">
                <label>Admin Username</label>
                <input type="text" name="admin_username" value="admin" required>
            </div>
            <div class="form-group">
                <label>Admin Password</label>
                <input type="password" name="admin_password" required>
            </div>
            <div class="form-group">
                <label>Admin Email</label>
                <input type="email" name="admin_email" value="admin@example.com" required>
            </div>
            <div class="form-group">
                <input type="submit" class="login-btn" value="Install">
            </div>
        </form>
    </div>
</body>
</html>
