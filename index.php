<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

$username = $_SESSION['username'];

$justLoggedIn = $_SESSION['just_logged_in'] ?? false;

unset($_SESSION['just_logged_in']);

?>

<script>
    const currentUsername = "<?php echo $username; ?>";
</script>

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

        <div class="search-bar">
            <span>⌕</span>
            <input id="search-input" type="text" placeholder="Search">
        </div>

        <div id="search-result" class="search-result"></div>

        <div class="user-menu">

            <button id="user-menu-button">
                @<?php echo $_SESSION['username']; ?>
            </button>

            <div id="user-dropdown">

                <a href="profile.php">Profile</a>

                <a href="settings.php">Settings</a>

                <button id="dark-mode-button">Dark Mode</button>

                <a href="logout.php">Logout</a>

            </div>

        </div>

    </header>

    <aside class="side-bar">

        <nav class="side-nav">
            <a href="index.php" class="home-link">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M3 10.5L12 3l9 7.5"></path>
                    <path d="M5 9.5V21h14V9.5"></path>
                    <path d="M9 21v-6h6v6"></path>
                </svg>
                <span>Home</span>
            </a>

            <a href="#">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <rect x="4" y="5" width="16" height="14" rx="2"></rect>
                    <path d="M8 9h8"></path>
                    <path d="M8 13h5"></path>
                    <path d="M8 17h3"></path>
                </svg>
                <span>Library</span>
            </a>

            <a href="#">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M4 6h10"></path>
                    <path d="M4 11h10"></path>
                    <path d="M4 16h6"></path>
                    <path d="M17 5v10"></path>
                    <path d="M17 8l3-1v7"></path>
                    <circle cx="14.5" cy="17.5" r="2.5"></circle>
                </svg>
                <span>Playlists</span>
            </a>

        </nav>

    </aside>

    <aside class="right-bar">

        <section class="now-playing">

            <h2>Now Playing</h2>

            <div class="now-playing-user">
                <div class="now-playing-avatar"></div>

                <div class="now-playing-user-info">
                    <span class="now-playing-username"></span>
                    <span class="now-playing-name"></span>
                </div>
            </div>

            <div class="now-playing-track">

                <img id="now-playing-cover" src="" alt="Now Playing">

                <div class="now-playing-info">
                    <span id="now-playing-title">Nothing playing</span>
                    <span id="now-playing-artist"></span>
                </div>

            </div>

        </section>

    </aside>

    <main class="main-content">

    <?php if ($justLoggedIn): ?>
        <h1 id="welcome-popup">
            Welcome, <?php echo $_SESSION['name']; ?>!
        </h1>
    <?php endif; ?>

        <section class="recently-played">

            <h2>Recently played</h2>

            <div class="track-list" id="recently-played-list">

            </div>

        </section>

        <section class="discover">

            <h2>Discover</h2>

            <div class="discover-list">

                <div class="discover-card">

                    <img class="discover-cover" src="images/kanye-west.png" alt="Hip Hop">

                    <div class="discover-details">
                        <span class="discover-title">Hip Hop</span>
                        <span class="discover-description">Explore the genre</span>
                    </div>
                    
                </div>

                <div class="discover-card">

                    <img class="discover-cover" src="images/boladin211.jpg" alt="Brazilian Funk">

                    <div class="discover-details">
                        <span class="discover-title">Brazilian Funk</span>
                        <span class="discover-description">Explore the genre</span>
                    </div>

                </div>

                <div class="discover-card">

                    <img class="discover-cover" src="images/fred-again.jpeg" alt="Electronic">

                    <div class="discover-details">
                        <span class="discover-title">Electronic</span>
                        <span class="discover-description">Explore the genre</span>
                    </div>

                </div>

                <div class="discover-card">

                    <img class="discover-cover" src="images/jorja-smith.jpg" alt="R&B">

                    <div class="discover-details">
                        <span class="discover-title">R&B</span>
                        <span class="discover-description">Explore the genre</span>
                    </div>

                </div>

            </div>

        </section>

    </main>

    <?php include "player.php"; ?> 
 
</body>
</html>