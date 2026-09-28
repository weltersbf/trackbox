<?php

session_start();

require "db.php";

if (!isset($_SESSION['user_id'])) {
    exit();
}

$user_id = $_SESSION['user_id'];
$track_title = $_POST['track_title'];
$artist = $_POST['artist'];
$is_playing = $_POST['is_playing'];

$sql = "INSERT INTO now_playing (user_id, track_title, artist, is_playing)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
        track_title = VALUES(track_title),
        artist = VALUES(artist),
        is_playing = VALUES(is_playing),
        started_at = CURRENT_TIMESTAMP";

$stmt = $connection->prepare($sql);

$stmt->bind_param(
    "issi",
    $user_id,
    $track_title,
    $artist,
    $is_playing
);

$stmt->execute();