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
        $success = "Setări salvate!";
    }

    // Handle Theme
    if (isset($_POST['theme'])) {
        $theme = $_POST['theme'];
        $stmt = $pdo->prepare("INSERT OR REPLACE INTO settings (setting_key, setting_value) VALUES ('theme', ?)");
        $stmt->execute([$theme]);
        $settings['theme'] = $theme;
        $success = "Temă actualizată!";
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
                // Delete old logo if exists
                if (!empty($settings['logo_path']) && file_exists(__DIR__ . '/' . $settings['logo_path'])) {
                    unlink(__DIR__ . '/' . $settings['logo_path']);
                }

                $db_logo_path = 'uploads/' . $new_name;
                $stmt = $pdo->prepare("INSERT OR REPLACE INTO settings (setting_key, setting_value) VALUES ('logo_path', ?)");
                $stmt->execute([$db_logo_path]);
                $settings['logo_path'] = $db_logo_path;
                $success = "Logo actualizat cu succes!";
            } else {
                $error = "Eroare la încărcarea fișierului.";
            }
        } else {
            $error = "Format fișier neacceptat. Folosiți JPG, PNG, SVG sau WEBP.";
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <h2 class="text-3xl font-bold">Setări Sistem</h2>
        <p class="text-gray-400">Personalizare platformă, logo și drepturi autor</p>
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

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Logo and Copyright Section -->
        <div class="glass p-8 rounded-2xl space-y-6">
            <h3 class="text-xl font-bold border-b border-white/5 pb-4">Identitate Vizuală</h3>

            <form method="POST" enctype="multipart/form-data" class="space-y-6">
                <div>
                    <label class="block text-sm text-gray-400 mb-2 font-medium">Logo Platformă</label>
                    <div class="flex items-center gap-6">
                        <div class="w-24 h-24 bg-white/5 border border-white/10 rounded-xl flex items-center justify-center overflow-hidden">
                            <?php if (!empty($settings['logo_path'])): ?>
                                <img src="<?php echo $settings['logo_path']; ?>" class="max-w-full max-h-full object-contain">
                            <?php else: ?>
                                <i class="fas fa-image text-gray-700 text-3xl"></i>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1">
                            <input type="file" name="logo" class="block w-full text-xs text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-full file:border-0
                                file:text-xs file:font-semibold
                                file:bg-blue-600/10 file:text-blue-400
                                hover:file:bg-blue-600/20 cursor-pointer
                            ">
                            <p class="text-[10px] text-gray-600 mt-2">Format: PNG, JPG, SVG. Recomandat: Fundal transparent.</p>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm text-gray-400 mb-2 font-medium">Text Copyright / Drepturi Platformă</label>
                    <textarea name="copyright" rows="2" class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 transition"><?php echo htmlspecialchars($settings['copyright'] ?? ''); ?></textarea>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition shadow-lg shadow-blue-600/20">
                    Salvează Modificările
                </button>
            </form>
        </div>

        <!-- Appearance Section -->
        <div class="glass p-8 rounded-2xl space-y-6">
            <h3 class="text-xl font-bold border-b border-white/5 pb-4">Aspect și Teme</h3>

            <form method="POST" class="space-y-6">
                <div>
                    <label class="block text-sm text-gray-400 mb-4 font-medium">Selectează Tema</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="cursor-pointer">
                            <input type="radio" name="theme" value="default" <?php echo ($settings['theme'] ?? 'default') == 'default' ? 'checked' : ''; ?> class="peer hidden">
                            <div class="p-4 border border-white/10 rounded-xl text-center peer-checked:bg-blue-600 peer-checked:border-blue-600 transition">
                                <div class="w-full h-12 bg-slate-900 rounded-lg mb-2 flex flex-col p-1 gap-1">
                                    <div class="h-2 w-1/2 bg-blue-500 rounded"></div>
                                    <div class="h-2 w-full bg-white/10 rounded"></div>
                                </div>
                                <span class="text-sm font-bold">Standard</span>
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input type="radio" name="theme" value="black" <?php echo ($settings['theme'] ?? 'default') == 'black' ? 'checked' : ''; ?> class="peer hidden">
                            <div class="p-4 border border-white/10 rounded-xl text-center peer-checked:bg-slate-800 peer-checked:border-slate-700 transition">
                                <div class="w-full h-12 bg-black rounded-lg mb-2 border border-white/5 flex flex-col p-1 gap-1">
                                    <div class="h-2 w-1/2 bg-slate-700 rounded"></div>
                                    <div class="h-2 w-full bg-white/5 rounded"></div>
                                </div>
                                <span class="text-sm font-bold">Noir (Black)</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="pt-10">
                    <button type="submit" class="w-full bg-slate-800 hover:bg-slate-700 text-white font-bold py-3 rounded-xl transition border border-white/10">
                        Aplică Tema
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
