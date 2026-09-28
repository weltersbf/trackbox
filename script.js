const playButton = document.getElementById("play-button");

const audioPlayer = document.getElementById("audio-player");

const currentTime = document.getElementById("current-time");

const duration = document.getElementById("duration");

const progressBar = document.getElementById("progress-bar");

const volumeBar = document.getElementById("volume-bar");

audioPlayer.volume = 0.5;

async function updateNowPlaying(title, artist, cover) {

    await fetch("now_playing.php", {

        method: "POST",

        body: new URLSearchParams({
            track_title: title,
            artist: artist,
            is_playing: 1
        })

    });

    document.getElementById("now-playing-title").textContent = title;

    document.getElementById("now-playing-artist").textContent = artist;
    
    const nowPlayingCover = document.getElementById("now-playing-cover");

    nowPlayingCover.src = cover;
    nowPlayingCover.style.display = "block";

    loadNowPlaying();

}

const volumeIcon = document.getElementById("volume-icon");

async function updatePlayingStatus(status) {

    await fetch("now_playing.php", {

        method: "POST",

        body: new URLSearchParams({
            track_title: document.querySelector(".music-player .track-title").textContent,
            artist: document.querySelector(".music-player .track-artist").textContent,
            is_playing: status
        })

    });

}

playButton.addEventListener("click", function() {

        if (audioPlayer.paused) {

            audioPlayer.play();

            updatePlayingStatus(1);

            playButton.textContent = "⏸";

        } else {

            audioPlayer.pause();

            updatePlayingStatus(0);

            playButton.textContent = "▶";

        }

});

audioPlayer.addEventListener("timeupdate", function() {

    const minutes = Math.floor(audioPlayer.currentTime / 60);
    const seconds = Math.floor(audioPlayer.currentTime % 60);

    currentTime.textContent = minutes + ":" + seconds.toString().padStart(2, "0");

    progressBar.value = (audioPlayer.currentTime / audioPlayer.duration) * 100;

    progressBar.style.background = `linear-gradient(
    to right,
        #ff7417 0%,
        #ff7417 ${progressBar.value}%,
        #e5e5e5 ${progressBar.value}%,
        #e5e5e5 100%
    )`;

    savePlayerState();

});

progressBar.addEventListener("input", function() {

    audioPlayer.currentTime = (progressBar.value / 100) * audioPlayer.duration;

});

volumeBar.addEventListener("input", function() {

    audioPlayer.volume = volumeBar.value / 100;

    volumeBar.style.background = `linear-gradient(
        to right,
        #ff7417 0%,
        #ff7417 ${volumeBar.value}%,
        #e5e5e5 ${volumeBar.value}%,
        #e5e5e5 100%
    )`;

    if (volumeBar.value == 0) {
        volumeIcon.textContent = "🔇";
    } else if (volumeBar.value < 50) {
        volumeIcon.textContent = "🔉";
    } else {
        volumeIcon.textContent = "🔊";
    }

});

audioPlayer.addEventListener("ended", async function() {

    playButton.textContent = "▶";

    audioPlayer.currentTime = 0;

    progressBar.value = 0;

    await updatePlayingStatus(0);

    loadNowPlaying();

});

audioPlayer.addEventListener("ended", function() {
    
    playButton.textContent = "▶";
    audioPlayer.currentTime = 0;
    progressBar.value = 0;

});

function saveRecentlyPlayed(songId) {

    fetch("recently_played.php", {

        method: "POST",

        headers: {
            "Content-Type": "application/json"
        },

        body: JSON.stringify({
            song_id: songId
        })

    })
    .then(response => response.json())
    .then(data => {

        console.log("Recently played:", data);

    })
    .catch(error => {

        console.error("Recently played error:", error);

    });

}

function setupTrackLink() {

    const trackLink = document.querySelector(".track-link");

    if (trackLink) {

        trackLink.addEventListener("click", function(event) {

            event.preventDefault();

            const title = trackLink.dataset.title;
            const artist = trackLink.dataset.artist;
            const source = trackLink.dataset.src;
            const cover = trackLink.dataset.cover;

            audioPlayer.src = source;

            document.querySelector(".music-player .track-title").textContent = title;

            document.querySelector(".music-player .track-artist").textContent = artist;

            document.querySelector(".music-player .player-cover").src = cover;

            audioPlayer.play();

            updateNowPlaying(title, artist, cover);

            playButton.textContent = "⏸";

        });

    }

}

setupTrackLink();

function setupTrackButtons() {

    const trackButtons = document.querySelectorAll(".track-play");

    trackButtons.forEach(function(button) {

        button.addEventListener("click", function() {

            const title = button.dataset.title;
            const artist = button.dataset.artist;
            const source = button.dataset.src;
            const cover = button.dataset.cover;

            audioPlayer.src = source;

            document.querySelector(".music-player .track-title").textContent = title;

            document.querySelector(".music-player .track-artist").textContent = artist;

            document.querySelector(".music-player .player-cover").src = cover;

            audioPlayer.play();

            updateNowPlaying(title, artist, cover);

            playButton.textContent = "⏸";

        });

    });

}

setupTrackButtons();

function setupLikedButtons() {

    const likedButtons = document.querySelectorAll(".liked-play");

    likedButtons.forEach(function(button) {

        button.addEventListener("click", function() {

            const songId = button.dataset.songId;
            const title = button.dataset.title;
            const artist = button.dataset.artist;
            const source = button.dataset.src;
            const cover = button.dataset.cover;

            audioPlayer.src = source;

            document.querySelector(".music-player .track-title").textContent = title;

            document.querySelector(".music-player .track-artist").textContent = artist;

            document.querySelector(".music-player .player-cover").src = cover;

            audioPlayer.play();

            updateNowPlaying(title, artist, cover);

            playButton.textContent = "⏸";

            if (songId) {
                saveRecentlyPlayed(songId);
            }

        });

    });

}

setupLikedButtons();

function setupLikedHearts() {

    const likedHearts = document.querySelectorAll(".liked-heart");

    likedHearts.forEach(function(heart) {

        heart.addEventListener("click", async function() {

            const song = heart.closest(".liked-song");
            const songId = song.querySelector(".liked-play").dataset.songId;

            const response = await fetch("like.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: `song_id=${songId}`
            });

            const data = await response.json();

            if (!data.liked) {
                song.remove();

                const likesCount = document.querySelector(".profile-stat strong");

                if (likesCount) {
                    likesCount.textContent =
                        document.querySelectorAll(".liked-song").length;
                }
            }

        });

    });

}

setupLikedHearts();

function setupLikedSongsPlaylist() {

    const playAllButton = document.querySelector("#play-liked-songs");

    if (!playAllButton) {
        return;
    }

    const likedButtons = Array.from(
        document.querySelectorAll(".liked-play")
    );

    let currentIndex = 0;

    function playLikedSong(index) {

        if (index >= likedButtons.length) {
            return;
        }

        const button = likedButtons[index];

        audioPlayer.src = button.dataset.src;

        document.querySelector(".music-player .track-title").textContent =
            button.dataset.title;

        document.querySelector(".music-player .track-artist").textContent =
            button.dataset.artist;

        document.querySelector(".music-player .player-cover").src =
            button.dataset.cover;

        audioPlayer.play();

        updateNowPlaying(
            button.dataset.title,
            button.dataset.artist,
            button.dataset.cover
        );

        playButton.textContent = "⏸";

        currentIndex = index;
    }

    playAllButton.addEventListener("click", function() {

        if (likedButtons.length === 0) {
            return;
        }

        playLikedSong(0);

    });

    audioPlayer.addEventListener("ended", function() {

        if (currentIndex + 1 < likedButtons.length) {
            playLikedSong(currentIndex + 1);
        }

    });

}

setupLikedSongsPlaylist();

function savePlayerState() {

    localStorage.setItem("trackSrc", audioPlayer.src);

    localStorage.setItem(
        "trackTitle",
        document.querySelector(".music-player .track-title").textContent
    );

    localStorage.setItem(
        "trackArtist",
        document.querySelector(".music-player .track-artist").textContent
    );

    localStorage.setItem(
        "trackCover",
        document.querySelector(".music-player .player-cover").src
    );

    localStorage.setItem(
        "trackTime",
        audioPlayer.currentTime
    );

    localStorage.setItem(
        "isPlaying",
        !audioPlayer.paused
    );

}

function restorePlayerState() {

    const trackSrc = localStorage.getItem("trackSrc");
    const trackTitle = localStorage.getItem("trackTitle");
    const trackArtist = localStorage.getItem("trackArtist");
    const trackCover = localStorage.getItem("trackCover");
    const trackTime = localStorage.getItem("trackTime");
    const isPlaying = localStorage.getItem("isPlaying");

    if (!trackSrc) {
        return;
    }

    audioPlayer.addEventListener("loadedmetadata", function() {

        audioPlayer.currentTime = Number(trackTime);

        if (isPlaying === "true") {

            audioPlayer.play()
                .then(function() {

                    playButton.textContent = "⏸";

                })
                .catch(function(error) {

                    console.log("Autoplay blocked:", error);
                    
                });

        }

    }, { once: true });

    audioPlayer.src = trackSrc;

    document.querySelector(".music-player .track-title").textContent = trackTitle;

    document.querySelector(".music-player .track-artist").textContent = trackArtist;

    document.querySelector(".music-player .player-cover").src = trackCover;

}

restorePlayerState();

function setupHomeLinks() {

    const homeLinks = document.querySelectorAll(".home-link");

    homeLinks.forEach(function(homeLink) {

        homeLink.addEventListener("click", async function(event) {

            event.preventDefault();

            const response = await fetch("index.php");

            const html = await response.text();

            const parser = new DOMParser();

            const documentHTML = parser.parseFromString(html, "text/html");

            const newMainContent = documentHTML.querySelector(".main-content");

            document.querySelector(".main-content").replaceWith(newMainContent);

            setupAlbumLinks();

            setupTrackLink();

            loadRecentlyPlayed();

            history.pushState({}, "", "index.php");

        });

    });

}

setupHomeLinks();

function setupAlbumLinks() {

    const albumLinks = document.querySelectorAll(".album-link");

    albumLinks.forEach(function(albumLink) {

        albumLink.addEventListener("click", async function(event) {

            event.preventDefault();

            const response = await fetch("album.php");

            const html = await response.text();

            const parser = new DOMParser();

            const documentHTML = parser.parseFromString(html, "text/html");

            const newMainContent = documentHTML.querySelector(".main-content");

            document.querySelector(".main-content").replaceWith(newMainContent);

            setupTrackButtons();

            setupHomeLinks();

            setupAlbumLinks();

            history.pushState({}, "", "album.php");

        });

    });

}

setupAlbumLinks();

async function loadNowPlaying() {

    const response = await fetch("get_now_playing.php");

    const data = await response.json();

    const nowPlayingAvatar = document.querySelector(
        ".profile-now-avatar, .now-playing-avatar"
    );

    const nowPlayingUsername = document.querySelector(
        ".now-playing-username"
    );

    const nowPlayingName = document.querySelector(
        ".now-playing-name"
    );

    const nowPlayingTitle = document.getElementById(
        "now-playing-title"
    );

    const nowPlayingArtist = document.getElementById(
        "now-playing-artist"
    );

if (data.length === 0) {

    if (nowPlayingAvatar) {
        nowPlayingAvatar.innerHTML = "";
        nowPlayingAvatar.style.display = "none";
    }

    if (nowPlayingUsername) {
        nowPlayingUsername.textContent = "";
    }

    if (nowPlayingName) {
        nowPlayingName.textContent = "";
    }

    if (nowPlayingTitle) {
        nowPlayingTitle.textContent = "Nothing playing";
    }

    if (nowPlayingArtist) {
        nowPlayingArtist.textContent = "";
    }

    const nowPlayingCover = document.getElementById("now-playing-cover");

    if (nowPlayingCover) {
        nowPlayingCover.src = "";
        nowPlayingCover.style.display = "none";
    }

    return;
}

    const nowPlaying = data[0];

    if (nowPlayingUsername) {
        nowPlayingUsername.textContent =
            "@" + nowPlaying.username;
    }

    if (nowPlayingName) {
        nowPlayingName.textContent =
            nowPlaying.full_name;
    }

    if (nowPlayingTitle) {
        nowPlayingTitle.textContent =
            nowPlaying.track_title;
    }

    if (nowPlayingArtist) {
        nowPlayingArtist.textContent =
            nowPlaying.artist;
    }

    if (nowPlayingAvatar) {

        nowPlayingAvatar.style.display = "flex";

        if (nowPlaying.profile_picture) {

            nowPlayingAvatar.innerHTML = `
                <img
                    src="${nowPlaying.profile_picture}"
                    alt="Profile picture"
                >
            `;

        } else {

            nowPlayingAvatar.textContent =
                nowPlaying.username
                    .charAt(0)
                    .toUpperCase();

        }
    }
}

loadNowPlaying();

const userMenuButton = document.getElementById("user-menu-button");

const userDropdown = document.getElementById("user-dropdown");

userMenuButton.addEventListener("click", function() {

    userDropdown.classList.toggle("show");

});

document.addEventListener("click", function(event) {

    if (!userMenuButton.contains(event.target) && !userDropdown.contains(event.target)) {

        userDropdown.classList.remove("show");

    }

});

const darkModeButton = document.getElementById("dark-mode-button");

if (darkModeButton) {

    darkModeButton.addEventListener("click", function() {

        document.body.classList.toggle("dark-mode");

        if (document.body.classList.contains("dark-mode")) {
            localStorage.setItem("theme", "dark");
        } else {
            localStorage.setItem("theme", "light");
        }

    });

}

if (localStorage.getItem("theme") === "dark") {
    document.body.classList.add("dark-mode");
}

const welcomePopup = document.getElementById("welcome-popup");

if (welcomePopup) {

    setTimeout(function () {

        welcomePopup.style.opacity = "0";

        setTimeout(function () {
            welcomePopup.style.display = "none";
        }, 600);

    }, 4000);
}

const searchInput = document.getElementById("search-input");
const searchResult = document.getElementById("search-result");

searchInput.addEventListener("input", function() {

    const query = searchInput.value.trim().toLowerCase();

    searchResult.innerHTML = "";

    if (query === "") {
        return;
    }

    // =========================
    // USERS
    // =========================

    fetch("search.php?q=" + encodeURIComponent(query))
        .then(response => response.json())
        .then(users => {

            if (users.length > 0) {

                const peopleTitle = document.createElement("div");

                peopleTitle.classList.add("search-section-title");
                peopleTitle.textContent = "People";

                searchResult.appendChild(peopleTitle);

                users.forEach(function(user) {

                    const userElement = document.createElement("div");

                    userElement.classList.add("search-user");

                    const image = user.profile_picture
                        ? user.profile_picture
                        : "";

                    userElement.innerHTML = `
                        ${
                            image
                            ? `<img src="${image}" alt="Profile picture">`
                            : `<div class="search-user-placeholder">
                                ${user.full_name.charAt(0).toUpperCase()}
                            </div>`
                        }

                        <div class="search-user-info">
                            <strong>${user.full_name}</strong>
                            <span>@${user.username}</span>
                        </div>
                    `;

                    userElement.addEventListener("click", function() {

                        window.location.href =
                            "profile.php?username=" +
                            encodeURIComponent(user.username);

                    });

                    searchResult.appendChild(userElement);

                });

            }

// =========================
// SONGS
// =========================

fetch("songs.php")
    .then(response => response.json())
    .then(songs => {

        const matchingSongs = songs.filter(function(song) {

            const title = song.title.toLowerCase();
            const artist = song.artist.toLowerCase();

            return (
                title.includes(query) ||
                artist.includes(query)
            );

        });

        if (matchingSongs.length === 0) {
            return;
        }

        const songsTitle = document.createElement("div");

        songsTitle.classList.add("search-section-title");
        songsTitle.textContent = "Songs";

        searchResult.appendChild(songsTitle);

        matchingSongs.slice(0, 5).forEach(function(song) {

            const songElement = document.createElement("div");

            songElement.classList.add("search-song");

            songElement.dataset.songId = song.id;

            songElement.innerHTML = `
                <img
                    src="${song.cover_path}"
                    alt="${song.title}"
                >

                <div class="search-song-info">
                    <strong>${song.title}</strong>
                    <span>${song.artist}</span>
                </div>
            `;

            songElement.addEventListener("click", function() {

                audioPlayer.src = song.audio_path;

                document.querySelector(".music-player .track-title").textContent =
                    song.title;

                document.querySelector(".music-player .track-artist").textContent =
                    song.artist;

                document.querySelector(".music-player .player-cover").src =
                    song.cover_path;

                    saveRecentlyPlayed(song.id);

                audioPlayer.play();

                updateNowPlaying(
                    song.title,
                    song.artist,
                    song.cover_path
                );

                playButton.textContent = "⏸";

            });

            searchResult.appendChild(songElement);

        });

    })
    .catch(error => {

        console.error("Song search error:", error);

});

        })
        .catch(error => {
            console.error("Search error:", error);
        });

});

// Close search when clicking outside

const searchBar = document.querySelector(".search-bar");

document.addEventListener("click", function(event) {

    if (
        !searchBar.contains(event.target) &&
        !searchResult.contains(event.target)
    ) {

        searchInput.value = "";

        searchResult.innerHTML = "";

        searchInput.blur();

    }

});

// =========================
// LOAD RECENTLY PLAYED
// =========================

function loadRecentlyPlayed() {

    const recentlyPlayedList =
        document.getElementById("recently-played-list");

    if (!recentlyPlayedList) {
        return;
    }

    recentlyPlayedList.innerHTML = "";

    fetch("recently_played_list.php")
        .then(response => response.json())
        .then(songs => {

            songs.slice(0, 8).forEach(function(song) {

                const trackCard =
                    document.createElement("div");

                trackCard.classList.add("track-card");

                trackCard.dataset.id = song.id;
                trackCard.dataset.title = song.title;
                trackCard.dataset.artist = song.artist;
                trackCard.dataset.src = song.audio_path;
                trackCard.dataset.cover = song.cover_path;

                trackCard.innerHTML = `
                    <img
                        class="track-cover"
                        src="${song.cover_path}"
                        alt="${song.title}"
                    >

                    <div class="card-details">

                        <span class="card-title">
                            ${song.title}
                        </span>

                        <span class="card-artist">
                            ${song.artist}
                        </span>

                    </div>

                    <button class="like-button" data-song-id="${song.id}">
                        ${song.liked ? '♥' : '♡'}
                    </button>
                `;

                const likeButton = trackCard.querySelector('.like-button');

                likeButton.addEventListener('click', async (event) => {
                    event.stopPropagation();

                    const songId = likeButton.dataset.songId;

                    const response = await fetch('like.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `song_id=${songId}`
                    });

                    const data = await response.json();

                    likeButton.textContent = data.liked ? '♥' : '♡';
                });

                trackCard.addEventListener("click", function() {

                    const title =
                        trackCard.dataset.title;

                    const artist =
                        trackCard.dataset.artist;

                    const source =
                        trackCard.dataset.src;

                    const cover =
                        trackCard.dataset.cover;

                    const songId =
                        trackCard.dataset.id;

                    audioPlayer.src = source;

                    document.querySelector(
                        ".music-player .track-title"
                    ).textContent = title;

                    document.querySelector(
                        ".music-player .track-artist"
                    ).textContent = artist;

                    document.querySelector(
                        ".music-player .player-cover"
                    ).src = cover;

                    saveRecentlyPlayed(songId);

                    audioPlayer.play();

                    updateNowPlaying(
                        title,
                        artist,
                        cover
                    );

                    playButton.textContent = "⏸";

                });

                recentlyPlayedList.appendChild(trackCard);

            });

        })
        .catch(error => {

            console.error(
                "Error loading recently played:",
                error
            );

        });

}

loadRecentlyPlayed();