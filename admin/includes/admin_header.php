<?php
// Includes config and auth are expected to be included before this header
// Fetch admin settings
$stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
$admin_settings = [];
while ($row = $stmt->fetch()) {
    $admin_settings[$row['setting_key']] = $row['setting_value'];
}
$admin_theme = $admin_settings['admin_theme'] ?? 'standard';
$site_theme = $admin_settings['theme'] ?? 'light'; // For reference if needed
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - <?php echo htmlspecialchars($admin_settings['site_name'] ?? 'Showcase'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }

        <?php if ($admin_theme === 'neon'): ?>
            @keyframes neon-pulse {
                0% { box-shadow: 0 0 5px rgba(59, 130, 246, 0.5), 0 0 10px rgba(59, 130, 246, 0.3); }
                50% { box-shadow: 0 0 15px rgba(59, 130, 246, 0.8), 0 0 25px rgba(59, 130, 246, 0.5); }
                100% { box-shadow: 0 0 5px rgba(59, 130, 246, 0.5), 0 0 10px rgba(59, 130, 246, 0.3); }
            }
            .admin-body {
                background-color: #020617;
                color: #f8fafc;
            }
            .sidebar {
                background: rgba(15, 23, 42, 0.8);
                backdrop-filter: blur(10px);
                border-right: 1px solid rgba(59, 130, 246, 0.2);
            }
            .nav-link {
                transition: all 0.3s ease;
                border-radius: 12px;
                margin-bottom: 8px;
                border: 1px solid transparent;
            }
            .nav-link:hover {
                background: rgba(59, 130, 246, 0.1);
                border-color: rgba(59, 130, 246, 0.3);
                color: #60a5fa;
                transform: translateX(5px);
            }
            .nav-link.active {
                background: rgba(59, 130, 246, 0.2);
                border-color: #3b82f6;
                color: #fff;
                box-shadow: 0 0 15px rgba(59, 130, 246, 0.3);
                animation: neon-pulse 2s infinite;
            }
            .admin-card {
                background: rgba(30, 41, 59, 0.5);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(59, 130, 246, 0.1);
                border-radius: 20px;
            }
            .neon-text {
                color: #60a5fa;
                text-shadow: 0 0 8px rgba(59, 130, 246, 0.6);
            }
            .modern-input {
                background: rgba(15, 23, 42, 0.6);
                border: 1px solid rgba(59, 130, 246, 0.2);
                color: white;
                transition: all 0.3s ease;
            }
            .modern-input:focus {
                border-color: #3b82f6;
                box-shadow: 0 0 10px rgba(59, 130, 246, 0.3);
                outline: none;
            }
        <?php elseif ($admin_theme === 'romania'): ?>
            @keyframes romania-bg {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }
            .admin-body {
                background: linear-gradient(-45deg, #002b7f, #fcd116, #ce1126);
                background-size: 400% 400%;
                animation: romania-bg 15s ease infinite;
                color: #111827;
            }
            .sidebar { background-color: #0f172a; color: white; }
            .admin-card { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(5px); border-radius: 16px; }
            .nav-link.active { background-color: #3b82f6; color: white; }
        <?php else: ?>
            .admin-body { background-color: #f3f4f6; color: #1f2937; }
            .sidebar { background-color: #0f172a; color: white; }
            .admin-card { background-color: #ffffff; border-radius: 12px; border: 1px solid #e5e7eb; }
            .nav-link.active { background-color: #3b82f6; color: white; }
        <?php endif; ?>
    </style>
</head>
<body class="admin-body flex min-h-screen">
    <!-- Sidebar -->
    <div class="sidebar w-72 p-6 sticky top-0 h-screen flex flex-col">
        <div class="mb-10 flex items-center px-4">
            <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center mr-3 shadow-lg <?php echo $admin_theme === 'neon' ? 'animate-pulse shadow-blue-500/50' : ''; ?>">
                <i class="fas fa-rocket text-white"></i>
            </div>
            <h1 class="text-xl font-bold <?php echo $admin_theme === 'neon' ? 'neon-text' : ''; ?>">Admin Panel</h1>
        </div>

        <nav class="flex-grow space-y-2">
            <?php
            $current_page = basename($_SERVER['PHP_SELF']);
            $links = [
                ['index.php', 'fas fa-chart-pie', 'Dashboard'],
                ['platforms.php', 'fas fa-layer-group', 'Platforme'],
                ['users.php', 'fas fa-users', 'Utilizatori'],
                ['settings.php', 'fas fa-sliders-h', 'Setări Sistem'],
            ];
            foreach ($links as $link):
                $active = ($current_page === $link[0]) ? 'active' : '';
            ?>
                <a href="<?php echo $link[0]; ?>" class="nav-link flex items-center py-3 px-5 text-sm font-semibold <?php echo $active; ?>">
                    <i class="<?php echo $link[1]; ?> mr-4 text-lg"></i> <?php echo $link[2]; ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="mt-auto pt-6 border-t border-gray-700/50">
            <a href="../logout.php" class="flex items-center py-3 px-5 text-sm font-semibold text-red-400 hover:text-red-300 transition hover:translate-x-1">
                <i class="fas fa-sign-out-alt mr-4 text-lg"></i> Logout
            </a>
        </div>
    </div>

    <!-- Content -->
    <div class="flex-1 p-8 md:p-12">
        <header class="flex justify-between items-center mb-12">
            <div>
                <h2 class="text-3xl font-bold <?php echo $admin_theme === 'neon' ? 'text-white' : 'text-gray-800'; ?>">
                    <?php echo $header_title ?? 'Control Center'; ?>
                </h2>
                <p class="text-sm <?php echo $admin_theme === 'neon' ? 'text-gray-400' : 'text-gray-500'; ?> mt-1">
                    Gestionați-vă ecosistemul digital cu precizie.
                </p>
            </div>
            <div class="flex items-center space-x-6">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold <?php echo $admin_theme === 'neon' ? 'text-white' : 'text-gray-900'; ?>"><?php echo htmlspecialchars($_SESSION['username']); ?></p>
                    <p class="text-xs text-blue-500 font-bold uppercase tracking-wider">Administrator</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-tr from-blue-600 to-blue-400 rounded-2xl flex items-center justify-center text-white font-bold shadow-xl">
                    <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                </div>
            </div>
        </header>
