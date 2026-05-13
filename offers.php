<?php
require_once 'includes/header.php';

$stmt = $pdo->query("SELECT * FROM platforms WHERE discount_price > 0 AND discount_price < price ORDER BY created_at DESC");
$offers = $stmt->fetchAll();
?>

    <header class="py-20 text-center px-6">
        <h2 class="text-5xl font-extrabold mb-6 tracking-tight premium-glow">
            <?php echo __('offers'); ?> Speciale
        </h2>
        <p class="text-xl opacity-90 max-w-3xl mx-auto leading-relaxed">
            Profită de cele mai bune prețuri pentru platformele noastre premium.
        </p>
    </header>

    <main class="max-w-7xl mx-auto px-6 pb-24">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
            <?php foreach ($offers as $platform): ?>
                <div class="card rounded-3xl shadow-2xl overflow-hidden flex flex-col group">
                    <div class="relative overflow-hidden">
                        <?php if ($platform['image_url']): ?>
                            <img src="<?php echo $platform['image_url']; ?>" alt="<?php echo $platform['title']; ?>" class="h-56 w-full object-cover transition-transform duration-700 group-hover:scale-110">
                        <?php else: ?>
                            <div class="h-56 w-full bg-gray-200 flex items-center justify-center">
                                <i class="fas fa-image text-5xl"></i>
                            </div>
                        <?php endif; ?>
                        <div class="absolute top-4 left-4">
                            <span class="bg-red-600 text-white px-4 py-1 rounded-full font-bold shadow-lg text-sm">
                                -<?php echo round((($platform['price'] - $platform['discount_price']) / $platform['price']) * 100); ?>%
                            </span>
                        </div>
                    </div>

                    <div class="p-8 flex-grow text-center">
                        <h3 class="text-2xl font-bold mb-4"><?php echo htmlspecialchars($platform['title']); ?></h3>
                        <div class="mb-6">
                            <span class="text-gray-500 line-through text-lg mr-3"><?php echo $platform['price']; ?> EUR</span>
                            <span class="text-3xl font-extrabold text-blue-500"><?php echo $platform['discount_price']; ?> EUR</span>
                        </div>
                        <a href="order.php?id=<?php echo $platform['id']; ?>" class="block w-full bg-blue-600 text-white font-bold py-4 rounded-2xl hover:bg-blue-700 transition shadow-xl transform hover:-translate-y-1">
                            <?php echo __('order_now'); ?>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (empty($offers)): ?>
                <div class="col-span-full text-center py-20 opacity-50">
                    Nu există oferte active momentan.
                </div>
            <?php endif; ?>
        </div>
    </main>

<?php require_once 'includes/footer.php'; ?>
