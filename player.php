<?php
require_once 'includes/db.php';

$channel_id = $_GET['channel'] ?? 0;

if (!$channel_id) {
    die("Canalul nu a fost specificat.");
}

$stmt = $pdo->prepare("SELECT name FROM channels WHERE id = ?");
$stmt->execute([$channel_id]);
$channel = $stmt->fetch();

if (!$channel) {
    die("Canalul nu a fost gasit.");
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($channel['name']); ?> - Viziere Digitale</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            background: #000;
            font-family: Arial, sans-serif;
        }
        .layout {
            display: flex;
            flex-direction: column;
            width: 100%;
            height: 100%;
        }
        .header {
            height: 80px;
            background: #1a1a1a;
            color: white;
            display: flex;
            align-items: center;
            padding: 0 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.5);
            z-index: 10;
        }
        .logo {
            height: 60px;
            max-width: 150px;
            object-fit: contain;
            margin-right: 30px;
        }
        .ticker-container {
            flex-grow: 1;
            overflow: hidden;
            white-space: nowrap;
            background: rgba(0,0,0,0.3);
            border-radius: 40px;
            height: 50px;
            display: flex;
            align-items: center;
            position: relative;
        }
        .ticker {
            display: inline-block;
            white-space: nowrap;
            padding-left: 100%;
            animation: ticker 20s linear infinite;
            font-size: 28px;
            font-weight: bold;
            color: #60a5fa;
        }
        @keyframes ticker {
            0% { transform: translateX(0); }
            100% { transform: translateX(-100%); }
        }
        .content {
            flex-grow: 1;
            position: relative;
            background: #000;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .media-item {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: none;
            object-fit: contain;
        }
        .media-item.active {
            display: block;
        }
        #no-media {
            color: white;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="layout">
        <div class="header">
            <img id="channel-logo" src="" class="logo" style="display:none;">
            <div class="ticker-container">
                <div id="ticker-text" class="ticker"></div>
            </div>
        </div>
        <div class="content" id="player-container">
            <div id="no-media">Incarcare...</div>
        </div>
    </div>

    <script>
        const channelId = <?php echo (int)$channel_id; ?>;
        const container = document.getElementById('player-container');
        const logoEl = document.getElementById('channel-logo');
        const tickerEl = document.getElementById('ticker-text');

        let mediaItems = [];
        let currentIndex = 0;
        let playTimeout = null;
        let lastResponseStr = '';

        async function fetchMedia() {
            try {
                const response = await fetch(`api_media.php?channel=${channelId}`);
                const data = await response.json();

                const responseStr = JSON.stringify(data);
                if (responseStr !== lastResponseStr) {
                    lastResponseStr = responseStr;

                    // Update header config
                    if (data.config.logo) {
                        logoEl.src = data.config.logo;
                        logoEl.style.display = 'block';
                    } else {
                        logoEl.style.display = 'none';
                    }
                    tickerEl.textContent = data.config.ticker || '';

                    // Update media
                    mediaItems = data.media;
                    updatePlayer();
                }
            } catch (e) {
                console.error("Fetch media failed:", e);
            }
        }

        function updatePlayer() {
            // Clear current player
            container.innerHTML = '';
            if (mediaItems.length === 0) {
                container.innerHTML = '<div id="no-media">Canalul nu are media incarcata.</div>';
                return;
            }

            mediaItems.forEach((media, index) => {
                let el;
                if (media.type === 'image') {
                    el = document.createElement('img');
                    el.src = media.file_path;
                    el.className = 'media-item';
                    el.setAttribute('data-duration', (media.duration || 10) * 1000);
                } else {
                    el = document.createElement('video');
                    el.src = media.file_path;
                    el.className = 'media-item';
                    el.muted = true;
                    el.playsInline = true;
                }
                container.appendChild(el);
            });

            currentIndex = 0;
            playCurrent();
        }

        function showNext() {
            const elements = document.querySelectorAll('.media-item');
            if (elements.length === 0) return;

            const current = elements[currentIndex];
            if (current) {
                current.classList.remove('active');
                if (current.tagName === 'VIDEO') {
                    current.pause();
                    current.currentTime = 0;
                }
            }

            currentIndex = (currentIndex + 1) % elements.length;
            playCurrent();
        }

        function playCurrent() {
            const elements = document.querySelectorAll('.media-item');
            if (elements.length === 0) return;

            const current = elements[currentIndex];
            if (!current) return;

            current.classList.add('active');

            if (playTimeout) clearTimeout(playTimeout);

            if (current.tagName === 'VIDEO') {
                current.play().catch(e => {
                    console.error("Video play failed:", e);
                    showNext();
                });
                current.onended = showNext;
            } else {
                const duration = parseInt(current.getAttribute('data-duration')) || 10000;
                playTimeout = setTimeout(showNext, duration);
            }
        }

        // Initial fetch
        fetchMedia();

        // Check for updates every 30 seconds
        setInterval(fetchMedia, 30000);
    </script>
</body>
</html>
