<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireAdmin();

$message = '';

if (isset($_POST['add_link'])) {
    $title = $_POST['title'];
    $url = $_POST['url'];
    $location = $_POST['location'];
    $order = $_POST['sort_order'] ?? 0;

    $stmt = $pdo->prepare("INSERT INTO links (title, url, location, sort_order) VALUES (?, ?, ?, ?)");
    $stmt->execute([$title, $url, $location, $order]);
    $message = "Link adăugat!";
}

if (isset($_POST['delete_id'])) {
    $stmt = $pdo->prepare("DELETE FROM links WHERE id = ?");
    $stmt->execute([$_POST['delete_id']]);
    $message = "Link șters!";
}

$stmt = $pdo->query("SELECT * FROM links ORDER BY location, sort_order ASC");
$links = $stmt->fetchAll();

$header_title = "Gestionare Link-uri Meniu";
require_once 'includes/admin_header.php';
?>

        <?php if ($message): ?>
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-6 py-4 rounded-xl mb-8 shadow-sm">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="admin-card p-10 shadow-lg mb-12">
            <h3 class="text-xl font-bold mb-8 flex items-center">
                <i class="fas fa-link mr-3 text-blue-500"></i> Adaugă Link Nou
            </h3>
            <form method="POST">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-sm font-bold opacity-75 mb-2">Titlu Link</label>
                        <input type="text" name="title" required class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border border-gray-200' : ''; ?>">
                    </div>
                    <div>
                        <label class="block text-sm font-bold opacity-75 mb-2">URL Link</label>
                        <input type="text" name="url" required class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border border-gray-200' : ''; ?>">
                    </div>
                    <div>
                        <label class="block text-sm font-bold opacity-75 mb-2">Locație</label>
                        <select name="location" class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border border-gray-200' : ''; ?>">
                            <option value="header">Header (Meniu Sus)</option>
                            <option value="footer">Footer (Meniu Jos)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold opacity-75 mb-2">Ordine</label>
                        <input type="number" name="sort_order" value="0" class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border border-gray-200' : ''; ?>">
                    </div>
                </div>
                <button type="submit" name="add_link" class="mt-6 bg-blue-600 text-white px-10 py-3 rounded-xl font-bold hover:bg-blue-700 transition">Adaugă în Meniu</button>
            </form>
        </div>

        <div class="admin-card shadow-lg overflow-hidden">
             <table class="w-full text-left">
                <thead class="bg-gray-50/10 border-b border-gray-700/30">
                    <tr>
                        <th class="px-8 py-5">Titlu</th>
                        <th class="px-8 py-5">URL</th>
                        <th class="px-8 py-5">Locație</th>
                        <th class="px-8 py-5">Ordine</th>
                        <th class="px-8 py-5">Acțiuni</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/20">
                    <?php foreach ($links as $l): ?>
                    <tr>
                        <td class="px-8 py-6 font-bold"><?php echo htmlspecialchars($l['title']); ?></td>
                        <td class="px-8 py-6 opacity-60 text-sm"><?php echo htmlspecialchars($l['url']); ?></td>
                        <td class="px-8 py-6 uppercase text-[10px] font-extrabold tracking-widest"><?php echo $l['location']; ?></td>
                        <td class="px-8 py-6"><?php echo $l['sort_order']; ?></td>
                        <td class="px-8 py-6">
                            <form method="POST" onsubmit="return confirm('Ștergi link-ul?');">
                                <input type="hidden" name="delete_id" value="<?php echo $l['id']; ?>">
                                <button type="submit" class="text-red-500 hover:text-red-400 font-bold">Șterge</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
             </table>
        </div>

<?php require_once 'includes/admin_footer.php'; ?>
