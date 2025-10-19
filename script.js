document.addEventListener('DOMContentLoaded', () => {
    const navLinks = document.querySelectorAll('nav a');
    const sections = document.querySelectorAll('main section');
    const generateBtn = document.getElementById('generate-btn');

    // Function to handle navigation
    const showSection = (targetId) => {
        sections.forEach(section => {
            if (`#${section.id}` === targetId) {
                section.style.display = 'block';
            } else {
                section.style.display = 'none';
            }
        });
    };

    // Set up navigation links
    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const targetId = e.target.getAttribute('href');
            showSection(targetId);
        });
    });

    // Handle song generation
    generateBtn.addEventListener('click', () => {
        const lyrics = document.getElementById('lyrics').value;
        const style = document.getElementById('style').value;
        const title = document.getElementById('title').value;
        const voice_id = document.getElementById('voice_id').value;

        const formData = new FormData();
        formData.append('lyrics', lyrics);
        formData.append('style', style);
        formData.append('title', title);
        formData.append('voice_id', voice_id);

        fetch('generate.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            console.log(data); // For debugging
            alert('Song generated successfully! (Placeholder)');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while generating the song.');
        });
    });

    // Show the generator section by default
    showSection('#generator');
});
