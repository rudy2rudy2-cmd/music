    </main>
    <footer class="fixed bottom-4 right-8 text-[10px] text-gray-500 bg-black/20 backdrop-blur px-3 py-1 rounded-full border border-white/5">
        &copy; <?php echo $copyright ?? '2026 Hotel Defects Control • Stoian Rudolf Florian'; ?>
    </footer>
    <script>
        // Instant search functionality
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const term = e.target.value.toLowerCase();
                const rows = document.querySelectorAll('tbody tr');

                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(term)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
    </script>
</body>
</html>
