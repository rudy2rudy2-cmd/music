<?php
// A simple installation script for the AI Music Generator Platform

$step = isset($_POST['step']) ? (int)$_POST['step'] : 1;
$error_message = '';

// --- Step 2: Process form data and install ---
if ($step === 2 && $_SERVER["REQUEST_METHOD"] == "POST") {

    // --- Collect data ---
    $db_server = $_POST['db_server'] ?? '127.0.0.1';
    $db_username = $_POST['db_username'] ?? 'root';
    $db_password = $_POST['db_password'] ?? '';
    $db_name = $_POST['db_name'] ?? 'ai_music_platform';

    $admin_user = $_POST['admin_user'] ?? 'admin';
    $admin_pass = $_POST['admin_pass'] ?? '';
    $admin_email = $_POST['admin_email'] ?? 'admin@example.com';

    // --- Create config.php file ---
    $config_content = "<?php\n";
    $config_content .= "define('DB_SERVER', '$db_server');\n";
    $config_content .= "define('DB_USERNAME', '$db_username');\n";
    $config_content .= "define('DB_PASSWORD', '$db_password');\n";
    $config_content .= "define('DB_NAME', '$db_name');\n\n";
    $config_content .= "\$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);\n";
    $config_content .= "if(\$link === false){\n";
    $config_content .= "    die('ERROR: Could not connect. ' . mysqli_connect_error());\n";
    $config_content .= "}\n?>";

    if (file_put_contents('config.php', $config_content) === false) {
        $error_message = "Error: Could not write to config.php. Please check file permissions.";
        $step = 1;
    } else {
        // --- Setup database ---
        // 1. Connect without selecting a DB to create it
        $conn = new mysqli($db_server, $db_username, $db_password);
        if ($conn->connect_error) {
            $error_message = "Database Connection Failed: " . $conn->connect_error;
            $step = 1;
        } else {
            // 2. Create database
            $conn->query("CREATE DATABASE IF NOT EXISTS $db_name");
            $conn->select_db($db_name);

            // 3. Execute database.sql
            $sql_script = file_get_contents('database.sql');
            if ($sql_script === false) {
                 $error_message = "Error: database.sql not found.";
                 $step = 1;
            } else {
                $conn->multi_query($sql_script);
                // Clear multi-query results
                do { if ($res = $conn->store_result()) { $res->free(); } } while ($conn->more_results() && $conn->next_result());

                // 4. Create admin user
                $hashed_password = password_hash($admin_pass, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (username, password, email, is_admin) VALUES (?, ?, ?, 1)");
                if ($stmt) {
                    $stmt->bind_param("sss", $admin_user, $hashed_password, $admin_email);
                    $stmt->execute();
                    $stmt->close();
                }
                $conn->close();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Platform Installer</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .installer-container { max-width: 600px; margin: 4rem auto; padding: 2rem; background-color: var(--card-bg-color); border-radius: 10px; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; }
        .form-group input { width: 100%; padding: 0.8rem; background-color: #2c2c34; border: 1px solid #444; border-radius: 5px; color: var(--text-color); }
        .install-btn { width: 100%; padding: 1rem; background: var(--accent-gradient); color: #fff; border: none; border-radius: 8px; font-size: 1.1rem; cursor: pointer; }
        .success-message, .error-message { padding: 1.5rem; border-radius: 8px; margin-bottom: 1rem; }
        .success-message { background-color: #2ECC40; color: #fff; }
        .error-message { background-color: #FF4136; color: #fff; }
        .security-warning { background-color: #FFDC00; color: #111; padding: 1.5rem; border-radius: 8px; font-weight: bold; }
    </style>
</head>
<body data-theme="dark" data-color="blue">
    <div class="installer-container">
        <h1>Platform Installer</h1>

        <?php if ($step === 1): ?>
            <p>Please provide the following details to set up your platform.</p>
            <?php if ($error_message): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form method="post">
                <input type="hidden" name="step" value="2">

                <h3>Database Settings</h3>
                <div class="form-group">
                    <label for="db_server">DB Server</label>
                    <input type="text" name="db_server" value="127.0.0.1">
                </div>
                <div class="form-group">
                    <label for="db_name">DB Name</label>
                    <input type="text" name="db_name" value="ai_music_platform">
                </div>
                <div class="form-group">
                    <label for="db_username">DB Username</label>
                    <input type="text" name="db_username" value="root">
                </div>
                 <div class="form-group">
                    <label for="db_password">DB Password</label>
                    <input type="password" name="db_password">
                </div>

                <h3>Admin Account</h3>
                <div class="form-group">
                    <label for="admin_user">Admin Username</label>
                    <input type="text" name="admin_user" value="admin">
                </div>
                <div class="form-group">
                    <label for="admin_pass">Admin Password</label>
                    <input type="password" name="admin_pass" value="rudyrudy181989">
                </div>
                <div class="form-group">
                    <label for="admin_email">Admin Email</label>
                    <input type="email" name="admin_email" value="admin@example.com">
                </div>

                <button type="submit" class="install-btn">Install Now</button>
            </form>

        <?php elseif ($step === 2 && empty($error_message)): ?>
            <div class="success-message">
                <h2>Installation Complete!</h2>
                <p>Your platform has been set up successfully.</p>
                <p>You can now <a href="login.php" style="color:white;font-weight:bold;">log in</a> with your admin credentials.</p>
            </div>
            <div class="security-warning">
                <p>⚠️ For security reasons, please DELETE this `install.php` file from your server immediately.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
