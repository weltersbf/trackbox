<?php

session_start();

require_once "db.php";

$sql = "SELECT
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
    ) AS liked
        FROM songs
        INNER JOIN artists
            ON songs.artist_id = artists.id
        LEFT JOIN albums
            ON songs.album_id = albums.id
        ORDER BY songs.id ASC";

$stmt = $connection->prepare($sql);

$stmt->bind_param("i", $_SESSION['user_id']);

$stmt->execute();

$result = $stmt->get_result();

$songs = [];

while ($song = $result->fetch_assoc()) {
    $songs[] = $song;
}

header("Content-Type: application/json");

echo json_encode($songs);