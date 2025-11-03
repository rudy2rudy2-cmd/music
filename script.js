document.addEventListener('DOMContentLoaded', () => {
    const durationSlider = document.getElementById('duration');
    const durationValue = document.getElementById('duration-value');
    const generateBtn = document.getElementById('generate-btn');
    const promptInput = document.getElementById('prompt');
    const optionBtns = document.querySelectorAll('.option-btn');
    const trackContainer = document.getElementById('generated-track-container');
    const audioPlayer = document.getElementById('audio-player');

    // Handle option button clicks
    optionBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll(`.option-btn[data-group="${btn.dataset.group}"]`).forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        });
    });

    // Update duration display
    durationSlider.addEventListener('input', () => {
        const minutes = Math.floor(durationSlider.value / 60);
        const seconds = durationSlider.value % 60;
        durationValue.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
    });

    // Handle song generation
    generateBtn.addEventListener('click', () => {
        const prompt = promptInput.value;
        const duration = durationSlider.value;
        const voice = document.querySelector('.option-btn[data-group="voice"].active').value;
        const genre = document.querySelector('.option-btn[data-group="genre"].active').value;
        const mood = document.querySelector('.option-btn[data-group="mood"].active').value;

        const formData = new FormData();
        formData.append('prompt', prompt);
        formData.append('duration', duration);
        formData.append('voice', voice);
        formData.append('genre', genre);
        formData.append('mood', mood);

        fetch('generate.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.file_path) {
                audioPlayer.src = data.file_path;
                trackContainer.style.display = 'block';
                audioPlayer.load();
                audioPlayer.play();
            } else {
                alert(data.message || 'An error occurred.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred.');
        });
    });
});
