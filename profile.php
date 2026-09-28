<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

$username = $_GET['username'] ?? $_SESSION['username'];

require "db.php";

$sql = "SELECT id, full_name, username, profile_picture, profile_banner, profile_picture_zoom, profile_banner_position FROM users WHERE username = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0) {
    exit("User not found.");
}

$stmt->bind_result($profile_id, $profile_name, $profile_username, $profile_picture, $profile_banner, $profile_picture_zoom, $profile_banner_position);
$stmt->fetch();

$is_following = false;

$is_owner = ($_SESSION['user_id'] == $profile_id);

if ($_SESSION['user_id'] != $profile_id) {
    $sql = "SELECT id FROM follows
            WHERE follower_id = ? AND following_id = ?";

    $stmt = $connection->prepare($sql);
    $stmt->bind_param("ii", $_SESSION['user_id'], $profile_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $is_following = true;
    }
}

$sql = "SELECT COUNT(*) FROM follows WHERE following_id = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $profile_id);
$stmt->execute();
$stmt->bind_result($followers_count);
$stmt->fetch();
$stmt->close();

$sql = "SELECT COUNT(*) FROM follows WHERE follower_id = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $profile_id);
$stmt->execute();
$stmt->bind_result($following_count);
$stmt->fetch();
$stmt->close();

$sql = "SELECT COUNT(*) 
        FROM likes 
        WHERE user_id = ?";

$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $profile_id);
$stmt->execute();
$stmt->bind_result($likes_count);
$stmt->fetch();
$stmt->close();

$sql = "SELECT
            songs.id,
            songs.title,
            artists.name AS artist,
            songs.cover_path,
            songs.audio_path
        FROM likes
        INNER JOIN songs
            ON likes.song_id = songs.id
        INNER JOIN artists
            ON songs.artist_id = artists.id
        WHERE likes.user_id = ?
        ORDER BY likes.created_at DESC";

$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $profile_id);
$stmt->execute();

$liked_songs = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Profile - TrackBox</title>

    <link rel="stylesheet" href="css/home.css">

</head>

<body>

    <header class="top-bar">

        <div class="logo">
            TRACKBOX
        </div>

        <div class="user-menu">

            <button id="user-menu-button">
                @<?php echo $_SESSION['username']; ?>
            </button>

            <div id="user-dropdown">

                <a href="index.php">Home</a>

                <a href="settings.php">Settings</a>

                <button id="dark-mode-button">Dark Mode</button>

                <a href="logout.php">Logout</a>

            </div>

        </div>

    </header>

    <main class="profile-page">

        <section class="profile-card">

            <div class="profile-banner-wrapper">

                <div
                    class="profile-banner"
                    <?php if (!empty($profile_banner)): ?>
                        style="
                            background-image: url('<?php echo htmlspecialchars($profile_banner); ?>');
                            background-position: center <?php echo $profile_banner_position; ?>%;
                        "
                    <?php endif; ?>
                ></div>

                <?php if ($is_owner): ?>

                    <button type="button" class="profile-banner-edit">
                        Edit
                    </button>

                    <input
                        type="file"
                        id="profile-banner-input"
                        accept="image/*"
                        hidden
                    >

                <?php endif; ?>

                <div class="image-editor-modal" id="banner-image-editor">

                    <div class="image-editor">

                        <h2>Edit banner</h2>

                        <div class="banner-image-editor-preview">
                            <img id="banner-image-preview" src="" alt="Banner preview">
                        </div>

                        <div class="image-editor-controls">

                            <label for="banner-image-position">
                                Vertical position
                            </label>

                            <input
                                type="range"
                                id="banner-image-position"
                                min="0"
                                max="100"
                                step="1"
                                value="50"
                            >

                        </div>

                        <div class="image-editor-actions">

                            <button type="button" id="banner-image-cancel">
                                Cancel
                            </button>

                            <button type="button" id="banner-image-confirm">
                                Use banner
                            </button>

                        </div>

                    </div>

                </div>

            </div>

            <div class="profile-content">

                <div class="profile-avatar-wrapper">

                    <div class="profile-avatar">
                        <?php if (!empty($profile_picture)): ?>
                           <img
                                src="<?php echo htmlspecialchars($profile_picture); ?>"
                                alt="Profile picture"
                                style="transform: scale(<?php echo $profile_picture_zoom; ?>);"
                            >
                        <?php else: ?>
                            <?php echo strtoupper(substr($profile_username, 0, 1)); ?>
                        <?php endif; ?>
                    </div>

                    <?php if ($is_owner): ?>

                        <button type="button" class="profile-avatar-edit">
                            Edit
                        </button>

                        <input
                            type="file"
                            id="profile-avatar-input"
                            accept="image/*"
                            hidden
                        >

                    <?php endif; ?>

                    <div class="image-editor-modal" id="profile-image-editor">

                        <div class="image-editor">

                            <h2>Edit profile picture</h2>

                            <div class="image-editor-preview">
                                <img id="profile-image-preview" src="" alt="Profile preview">
                            </div>

                            <div class="image-editor-controls">

                                <label for="profile-image-zoom">
                                    Zoom
                                </label>

                                <input
                                    type="range"
                                    id="profile-image-zoom"
                                    min="1"
                                    max="2"
                                    step="0.01"
                                    value="1"
                                >

                            </div>

                            <div class="image-editor-actions">

                                <button type="button" id="profile-image-cancel">
                                    Cancel
                                </button>

                                <button type="button" id="profile-image-confirm">
                                    Use photo
                                </button>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="profile-details">
                    <h1><?php echo $profile_name; ?></h1>
                    <p>@<?php echo $profile_username; ?></p>

                    <?php if($_SESSION['user_id'] != $profile_id): ?>
                        <form action="follow.php" method="POST">
                            <input type="hidden" name="following_id" value="<?php echo $profile_id; ?>">
                            <input type="hidden" name="username" value="<?php echo $profile_username; ?>">

                            <button type="submit" class="<?php if ($is_following) echo 'following'; ?>">
                                <?php if ($is_following) echo 'Following'; else echo 'Follow'; ?>
                            </button>
                        </form>
                    <?php endif; ?>
                </div>

            </div>

            <div class="profile-navigation">

                <nav class="profile-tabs">
                    <a href="#" class="active">Overview</a>
                    <a href="#">Tracks</a>
                    <a href="#">Albums</a>
                    <a href="#">Playlists</a>
                </nav>

                <div class="profile-stats">

                    <div class="profile-stat">
                        <strong><?php echo $followers_count; ?></strong>
                        <span>Followers</span>
                    </div>

                    <div class="profile-stat">
                        <strong><?php echo $following_count; ?></strong>
                        <span>Following</span>
                    </div>

                    <div class="profile-stat">
                        <strong><?php echo $likes_count; ?></strong>

                        <span>Likes</span>
                    </div>

                </div>

            </div>

        </section>

                <section class="profile-layout">

            <aside class="profile-feed">

                <h2>Activity</h2>

                <div class="post-composer">

                    <div class="post-composer-top">

                        <div class="post-composer-avatar">
                            <?php echo strtoupper(substr($profile_username, 0, 1)); ?>
                        </div>

                        <textarea placeholder="What's good?"></textarea>

                    </div>

                    <div class="post-composer-bottom">
                        <button type="button">Post</button>
                    </div>
                
                </div>
            </aside>


            <section class="liked-songs">

                <div class="liked-header">
                    <div>
                        <h2>Liked Songs</h2>
                        <span><?php echo $likes_count; ?> songs</span>
                    </div>

                    <button id="play-liked-songs">▶ Play all</button>
                </div>

                <?php while ($song = $liked_songs->fetch_assoc()): ?>

                    <div class="liked-song">

                        <button
                            class="liked-play song-play"
                            data-song-id="<?php echo $song['id']; ?>"
                            data-title="<?php echo htmlspecialchars($song['title']); ?>"
                            data-artist="<?php echo htmlspecialchars($song['artist']); ?>"
                            data-src="<?php echo htmlspecialchars($song['audio_path']); ?>"
                            data-cover="<?php echo htmlspecialchars($song['cover_path']); ?>"
                        >
                            ▶
                        </button>

                        <img src="<?php echo htmlspecialchars($song['cover_path']); ?>" 
                            alt="<?php echo htmlspecialchars($song['title']); ?>">

                        <div class="liked-song-info">
                            <strong><?php echo htmlspecialchars($song['title']); ?></strong>
                            <span><?php echo htmlspecialchars($song['artist']); ?></span>
                        </div>

                        <button class="liked-heart">♥</button>

                    </div>

                <?php endwhile; ?>

            </section>

            <aside class="profile-suggestions">

                <h2>Suggested for you</h2>

                <div class="suggestion-card">

                    <div class="suggestion-avatar">
                        A
                    </div>

                    <div class="suggestion-info">
                        <strong>Alex</strong>
                        <span>@alex</span>
                    </div>

                    <button>Follow</button>

                </div>

                <div class="suggestion-card">

                    <div class="suggestion-avatar">
                        B
                    </div>

                    <div class="suggestion-info">
                        <strong>Bruno</strong>
                        <span>@bruno</span>
                    </div>

                    <button>Follow</button>

                </div>

                <div class="suggestion-card">

                    <div class="suggestion-avatar">
                        L
                    </div>

                    <div class="suggestion-info">
                        <strong>Lucas</strong>
                        <span>@lucas</span>
                    </div>

                    <button>Follow</button>

                </div>

            </aside>

            <aside class="profile-now-playing">

                <h2>Now Playing</h2>

                <div class="profile-now-user">
                        <div class="profile-now-avatar">
                            <?php if (!empty($profile_picture)): ?>
                                <img
                                    src="<?php echo htmlspecialchars($profile_picture); ?>"
                                    alt="Profile picture"
                                >
                            <?php else: ?>
                                <?php echo strtoupper(substr($profile_username, 0, 1)); ?>
                            <?php endif; ?>
                        </div>

                    <div>
                        <strong>@<?php echo $profile_username; ?></strong>
                        <span><?php echo $profile_name; ?></span>
                    </div>
                </div>

                <div class="profile-now-empty">
                    <span>Nothing playing</span>
                </div>

            </aside>

        </section>

    </main>

<?php include "player.php"; ?>

<script src="script.js"></script>

<script>

const profileAvatarEdit = document.querySelector(".profile-avatar-edit");
const profileAvatarInput = document.querySelector("#profile-avatar-input");

profileAvatarEdit.addEventListener("click", function() {
    profileAvatarInput.click();
});

profileAvatarInput.addEventListener("change", function() {

    const file = profileAvatarInput.files[0];

    if (!file) {
        return;
    }

    const imageUrl = URL.createObjectURL(file);

    profileImagePreview.src = imageUrl;

    profileImageEditor.style.display = "flex";

});

const profileImageZoom = document.querySelector("#profile-image-zoom");

profileImageZoom.addEventListener("input", function() {

    const zoom = profileImageZoom.value;

    profileImagePreview.style.transform = `scale(${zoom})`;

});

const profileImageCancel = document.querySelector("#profile-image-cancel");
const profileImageConfirm = document.querySelector("#profile-image-confirm");

const profileAvatar = document.querySelector(".profile-avatar");


profileImageCancel.addEventListener("click", function() {

    profileImageEditor.style.display = "none";

    profileImagePreview.src = "";

    profileAvatarInput.value = "";

    profileImageZoom.value = "1";

    profileImagePreview.style.transform = "scale(1)";

});

profileImageConfirm.addEventListener("click", function() {

    const file = profileAvatarInput.files[0];

    const zoom = profileImageZoom.value;

    const formData = new FormData();

    formData.append("profile_picture", file);

    formData.append("profile_picture_zoom", zoom);

    fetch("upload_profile_picture.php", {
        method: "POST",
        body: formData
    });

    const imageUrl = profileImagePreview.src;

    let profileImage = profileAvatar.querySelector("img");

    if (!profileImage) {
        profileImage = document.createElement("img");
        profileImage.alt = "Profile picture";
        profileAvatar.innerHTML = "";
        profileAvatar.appendChild(profileImage);
    }

    profileImage.src = imageUrl;

    profileImage.style.transform = `scale(${zoom})`;

    profileAvatar.style.fontSize = "0";

    profileImageEditor.style.display = "none";

});

const profileBannerEdit = document.querySelector(".profile-banner-edit");
const profileBannerInput = document.querySelector("#profile-banner-input");

const bannerImageEditor = document.querySelector("#banner-image-editor");
const bannerImagePreview = document.querySelector("#banner-image-preview");

const bannerImagePosition = document.querySelector("#banner-image-position");

const bannerImageCancel = document.querySelector("#banner-image-cancel");
const bannerImageConfirm = document.querySelector("#banner-image-confirm");

const profileBanner = document.querySelector(".profile-banner");


profileBannerEdit.addEventListener("click", function() {
    profileBannerInput.click();
});

profileBannerInput.addEventListener("change", function() {

    const file = profileBannerInput.files[0];

    if (!file) {
        return;
    }

    const imageUrl = URL.createObjectURL(file);

    bannerImagePreview.src = imageUrl;

    bannerImagePreview.style.objectPosition = `center ${bannerImagePosition.value}%`;

    bannerImageEditor.style.display = "flex";

});

bannerImagePosition.addEventListener("input", function() {

    const position = bannerImagePosition.value;

    bannerImagePreview.style.objectPosition = `center ${position}%`;

});

bannerImageCancel.addEventListener("click", function() {

    bannerImageEditor.style.display = "none";

    bannerImagePreview.src = "";

    profileBannerInput.value = "";

});

bannerImageConfirm.addEventListener("click", function() {

    const file = profileBannerInput.files[0];

    const position = bannerImagePosition.value;

    const formData = new FormData();

    formData.append("profile_banner", file);

    formData.append("profile_banner_position", position);

    fetch("upload_profile_banner.php", {
        method: "POST",
        body: formData
    });

    const imageUrl = bannerImagePreview.src;

    profileBanner.style.backgroundImage = `url("${imageUrl}")`;

    profileBanner.style.backgroundSize = "cover";

    profileBanner.style.backgroundPosition = `center ${position}%`;

    bannerImageEditor.style.display = "none";

});

const profileImageEditor = document.querySelector("#profile-image-editor");
const profileImagePreview = document.querySelector("#profile-image-preview");

</script>

</body>

</html>