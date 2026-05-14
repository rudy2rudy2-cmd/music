<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireLogin();

if (isAdmin()) {
    header("Location: ../admin/index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT p.*, pur.created_at as purchase_date FROM purchases pur JOIN platforms p ON pur.platform_id = p.id WHERE pur.user_id = ? ORDER BY pur.created_at DESC");
$stmt->execute([$user_id]);
$purchased_platforms = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Utilizator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-900 text-white min-h-screen">
    <nav class="bg-gray-800 p-4 border-b border-gray-700">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold text-blue-400">User Dashboard</h1>
            <div class="flex items-center space-x-6">
                <span>Salut, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="../logout.php" class="bg-red-600 px-4 py-2 rounded-lg font-bold text-sm">Logout</a>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto p-10">
        <h2 class="text-3xl font-bold mb-10">Produsele Tale</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($purchased_platforms as $platform): ?>
                <div class="bg-gray-800 rounded-3xl p-6 border border-gray-700 shadow-xl">
                    <h3 class="text-xl font-bold mb-2"><?php echo htmlspecialchars($platform['title']); ?></h3>
                    <p class="text-xs text-blue-400 font-bold mb-4 uppercase tracking-widest">Versiune <?php echo htmlspecialchars($platform['version']); ?></p>
                    <p class="text-sm opacity-60 mb-6 line-clamp-2"><?php echo htmlspecialchars($platform['description']); ?></p>

                    <div class="border-t border-gray-700 pt-4 flex justify-between items-center">
                        <span class="text-xs opacity-40">Achiziționat: <?php echo date('d.m.Y', strtotime($platform['purchase_date'])); ?></span>
                        <a href="<?php echo htmlspecialchars($platform['demo_url']); ?>" target="_blank" class="text-blue-500 font-bold hover:underline">Accesează Demo</a>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (empty($purchased_platforms)): ?>
                <div class="col-span-full bg-gray-800/50 border-2 border-dashed border-gray-700 rounded-3xl p-20 text-center">
                    <p class="opacity-40 italic text-xl">Nu ai achiziționat nicio platformă încă.</p>
                    <a href="../offers" class="mt-6 inline-block bg-blue-600 text-white px-8 py-3 rounded-xl font-bold">Vezi Oferte</a>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
