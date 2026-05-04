<?php
require_once __DIR__ . '/includes/db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$error = "";
$success = "";

// Fetch current settings
$stmt = $pdo->query("SELECT * FROM settings");
$settings = [];
while ($row = $stmt->fetch()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $keys = ['copyright', 'site_title', 'logo_size', 'theme', 'total_rooms', 'default_filter', 'timezone'];

    foreach ($keys as $key) {
        if (isset($_POST[$key])) {
            $val = $_POST[$key];
            $stmt = $pdo->prepare("INSERT OR REPLACE INTO settings (setting_key, setting_value) VALUES (?, ?)");
            $stmt->execute([$key, $val]);
            $settings[$key] = $val;
        }
    }

    // Handle Logo Upload
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'svg', 'webp'];
        $filename = $_FILES['logo']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $new_name = 'logo_' . time() . '.' . $ext;
            $upload_path = __DIR__ . '/uploads/' . $new_name;

            if (move_uploaded_file($_FILES['logo']['tmp_name'], $upload_path)) {
                if (!empty($settings['logo_path']) && file_exists(__DIR__ . '/' . $settings['logo_path'])) {
                    @unlink(__DIR__ . '/' . $settings['logo_path']);
                }

                $db_logo_path = 'uploads/' . $new_name;
                $stmt = $pdo->prepare("INSERT OR REPLACE INTO settings (setting_key, setting_value) VALUES ('logo_path', ?)");
                $stmt->execute([$db_logo_path]);
                $settings['logo_path'] = $db_logo_path;
            }
        }
    }

    $success = "Toate modificările au fost salvate!";
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <h2 class="text-3xl font-bold">Setări Sistem</h2>
        <p class="text-gray-400">Configurare avansată platformă</p>
    </div>

    <?php if ($success): ?>
        <div class="bg-green-500/10 border border-green-500/50 text-green-400 p-4 rounded-xl mb-6 flex items-center gap-3">
            <i class="fas fa-check-circle"></i>
            <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Branding Section -->
        <div class="glass p-8 rounded-2xl space-y-6">
            <h3 class="text-xl font-bold border-b border-white/5 pb-4">Identitate și Text</h3>

            <div>
                <label class="block text-sm text-gray-400 mb-2">Titlu Platformă</label>
                <input type="text" name="site_title" value="<?php echo htmlspecialchars($settings['site_title'] ?? 'HotelDefects'); ?>" class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 transition">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-400 mb-2">Total Camere</label>
                    <input type="number" name="total_rooms" value="<?php echo htmlspecialchars($settings['total_rooms'] ?? '100'); ?>" class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 transition">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-2">Logo Size (px)</label>
                    <input type="number" name="logo_size" value="<?php echo htmlspecialchars($settings['logo_size'] ?? '32'); ?>" class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 transition">
                </div>
            </div>

            <div>
                <label class="block text-sm text-gray-400 mb-2">Text Copyright</label>
                <textarea name="copyright" rows="2" class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 transition"><?php echo htmlspecialchars($settings['copyright'] ?? ''); ?></textarea>
            </div>
        </div>

        <!-- System Section -->
        <div class="glass p-8 rounded-2xl space-y-6">
            <h3 class="text-xl font-bold border-b border-white/5 pb-4">Preferințe și Timp</h3>

            <div>
                <label class="block text-sm text-gray-400 mb-2">Filtru Dashboard Implicit</label>
                <select name="default_filter" class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 appearance-none">
                    <option value="all" <?php echo ($settings['default_filter'] ?? 'all') == 'all' ? 'selected' : ''; ?> class="bg-slate-900">Toate</option>
                    <option value="active" <?php echo ($settings['default_filter'] ?? 'all') == 'active' ? 'selected' : ''; ?> class="bg-slate-900">Doar Active</option>
                    <option value="resolved" <?php echo ($settings['default_filter'] ?? 'all') == 'resolved' ? 'selected' : ''; ?> class="bg-slate-900">Doar Rezolvate</option>
                </select>
            </div>

            <div>
                <label class="block text-sm text-gray-400 mb-2">Fus Orar (Timezone)</label>
                <input type="text" name="timezone" value="<?php echo htmlspecialchars($settings['timezone'] ?? 'Europe/Bucharest'); ?>" class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 transition" placeholder="Ex: Europe/Bucharest">
                <p class="text-[10px] text-gray-600 mt-1">Setați pentru ora locală corectă.</p>
            </div>

            <div>
                <label class="block text-sm text-gray-400 mb-2">Logo Nou</label>
                <input type="file" name="logo" class="text-xs text-gray-500 cursor-pointer">
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl transition shadow-lg shadow-blue-600/20">
                    Salvează Setările
                </button>
            </div>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
