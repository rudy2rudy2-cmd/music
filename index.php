<?php
if (!file_exists('includes/config.php')) {
    header("Location: install.php");
    exit();
}
require_once 'includes/header.php';

// Fetch platforms
$stmt = $pdo->query("SELECT * FROM platforms ORDER BY created_at DESC");
$platforms = $stmt->fetchAll();

$is_premium = ($theme === 'premium');
?>

    <header class="py-24 text-center px-6 relative overflow-hidden">
        <?php if($is_premium): ?>
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full pointer-events-none opacity-20">
                <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-500 rounded-full blur-[120px]"></div>
                <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-purple-500 rounded-full blur-[120px]"></div>

                <!-- Eagle -->
                <div class="eagle-bg">
                    <svg viewBox="0 0 100 100" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M50 10 C 60 20, 90 30, 95 45 C 90 40, 70 40, 60 45 C 70 50, 80 65, 75 75 C 70 70, 60 60, 50 65 C 40 60, 30 70, 25 75 C 20 65, 30 50, 40 45 C 30 40, 10 40, 5 45 C 10 30, 40 20, 50 10" />
                        <path d="M50 15 L 52 25 L 50 22 L 48 25 Z" fill="rgba(255,255,255,0.5)" />
                    </svg>
                </div>

                <!-- Coding Lines -->
                <div class="code-lines">
                    <div class="code-line" style="animation-delay: 0s;">const showcase = new App();</div>
                    <div class="code-line" style="animation-delay: 2s;">showcase.render('Premium');</div>
                    <div class="code-line" style="animation-delay: 4s;">while(true) { build(); }</div>
                    <div class="code-line" style="animation-delay: 6s;">optimize(performance);</div>
                    <div class="code-line" style="animation-delay: 8s;">deploy('Futuristic');</div>
                </div>
            </div>
        <?php endif; ?>

        <div class="relative z-10">
            <h2 class="<?php echo $is_premium ? 'text-6xl font-bold tracking-tighter mb-8 premium-glow' : 'text-5xl font-extrabold mb-6 tracking-tight'; ?>">
                <?php
                if($is_premium && empty($settings['hero_title'])) echo "Construim experiențe digitale moderne și memorabile.";
                else echo htmlspecialchars($settings['hero_title'] ?? 'Platformele Noastre Web');
                ?>
            </h2>
            <p class="text-xl opacity-90 max-w-3xl mx-auto leading-relaxed mb-12">
                <?php
                if($is_premium && empty($settings['hero_subtitle'])) echo "Design futurist, performanță rapidă și animații fluide pentru un site care atrage atenția din prima secundă.";
                else echo htmlspecialchars($settings['hero_subtitle'] ?? 'Explorați creațiile noastre recente și testați demo-urile interactive.');
                ?>
            </p>
            <div class="flex flex-col sm:flex-row justify-center items-center gap-6">
                <a href="#projects" class="<?php echo $is_premium ? 'premium-btn text-white px-10 py-4 rounded-full font-bold text-lg transition transform hover:-translate-y-1' : 'bg-yellow-500 text-gray-900 px-8 py-4 rounded-full font-bold text-lg hover:bg-yellow-400 transition shadow-xl transform hover:-translate-y-1'; ?>">
                    <i class="fas fa-layer-group mr-2"></i> Vezi proiectele
                </a>
                <a href="<?php echo htmlspecialchars($settings['order_url'] ?? 'offers.php'); ?>" class="<?php echo $is_premium ? 'bg-transparent border border-blue-500/50 text-blue-400 px-10 py-4 rounded-full font-bold text-lg hover:bg-blue-500/10 transition' : 'bg-white text-blue-600 px-8 py-4 rounded-full font-bold text-lg border-2 border-blue-600 hover:bg-blue-50 transition'; ?>">
                    <i class="fas fa-paper-plane mr-2"></i> Contactează-ne
                </a>
            </div>
        </div>
    </header>

    <main id="projects" class="max-w-7xl mx-auto px-6 pb-32">
        <?php if($is_premium): ?>
            <div class="text-center mb-20">
                <p class="text-blue-500 font-bold uppercase tracking-[0.3em] text-xs mb-4">Portofoliu</p>
                <h3 class="text-3xl font-bold">Produse de Elite</h3>
                <div class="w-20 h-1 bg-blue-600 mx-auto mt-4 rounded-full"></div>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
            <?php foreach ($platforms as $platform): ?>
                <div class="card rounded-3xl shadow-2xl overflow-hidden flex flex-col group">
                    <div class="relative overflow-hidden">
                        <?php if ($platform['image_url']): ?>
                            <img src="<?php echo $platform['image_url']; ?>" alt="<?php echo $platform['title']; ?>" class="h-56 w-full object-cover transition-transform duration-700 group-hover:scale-110">
                        <?php else: ?>
                            <div class="h-56 w-full bg-gray-200 flex items-center justify-center text-gray-400 group-hover:bg-gray-300 transition">
                                <i class="fas fa-image text-5xl"></i>
                            </div>
                        <?php endif; ?>
                        <div class="absolute top-4 right-4">
                            <span class="text-xs font-bold bg-blue-600 text-white px-3 py-1 rounded-full shadow-lg">v<?php echo htmlspecialchars($platform['version']); ?></span>
                        </div>
                        <?php if($is_premium): ?>
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent to-transparent opacity-0 group-hover:opacity-60 transition-opacity duration-300"></div>
                        <?php endif; ?>
                    </div>

                    <div class="p-8 flex-grow">
                        <h3 class="text-2xl font-bold mb-4 <?php echo $is_premium ? 'group-hover:text-blue-400' : 'group-hover:text-blue-500'; ?> transition"><?php echo htmlspecialchars($platform['title']); ?></h3>
                        <p class="text-sm opacity-80 leading-relaxed mb-6"><?php echo nl2br(htmlspecialchars($platform['description'])); ?></p>
                    </div>

                    <div class="p-8 pt-0 mt-auto space-y-4">
                        <?php if ($platform['demo_url']): ?>
                            <a href="<?php echo htmlspecialchars($platform['demo_url']); ?>" target="_blank" class="block w-full text-center <?php echo $is_premium ? 'bg-blue-600/20 text-blue-400 border border-blue-500/30' : 'bg-gray-800 text-white'; ?> font-bold py-3 rounded-xl hover:bg-blue-600 hover:text-white transition flex items-center justify-center">
                                <i class="fas fa-play-circle mr-2"></i> Rulare Demo
                            </a>
                        <?php endif; ?>
                        <a href="order.php?id=<?php echo $platform['id']; ?>" class="block w-full text-center border-2 border-blue-600 text-blue-600 font-bold py-3 rounded-xl hover:bg-blue-600 hover:text-white transition flex items-center justify-center">
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
