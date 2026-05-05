    </main>
    <footer class="fixed bottom-4 left-1/2 -translate-x-1/2 text-sm text-white bg-black/40 backdrop-blur-md px-8 py-3 rounded-full border border-white/10 shadow-lg font-bold">
        Management Defecțiuni Hotel – Platformă pentru Managementul HoReCa
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
