<?php
require_once 'config.php';
require_once 'auth.php';

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($settings['site_name'] ?? 'Showcase'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Poppins:wght@400;600;700&family=Inter:wght@400;600&family=Orbitron:wght@400;700&family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        <?php if ($theme === 'dark'): ?>
            body { background-color: #111827; color: #f3f4f6; }
            .card { background-color: #1f2937; color: #f3f4f6; }
            .header-bar { background-color: #1f2937; }
        <?php elseif ($theme === 'accent'): ?>
            body { background-color: #f0fdf4; color: #166534; }
            .card { background-color: #ffffff; border-top: 4px solid #22c55e; }
            .header-bar { background-color: #ffffff; border-bottom: 2px solid #22c55e; }
        <?php elseif ($theme === 'romania'): ?>
            @keyframes romania-bg {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }
            body {
                background: linear-gradient(-45deg, #002b7f, #fcd116, #ce1126);
                background-size: 400% 400%;
                animation: romania-bg 15s ease infinite;
                color: #ffffff;
            }
            .card { background: rgba(255, 255, 255, 0.9); color: #111827; backdrop-filter: blur(5px); }
            .header-bar { background: rgba(0, 43, 127, 0.8); backdrop-filter: blur(10px); }
            .footer-bar { background: rgba(206, 17, 38, 0.8); backdrop-filter: blur(10px); }
        <?php elseif ($theme === 'premium'): ?>
            @keyframes premium-bg {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }
            body {
                background: linear-gradient(-45deg, #0f172a, #1e293b, #020617);
                background-size: 400% 400%;
                animation: premium-bg 10s ease infinite;
                color: #f8fafc;
                font-family: 'Poppins', sans-serif;
            }
            .header-bar {
                background: rgba(15, 23, 42, 0.6);
                backdrop-filter: blur(12px);
                border-bottom: 1px solid rgba(59, 130, 246, 0.3);
                box-shadow: 0 4px 20px -5px rgba(59, 130, 246, 0.2);
            }
            .premium-glow { text-shadow: 0 0 10px rgba(59, 130, 246, 0.8); }
            .card {
                background: rgba(30, 41, 59, 0.7);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(59, 130, 246, 0.2);
                color: #f1f5f9;
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .card:hover {
                border-color: rgba(59, 130, 246, 0.6);
                box-shadow: 0 0 30px rgba(59, 130, 246, 0.2);
                transform: translateY(-10px);
            }
            .premium-btn {
                background: linear-gradient(90deg, #3b82f6, #2563eb);
                box-shadow: 0 0 15px rgba(59, 130, 246, 0.5);
                border: none;
            }
            .premium-btn:hover {
                box-shadow: 0 0 25px rgba(59, 130, 246, 0.8);
            }

            /* Premium Eagle & Code Animations */
            @keyframes float-eagle {
                0% { transform: translate(0, 0) rotate(0deg); opacity: 0.1; }
                50% { transform: translate(30px, -50px) rotate(5deg); opacity: 0.2; }
                100% { transform: translate(0, 0) rotate(0deg); opacity: 0.1; }
            }
            @keyframes scroll-code {
                0% { transform: translateY(100%); opacity: 0; }
                10% { opacity: 0.5; }
                90% { opacity: 0.5; }
                100% { transform: translateY(-100%); opacity: 0; }
            }
            .eagle-bg {
                position: absolute;
                top: 10%;
                right: 5%;
                font-size: 300px;
                color: #3b82f6;
                filter: blur(2px);
                animation: float-eagle 20s ease-in-out infinite;
                z-index: 0;
                pointer-events: none;
            }
            .code-lines {
                position: absolute;
                top: 0;
                left: 10%;
                width: 300px;
                height: 100%;
                font-family: 'monospace';
                font-size: 14px;
                color: #3b82f6;
                opacity: 0.15;
                overflow: hidden;
                z-index: 0;
                pointer-events: none;
            }
            .code-line {
                display: block;
                white-space: nowrap;
                animation: scroll-code 15s linear infinite;
            }
        <?php else: ?>
            body { background-color: #f8fafc; color: #1e293b; }
            .card { background-color: #ffffff; }
            .header-bar { background-color: #ffffff; border-bottom: 1px solid #e2e8f0; }
        <?php endif; ?>
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <nav class="header-bar sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <a href="index.php" class="text-2xl font-bold flex items-center">
                <?php if($theme === 'romania'): ?>
                    <span class="text-yellow-400 mr-2"><i class="fas fa-flag"></i></span>
                <?php endif; ?>
                <span class="<?php echo ($theme === 'romania' ? 'text-white' : 'text-blue-600'); ?>"><?php echo htmlspecialchars($settings['site_name'] ?? 'Showcase'); ?></span>
            </a>
            <div class="space-x-6 flex items-center">
                <a href="index.php" class="font-medium hover:opacity-75 transition <?php echo ($theme === 'premium' ? 'text-blue-400' : ''); ?>">Acasă</a>
                <?php if (isLoggedIn()): ?>
                    <?php if (isAdmin()): ?>
                        <a href="admin/index.php" class="font-medium hover:opacity-75 transition">Admin</a>
                    <?php else: ?>
                        <a href="dashboard/index.php" class="font-medium hover:opacity-75 transition">Dashboard</a>
                    <?php endif; ?>
                    <a href="logout.php" class="bg-red-500 text-white px-5 py-2 rounded-lg font-bold hover:bg-red-600 transition shadow-md">Logout</a>
                <?php else: ?>
                    <a href="register.php" class="font-medium hover:opacity-75 transition">Înregistrare</a>
                    <a href="login.php" class="bg-blue-600 text-white px-5 py-2 rounded-lg font-bold hover:bg-blue-700 transition shadow-md">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
