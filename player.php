<?php
require_once 'includes/db.php';

$channel_id = $_GET['channel'] ?? 0;
$is_preview = isset($_GET['preview']) ? true : false;

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
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            background: #000;
            font-family: 'Inter', sans-serif;
        }
        .layout {
            display: flex;
            flex-direction: column;
            width: 100%;
            height: 100%;
        }
        .header {
            height: 90px;
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(20px);
            color: white;
            display: flex;
            align-items: center;
            padding: 0 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
            z-index: 100;
            border-bottom: 2px solid rgba(255,255,255,0.05);
        }
        .logo-container {
            width: 180px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.05);
            border-radius: 15px;
            margin-right: 40px;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .logo {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            padding: 10px;
        }
        .ticker-container {
            flex-grow: 1;
            overflow: hidden;
            white-space: nowrap;
            background: rgba(255,255,255,0.03);
            border-radius: 20px;
            height: 50px;
            display: flex;
            align-items: center;
            position: relative;
            box-shadow: inset 0 2px 10px rgba(0,0,0,0.3);
        }
        .ticker {
            display: inline-block;
            white-space: nowrap;
            padding-left: 100%;
            animation: ticker 25s linear infinite;
            font-size: 28px;
            font-weight: 700;
            color: #60a5fa;
            text-transform: uppercase;
            letter-spacing: 1px;
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
            overflow: hidden;
        }
        .media-item {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            object-fit: contain;
            transition: opacity 1.5s ease-in-out, transform 20s ease-out;
            transform: scale(1.05);
        }
        .media-item.active {
            opacity: 1;
            transform: scale(1);
        }

        /* Floating Channel Selector */
        .channel-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background: #2563eb;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 1000;
            box-shadow: 0 10px 30px rgba(37, 99, 235, 0.4);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 4px solid rgba(255,255,255,0.2);
            opacity: 0.2;
        }
        .channel-btn:hover {
            opacity: 1;
            transform: scale(1.1) rotate(90deg);
        }
        .channel-selector {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(10px);
            z-index: 999;
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            transition: opacity 0.5s ease;
        }
        .selector-active {
            display: flex;
            animation: fadeIn 0.3s ease;
        }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        .channel-list {
            display: grid;
            grid-template-cols: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            width: 80%;
            max-width: 1000px;
        }
        .channel-item {
            padding: 30px;
            background: rgba(255,255,255,0.05);
            border-radius: 25px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            font-weight: bold;
            font-size: 1.2rem;
        }
        .channel-item:hover {
            background: rgba(255,255,255,0.15);
            transform: translateY(-10px);
            border-color: #3b82f6;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }

        /* Hide UI for preview */
        .is-preview .channel-btn { display: none; }
        .is-preview .header { height: 60px; padding: 0 20px; }
        .is-preview .logo-container { width: 120px; height: 40px; margin-right: 20px; }
        .is-preview .ticker { font-size: 18px; }
    </style>
</head>
<body class="<?php echo $is_preview ? 'is-preview' : ''; ?>">
    <div class="layout">
        <header class="header">
            <div class="logo-container" id="logo-box">
                <img id="channel-logo" src="" class="logo" style="display:none;">
            </div>
            <div class="ticker-container">
                <div id="ticker-text" class="ticker"></div>
            </div>
        </header>
        <div class="content" id="player-container">
            <div id="no-media" class="text-slate-400 font-bold uppercase tracking-widest text-2xl animate-pulse">Sistemul se încarcă...</div>
        </div>
    </div>

    <!-- Channel Selector Trigger -->
    <div class="channel-btn" onclick="toggleSelector()" title="Schimbă Canalul">
        <i class="fas fa-th-large text-2xl"></i>
    </div>

    <!-- Selector Overlay -->
    <div id="selector-overlay" class="channel-selector" onclick="if(event.target === this) toggleSelector()">
        <h2 class="text-4xl font-bold mb-12 uppercase tracking-widest">Selectează Canalul</h2>
        <div class="channel-list" id="all-channels-list">
            <!-- Dynamically populated -->
        </div>
        <button onclick="toggleSelector()" class="mt-16 text-slate-400 hover:text-white transition font-bold uppercase tracking-widest border-b border-slate-700 pb-1">Închide Meniul</button>
    </div>

    <script>
        const channelId = <?php echo (int)$channel_id; ?>;
        const container = document.getElementById('player-container');
        const logoBox = document.getElementById('logo-box');
        const logoEl = document.getElementById('channel-logo');
        const tickerEl = document.getElementById('ticker-text');
        const channelsList = document.getElementById('all-channels-list');

        let mediaItems = [];
        let currentIndex = 0;
        let playTimeout = null;
        let lastResponseStr = '';

        async function fetchMedia() {
            try {
                const response = await fetch(`api_media.php?channel=${channelId}`);
                const data = await response.json();

                const responseStr = JSON.stringify(data.media + JSON.stringify(data.config));
                if (responseStr !== lastResponseStr) {
                    lastResponseStr = responseStr;

                    // Update header config
                    if (data.config.logo) {
                        logoEl.src = data.config.logo;
                        logoEl.style.display = 'block';
                        logoBox.style.background = 'white';
                    } else {
                        logoEl.style.display = 'none';
                        logoBox.style.background = 'rgba(255,255,255,0.05)';
                    }
                    tickerEl.textContent = data.config.ticker || 'VĂ RUGĂM SĂ PĂSTRAȚI CURĂȚENIA. VIZIERE DIGITALE © 2025.';

                    // Update media
                    mediaItems = data.media;
                    updatePlayer();
                }

                // Update all channels list
                populateSelector(data.all_channels);
            } catch (e) {
                console.error("Fetch media failed:", e);
            }
        }

        function populateSelector(channels) {
            channelsList.innerHTML = '';
            channels.forEach(ch => {
                const item = document.createElement('div');
                item.className = 'channel-item';
                item.textContent = ch.name;
                item.onclick = () => {
                    window.location.href = `player.php?channel=${ch.id}`;
                };
                channelsList.appendChild(item);
            });
        }

        function updatePlayer() {
            // Store reference to current active if exists to avoid jarring swap if possible
            const currentActiveEl = document.querySelector('.media-item.active');

            container.innerHTML = '';
            if (mediaItems.length === 0) {
                container.innerHTML = '<div id="no-media" class="text-slate-400 font-bold uppercase tracking-widest text-2xl animate-pulse">Nicio media încărcată pe acest canal.</div>';
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

            // Wait for next frame to ensure transition triggers
            requestAnimationFrame(() => {
                current.classList.add('active');
            });

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

        function toggleSelector() {
            document.getElementById('selector-overlay').classList.toggle('selector-active');
        }

        // Initial fetch
        fetchMedia();

        // Check for updates every 30 seconds
        setInterval(fetchMedia, 30000);

        // Hide selector after some time of inactivity or just start hidden
        document.addEventListener('mousemove', () => {
            const btn = document.querySelector('.channel-btn');
            if (btn) btn.style.opacity = '0.8';
            clearTimeout(window.hideBtnTimeout);
            window.hideBtnTimeout = setTimeout(() => {
                if (btn) btn.style.opacity = '0.2';
            }, 3000);
        });
    </script>
</body>
</html>
