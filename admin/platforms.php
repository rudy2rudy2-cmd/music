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

        $stmt = $pdo->prepare("INSERT INTO platforms (title, description, version, demo_url, image_url) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $version, $demo_url, $image_url]);
        $message = "Platformă adăugată cu succes!";
    }

    if (isset($_POST['delete_id'])) {
        $stmt = $pdo->prepare("DELETE FROM platforms WHERE id = ?");
        $stmt->execute([$_POST['delete_id']]);
        $message = "Platformă ștearsă!";
    }
}

$stmt = $pdo->query("SELECT * FROM platforms ORDER BY created_at DESC");
$platforms = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Gestionare Platforme</title>
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
            <a href="platforms.php" class="block py-2.5 px-4 rounded transition duration-200 bg-blue-600">
                <i class="fas fa-layer-group mr-2"></i> Platforme
            </a>
            <a href="users.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-slate-800">
                <i class="fas fa-users mr-2"></i> Utilizatori
            </a>
            <a href="settings.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-slate-800">
                <i class="fas fa-cog mr-2"></i> Setări
            </a>
        </nav>
    </div>

    <div class="flex-1 p-10">
        <h2 class="text-3xl font-bold mb-8">Gestionare Platforme Web</h2>

        <?php if ($message): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Formular Adăugare -->
        <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100 mb-10">
            <h3 class="text-xl font-bold mb-6">Adaugă Platformă Nouă</h3>
            <form method="POST" enctype="multipart/form-data">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Titlu</label>
                        <input type="text" name="title" required class="mt-1 block w-full p-2 border rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Versiune</label>
                        <input type="text" name="version" placeholder="1.0.0" class="mt-1 block w-full p-2 border rounded-md">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Descriere</label>
                        <textarea name="description" rows="3" class="mt-1 block w-full p-2 border rounded-md"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Rulare Demo (URL)</label>
                        <input type="url" name="demo_url" placeholder="https://demo.example.com" class="mt-1 block w-full p-2 border rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Imagine Prezentare</label>
                        <input type="file" name="image" class="mt-1 block w-full p-2 border rounded-md">
                    </div>
                </div>
                <button type="submit" name="add_platform" class="mt-6 bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition">
                    Adaugă Platformă
                </button>
            </form>
        </div>

        <!-- Tabel Listare -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-sm font-bold text-gray-600 uppercase">Titlu</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-600 uppercase">Versiune</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-600 uppercase">Acțiuni</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach ($platforms as $platform): ?>
                    <tr>
                        <td class="px-6 py-4 font-medium text-gray-800"><?php echo htmlspecialchars($platform['title']); ?></td>
                        <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars($platform['version']); ?></td>
                        <td class="px-6 py-4">
                            <form method="POST" onsubmit="return confirm('Sigur dorești să ștergi?');">
                                <input type="hidden" name="delete_id" value="<?php echo $platform['id']; ?>">
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i> Șterge
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($platforms)): ?>
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-gray-500 italic">Nu există platforme adăugate.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
