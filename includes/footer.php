    <footer class="footer-bar mt-auto py-8 text-center <?php echo ($theme === 'romania' ? 'text-white' : 'bg-white border-t border-gray-100 text-gray-600'); ?>">
        <div class="max-w-7xl mx-auto px-6">
            <div class="mb-4 space-x-4">
                <a href="#" class="hover:opacity-75"><i class="fab fa-facebook"></i></a>
                <a href="#" class="hover:opacity-75"><i class="fab fa-instagram"></i></a>
                <a href="#" class="hover:opacity-75"><i class="fab fa-twitter"></i></a>
            </div>
            <p class="text-sm font-medium">
                &copy; <?php echo date('Y'); ?> <b><?php echo htmlspecialchars($settings['site_name'] ?? 'Showcase'); ?></b> by <a href="https://sglprime.com" target="_blank" class="underline hover:text-blue-400">sglprime.com</a>.
            </p>
            <p class="text-xs opacity-60 mt-2">Toate drepturile rezervate.</p>
        </div>
    </footer>
</body>
</html>
