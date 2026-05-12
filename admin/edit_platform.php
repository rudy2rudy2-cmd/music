<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireAdmin();

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: platforms.php");
    exit();
}

$message = '';
$error = '';

$stmt = $pdo->prepare("SELECT * FROM platforms WHERE id = ?");
$stmt->execute([$id]);
$platform = $stmt->fetch();

if (!$platform) {
    header("Location: platforms.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $version = $_POST['version'] ?? '';
    $demo_url = $_POST['demo_url'] ?? '';

    $image_url = $platform['image_url'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        if (in_array($ext, $allowed_exts)) {
            $filename = uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/' . $filename)) {
                $image_url = 'uploads/' . $filename;
            }
        } else {
            $error = "Eroare: Tip de fișier nepermis.";
        }
    }

    if (empty($error)) {
        try {
            $stmt = $pdo->prepare("UPDATE platforms SET title = ?, description = ?, version = ?, demo_url = ?, image_url = ? WHERE id = ?");
            $stmt->execute([$title, $description, $version, $demo_url, $image_url, $id]);
            $message = "Platformă actualizată cu succes!";
            // Refresh platform data
            $stmt = $pdo->prepare("SELECT * FROM platforms WHERE id = ?");
            $stmt->execute([$id]);
            $platform = $stmt->fetch();
        } catch (PDOException $e) {
            $error = "Eroare: " . $e->getMessage();
        }
    }
}

// Get theme for admin panel
$stmt = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'theme'");
$theme = $stmt->fetchColumn() ?: 'light';
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Editează Platforma</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        <?php if ($theme === 'romania'): ?>
        @keyframes romania-bg {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .admin-body {
            background: linear-gradient(-45deg, #002b7f, #fcd116, #ce1126);
            background-size: 400% 400%;
            animation: romania-bg 15s ease infinite;
        }
        .admin-card { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(5px); }
        <?php else: ?>
        .admin-body { background-color: #f3f4f6; }
        .admin-card { background-color: #ffffff; }
        <?php endif; ?>
    </style>
</head>
<body class="admin-body flex min-h-screen">
    <div class="w-64 bg-slate-900 text-white p-6 sticky top-0 h-screen">
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
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold">Editează Platforma</h2>
            <a href="platforms.php" class="text-blue-600 hover:underline">
                <i class="fas fa-arrow-left mr-2"></i> Înapoi la listă
            </a>
        </div>

        <?php if ($message): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="admin-card p-8 rounded-xl shadow-sm border border-gray-100">
            <form method="POST" enctype="multipart/form-data">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Titlu</label>
                        <input type="text" name="title" value="<?php echo htmlspecialchars($platform['title']); ?>" required class="mt-1 block w-full p-2 border rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Versiune</label>
                        <input type="text" name="version" value="<?php echo htmlspecialchars($platform['version']); ?>" placeholder="1.0.0" class="mt-1 block w-full p-2 border rounded-md">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Descriere</label>
                        <textarea name="description" rows="5" class="mt-1 block w-full p-2 border rounded-md"><?php echo htmlspecialchars($platform['description']); ?></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Rulare Demo (URL)</label>
                        <input type="url" name="demo_url" value="<?php echo htmlspecialchars($platform['demo_url']); ?>" placeholder="https://demo.example.com" class="mt-1 block w-full p-2 border rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Imagine Actuală</label>
                        <?php if($platform['image_url']): ?>
                            <img src="../<?php echo $platform['image_url']; ?>" class="h-20 w-auto rounded mt-2 mb-2">
                        <?php endif; ?>
                        <input type="file" name="image" class="mt-1 block w-full p-2 border rounded-md">
                        <p class="text-xs text-gray-500 mt-1">Încarcă o imagine nouă pentru a o înlocui pe cea actuală.</p>
                    </div>
                </div>
                <button type="submit" class="mt-8 bg-blue-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-blue-700 transition shadow-lg">
                    Salvează Modificările
                </button>
            </form>
        </div>
    </div>
</body>
</html>
