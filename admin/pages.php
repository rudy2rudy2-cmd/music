<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireAdmin();

$message = '';

if (isset($_POST['add_page'])) {
    $title = $_POST['title'];
    $slug = $_POST['slug'];
    $content = $_POST['content'];
    $seo_title = $_POST['seo_title'];
    $seo_description = $_POST['seo_description'];

    $stmt = $pdo->prepare("INSERT INTO pages (title, slug, content, seo_title, seo_description) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$title, $slug, $content, $seo_title, $seo_description]);
    $message = "Pagină creată!";
}

if (isset($_POST['delete_id'])) {
    $stmt = $pdo->prepare("DELETE FROM pages WHERE id = ?");
    $stmt->execute([$_POST['delete_id']]);
    $message = "Pagină ștearsă!";
}

$stmt = $pdo->query("SELECT * FROM pages ORDER BY created_at DESC");
$pages = $stmt->fetchAll();

$header_title = "Gestionare Pagini";
require_once 'includes/admin_header.php';
?>

        <?php if ($message): ?>
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-6 py-4 rounded-xl mb-8 shadow-sm">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="admin-card p-10 shadow-lg mb-12">
            <h3 class="text-xl font-bold mb-8 flex items-center">
                <i class="fas fa-file-alt mr-3 text-blue-500"></i> Adaugă Pagină Nouă
            </h3>
            <form method="POST">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div>
                        <label class="block text-sm font-bold opacity-75 mb-2">Titlu Pagină</label>
                        <input type="text" name="title" required class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border-gray-200 border' : ''; ?>">
                    </div>
                    <div>
                        <label class="block text-sm font-bold opacity-75 mb-2">Slug (ex: despre-noi)</label>
                        <input type="text" name="slug" required class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border-gray-200 border' : ''; ?>">
                    </div>
                </div>
                <div class="mb-8">
                    <label class="block text-sm font-bold opacity-75 mb-2">Conținut Pagină (HTML permis)</label>
                    <textarea name="content" rows="10" class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border-gray-200 border' : ''; ?>"></textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div>
                        <label class="block text-sm font-bold opacity-75 mb-2">SEO Title</label>
                        <input type="text" name="seo_title" class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border-gray-200 border' : ''; ?>">
                    </div>
                    <div>
                        <label class="block text-sm font-bold opacity-75 mb-2">SEO Description</label>
                        <input type="text" name="seo_description" class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border-gray-200 border' : ''; ?>">
                    </div>
                </div>
                <button type="submit" name="add_page" class="bg-blue-600 text-white px-10 py-3 rounded-xl font-bold hover:bg-blue-700 transition shadow-lg">
                    Salvează Pagina
                </button>
            </form>
        </div>

        <div class="admin-card shadow-lg overflow-hidden">
             <table class="w-full text-left">
                <thead class="bg-gray-50/10 border-b border-gray-700/30">
                    <tr>
                        <th class="px-8 py-5">Titlu</th>
                        <th class="px-8 py-5">Slug</th>
                        <th class="px-8 py-5">Acțiuni</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/20">
                    <?php foreach ($pages as $p): ?>
                    <tr>
                        <td class="px-8 py-6 font-bold"><?php echo htmlspecialchars($p['title']); ?></td>
                        <td class="px-8 py-6 opacity-60">/page/<?php echo htmlspecialchars($p['slug']); ?></td>
                        <td class="px-8 py-6">
                            <form method="POST" onsubmit="return confirm('Ștergi pagina?');">
                                <input type="hidden" name="delete_id" value="<?php echo $p['id']; ?>">
                                <button type="submit" class="text-red-500 hover:text-red-400 font-bold">Șterge</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
             </table>
        </div>

<?php require_once 'includes/admin_footer.php'; ?>
