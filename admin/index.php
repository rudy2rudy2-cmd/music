<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireAdmin();

$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$user_count = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM platforms");
$platform_count = $stmt->fetchColumn();

// Fetch settings
$stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
$settings = [];
while ($row = $stmt->fetch()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
$theme = $settings['theme'] ?? 'light';
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<style>
    <?php if ($theme === 'romania'): ?>
    @keyframes romania-bg {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    .admin-body {
        background: linear-gradient(-45deg, #002b7f, #fcd116, #ce1126);
        background-size: 400% 400%;
        animation: romania-bg 15s ease infinite;
    }
    .admin-card { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(5px); }
    <?php else: ?>
    .admin-body { background-color: #f3f4f6; }
    .admin-card { background-color: #ffffff; }
    <?php endif; ?>
</style>
<body class="admin-body flex min-h-screen">
    <!-- Sidebar -->
    <div class="w-64 bg-slate-900 text-white p-6 sticky top-0 h-screen">
        <h1 class="text-2xl font-bold mb-10 text-blue-400">Admin Panel</h1>
        <nav class="space-y-4">
            <a href="index.php" class="block py-2.5 px-4 rounded transition duration-200 bg-blue-600">
                <i class="fas fa-home mr-2"></i> Dashboard
            </a>
            <a href="platforms.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-slate-800">
                <i class="fas fa-layer-group mr-2"></i> Platforme
            </a>
            <a href="users.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-slate-800">
                <i class="fas fa-users mr-2"></i> Utilizatori
            </a>
            <a href="settings.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-slate-800">
                <i class="fas fa-cog mr-2"></i> Setări
            </a>
            <div class="pt-10">
                <a href="../logout.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-red-600 text-red-400 hover:text-white">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                </a>
            </div>
        </nav>
    </div>

    <!-- Content -->
    <div class="flex-1 p-10">
        <header class="flex justify-between items-center mb-10">
            <h2 class="text-3xl font-bold text-gray-800">Overview</h2>
            <div class="flex items-center space-x-4">
                <span class="text-gray-600">Salut, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold">
                    <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                </div>
            </div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="admin-card p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <i class="fas fa-layer-group text-blue-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500 uppercase font-bold tracking-wider">Total Platforme</p>
                        <p class="text-2xl font-bold text-gray-800"><?php echo $platform_count; ?></p>
                    </div>
                </div>
            </div>
            <div class="admin-card p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <i class="fas fa-users text-green-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500 uppercase font-bold tracking-wider">Total Utilizatori</p>
                        <p class="text-2xl font-bold text-gray-800"><?php echo $user_count; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-10 admin-card p-8 rounded-xl shadow-sm border border-gray-100 text-center">
            <h3 class="text-xl font-bold mb-4">Bine ați venit în panoul de administrare!</h3>
            <p class="text-gray-600">De aici puteți gestiona platformele prezentate pe site, utilizatorii și setările generale.</p>
            <div class="mt-6">
                 <a href="../index.php" target="_blank" class="text-blue-600 font-semibold hover:underline">Vezi Site-ul Frontend</a>
            </div>
        </div>
    </div>
</body>
</html>
