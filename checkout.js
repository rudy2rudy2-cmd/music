document.addEventListener('DOMContentLoaded', () => {
    const tabLinks = document.querySelectorAll('.tab-link');
    const paymentTabs = document.querySelectorAll('.payment-tab');
    const form = document.querySelector('.checkout-container form');

    // Function to switch tabs
    function switchTab(tabId) {
        paymentTabs.forEach(tab => {
            const isActive = tab.id === tabId;
            tab.classList.toggle('active', isActive);

            // Enable/disable required attributes on inputs
            tab.querySelectorAll('input').forEach(input => {
                input.required = isActive;
            });
        });

        tabLinks.forEach(link => {
            link.classList.toggle('active', link.dataset.tab === tabId);
        });
    }

    // Add click event listeners to tab links
    tabLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            switchTab(e.target.dataset.tab);
        });
    });

    // Initialize the first tab
    switchTab('card');

    // On form submit, ensure only visible inputs are considered
    form.addEventListener('submit', () => {
        paymentTabs.forEach(tab => {
            if (!tab.classList.contains('active')) {
                tab.querySelectorAll('input').forEach(input => {
                    input.removeAttribute('required');
                });
            }
        });
    });
});
