<?php
session_start();
require_once "../src/config.php";
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !$_SESSION["is_admin"]) { header("location: ../login.php"); exit; }

// Handle user deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $sql = "DELETE FROM users WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $_POST['delete_id']);
        $stmt->execute();
        $stmt->close();
    }
}

// Handle user addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username'])) {
    $username = trim($_POST['username']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $email = trim($_POST['email']);
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;
    $sql = "INSERT INTO users (username, password, email, is_admin) VALUES (?, ?, ?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sssi", $username, $password, $email, $is_admin);
        $stmt->execute();
        $stmt->close();
    }
}

$users = $conn->query("SELECT id, username, email, is_admin FROM users");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>Manage Users</title><link rel="stylesheet" href="../style.css">
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
            <h2>Manage Users</h2>

            <h3>Add User</h3>
            <form action="users.php" method="post" class="generator-form">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="email" name="email" placeholder="Email" required>
                <label><input type="checkbox" name="is_admin" value="1"> Is Admin?</label>
                <button type="submit" class="login-btn">Add User</button>
            </form>

            <h3>Existing Users</h3>
            <table style="width:100%; text-align: left;">
                <tr><th>ID</th><th>Username</th><th>Email</th><th>Admin</th><th>Action</th></tr>
                <?php while($row = $users->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo $row['is_admin'] ? 'Yes' : 'No'; ?></td>
                    <td>
                        <form action="users.php" method="post" onsubmit="return confirm('Are you sure?');">
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
