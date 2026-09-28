<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Whole Lotta Red - TrackBox</title>

    <link rel="stylesheet" href="css/home.css">

    <script src="script.js" defer></script>

</head>
<body>

    <header class="top-bar">

        <div class="logo">
            TRACKBOX
        </div>

        <div class="search-bar">
            <span>⌕</span>
            <input type="text" placeholder="Search">
        </div>

        <div class="user-menu">
            <span>@<?php echo $_SESSION['username']; ?></span>

            <a href="logout.php">Logout</a>
        </div>

    </header>

    <aside class="side-bar">

        <nav class="side-nav">
            <a href="index.php" class="home-link">Home</a>
            <a href="#">Library</a>
            <a href="#">Playlists</a>
        </nav>

    </aside>

    <main class="main-content">

        <section class="album-header">

            <img
                class="album-cover"
                src="images/Playboi_Carti_-_Whole_Lotta_Red.png"
                alt="Whole Lotta Red">

            <div class="album-info">

                <p>Album</p>

                <h1>Whole Lotta Red</h1>

                <span class="album-artist">Playboi Carti</span>

            </div>

        </section>

        <section class="album-tracks">

            <h2>Tracks</h2>

            <div class="album-track-list">

                <div class="album-track">

                    <span class="track-number">01</span>

                    <div class="album-track-info">

                        <span class="track-title">Rockstar Made</span>
                        <span class="track-artist">Playboi Carti</span>

                    </div>

                    <button
                        class="track-play"
                        data-title="Rockstar Made"
                        data-artist="Playboi Carti"
                        data-src="music/playboi-carti_rockstar-made.mp3"
                        data-cover="images/Playboi_Carti_-_Whole_Lotta_Red.png">
                        ▶
                    </button>

                </div>

                <div class="album-track">

                    <span class="track-number">02</span>

                    <div class="album-track-info">

                        <span class="track-title">Go2DaMoon</span>
                        <span class="track-artist">Playboi Carti</span>

                    </div>

                    <button
                        class="track-play"
                        data-title="Go2DaMoon"
                        data-artist="Playboi Carti"
                        data-src="music/playboi-carti_Go2DaMoon.mp3"
                        data-cover="images/Playboi_Carti_-_Whole_Lotta_Red.png">
                        ▶
                    </button>

                </div>

                <div class="album-track">

                    <span class="track-number">03</span>

                    <div class="album-track-info">

                        <span class="track-title">Stop Breathing</span>
                        <span class="track-artist">Playboi Carti</span>

                    </div>

                    <button class="track-play">▶</button>

                </div>

            </div>

        </section>

    </main>

    <?php include "player.php"; ?> 
    
</body>
</html>