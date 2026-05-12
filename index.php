<?php
if (!file_exists('includes/config.php')) {
    header("Location: install.php");
    exit();
}
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Fetch settings
$stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
$settings = [];
while ($row = $stmt->fetch()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

$theme = $settings['theme'] ?? 'light';

// Fetch platforms
$stmt = $pdo->query("SELECT * FROM platforms ORDER BY created_at DESC");
$platforms = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $settings['site_name'] ?? 'Showcase'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        <?php if ($theme === 'dark'): ?>
        body { background-color: #1a202c; color: #e2e8f0; }
        .card { background-color: #2d3748; color: #e2e8f0; }
        <?php elseif ($theme === 'accent'): ?>
        body { background-color: #f0f4f8; color: #334e68; }
        .card { background-color: #ffffff; border-top: 4px solid #48bb78; }
        .btn-primary { background-color: #48bb78; }
        <?php else: ?>
        body { background-color: #f8fafc; color: #1e293b; }
        .card { background-color: #ffffff; }
        <?php endif; ?>
    </style>
</head>
<body class="min-h-screen">
    <nav class="p-6 flex justify-between items-center max-w-7xl mx-auto">
        <h1 class="text-2xl font-bold text-blue-600"><?php echo $settings['site_name'] ?? 'Showcase'; ?></h1>
        <div class="space-x-4">
            <?php if (isLoggedIn()): ?>
                <?php if (isAdmin()): ?>
                    <a href="admin/index.php" class="hover:text-blue-500 font-medium">Admin</a>
                <?php else: ?>
                    <a href="dashboard/index.php" class="hover:text-blue-500 font-medium">Dashboard</a>
                <?php endif; ?>
                <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 transition">Logout</a>
            <?php else: ?>
                <a href="login.php" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">Login</a>
            <?php endif; ?>
        </div>
    </nav>

    <header class="py-16 text-center">
        <h2 class="text-4xl font-extrabold mb-4">Platformele Noastre Web</h2>
        <p class="text-lg opacity-80 max-w-2xl mx-auto">Explorați creațiile noastre recente și testați demo-urile interactive.</p>
    </header>

    <main class="max-w-7xl mx-auto px-6 pb-24">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($platforms as $platform): ?>
                <div class="card rounded-2xl shadow-xl overflow-hidden flex flex-col transition-transform hover:scale-105">
                    <?php if ($platform['image_url']): ?>
                        <img src="<?php echo $platform['image_url']; ?>" alt="<?php echo $platform['title']; ?>" class="h-48 w-full object-cover">
                    <?php else: ?>
                        <div class="h-48 w-full bg-gray-200 flex items-center justify-center text-gray-400">
                            Fără Imagine
                        </div>
                    <?php endif; ?>
                    <div class="p-6 flex-grow">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-xl font-bold"><?php echo htmlspecialchars($platform['title']); ?></h3>
                            <span class="text-xs font-semibold bg-blue-100 text-blue-800 px-2 py-1 rounded">v<?php echo htmlspecialchars($platform['version']); ?></span>
                        </div>
                        <p class="text-sm opacity-70 mb-6"><?php echo nl2br(htmlspecialchars($platform['description'])); ?></p>
                    </div>
                    <div class="p-6 pt-0 mt-auto">
                        <?php if ($platform['demo_url']): ?>
                            <a href="<?php echo htmlspecialchars($platform['demo_url']); ?>" target="_blank" class="block w-full text-center bg-blue-600 text-white font-bold py-3 rounded-xl hover:bg-blue-700 transition shadow-lg btn-primary">
                                Rulare Demo
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (empty($platforms)): ?>
                <div class="col-span-full text-center py-12 opacity-50">
                    Nu există platforme adăugate încă.
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer class="text-center py-8 opacity-60 text-sm">
        &copy; <?php echo date('Y'); ?> <?php echo $settings['site_name'] ?? 'Showcase'; ?>. Toate drepturile rezervate.
    </footer>
</body>
</html>
