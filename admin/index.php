<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !$_SESSION["is_admin"]){
    header("location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="wrapper">
        <h2>Admin Dashboard</h2>
        <p>Welcome, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>.</p>
        <p>
            <a href="users.php" class="btn btn-primary">Manage Users</a>
            <a href="voices.php" class="btn btn-primary">Manage Voices</a>
            <a href="../logout.php" class="btn btn-danger">Sign Out of Your Account</a>
        </p>
    </div>
</body>
</html>