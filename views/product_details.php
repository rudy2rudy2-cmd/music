<?php
$id = $_GET['id'] ?? null;
if (!$id) {
    echo "Produs negăsit.";
    return;
}

$stmt = $pdo->prepare("SELECT * FROM platforms WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    echo "Produs negăsit.";
    return;
}

$final_price = ($product['discount_price'] > 0) ? $product['discount_price'] : $product['price'];
?>

<div class="max-w-7xl mx-auto px-6 py-20">
    <div class="flex flex-col lg:flex-row gap-16">
        <!-- Image Gallery -->
        <div class="lg:w-1/2">
            <div class="rounded-3xl shadow-2xl overflow-hidden border border-gray-700/20 bg-white/5 backdrop-blur-sm">
                <?php if ($product['image_url']): ?>
                    <img src="/<?php echo $product['image_url']; ?>" alt="<?php echo $product['title']; ?>" class="w-full h-auto">
                <?php else: ?>
                    <div class="aspect-video bg-gray-700/20 flex items-center justify-center text-gray-500 text-6xl">
                        <i class="fas fa-image"></i>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Product Info -->
        <div class="lg:w-1/2 flex flex-col justify-center">
            <nav class="flex space-x-2 text-sm opacity-50 mb-6">
                <a href="/" class="hover:underline">Acasă</a>
                <span>/</span>
                <span class="font-bold"><?php echo htmlspecialchars($product['title']); ?></span>
            </nav>

            <h1 class="text-5xl font-extrabold mb-4 tracking-tighter premium-glow">
                <?php echo htmlspecialchars($product['title']); ?>
            </h1>
            <div class="flex items-center space-x-4 mb-8">
                <span class="bg-blue-600 text-white px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest">v<?php echo htmlspecialchars($product['version']); ?></span>
                <span class="text-sm opacity-60 italic">Creat la: <?php echo date('d.m.Y', strtotime($product['created_at'])); ?></span>
            </div>

            <div class="prose prose-invert mb-10 text-lg opacity-80 leading-relaxed">
                <?php echo nl2br(htmlspecialchars($product['description'])); ?>
            </div>

            <div class="mb-12">
                <?php if($product['discount_price'] > 0): ?>
                    <div class="flex items-baseline space-x-4">
                        <span class="text-4xl font-extrabold text-blue-500"><?php echo $product['discount_price']; ?> EUR</span>
                        <span class="text-xl text-gray-500 line-through"><?php echo $product['price']; ?> EUR</span>
                    </div>
                <?php else: ?>
                    <span class="text-4xl font-extrabold text-blue-500"><?php echo $product['price']; ?> EUR</span>
                <?php endif; ?>
            </div>

            <div class="flex flex-col sm:flex-row gap-6">
                <a href="/order.php?id=<?php echo $product['id']; ?>" class="flex-1 bg-blue-600 text-white text-center font-extrabold py-4 rounded-2xl shadow-xl hover:bg-blue-700 transition transform hover:-translate-y-1">
                    <i class="fas fa-shopping-cart mr-2"></i> Cumpără Acum
                </a>
                <?php if ($product['demo_url']): ?>
                    <a href="<?php echo htmlspecialchars($product['demo_url']); ?>" target="_blank" class="flex-1 border-2 border-gray-700/30 text-center font-extrabold py-4 rounded-2xl hover:bg-white/10 transition">
                        <i class="fas fa-play-circle mr-2"></i> Rulare Demo
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
