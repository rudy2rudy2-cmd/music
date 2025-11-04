document.addEventListener('DOMContentLoaded', () => {
    // Sliders
    const durationSlider = document.getElementById('duration');
    const durationValue = document.getElementById('duration-value');
    const tempoSlider = document.getElementById('tempo');
    const tempoValue = document.getElementById('tempo-value');

    // Form and result elements
    const form = document.querySelector('.generator-form');
    const generateBtn = document.getElementById('generate-btn');
    const resultContainer = document.getElementById('generation-result');
    const audioPlayerContainer = document.getElementById('audio-player');
    const downloadLink = document.getElementById('download-link');
    const saveProjectBtn = document.getElementById('save-project-btn');

    // Update duration display
    if (durationSlider) {
        durationSlider.addEventListener('input', () => {
            if (durationValue) durationValue.textContent = durationSlider.value;
        });
    }

    // Update tempo display
    if (tempoSlider) {
        tempoSlider.addEventListener('input', () => {
            if (tempoValue) tempoValue.textContent = tempoSlider.value;
        });
    }

    // Handle option button clicks
    const optionButtons = document.querySelectorAll('.option-btn');
    optionButtons.forEach(button => {
        button.addEventListener('click', () => {
            const type = button.dataset.type;
            const value = button.dataset.value;

            const input = document.getElementById(`${type}-input`);
            if (input) input.value = value;

            document.querySelectorAll(`.option-btn[data-type="${type}"]`).forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
        });
    });

    // Handle form submission with Fetch API
    if (form) {
        form.addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(this);
            const btnSpan = generateBtn.querySelector('span');

            // --- Loading State ---
            generateBtn.disabled = true;
            if(btnSpan) btnSpan.textContent = 'AI compune melodia...';
            resultContainer.classList.add('hidden');
            audioPlayerContainer.innerHTML = ''; // Clear previous results

            fetch('generate.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.file_path) {
                    // Create audio element
                    const audio = document.createElement('audio');
                    audio.controls = true;
                    audio.src = data.file_path;
                    audioPlayerContainer.appendChild(audio);

                    downloadLink.href = data.file_path;
                    resultContainer.classList.remove('hidden');
                    audio.play();

                } else {
                    // Display error message, allowing for HTML links
                    const errorContainer = document.createElement('div');
                    errorContainer.classList.add('alert', 'error');
                    errorContainer.innerHTML = data.message || 'A apărut o eroare la generare.';
                    form.insertAdjacentElement('afterend', errorContainer);
                    setTimeout(() => errorContainer.remove(), 5000); // Remove after 5s
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('A apărut o eroare critică. Vă rugăm verificați consola.');
            })
            .finally(() => {
                // --- Reset Button State ---
                generateBtn.disabled = false;
                if(btnSpan) btnSpan.textContent = 'Generează';
            });
        });
    }

    // Save project button (placeholder functionality)
    if(saveProjectBtn) {
        saveProjectBtn.addEventListener('click', () => {
            alert('Funcționalitate în dezvoltare! Proiectul tău va putea fi salvat aici.');
        });
    }
});
