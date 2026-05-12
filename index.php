<?php
if (!file_exists('includes/config.php')) {
    header("Location: install.php");
    exit();
}
require_once 'includes/header.php';

// Fetch platforms
$stmt = $pdo->query("SELECT * FROM platforms ORDER BY created_at DESC");
$platforms = $stmt->fetchAll();
?>

    <header class="py-20 text-center px-6">
        <h2 class="text-5xl font-extrabold mb-6 tracking-tight">
            <?php echo htmlspecialchars($settings['hero_title'] ?? 'Platformele Noastre Web'); ?>
        </h2>
        <p class="text-xl opacity-90 max-w-3xl mx-auto leading-relaxed">
            <?php echo htmlspecialchars($settings['hero_subtitle'] ?? 'Explorați creațiile noastre recente și testați demo-urile interactive.'); ?>
        </p>
        <div class="mt-10">
            <a href="<?php echo htmlspecialchars($settings['order_url'] ?? '#'); ?>" class="bg-yellow-500 text-gray-900 px-8 py-4 rounded-full font-bold text-lg hover:bg-yellow-400 transition shadow-xl transform hover:-translate-y-1 inline-block">
                <i class="fas fa-shopping-cart mr-2"></i> <?php echo htmlspecialchars($settings['order_button_text'] ?? 'Comandă Acum'); ?>
            </a>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-6 pb-24">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
            <?php foreach ($platforms as $platform): ?>
                <div class="card rounded-3xl shadow-2xl overflow-hidden flex flex-col transition-all duration-300 hover:shadow-blue-500/20 group">
                    <div class="relative overflow-hidden">
                        <?php if ($platform['image_url']): ?>
                            <img src="<?php echo $platform['image_url']; ?>" alt="<?php echo $platform['title']; ?>" class="h-56 w-full object-cover transition-transform duration-500 group-hover:scale-110">
                        <?php else: ?>
                            <div class="h-56 w-full bg-gray-200 flex items-center justify-center text-gray-400 group-hover:bg-gray-300 transition">
                                <i class="fas fa-image text-5xl"></i>
                            </div>
                        <?php endif; ?>
                        <div class="absolute top-4 right-4">
                            <span class="text-xs font-bold bg-blue-600 text-white px-3 py-1 rounded-full shadow-lg">v<?php echo htmlspecialchars($platform['version']); ?></span>
                        </div>
                    </div>

                    <div class="p-8 flex-grow">
                        <h3 class="text-2xl font-bold mb-4 group-hover:text-blue-500 transition"><?php echo htmlspecialchars($platform['title']); ?></h3>
                        <p class="text-sm opacity-80 leading-relaxed mb-6"><?php echo nl2br(htmlspecialchars($platform['description'])); ?></p>
                    </div>

                    <div class="p-8 pt-0 mt-auto space-y-4">
                        <?php if ($platform['demo_url']): ?>
                            <a href="<?php echo htmlspecialchars($platform['demo_url']); ?>" target="_blank" class="block w-full text-center bg-gray-800 text-white font-bold py-3 rounded-xl hover:bg-gray-900 transition flex items-center justify-center">
                                <i class="fas fa-play-circle mr-2"></i> Rulare Demo
                            </a>
                        <?php endif; ?>
                        <a href="<?php echo htmlspecialchars($settings['order_url'] ?? '#'); ?>" class="block w-full text-center border-2 border-blue-600 text-blue-600 font-bold py-3 rounded-xl hover:bg-blue-600 hover:text-white transition flex items-center justify-center">
                             <i class="fas fa-cart-plus mr-2"></i> <?php echo htmlspecialchars($settings['order_button_text'] ?? 'Comandă Acum'); ?>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (empty($platforms)): ?>
                <div class="col-span-full text-center py-20 opacity-50">
                    <i class="fas fa-box-open text-6xl mb-4 block"></i>
                    Nu există platforme adăugate încă.
                </div>
            <?php endif; ?>
        </div>
    </main>

<?php require_once 'includes/footer.php'; ?>
