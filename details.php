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

    <title>TrackBox</title>

    <link rel="stylesheet" href="css/home.css">
    
    <script src="script.js" defer></script>

</head>
<body>

    <header class="top-bar">

        <div class="logo">
            TRACKBOX
        </div>

        <nav class="top-nav">
            <a href="index.php">Home</a>
            <a href="#">Search</a>
        </nav>

        <div class="">
            <span><?php echo $_SESSION['name']; ?></span>

            <a href="logout.php">Logout</a>
        </div>

    </header>

    <aside class="side-bar">

        <nav class="side-nav">
            <a href="index.php">Home</a>
            <a href="#">Library</a>
            <a href="#">Playlists</a>
        </nav>

    </aside>

<main class="main-content">

    <section class="track-details">

        <img class="details-cover" src="images/febem-elevador.jpg" alt="Castelo Vazio">

        <div class="track-info">

            <h1>Castelo Vazio</h1>

            <p>Febem</p>

            <button id="play-button">▶</button>

            <audio id="audio-player" src="music/castelo-vazio.mp3"></audio>

            <div class="progress-control">

                <span id="current-time">0:00</span>

                <input id="progress-bar" type="range" min="0" max="100" value="0">

                <span id="duration">0:00</span>

                <div class="volume-control">

                    <span id="volume-icon">🔊</span>

                    <input id="volume-bar" type="range" min="0" max="100" value="50">

                </div>

            </div>

        </div>
    </section>
</main>

</body>
</html>