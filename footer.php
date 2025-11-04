<footer>
    <div class="footer-content">
        <p>copyright renul-music 2025</p>
        <div class="social-icons">
            <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
            <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="#" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
            <a href="#" aria-label="X"><i class="fab fa-twitter"></i></a>
            <a href="#" aria-label="Google"><i class="fab fa-google"></i></a>
        </div>
    </div>
</footer>

<!-- Theme-specific animated backgrounds -->
<?php
// Note: This requires the $theme variable to be available from the main page script.
if (isset($theme)) {
    if ($theme === 'neon') {
        echo '<div id="particles-js"></div>';
        echo '<script src="particles.js"></script>';
        echo '<script src="particles-config.js"></script>';
    } elseif ($theme === 'forest') {
        echo '<div class="organic-shape shape1"></div>';
        echo '<div class="organic-shape shape2"></div>';
    }
}
?>

<!-- Live Chat Button and Widget -->
<div class="chat-widget">
    <button id="chat-toggle-btn"><i class="fas fa-comment-dots"></i></button>
    <div id="chat-window" class="hidden">
        <div class="chat-header">
            <h3>Live Chat</h3>
            <button id="chat-close-btn">&times;</button>
        </div>
        <div class="chat-body">
            <p>Salut! Ai întrebări? Trimite-ne un mesaj și îți vom răspunde în cel mai scurt timp.</p>
            <form id="contact-form">
                <input type="email" name="email" placeholder="Adresa ta de email" required>
                <textarea name="message" placeholder="Mesajul tău..." required></textarea>
                <button type="submit">Trimite</button>
            </form>
        </div>
    </div>
</div>
<script src="chat.js"></script>
