<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireAdmin();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updates = [
        'theme' => $_POST['theme'] ?? 'light',
        'site_name' => $_POST['site_name'] ?? 'Web Showcase',
        'hero_title' => $_POST['hero_title'] ?? '',
        'hero_subtitle' => $_POST['hero_subtitle'] ?? '',
        'order_button_text' => $_POST['order_button_text'] ?? 'Comandă Acum',
        'order_url' => $_POST['order_url'] ?? '#'
    ];

    foreach ($updates as $key => $value) {
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$key, $value, $value]);
    }
    $message = "Setări salvate cu succes!";
}

$stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
$settings = [];
while ($row = $stmt->fetch()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Setări Sistem</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 flex">
    <div class="w-64 bg-slate-900 text-white min-h-screen p-6">
        <h1 class="text-2xl font-bold mb-10 text-blue-400">Admin Panel</h1>
        <nav class="space-y-4">
            <a href="index.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-slate-800">
                <i class="fas fa-home mr-2"></i> Dashboard
            </a>
            <a href="platforms.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-slate-800">
                <i class="fas fa-layer-group mr-2"></i> Platforme
            </a>
            <a href="users.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-slate-800">
                <i class="fas fa-users mr-2"></i> Utilizatori
            </a>
            <a href="settings.php" class="block py-2.5 px-4 rounded transition duration-200 bg-blue-600">
                <i class="fas fa-cog mr-2"></i> Setări
            </a>
        </nav>
    </div>

    <div class="flex-1 p-10">
        <h2 class="text-3xl font-bold mb-8">Setări Site</h2>

        <?php if ($message): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100 max-w-2xl">
            <form method="POST">
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nume Site</label>
                        <input type="text" name="site_name" value="<?php echo htmlspecialchars($settings['site_name'] ?? ''); ?>" class="mt-1 block w-full p-2 border rounded-md">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Titlu Principal (Hero)</label>
                            <input type="text" name="hero_title" value="<?php echo htmlspecialchars($settings['hero_title'] ?? ''); ?>" class="mt-1 block w-full p-2 border rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Subtitlu (Hero)</label>
                            <input type="text" name="hero_subtitle" value="<?php echo htmlspecialchars($settings['hero_subtitle'] ?? ''); ?>" class="mt-1 block w-full p-2 border rounded-md">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Text Buton Comandă</label>
                            <input type="text" name="order_button_text" value="<?php echo htmlspecialchars($settings['order_button_text'] ?? 'Comandă Acum'); ?>" class="mt-1 block w-full p-2 border rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">URL Buton Comandă</label>
                            <input type="text" name="order_url" value="<?php echo htmlspecialchars($settings['order_url'] ?? '#'); ?>" class="mt-1 block w-full p-2 border rounded-md">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Temă Interfață (Frontend)</label>
                        <div class="grid grid-cols-3 gap-4">
                            <label class="cursor-pointer">
                                <input type="radio" name="theme" value="light" class="peer hidden" <?php echo ($settings['theme'] ?? 'light') === 'light' ? 'checked' : ''; ?>>
                                <div class="p-4 border rounded-lg text-center peer-checked:border-blue-600 peer-checked:bg-blue-50">
                                    <div class="w-full h-10 bg-white border mb-2 rounded"></div>
                                    Light
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="theme" value="dark" class="peer hidden" <?php echo ($settings['theme'] ?? '') === 'dark' ? 'checked' : ''; ?>>
                                <div class="p-4 border rounded-lg text-center peer-checked:border-blue-600 peer-checked:bg-blue-50">
                                    <div class="w-full h-10 bg-slate-800 mb-2 rounded"></div>
                                    Dark
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="theme" value="accent" class="peer hidden" <?php echo ($settings['theme'] ?? '') === 'accent' ? 'checked' : ''; ?>>
                                <div class="p-4 border rounded-lg text-center peer-checked:border-blue-600 peer-checked:bg-blue-50">
                                    <div class="w-full h-10 bg-green-500 mb-2 rounded"></div>
                                    Accent
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="theme" value="romania" class="peer hidden" <?php echo ($settings['theme'] ?? '') === 'romania' ? 'checked' : ''; ?>>
                                <div class="p-4 border rounded-lg text-center peer-checked:border-blue-600 peer-checked:bg-blue-50">
                                    <div class="w-full h-10 bg-gradient-to-r from-blue-700 via-yellow-400 to-red-600 mb-2 rounded animate-pulse"></div>
                                    România
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="theme" value="premium" class="peer hidden" <?php echo ($settings['theme'] ?? '') === 'premium' ? 'checked' : ''; ?>>
                                <div class="p-4 border rounded-lg text-center peer-checked:border-blue-600 peer-checked:bg-blue-50">
                                    <div class="w-full h-10 bg-slate-900 border-2 border-blue-500 mb-2 rounded shadow-[0_0_10px_rgba(59,130,246,0.5)]"></div>
                                    Premium
                                </div>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 rounded-lg hover:bg-blue-700 transition">
                        Salvează Modificările
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
