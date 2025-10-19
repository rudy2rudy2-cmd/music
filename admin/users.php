<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !$_SESSION["is_admin"]){
    header("location: ../login.php");
    exit;
}

require_once "../src/config.php";

// Logic to handle user deletion with POST request
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])){
    $delete_id = $_POST['delete_id'];
    $sql = "DELETE FROM users WHERE id = ?";
    if($stmt = $conn->prepare($sql)){
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Logic to handle adding a new user
if($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['delete_id'])){
    // Same logic as in register.php but adapted for admin
    $username = trim($_POST['username']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $email = trim($_POST['email']);
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    $sql = "INSERT INTO users (username, password, email, is_admin) VALUES (?, ?, ?, ?)";
    if($stmt = $conn->prepare($sql)){
        $stmt->bind_param("sssi", $username, $password, $email, $is_admin);
        $stmt->execute();
        $stmt->close();
    }
}

$sql = "SELECT id, username, email, is_admin FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="wrapper">
        <h2>Manage Users</h2>
        <a href="index.php">Back to Dashboard</a>

        <h3>Add New User</h3>
        <form action="users.php" method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Is Admin</label>
                <input type="checkbox" name="is_admin" value="1">
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Add User">
            </div>
        </form>

        <h3>All Users</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Admin</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['is_admin'] ? 'Yes' : 'No'; ?></td>
                        <td>
                            <form action="users.php" method="post" onsubmit="return confirm('Are you sure?');">
                                <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                                <input type="submit" value="Delete" class="btn btn-danger">
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No users found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
