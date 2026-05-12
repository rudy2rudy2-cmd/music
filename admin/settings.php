<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireAdmin();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updates = [
        'theme' => $_POST['theme'] ?? 'light',
        'admin_theme' => $_POST['admin_theme'] ?? 'standard',
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

$header_title = "Setări Sistem";
require_once 'includes/admin_header.php';
?>

        <?php if ($message): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-xl mb-8 shadow-sm">
                <i class="fas fa-check-circle mr-2"></i> <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="admin-card p-10 shadow-xl max-w-4xl">
            <form method="POST">
                <div class="space-y-10">
                    <div>
                        <h3 class="text-xl font-bold mb-6 flex items-center">
                            <i class="fas fa-globe mr-3 text-blue-500"></i> Informații Generale
                        </h3>
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label class="block text-sm font-bold opacity-75 mb-2">Nume Site</label>
                                <input type="text" name="site_name" value="<?php echo htmlspecialchars($admin_settings['site_name'] ?? ''); ?>" class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border-gray-200 border' : ''; ?>">
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-xl font-bold mb-6 flex items-center">
                            <i class="fas fa-paint-brush mr-3 text-purple-500"></i> Design Site (Frontend)
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <label class="block text-sm font-bold opacity-75 mb-2">Titlu Hero</label>
                                <input type="text" name="hero_title" value="<?php echo htmlspecialchars($admin_settings['hero_title'] ?? ''); ?>" class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border-gray-200 border' : ''; ?>">
                            </div>
                            <div>
                                <label class="block text-sm font-bold opacity-75 mb-2">Subtitlu Hero</label>
                                <input type="text" name="hero_subtitle" value="<?php echo htmlspecialchars($admin_settings['hero_subtitle'] ?? ''); ?>" class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border-gray-200 border' : ''; ?>">
                            </div>
                            <div>
                                <label class="block text-sm font-bold opacity-75 mb-2">Text Buton Comandă</label>
                                <input type="text" name="order_button_text" value="<?php echo htmlspecialchars($admin_settings['order_button_text'] ?? 'Comandă Acum'); ?>" class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border-gray-200 border' : ''; ?>">
                            </div>
                            <div>
                                <label class="block text-sm font-bold opacity-75 mb-2">URL Buton Comandă</label>
                                <input type="text" name="order_url" value="<?php echo htmlspecialchars($admin_settings['order_url'] ?? '#'); ?>" class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border-gray-200 border' : ''; ?>">
                            </div>
                        </div>

                        <label class="block text-sm font-bold opacity-75 mb-4">Selectează Temă Frontend</label>
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                            <?php
                            $frontend_themes = [
                                ['light', 'bg-white border', 'Light'],
                                ['dark', 'bg-slate-800', 'Dark'],
                                ['accent', 'bg-green-500', 'Accent'],
                                ['romania', 'bg-gradient-to-r from-blue-700 via-yellow-400 to-red-600', 'România'],
                                ['premium', 'bg-slate-900 border-2 border-blue-500', 'Premium']
                            ];
                            foreach($frontend_themes as $t):
                            ?>
                            <label class="cursor-pointer group">
                                <input type="radio" name="theme" value="<?php echo $t[0]; ?>" class="peer hidden" <?php echo ($admin_settings['theme'] ?? 'light') === $t[0] ? 'checked' : ''; ?>>
                                <div class="p-4 border rounded-xl text-center transition-all peer-checked:ring-2 peer-checked:ring-blue-500 peer-checked:bg-blue-500/10 <?php echo $admin_theme === 'neon' ? 'border-gray-700' : 'border-gray-200'; ?>">
                                    <div class="w-full h-8 <?php echo $t[1]; ?> mb-2 rounded-lg"></div>
                                    <span class="text-xs font-bold"><?php echo $t[2]; ?></span>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-700/30">
                        <h3 class="text-xl font-bold mb-6 flex items-center">
                            <i class="fas fa-user-shield mr-3 text-red-500"></i> Aspect Panou Admin
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <?php
                            $admin_themes = [
                                ['standard', 'bg-slate-100 border', 'Standard'],
                                ['romania', 'bg-gradient-to-br from-blue-900 to-red-900', 'România'],
                                ['neon', 'bg-black border-2 border-blue-400 shadow-[0_0_10px_rgba(59,130,246,0.5)]', 'Dark Neon']
                            ];
                            foreach($admin_themes as $at):
                            ?>
                            <label class="cursor-pointer group">
                                <input type="radio" name="admin_theme" value="<?php echo $at[0]; ?>" class="peer hidden" <?php echo ($admin_settings['admin_theme'] ?? 'standard') === $at[0] ? 'checked' : ''; ?>>
                                <div class="p-6 border rounded-2xl text-center transition-all peer-checked:ring-2 peer-checked:ring-blue-500 peer-checked:bg-blue-500/10 <?php echo $admin_theme === 'neon' ? 'border-gray-700' : 'border-gray-200'; ?>">
                                    <div class="w-full h-12 <?php echo $at[1]; ?> mb-3 rounded-xl"></div>
                                    <span class="text-sm font-bold"><?php echo $at[2]; ?></span>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white font-bold py-4 rounded-2xl hover:bg-blue-700 transition shadow-lg transform hover:-translate-y-1">
                        <i class="fas fa-save mr-2"></i> Salvează Toate Modificările
                    </button>
                </div>
            </form>
        </div>

<?php require_once 'includes/admin_footer.php'; ?>
