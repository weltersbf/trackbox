<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    exit;
}

$user_id = $_SESSION['user_id'];
$song_id = $_POST['song_id'] ?? null;

$stmt = $connection->prepare("
    SELECT id
    FROM likes
    WHERE user_id = ?
      AND song_id = ?
");

$stmt->bind_param("ii", $user_id, $song_id);

$stmt->execute();

$result = $stmt->get_result();

$like = $result->fetch_assoc();

if ($like) {
    $stmt = $connection->prepare("
        DELETE FROM likes
        WHERE id = ?
    ");

    $stmt->bind_param("i", $like['id']);

    $stmt->execute();
} else {
    $stmt = $connection->prepare("
        INSERT INTO likes (user_id, song_id)
        VALUES (?, ?)
    ");

    $stmt->bind_param("ii", $user_id, $song_id);

    $stmt->execute();
}

header('Content-Type: application/json');

echo json_encode([
    'liked' => !$like
]);

?>