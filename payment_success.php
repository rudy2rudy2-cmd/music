<?php
session_start();
require_once 'config.php';

// If user is not logged in, redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !isset($_POST['plan'])) {
    header("location: login.php");
    exit;
}

// Allowed plans and their generation counts
$allowed_plans = [
    'personal' => 10,
    'pro' => 100,
    'business' => 999999 // Simulate "unlimited"
];

$plan = $_POST['plan'];

// Validate the plan from the POST data
if (!array_key_exists($plan, $allowed_plans)) {
    // If plan is invalid, redirect to pricing page
    $_SESSION['payment_error'] = "A apărut o eroare. Planul selectat este invalid.";
    header("location: pricing.php");
    exit;
}

$generations_to_add = $allowed_plans[$plan];
$user_id = $_SESSION['id'];

// Update the user's plan and generation count in the database
$sql = "UPDATE users SET plan = ?, generations_left = generations_left + ? WHERE id = ?";

if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "sii", $plan, $generations_to_add, $user_id);

    if (mysqli_stmt_execute($stmt)) {
        // Success
        $_SESSION['payment_success'] = "Plata a fost finalizată cu succes! Planul tău a fost actualizat.";
    } else {
        // Database error
        $_SESSION['payment_error'] = "A apărut o eroare la actualizarea planului. Te rugăm să încerci din nou.";
    }
    mysqli_stmt_close($stmt);
} else {
    // SQL error
    $_SESSION['payment_error'] = "A apărut o eroare de sistem. Te rugăm să contactezi suportul.";
}

// Redirect back to the main page
header("location: index.php");
exit;
?>
