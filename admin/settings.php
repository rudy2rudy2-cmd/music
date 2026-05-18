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
        'order_url' => $_POST['order_url'] ?? 'offers.php',
        'live_chat_code' => $_POST['live_chat_code'] ?? '',
        'logo_type' => $_POST['logo_type'] ?? 'text',
        'footer_name' => $_POST['footer_name'] ?? '',
        'seo_meta_title' => $_POST['seo_meta_title'] ?? '',
        'seo_meta_description' => $_POST['seo_meta_description'] ?? '',
        'seo_keywords' => $_POST['seo_keywords'] ?? '',
        'use_smtp' => isset($_POST['use_smtp']) ? '1' : '0',
        'smtp_host' => $_POST['smtp_host'] ?? '',
        'smtp_port' => $_POST['smtp_port'] ?? '587',
        'smtp_user' => $_POST['smtp_user'] ?? '',
        'smtp_pass' => $_POST['smtp_pass'] ?? '',
        'smtp_encryption' => $_POST['smtp_encryption'] ?? 'tls'
    ];

    if (isset($_FILES['logo_image']) && $_FILES['logo_image']['error'] === 0) {
        $ext = pathinfo($_FILES['logo_image']['name'], PATHINFO_EXTENSION);
        $filename = 'logo_' . time() . '.' . $ext;
        if (move_uploaded_file($_FILES['logo_image']['tmp_name'], '../uploads/' . $filename)) {
            $updates['logo_image'] = 'uploads/' . $filename;
        }
    }

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
                            <i class="fas fa-globe mr-3 text-blue-500"></i> Identitate & Footer
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-bold opacity-75 mb-2">Nume Site (Header)</label>
                                <input type="text" name="site_name" value="<?php echo htmlspecialchars($admin_settings['site_name'] ?? ''); ?>" class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border-gray-200 border' : ''; ?>">
                            </div>
                            <div>
                                <label class="block text-sm font-bold opacity-75 mb-2">Nume Footer</label>
                                <input type="text" name="footer_name" value="<?php echo htmlspecialchars($admin_settings['footer_name'] ?? ''); ?>" class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border-gray-200 border' : ''; ?>">
                            </div>
                            <div>
                                <label class="block text-sm font-bold opacity-75 mb-2">Tip Logo</label>
                                <select name="logo_type" class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border-gray-200 border' : ''; ?>">
                                    <option value="text" <?php echo ($admin_settings['logo_type'] ?? '') === 'text' ? 'selected' : ''; ?>>Doar Text</option>
                                    <option value="image" <?php echo ($admin_settings['logo_type'] ?? '') === 'image' ? 'selected' : ''; ?>>Doar Imagine</option>
                                    <option value="both" <?php echo ($admin_settings['logo_type'] ?? '') === 'both' ? 'selected' : ''; ?>>Imagine + Text</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold opacity-75 mb-2">Încărcare Logo</label>
                                <input type="file" name="logo_image" class="w-full p-2 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border-gray-200 border' : ''; ?>">
                                <?php if(!empty($admin_settings['logo_image'])): ?>
                                    <img src="/<?php echo $admin_settings['logo_image']; ?>" class="h-8 mt-2">
                                <?php endif; ?>
                            </div>
                            <div>
                                <label class="block text-sm font-bold opacity-75 mb-2">Cod Live Chat (Script)</label>
                                <textarea name="live_chat_code" rows="1" class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border-gray-200 border' : ''; ?>"><?php echo htmlspecialchars($admin_settings['live_chat_code'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-700/30">
                        <h3 class="text-xl font-bold mb-6 flex items-center text-green-500">
                            <i class="fas fa-search mr-3"></i> Configurare SEO
                        </h3>
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label class="block text-sm font-bold opacity-75 mb-2">Meta Title</label>
                                <input type="text" name="seo_meta_title" value="<?php echo htmlspecialchars($admin_settings['seo_meta_title'] ?? ''); ?>" class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border-gray-200 border' : ''; ?>">
                            </div>
                            <div>
                                <label class="block text-sm font-bold opacity-75 mb-2">Meta Description</label>
                                <textarea name="seo_meta_description" rows="2" class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border-gray-200 border' : ''; ?>"><?php echo htmlspecialchars($admin_settings['seo_meta_description'] ?? ''); ?></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-bold opacity-75 mb-2">Keywords (separate prin virgula)</label>
                                <input type="text" name="seo_keywords" value="<?php echo htmlspecialchars($admin_settings['seo_keywords'] ?? ''); ?>" class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border-gray-200 border' : ''; ?>">
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-700/30">
                        <h3 class="text-xl font-bold mb-6 flex items-center text-orange-500">
                            <i class="fas fa-paper-plane mr-3"></i> Configurare Email (SMTP)
                        </h3>
                        <div class="mb-6">
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="checkbox" name="use_smtp" value="1" <?php echo ($admin_settings['use_smtp'] ?? '0') === '1' ? 'checked' : ''; ?> class="w-5 h-5 rounded border-gray-300">
                                <span class="font-bold">Folosește SMTP (PHPMailer)</span>
                            </label>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <input type="text" name="smtp_host" value="<?php echo htmlspecialchars($admin_settings['smtp_host'] ?? ''); ?>" placeholder="SMTP Host" class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border-gray-200 border' : ''; ?>">
                            <input type="text" name="smtp_port" value="<?php echo htmlspecialchars($admin_settings['smtp_port'] ?? '587'); ?>" placeholder="Port" class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border-gray-200 border' : ''; ?>">
                            <input type="text" name="smtp_user" value="<?php echo htmlspecialchars($admin_settings['smtp_user'] ?? ''); ?>" placeholder="Utilizator / Email" class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border-gray-200 border' : ''; ?>">
                            <input type="password" name="smtp_pass" value="<?php echo htmlspecialchars($admin_settings['smtp_pass'] ?? ''); ?>" placeholder="Parolă" class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border-gray-200 border' : ''; ?>">
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
                                <input type="text" name="order_url" value="<?php echo htmlspecialchars($admin_settings['order_url'] ?? 'offers.php'); ?>" class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border-gray-200 border' : ''; ?>">
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
                                ['premium', 'bg-slate-900 border-2 border-blue-500', 'Premium'],
                                ['monochrome', 'bg-black border border-white', 'Monochrome']
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
