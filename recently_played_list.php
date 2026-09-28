<?php

session_start();

header("Content-Type: application/json");

require_once "db.php";

if (!isset($_SESSION['user_id'])) {

    echo json_encode([]);

    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "
    SELECT
        songs.id,
        songs.title,
        artists.name AS artist,
        albums.title AS album,
        songs.audio_path,
        songs.cover_path,
        EXISTS (
            SELECT 1
            FROM likes
            WHERE likes.song_id = songs.id
            AND likes.user_id = ?
        ) AS liked,
        recently_played.played_at

    FROM recently_played

    INNER JOIN songs
        ON songs.id = recently_played.song_id

    INNER JOIN artists
        ON songs.artist_id = artists.id

    LEFT JOIN albums
        ON songs.album_id = albums.id

    WHERE recently_played.user_id = ?

    ORDER BY recently_played.played_at DESC

    LIMIT 20
";

$stmt = $connection->prepare($sql);

$stmt->bind_param(
    "ii",
    $user_id,
    $user_id
);

$stmt->execute();

$result = $stmt->get_result();

$songs = [];

while ($song = $result->fetch_assoc()) {

    $songs[] = $song;

}

$stmt->close();

echo json_encode($songs);