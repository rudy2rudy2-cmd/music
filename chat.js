document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('chat-toggle-btn');
    const closeBtn = document.getElementById('chat-close-btn');
    const chatWindow = document.getElementById('chat-window');
    const contactForm = document.getElementById('contact-form');

    if (toggleBtn && chatWindow) {
        toggleBtn.addEventListener('click', () => {
            chatWindow.classList.toggle('hidden');
        });
    }

    if (closeBtn && chatWindow) {
        closeBtn.addEventListener('click', () => {
            chatWindow.classList.add('hidden');
        });
    }

    if (contactForm) {
        contactForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const email = contactForm.querySelector('input[name="email"]').value;
            const message = contactForm.querySelector('textarea[name="message"]').value;

            // Simulate form submission
            alert('Mesajul tău a fost trimis! Te vom contacta în curând la adresa ' + email);

            // Clear form and close window
            contactForm.reset();
            chatWindow.classList.add('hidden');
        });
    }
});
