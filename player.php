<footer class="music-player">

    <div class="current-track">

        <img
            class="player-cover"
            src="images/febem-elevador.jpg"
            alt="Castelo Vazio">

        <div class="track-info">

            <span class="track-title">Castelo Vazio</span>
            <span class="track-artist">Febem</span>

        </div>

    </div>

    <div class="player-center">

        <div class="player-controls">

            <button id="previous-button">⏮</button>

            <button id="play-button" class="play-button">▶</button>

            <button id="next-button">⏭</button>

        </div>

        <div class="progress-container">

            <span id="current-time">0:00</span>

            <input
                id="progress-bar"
                type="range"
                min="0"
                max="100"
                value="0">

            <span id="duration">0:00</span>

        </div>

    </div>

    <div class="player-volume">

        <span id="volume-icon">🔊</span>

        <input
            id="volume-bar"
            type="range"
            min="0"
            max="100"
            value="50">

    </div>

    <audio
        id="audio-player"
        src="music/castelo-vazio.mp3">
    </audio>

</footer>