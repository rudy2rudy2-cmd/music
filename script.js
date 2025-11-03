document.addEventListener('DOMContentLoaded', () => {
    const durationSlider = document.getElementById('duration');
    const durationValue = document.getElementById('duration-value');
    const form = document.querySelector('.generator-form');

    // Update duration display in real-time
    if (durationSlider) {
        durationSlider.addEventListener('input', () => {
            if (durationValue) {
                durationValue.textContent = durationSlider.value;
            }
        });
    }

    // Handle clicks on option buttons (voice, genre, mood)
    const optionButtons = document.querySelectorAll('.option-btn');
    optionButtons.forEach(button => {
        button.addEventListener('click', () => {
            const type = button.dataset.type;
            const value = button.dataset.value;

            // Update the corresponding hidden input
            const input = document.getElementById(`${type}-input`);
            if (input) {
                input.value = value;
            }

            // Update the active state for buttons in the same group
            document.querySelectorAll(`.option-btn[data-type="${type}"]`).forEach(btn => {
                btn.classList.remove('active');
            });
            button.classList.add('active');
        });
    });

    // Handle form submission with Fetch API
    if (form) {
        form.addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent traditional form submission

            const formData = new FormData(this);
            const generateBtn = document.getElementById('generate-btn');
            const resultContainer = document.getElementById('generation-result');
            const audioPlayerContainer = document.getElementById('audio-player');
            const downloadLink = document.getElementById('download-link');

            // Disable button and show a loading state
            generateBtn.disabled = true;
            generateBtn.querySelector('span').textContent = 'Generating...';
            resultContainer.classList.add('hidden');


            fetch('generate.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.file_path) {
                    // Create audio element
                    audioPlayerContainer.innerHTML = ''; // Clear previous player
                    const audio = document.createElement('audio');
                    audio.controls = true;
                    audio.src = data.file_path;

                    audioPlayerContainer.appendChild(audio);
                    downloadLink.href = data.file_path;

                    // Show the result section
                    resultContainer.classList.remove('hidden');
                    audio.play();

                } else {
                    alert(data.message || 'An error occurred during generation.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('A critical error occurred. Please check the console.');
            })
            .finally(() => {
                // Re-enable the button
                generateBtn.disabled = false;
                generateBtn.querySelector('span').textContent = 'Generate';
            });
        });
    }
});
