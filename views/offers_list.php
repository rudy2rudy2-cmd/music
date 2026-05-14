<?php
require_once 'includes/header.php';

// Fetch all platforms that have a price defined
$stmt = $pdo->query("SELECT * FROM platforms WHERE price > 0 ORDER BY created_at DESC");
$platforms = $stmt->fetchAll();
?>

    <header class="py-20 text-center px-6 relative overflow-hidden">
        <div class="relative z-10">
            <h2 class="text-5xl font-extrabold mb-6 tracking-tight <?php echo ($theme === 'premium' ? 'premium-glow' : ''); ?>">
                <?php echo __('offers'); ?> Disponibile
            </h2>
            <p class="text-xl opacity-90 max-w-3xl mx-auto leading-relaxed">
                Descoperă platformele noastre web și alege soluția potrivită pentru afacerea ta.
            </p>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-6 pb-24">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
            <?php foreach ($platforms as $platform): ?>
                <?php
                $has_discount = ($platform['discount_price'] > 0 && $platform['discount_price'] < $platform['price']);
                $display_price = $has_discount ? $platform['discount_price'] : $platform['price'];
                ?>
                <div class="card rounded-3xl shadow-2xl overflow-hidden flex flex-col group transition-all hover:scale-105">
                    <div class="relative overflow-hidden">
                        <?php if ($platform['image_url']): ?>
                            <img src="<?php echo $platform['image_url']; ?>" alt="<?php echo $platform['title']; ?>" class="h-60 w-full object-cover">
                        <?php else: ?>
                            <div class="h-60 w-full bg-gray-200 flex items-center justify-center text-gray-400">
                                <i class="fas fa-image text-5xl"></i>
                            </div>
                        <?php endif; ?>

                        <?php if($has_discount): ?>
                            <div class="absolute top-4 left-4">
                                <span class="bg-red-600 text-white px-4 py-1 rounded-full font-bold shadow-lg text-sm">
                                    OFERTĂ -<?php echo round((($platform['price'] - $platform['discount_price']) / $platform['price']) * 100); ?>%
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="p-8 flex-grow text-center">
                        <h3 class="text-2xl font-bold mb-4"><?php echo htmlspecialchars($platform['title']); ?></h3>
                        <p class="text-sm opacity-70 mb-6 line-clamp-2"><?php echo htmlspecialchars($platform['description']); ?></p>

                        <div class="mb-8">
                            <?php if($has_discount): ?>
                                <span class="text-gray-500 line-through text-lg mr-3"><?php echo $platform['price']; ?> EUR</span>
                            <?php endif; ?>
                            <span class="text-4xl font-extrabold text-blue-500"><?php echo $display_price; ?> EUR</span>
                        </div>

                        <div class="space-y-4">
                            <a href="/product/<?php echo $platform['id']; ?>" class="block w-full bg-gray-800 text-white font-bold py-3 rounded-xl hover:bg-gray-900 transition flex items-center justify-center">
                                 <i class="fas fa-info-circle mr-2"></i> Detalii Produs
                            </a>
                            <a href="/order.php?id=<?php echo $platform['id']; ?>" class="block w-full bg-blue-600 text-white font-bold py-4 rounded-2xl hover:bg-blue-700 transition shadow-xl transform hover:-translate-y-1">
                                <i class="fas fa-shopping-cart mr-2"></i> <?php echo __('order_now'); ?>
                            </a>
                            <?php if ($platform['demo_url']): ?>
                                <a href="<?php echo htmlspecialchars($platform['demo_url']); ?>" target="_blank" class="block w-full text-center border-2 border-gray-700/30 font-bold py-3 rounded-2xl hover:bg-gray-100 transition">
                                    <?php echo __('run_demo'); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (empty($platforms)): ?>
                <div class="col-span-full text-center py-20 opacity-50">
                    <i class="fas fa-search text-6xl mb-4 block"></i>
                    Nu există oferte active momentan.
                </div>
            <?php endif; ?>
        </div>
    </main>

<?php require_once 'includes/footer.php'; ?>
