<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireLogin();

// Redirect admin to admin panel if they accidentally come here
if (isAdmin()) {
    header("Location: ../admin/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Utilizator</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <nav class="bg-white shadow-sm p-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold text-blue-600">User Dashboard</h1>
            <div class="flex items-center space-x-4">
                <span>Salut, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="../logout.php" class="text-red-500 hover:underline">Logout</a>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto p-10">
        <div class="bg-white p-8 rounded-xl shadow-md">
            <h2 class="text-2xl font-bold mb-4">Bine ai revenit!</h2>
            <p class="text-gray-600 mb-6">Momentan acesta este panoul tău personal. Aici vei putea vedea informații despre produsele achiziționate sau setările contului tău.</p>

            <div class="border-t pt-6">
                <a href="../index.php" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition">
                    Înapoi la Site
                </a>
            </div>
        </div>
    </main>
</body>
</html>
