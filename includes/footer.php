    <?php
    $is_premium = ($theme === 'premium');
    $footer_class = "";
    if ($theme === 'romania') {
        $footer_class = "footer-bar text-white";
    } elseif ($is_premium) {
        $footer_class = "bg-gradient-to-r from-red-950 via-black to-red-950 text-white border-t border-red-900/30";
    } else {
        $footer_class = "bg-white border-t border-gray-100 text-gray-600";
    }
    ?>
    <footer class="<?php echo $footer_class; ?> mt-auto py-12 text-center">
        <div class="max-w-7xl mx-auto px-6">
            <div class="mb-8 flex justify-center space-x-6">
                <a href="#" class="w-10 h-10 flex items-center justify-center rounded-full transition-all duration-300 <?php echo $is_premium ? 'text-red-500 border border-red-500/30 hover:bg-red-500 hover:text-white hover:shadow-[0_0_15px_rgba(239,68,68,0.5)]' : 'hover:opacity-75'; ?>">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="#" class="w-10 h-10 flex items-center justify-center rounded-full transition-all duration-300 <?php echo $is_premium ? 'text-red-500 border border-red-500/30 hover:bg-red-500 hover:text-white hover:shadow-[0_0_15px_rgba(239,68,68,0.5)]' : 'hover:opacity-75'; ?>">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="#" class="w-10 h-10 flex items-center justify-center rounded-full transition-all duration-300 <?php echo $is_premium ? 'text-red-500 border border-red-500/30 hover:bg-red-500 hover:text-white hover:shadow-[0_0_15px_rgba(239,68,68,0.5)]' : 'hover:opacity-75'; ?>">
                    <i class="fab fa-twitter"></i>
                </a>
            </div>

            <div class="space-y-4">
                <p class="text-sm font-medium tracking-wide">
                    <?php if($is_premium): ?>
                        <span class="text-gray-400">Crafted with precision & modern design.</span>
                    <?php else: ?>
                        &copy; <?php echo date('Y'); ?> <b><?php echo htmlspecialchars($settings['site_name'] ?? 'Showcase'); ?></b>
                    <?php endif; ?>
                    by <a href="https://sglprime.com" target="_blank" class="underline hover:text-blue-400">sglprime.com</a>.
                </p>
                <p class="text-xs opacity-50 mt-2">© <?php echo date('Y'); ?> <?php echo htmlspecialchars($settings['site_name'] ?? 'Showcase'); ?>. Toate drepturile rezervate.</p>
            </div>
        </div>
    </footer>
</body>
</html>
