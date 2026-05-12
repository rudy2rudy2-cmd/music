<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireAdmin();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_platform'])) {
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $version = $_POST['version'] ?? '';
        $demo_url = $_POST['demo_url'] ?? '';

        $image_url = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

            if (in_array($ext, $allowed_exts)) {
                $filename = uniqid() . '.' . $ext;
                if (move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/' . $filename)) {
                    $image_url = 'uploads/' . $filename;
                }
            } else {
                $message = "Eroare: Tip de fișier nepermis.";
            }
        }

        if(empty($message)) {
            $stmt = $pdo->prepare("INSERT INTO platforms (title, description, version, demo_url, image_url) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$title, $description, $version, $demo_url, $image_url]);
            $message = "Platformă adăugată cu succes!";
        }
    }

    if (isset($_POST['delete_id'])) {
        $stmt = $pdo->prepare("DELETE FROM platforms WHERE id = ?");
        $stmt->execute([$_POST['delete_id']]);
        $message = "Platformă ștearsă!";
    }
}

$stmt = $pdo->query("SELECT * FROM platforms ORDER BY created_at DESC");
$platforms = $stmt->fetchAll();

$header_title = "Gestionare Platforme";
require_once 'includes/admin_header.php';
?>

        <?php if ($message): ?>
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-6 py-4 rounded-xl mb-8 shadow-sm">
                <i class="fas fa-info-circle mr-2"></i> <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Formular Adăugare -->
        <div class="admin-card p-10 shadow-lg mb-12">
            <h3 class="text-xl font-bold mb-8 flex items-center">
                <i class="fas fa-plus-circle mr-3 text-blue-500"></i> Adaugă Platformă Nouă
            </h3>
            <form method="POST" enctype="multipart/form-data">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-sm font-bold opacity-75 mb-2">Titlu</label>
                        <input type="text" name="title" required class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border-gray-200 border' : ''; ?>">
                    </div>
                    <div>
                        <label class="block text-sm font-bold opacity-75 mb-2">Versiune</label>
                        <input type="text" name="version" placeholder="1.0.0" class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border-gray-200 border' : ''; ?>">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold opacity-75 mb-2">Descriere Detaliată</label>
                        <textarea name="description" rows="4" class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border-gray-200 border' : ''; ?>"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold opacity-75 mb-2">URL Demo</label>
                        <input type="url" name="demo_url" placeholder="https://demo.example.com" class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border-gray-200 border' : ''; ?>">
                    </div>
                    <div>
                        <label class="block text-sm font-bold opacity-75 mb-2">Imagine Prezentare</label>
                        <input type="file" name="image" class="w-full p-2 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border-gray-200 border' : ''; ?>">
                    </div>
                </div>
                <button type="submit" name="add_platform" class="mt-8 bg-blue-600 text-white px-10 py-3 rounded-xl font-bold hover:bg-blue-700 transition shadow-lg flex items-center">
                    <i class="fas fa-save mr-2"></i> Adaugă Platformă
                </button>
            </form>
        </div>

        <!-- Tabel Listare -->
        <div class="admin-card shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="<?php echo $admin_theme === 'neon' ? 'bg-slate-800/50' : 'bg-gray-50'; ?> border-b border-gray-700/30">
                        <tr>
                            <th class="px-8 py-5 text-sm font-bold uppercase tracking-wider opacity-60">Previzualizare</th>
                            <th class="px-8 py-5 text-sm font-bold uppercase tracking-wider opacity-60">Titlu</th>
                            <th class="px-8 py-5 text-sm font-bold uppercase tracking-wider opacity-60">Versiune</th>
                            <th class="px-8 py-5 text-sm font-bold uppercase tracking-wider opacity-60">Acțiuni</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/20">
                        <?php foreach ($platforms as $platform): ?>
                        <tr class="hover:bg-blue-500/5 transition">
                            <td class="px-8 py-6">
                                <?php if ($platform['image_url']): ?>
                                    <img src="../<?php echo $platform['image_url']; ?>" class="h-16 w-16 object-cover rounded-xl shadow-lg border border-gray-700/30">
                                <?php else: ?>
                                    <div class="h-16 w-16 bg-gray-700/20 rounded-xl flex items-center justify-center text-gray-500">
                                        <i class="fas fa-image text-xl"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-8 py-6">
                                <div class="font-bold text-lg <?php echo $admin_theme === 'neon' ? 'text-white' : 'text-gray-800'; ?>">
                                    <?php echo htmlspecialchars($platform['title']); ?>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="bg-blue-500/10 text-blue-500 px-3 py-1 rounded-full text-xs font-extrabold uppercase">
                                    v<?php echo htmlspecialchars($platform['version']); ?>
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center space-x-6">
                                    <a href="edit_platform.php?id=<?php echo $platform['id']; ?>" class="text-blue-400 hover:text-blue-300 font-bold flex items-center">
                                        <i class="fas fa-edit mr-2"></i> Edit
                                    </a>
                                    <form method="POST" onsubmit="return confirm('Sigur dorești să ștergi?');" class="inline">
                                        <input type="hidden" name="delete_id" value="<?php echo $platform['id']; ?>">
                                        <button type="submit" class="text-red-500 hover:text-red-400 font-bold flex items-center">
                                            <i class="fas fa-trash-alt mr-2"></i> Șterge
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($platforms)): ?>
                        <tr>
                            <td colspan="4" class="px-8 py-16 text-center opacity-40 italic">
                                <i class="fas fa-folder-open text-4xl mb-4 block"></i>
                                Nu există platforme adăugate.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

<?php require_once 'includes/admin_footer.php'; ?>
