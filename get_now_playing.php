<?php

require "db.php";

$sql = "SELECT
            users.username,
            users.full_name,
            users.profile_picture,
            now_playing.track_title,
            now_playing.artist,
            now_playing.started_at
        FROM now_playing
        INNER JOIN users
            ON now_playing.user_id = users.id
        WHERE now_playing.is_playing = 1
        ORDER BY now_playing.started_at DESC
        LIMIT 1";

$result = $connection->query($sql);

$now_playing = [];

while ($row = $result->fetch_assoc()) {
    $now_playing[] = $row;
}

header("Content-Type: application/json");

echo json_encode($now_playing);