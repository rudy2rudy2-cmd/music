document.addEventListener('DOMContentLoaded', function() {
    const themeButtons = document.querySelectorAll('.option-btn[data-theme]');
    const colorButtons = document.querySelectorAll('.option-btn[data-color]');
    const body = document.body;

    let currentTheme = body.dataset.theme;
    let currentColor = body.dataset.color;

    // Function to update active button state
    function updateActiveButtons() {
        themeButtons.forEach(btn => {
            btn.classList.toggle('active', btn.dataset.theme === currentTheme);
        });
        colorButtons.forEach(btn => {
            btn.classList.toggle('active', btn.dataset.color === currentColor);
        });
    }

    // Set initial active buttons based on body data attributes
    // (These will be loaded from DB by PHP)
    currentTheme = document.body.dataset.theme || 'dark';
    currentColor = document.body.dataset.color || 'blue';
    updateActiveButtons();


    // Event listeners for theme buttons
    themeButtons.forEach(button => {
        button.addEventListener('click', () => {
            currentTheme = button.dataset.theme;
            body.dataset.theme = currentTheme;
            updateActiveButtons();
            saveSettings();
        });
    });

    // Event listeners for color buttons
    colorButtons.forEach(button => {
        button.addEventListener('click', () => {
            currentColor = button.dataset.color;
            body.dataset.color = currentColor;
            updateActiveButtons();
            saveSettings();
        });
    });

    // Function to save settings to the server
    function saveSettings() {
        fetch('save_theme.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                theme: currentTheme,
                color: currentColor
            }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                console.log('Settings saved successfully.');
                // Optionally show a success message to the user
            } else {
                console.error('Failed to save settings:', data.message);
                // Optionally show an error message
            }
        })
        .catch((error) => {
            console.error('Error:', error);
        });
    }
});
