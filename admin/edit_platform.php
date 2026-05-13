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
    $price = $_POST['price'] ?? 0;
    $discount_price = $_POST['discount_price'] ?? 0;

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
            $stmt = $pdo->prepare("UPDATE platforms SET title = ?, description = ?, version = ?, demo_url = ?, image_url = ?, price = ?, discount_price = ? WHERE id = ?");
            $stmt->execute([$title, $description, $version, $demo_url, $image_url, $price, $discount_price, $id]);
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

$header_title = "Editare Platformă";
require_once 'includes/admin_header.php';
?>

        <div class="mb-8">
            <a href="platforms.php" class="text-blue-500 hover:text-blue-400 font-bold flex items-center transition hover:-translate-x-1 inline-flex">
                <i class="fas fa-long-arrow-alt-left mr-2"></i> Revenire la listă
            </a>
        </div>

        <?php if ($message): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-xl mb-8 shadow-sm">
                <i class="fas fa-check-circle mr-2"></i> <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-xl mb-8 shadow-sm">
                <i class="fas fa-exclamation-triangle mr-2"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="admin-card p-10 shadow-xl max-w-4xl">
            <form method="POST" enctype="multipart/form-data">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-sm font-bold opacity-75 mb-2">Titlu Platformă</label>
                        <input type="text" name="title" value="<?php echo htmlspecialchars($platform['title']); ?>" required class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border-gray-200 border' : ''; ?>">
                    </div>
                    <div>
                        <label class="block text-sm font-bold opacity-75 mb-2">Versiune Program</label>
                        <input type="text" name="version" value="<?php echo htmlspecialchars($platform['version']); ?>" placeholder="1.0.0" class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border-gray-200 border' : ''; ?>">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold opacity-75 mb-2">Descriere Completă</label>
                        <textarea name="description" rows="6" class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border-gray-200 border' : ''; ?>"><?php echo htmlspecialchars($platform['description']); ?></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold opacity-75 mb-2">Adresă Demo (URL)</label>
                        <input type="url" name="demo_url" value="<?php echo htmlspecialchars($platform['demo_url']); ?>" placeholder="https://demo.example.com" class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border-gray-200 border' : ''; ?>">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold opacity-75 mb-2">Preț (EUR)</label>
                            <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($platform['price']); ?>" class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border-gray-200 border' : ''; ?>">
                        </div>
                        <div>
                            <label class="block text-sm font-bold opacity-75 mb-2">Preț Ofertă (EUR)</label>
                            <input type="number" step="0.01" name="discount_price" value="<?php echo htmlspecialchars($platform['discount_price']); ?>" class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border-gray-200 border' : ''; ?>">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold opacity-75 mb-2">Schimbă Imaginea</label>
                        <div class="flex items-center space-x-4 mb-2">
                            <?php if($platform['image_url']): ?>
                                <img src="../<?php echo $platform['image_url']; ?>" class="h-16 w-16 object-cover rounded-xl border border-gray-700/30">
                            <?php endif; ?>
                            <input type="file" name="image" class="flex-grow p-2 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border-gray-200 border' : ''; ?>">
                        </div>
                    </div>
                </div>
                <button type="submit" class="mt-10 bg-blue-600 text-white px-12 py-4 rounded-xl font-bold hover:bg-blue-700 transition shadow-lg transform hover:-translate-y-1">
                    <i class="fas fa-save mr-2"></i> Salvează Modificările
                </button>
            </form>
        </div>

<?php require_once 'includes/admin_footer.php'; ?>
