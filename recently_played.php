<?php

session_start();

header("Content-Type: application/json");

require_once "db.php";

if (!isset($_SESSION['user_id'])) {

    echo json_encode([
        "success" => false,
        "message" => "User not logged in"
    ]);

    exit;
}

$data = json_decode(
    file_get_contents("php://input"),
    true
);

if (!isset($data['song_id'])) {

    echo json_encode([
        "success" => false,
        "message" => "Song ID missing"
    ]);

    exit;
}

$user_id = $_SESSION['user_id'];
$song_id = (int) $data['song_id'];

$sql = "
    INSERT INTO recently_played
        (user_id, song_id, played_at)

    VALUES
        (?, ?, NOW())

    ON DUPLICATE KEY UPDATE
        played_at = NOW()
";

$stmt = $connection->prepare($sql);

$stmt->bind_param(
    "ii",
    $user_id,
    $song_id
);

$stmt->execute();

$stmt->close();

echo json_encode([
    "success" => true
]);