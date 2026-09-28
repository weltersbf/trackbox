<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

require "db.php";

$artist_id = $_GET['id'] ?? null;

if (!$artist_id || !is_numeric($artist_id)) {
    exit("Artist not found.");
}

$artist_id = (int) $artist_id;

/*
|--------------------------------------------------------------------------
| Get artist
|--------------------------------------------------------------------------
*/

$sql = "SELECT id, name, image
        FROM artists
        WHERE id = ?";

$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $artist_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    exit("Artist not found.");
}

$artist = $result->fetch_assoc();

$stmt->close();

/*
|--------------------------------------------------------------------------
| Get artist songs
|--------------------------------------------------------------------------
*/

$sql = "SELECT
            songs.id,
            songs.title,
            songs.album,
            songs.album_id,
            songs.cover_path,
            songs.audio_path,
            songs.created_at
        FROM songs
        WHERE songs.artist_id = ?
        ORDER BY songs.id DESC";

$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $artist_id);
$stmt->execute();

$songs_result = $stmt->get_result();

$songs = [];

while ($song = $songs_result->fetch_assoc()) {
    $songs[] = $song;
}

$stmt->close();

/*
|--------------------------------------------------------------------------
| Artist statistics
|--------------------------------------------------------------------------
*/

$track_count = count($songs);

/*
|--------------------------------------------------------------------------
| Artist image
|--------------------------------------------------------------------------
*/

$artist_image = !empty($artist['image'])
    ? $artist['image']
    : null;

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo htmlspecialchars($artist['name']); ?> - TrackBox</title>

    <link rel="stylesheet" href="css/home.css">

    <script src="script.js" defer></script>

    <style>

        /*
        |--------------------------------------------------------------------------
        | Artist Page
        |--------------------------------------------------------------------------
        */

        .artist-page {

            margin-left: 210px;

            margin-right: 280px;

            min-height: calc(100vh - 70px);

            padding: 40px 50px 130px;

            background-color: var(--bg-main);

        }


        /*
        |--------------------------------------------------------------------------
        | Artist Header
        |--------------------------------------------------------------------------
        */

        .artist-header {

            position: relative;

            min-height: 260px;

            display: flex;

            align-items: flex-end;

            gap: 28px;

            padding: 35px;

            overflow: hidden;

            border-radius: 14px;

            background:

                linear-gradient(

                    90deg,

                    rgba(0, 0, 0, 0.92),

                    rgba(0, 0, 0, 0.72),

                    rgba(0, 0, 0, 0.92)

                );

            border: 1px solid var(--border-dark);

        }


        .artist-header::before {

            content: "";

            position: absolute;

            inset: 0;

            background:

                radial-gradient(

                    circle at 20% 50%,

                    rgba(255, 116, 23, 0.25),

                    transparent 45%

                );

            pointer-events: none;

        }


        .artist-image {

            position: relative;

            z-index: 1;

            width: 170px;

            height: 170px;

            flex-shrink: 0;

            border-radius: 50%;

            overflow: hidden;

            border: 4px solid #ffffff;

            background-color: #ff7417;

            display: flex;

            align-items: center;

            justify-content: center;

            color: #ffffff;

            font-size: 54px;

            font-weight: 700;

        }


        .artist-image img {

            width: 100%;

            height: 100%;

            object-fit: cover;

        }


        .artist-header-info {

            position: relative;

            z-index: 1;

            padding-bottom: 5px;

            min-width: 0;

        }


        .artist-label {

            display: block;

            margin-bottom: 8px;

            color: #aaaaaa;

            font-size: 13px;

            font-weight: 600;

            text-transform: uppercase;

            letter-spacing: 0.8px;

        }


        .artist-name {

            margin: 0;

            color: #ffffff;

            font-size: 42px;

            line-height: 1.05;

            font-weight: 700;

            letter-spacing: -1px;

        }


        .artist-meta {

            display: flex;

            align-items: center;

            gap: 20px;

            margin-top: 15px;

            color: #aaaaaa;

            font-size: 14px;

        }


        .artist-meta strong {

            color: #ffffff;

            font-size: 15px;

        }


        /*
        |--------------------------------------------------------------------------
        | Artist Navigation
        |--------------------------------------------------------------------------
        */

        .artist-navigation {

            display: flex;

            align-items: center;

            gap: 28px;

            height: 62px;

            border-bottom: 1px solid var(--border);

        }


        .artist-navigation a {

            position: relative;

            height: 62px;

            display: flex;

            align-items: center;

            color: var(--text-secondary);

            text-decoration: none;

            font-size: 14px;

            font-weight: 600;

        }


        .artist-navigation a:hover {

            color: var(--text-primary);

        }


        .artist-navigation a.active {

            color: var(--accent);

        }


        .artist-navigation a.active::after {

            content: "";

            position: absolute;

            left: 0;

            right: 0;

            bottom: 0;

            height: 2px;

            background-color: var(--accent);

        }


        /*
        |--------------------------------------------------------------------------
        | Artist Content
        |--------------------------------------------------------------------------
        */

        .artist-content {

            display: grid;

            grid-template-columns: minmax(0, 1.7fr) minmax(230px, 0.7fr);

            gap: 30px;

            margin-top: 35px;

        }


        .artist-section {

            min-width: 0;

        }


        .artist-section-title {

            display: flex;

            align-items: center;

            justify-content: space-between;

            margin-bottom: 18px;

        }


        .artist-section-title h2 {

            margin: 0;

            font-size: 20px;

            color: var(--text-primary);

        }


        .artist-section-title span {

            color: var(--text-muted);

            font-size: 13px;

        }


        /*
        |--------------------------------------------------------------------------
        | Artist Tracks
        |--------------------------------------------------------------------------
        */

        .artist-track-list {

            display: flex;

            flex-direction: column;

            background-color: var(--surface);

            border: 1px solid var(--border);

            border-radius: 12px;

            overflow: hidden;

        }


        .artist-track {

            display: flex;

            align-items: center;

            gap: 15px;

            min-height: 82px;

            padding: 12px 15px;

            border-bottom: 1px solid var(--border);

            transition: background-color 0.15s ease;

        }


        .artist-track:last-child {

            border-bottom: none;

        }


        .artist-track:hover {

            background-color: var(--surface-dark);

        }


        .artist-track-cover {

            width: 58px;

            height: 58px;

            flex-shrink: 0;

            object-fit: cover;

            border-radius: 6px;

            background-color: #222222;

        }


        .artist-track-play {

            width: 38px;

            height: 38px;

            flex-shrink: 0;

            border: none;

            border-radius: 50%;

            background-color: var(--accent);

            color: #ffffff;

            cursor: pointer;

            font-size: 13px;

            display: flex;

            align-items: center;

            justify-content: center;

            transition:

                background-color 0.2s ease,

                transform 0.2s ease;

        }


        .artist-track-play:hover {

            background-color: #e85f00;

            transform: scale(1.05);

        }


        .artist-track-info {

            display: flex;

            flex-direction: column;

            min-width: 0;

            flex: 1;

            gap: 5px;

        }


        .artist-track-title {

            color: var(--text-primary);

            font-size: 15px;

            font-weight: 600;

            white-space: nowrap;

            overflow: hidden;

            text-overflow: ellipsis;

        }


        .artist-track-album {

            color: var(--text-secondary);

            font-size: 13px;

            white-space: nowrap;

            overflow: hidden;

            text-overflow: ellipsis;

        }


        .artist-track-number {

            width: 30px;

            color: var(--text-muted);

            font-size: 12px;

            text-align: center;

        }


        /*
        |--------------------------------------------------------------------------
        | Artist Sidebar
        |--------------------------------------------------------------------------
        */

        .artist-sidebar-card {

            padding: 22px;

            background-color: var(--surface);

            border: 1px solid var(--border);

            border-radius: 12px;

        }


        .artist-sidebar-card + .artist-sidebar-card {

            margin-top: 20px;

        }


        .artist-sidebar-card h3 {

            margin: 0 0 18px;

            color: var(--text-primary);

            font-size: 16px;

        }


        .artist-stat-row {

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding: 12px 0;

            border-bottom: 1px solid var(--border);

        }


        .artist-stat-row:last-child {

            border-bottom: none;

        }


        .artist-stat-row span {

            color: var(--text-secondary);

            font-size: 13px;

        }


        .artist-stat-row strong {

            color: var(--text-primary);

            font-size: 15px;

        }


        .artist-empty {

            padding: 40px 20px;

            text-align: center;

            color: var(--text-muted);

            font-size: 14px;

        }


        /*
        |--------------------------------------------------------------------------
        | Responsive
        |--------------------------------------------------------------------------
        */

        @media (max-width: 1000px) {

            .artist-page {

                margin-right: 0;

            }

            .artist-content {

                grid-template-columns: 1fr;

            }

        }


        @media (max-width: 700px) {

            .artist-page {

                margin-left: 0;

                padding: 25px 20px 130px;

            }

            .artist-header {

                min-height: 220px;

                padding: 25px;

                gap: 18px;

            }

            .artist-image {

                width: 120px;

                height: 120px;

            }

            .artist-name {

                font-size: 30px;

            }

            .artist-meta {

                flex-wrap: wrap;

                gap: 10px;

            }

        }

    </style>

</head>


<body>


    <!-- =========================================================
         TOP BAR
         ========================================================= -->

    <header class="top-bar">

        <div class="logo">
            TRACKBOX
        </div>


        <div class="search-bar">

            <span>⌕</span>

            <input
                id="search-input"
                type="text"
                placeholder="Search">

        </div>


        <div id="search-result" class="search-result"></div>


        <div class="user-menu">

            <button id="user-menu-button">
                @<?php echo htmlspecialchars($_SESSION['username']); ?>
            </button>


            <div id="user-dropdown">

                <a href="profile.php">
                    Profile
                </a>

                <a href="settings.php">
                    Settings
                </a>

                <button id="dark-mode-button">
                    Dark Mode
                </button>

                <a href="logout.php">
                    Logout
                </a>

            </div>

        </div>

    </header>


    <!-- =========================================================
         LEFT SIDEBAR
         ========================================================= -->

    <aside class="side-bar">

        <nav class="side-nav">

            <a href="index.php" class="home-link">

                <svg viewBox="0 0 24 24" aria-hidden="true">

                    <path d="M3 10.5L12 3l9 7.5"></path>

                    <path d="M5 9.5V21h14V9.5"></path>

                    <path d="M9 21v-6h6v6"></path>

                </svg>

                <span>
                    Home
                </span>

            </a>


            <a href="#">

                <svg viewBox="0 0 24 24" aria-hidden="true">

                    <rect
                        x="4"
                        y="5"
                        width="16"
                        height="14"
                        rx="2">
                    </rect>

                    <path d="M8 9h8"></path>

                    <path d="M8 13h5"></path>

                    <path d="M8 17h3"></path>

                </svg>

                <span>
                    Library
                </span>

            </a>


            <a href="#">

                <svg viewBox="0 0 24 24" aria-hidden="true">

                    <path d="M4 6h10"></path>

                    <path d="M4 11h10"></path>

                    <path d="M4 16h6"></path>

                    <path d="M17 5v10"></path>

                    <path d="M17 8l3-1v7"></path>

                    <circle
                        cx="14.5"
                        cy="17.5"
                        r="2.5">
                    </circle>

                </svg>

                <span>
                    Playlists
                </span>

            </a>

        </nav>

    </aside>


    <!-- =========================================================
         RIGHT SIDEBAR
         ========================================================= -->

    <aside class="right-bar">

        <section class="now-playing">

            <h2>
                Now Playing
            </h2>


            <div class="now-playing-user">

                <div class="now-playing-avatar"></div>


                <div class="now-playing-user-info">

                    <span class="now-playing-username"></span>

                    <span class="now-playing-name"></span>

                </div>

            </div>


            <div class="now-playing-track">

                <img
                    id="now-playing-cover"
                    src=""
                    alt="Now Playing">


                <div class="now-playing-info">

                    <span id="now-playing-title">
                        Nothing playing
                    </span>

                    <span id="now-playing-artist"></span>

                </div>

            </div>

        </section>

    </aside>


    <!-- =========================================================
         ARTIST PAGE
         ========================================================= -->

    <main class="artist-page">


        <!-- ARTIST HEADER -->

        <section class="artist-header">


            <div class="artist-image">

                <?php if ($artist_image): ?>

                    <img
                        src="<?php echo htmlspecialchars($artist_image); ?>"
                        alt="<?php echo htmlspecialchars($artist['name']); ?>">

                <?php else: ?>

                    <?php echo strtoupper(substr($artist['name'], 0, 1)); ?>

                <?php endif; ?>

            </div>


            <div class="artist-header-info">

                <span class="artist-label">
                    Artist
                </span>


                <h1 class="artist-name">

                    <?php echo htmlspecialchars($artist['name']); ?>

                </h1>


                <div class="artist-meta">

                    <span>

                        <strong>
                            <?php echo $track_count; ?>
                        </strong>

                        Tracks

                    </span>

                </div>

            </div>

        </section>


        <!-- ARTIST NAVIGATION -->

        <nav class="artist-navigation">

            <a
                href="#overview"
                class="active">

                Overview

            </a>


            <a href="#tracks">

                Tracks

            </a>

        </nav>


        <!-- ARTIST CONTENT -->

        <div class="artist-content" id="overview">


            <!-- TRACKS -->

            <section
                class="artist-section"
                id="tracks">


                <div class="artist-section-title">

                    <h2>
                        Recent tracks
                    </h2>

                    <span>
                        <?php echo $track_count; ?> songs
                    </span>

                </div>


                <div class="artist-track-list">


                    <?php if (count($songs) > 0): ?>


                        <?php foreach ($songs as $index => $song): ?>


                            <div class="artist-track">


                                <span class="artist-track-number">

                                    <?php
                                    echo str_pad(
                                        $index + 1,
                                        2,
                                        "0",
                                        STR_PAD_LEFT
                                    );
                                    ?>

                                </span>


                                <?php if (!empty($song['cover_path'])): ?>

                                    <img
                                        class="artist-track-cover"
                                        src="<?php echo htmlspecialchars($song['cover_path']); ?>"
                                        alt="<?php echo htmlspecialchars($song['title']); ?>">

                                <?php else: ?>

                                    <div class="artist-track-cover"></div>

                                <?php endif; ?>


                                <button
                                    type="button"
                                    class="artist-track-play"
                                    data-title="<?php echo htmlspecialchars($song['title']); ?>"
                                    data-artist="<?php echo htmlspecialchars($artist['name']); ?>"
                                    data-src="<?php echo htmlspecialchars($song['audio_path']); ?>"
                                    data-cover="<?php echo htmlspecialchars($song['cover_path'] ?? ''); ?>">

                                    ▶

                                </button>


                                <div class="artist-track-info">

                                    <span class="artist-track-title">

                                        <?php echo htmlspecialchars($song['title']); ?>

                                    </span>


                                    <span class="artist-track-album">

                                        <?php
                                        echo !empty($song['album'])
                                            ? htmlspecialchars($song['album'])
                                            : "Single";
                                        ?>

                                    </span>

                                </div>


                            </div>


                        <?php endforeach; ?>


                    <?php else: ?>


                        <div class="artist-empty">

                            This artist has no tracks yet.

                        </div>


                    <?php endif; ?>


                </div>


            </section>


            <!-- SIDEBAR -->

            <aside>


                <div class="artist-sidebar-card">

                    <h3>
                        About
                    </h3>


                    <div class="artist-stat-row">

                        <span>
                            Tracks
                        </span>

                        <strong>
                            <?php echo $track_count; ?>
                        </strong>

                    </div>


                    <div class="artist-stat-row">

                        <span>
                            Artist ID
                        </span>

                        <strong>
                            #<?php echo $artist_id; ?>
                        </strong>

                    </div>

                </div>


                <div class="artist-sidebar-card">

                    <h3>
                        Albums
                    </h3>


                    <?php

                    $albums = [];

                    foreach ($songs as $song) {

                        if (
                            !empty($song['album']) &&
                            !isset($albums[$song['album_id']])
                        ) {

                            $albums[$song['album_id']] = $song['album'];

                        }

                    }

                    ?>


                    <?php if (count($albums) > 0): ?>


                        <?php foreach ($albums as $album): ?>

                            <div class="artist-stat-row">

                                <span>
                                    <?php echo htmlspecialchars($album); ?>
                                </span>

                            </div>

                        <?php endforeach; ?>


                    <?php else: ?>


                        <div class="artist-empty">

                            No albums yet.

                        </div>


                    <?php endif; ?>


                </div>


            </aside>


        </div>


    </main>


    <!-- =========================================================
         EXISTING TRACKBOX PLAYER
         ========================================================= -->

    <?php include "player.php"; ?>


</body>

</html>