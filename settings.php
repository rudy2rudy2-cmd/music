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
    // Handle Copyright Text
    if (isset($_POST['copyright'])) {
        $copyright = $_POST['copyright'];
        $stmt = $pdo->prepare("INSERT OR REPLACE INTO settings (setting_key, setting_value) VALUES ('copyright', ?)");
        $stmt->execute([$copyright]);
        $settings['copyright'] = $copyright;
    }

    // Handle Site Title
    if (isset($_POST['site_title'])) {
        $site_title = $_POST['site_title'];
        $stmt = $pdo->prepare("INSERT OR REPLACE INTO settings (setting_key, setting_value) VALUES ('site_title', ?)");
        $stmt->execute([$site_title]);
        $settings['site_title'] = $site_title;
    }

    // Handle Logo Size
    if (isset($_POST['logo_size'])) {
        $logo_size = $_POST['logo_size'];
        $stmt = $pdo->prepare("INSERT OR REPLACE INTO settings (setting_key, setting_value) VALUES ('logo_size', ?)");
        $stmt->execute([$logo_size]);
        $settings['logo_size'] = $logo_size;
    }

    // Handle Theme
    if (isset($_POST['theme'])) {
        $theme = $_POST['theme'];
        $stmt = $pdo->prepare("INSERT OR REPLACE INTO settings (setting_key, setting_value) VALUES ('theme', ?)");
        $stmt->execute([$theme]);
        $settings['theme'] = $theme;
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
            } else {
                $error = "Eroare la încărcarea fișierului.";
            }
        } else {
            $error = "Format fișier neacceptat.";
        }
    }

    if (!$error) $success = "Toate modificările au fost salvate!";
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <h2 class="text-3xl font-bold">Setări Sistem</h2>
        <p class="text-gray-400">Personalizare platformă, logo și identitate</p>
    </div>

    <?php if ($error): ?>
        <div class="bg-red-500/10 border border-red-500/50 text-red-400 p-4 rounded-xl mb-6 flex items-center gap-3">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="bg-green-500/10 border border-green-500/50 text-green-400 p-4 rounded-xl mb-6 flex items-center gap-3">
            <i class="fas fa-check-circle"></i>
            <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Branding Section -->
        <div class="glass p-8 rounded-2xl space-y-6">
            <h3 class="text-xl font-bold border-b border-white/5 pb-4">Identitate Vizuală</h3>

            <div>
                <label class="block text-sm text-gray-400 mb-2 font-medium">Titlu Platformă</label>
                <input type="text" name="site_title" value="<?php echo htmlspecialchars($settings['site_title'] ?? 'HotelDefects'); ?>" class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 transition">
            </div>

            <div>
                <label class="block text-sm text-gray-400 mb-2 font-medium">Logo Platformă</label>
                <div class="flex items-center gap-6 mb-4">
                    <div class="w-20 h-20 bg-white/5 border border-white/10 rounded-xl flex items-center justify-center overflow-hidden">
                        <?php if (!empty($settings['logo_path'])): ?>
                            <img src="<?php echo $settings['logo_path']; ?>" class="max-w-full max-h-full object-contain">
                        <?php else: ?>
                            <i class="fas fa-image text-gray-700 text-2xl"></i>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1">
                        <input type="file" name="logo" class="text-xs text-gray-500 cursor-pointer">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm text-gray-400 mb-2 font-medium">Mărime Logo (Siderbar - px)</label>
                <input type="number" name="logo_size" value="<?php echo htmlspecialchars($settings['logo_size'] ?? '32'); ?>" class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 transition">
            </div>

            <div>
                <label class="block text-sm text-gray-400 mb-2 font-medium">Text Copyright</label>
                <textarea name="copyright" rows="2" class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 transition"><?php echo htmlspecialchars($settings['copyright'] ?? ''); ?></textarea>
            </div>
        </div>

        <!-- Appearance Section -->
        <div class="glass p-8 rounded-2xl space-y-6">
            <h3 class="text-xl font-bold border-b border-white/5 pb-4">Aspect și Teme</h3>

            <div class="grid grid-cols-1 gap-4">
                <label class="cursor-pointer">
                    <input type="radio" name="theme" value="default" <?php echo ($settings['theme'] ?? 'default') == 'default' ? 'checked' : ''; ?> class="peer hidden">
                    <div class="p-6 border border-white/10 rounded-xl flex items-center justify-between peer-checked:bg-blue-600 peer-checked:border-blue-600 transition">
                        <span class="font-bold">Standard Theme</span>
                        <div class="flex gap-1">
                            <div class="w-4 h-4 bg-slate-900 rounded-full border border-white/20"></div>
                            <div class="w-4 h-4 bg-blue-500 rounded-full"></div>
                        </div>
                    </div>
                </label>

                <label class="cursor-pointer">
                    <input type="radio" name="theme" value="black" <?php echo ($settings['theme'] ?? 'default') == 'black' ? 'checked' : ''; ?> class="peer hidden">
                    <div class="p-6 border border-white/10 rounded-xl flex items-center justify-between peer-checked:bg-slate-800 peer-checked:border-slate-700 transition">
                        <span class="font-bold">Noir (Black)</span>
                        <div class="flex gap-1">
                            <div class="w-4 h-4 bg-black rounded-full border border-white/20"></div>
                            <div class="w-4 h-4 bg-slate-700 rounded-full"></div>
                        </div>
                    </div>
                </label>
            </div>

            <div class="pt-8">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl transition shadow-lg shadow-blue-600/20 flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i> Salvează Toate Setările
                </button>
            </div>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
