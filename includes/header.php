<?php
session_start();
require_once __DIR__ . '/db.php';

function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Global settings fetch
$stmt = $pdo->query("SELECT * FROM settings");
$site_settings = [];
while ($row = $stmt->fetch()) {
    $site_settings[$row['setting_key']] = $row['setting_value'];
}

$theme = $site_settings['theme'] ?? 'default';
$logo = !empty($site_settings['logo_path']) ? $site_settings['logo_path'] : '';
$copyright = $site_settings['copyright'] ?? 'Copyright 2026 Autor Stoian Rudolf';
$site_title = $site_settings['site_title'] ?? 'HotelDefects';
$logo_size = $site_settings['logo_size'] ?? '32';
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($site_title); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0f172a;
            --sidebar-bg: rgba(255, 255, 255, 0.03);
            --card-bg: rgba(255, 255, 255, 0.03);
            --border-color: rgba(255, 255, 255, 0.05);
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --accent-color: #3b82f6;
        }

        <?php if ($theme === 'black'): ?>
        :root {
            --bg-color: #000000;
            --sidebar-bg: #0a0a0a;
            --card-bg: #0f0f0f;
            --border-color: #1a1a1a;
            --text-primary: #ffffff;
            --text-secondary: #71717a;
            --accent-color: #3f3f46;
        }
        <?php endif; ?>

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-color);
            color: var(--text-primary);
            transition: background 0.3s, color 0.3s;
        }
        .glass {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border-color);
        }
        .sidebar {
            width: 260px;
            transition: all 0.3s;
            background: var(--sidebar-bg);
            backdrop-filter: blur(20px);
        }
        .main-content {
            flex: 1;
        }
        .status-activ {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .status-rezolvat {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }
        .defect-desc {
            color: #ffffff !important;
        }
    </style>
</head>
<body class="flex min-h-screen">
    <?php if (isset($_SESSION['user_id'])): ?>
    <!-- Sidebar -->
    <aside class="sidebar border-r border-white/5 flex flex-col h-screen sticky top-0">
        <div class="p-6">
            <h1 class="text-xl font-bold flex items-center gap-3">
                <?php if ($logo): ?>
                    <img src="<?php echo $logo; ?>" style="height: <?php echo $logo_size; ?>px; width: auto;" class="object-contain">
                <?php else: ?>
                    <i class="fas fa-hotel text-blue-500" style="font-size: <?php echo $logo_size; ?>px;"></i>
                <?php endif; ?>
                <span><?php echo htmlspecialchars($site_title); ?></span>
            </h1>
        </div>

        <nav class="flex-1 px-4 space-y-2">
            <a href="index.php" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/5 transition <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'bg-blue-600/20 text-blue-400' : ''; ?>">
                <i class="fas fa-chart-line w-5"></i> Dashboard
            </a>
            <a href="add_defect.php" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/5 transition <?php echo basename($_SERVER['PHP_SELF']) == 'add_defect.php' ? 'bg-blue-600/20 text-blue-400' : ''; ?>">
                <i class="fas fa-plus-circle w-5"></i> Raport Nou
            </a>
            <?php if (isAdmin()): ?>
            <a href="users.php" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/5 transition <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'bg-blue-600/20 text-blue-400' : ''; ?>">
                <i class="fas fa-users w-5"></i> Utilizatori
            </a>
            <a href="settings.php" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/5 transition <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'bg-blue-600/20 text-blue-400' : ''; ?>">
                <i class="fas fa-cog w-5"></i> Setări Sistem
            </a>
            <?php endif; ?>
        </nav>

        <div class="p-4 mt-auto border-t border-white/5">
            <div class="flex items-center gap-3 px-4 py-3">
                <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-xs font-bold text-white">
                    <?php echo strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)); ?>
                </div>
                <div class="flex-1 overflow-hidden">
                    <p class="text-sm font-medium truncate"><?php echo $_SESSION['username'] ?? 'User'; ?></p>
                    <p class="text-xs text-gray-500 capitalize"><?php echo $_SESSION['role'] ?? 'Staff'; ?></p>
                </div>
                <a href="logout.php" class="text-gray-400 hover:text-red-400 transition" title="Deconectare">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </aside>
    <?php endif; ?>

    <main class="main-content p-8">
