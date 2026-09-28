<?php

session_start();

require "db.php";

if (!isset($_SESSION['user_id'])) {
    exit("User not logged in.");
}

$follower_id = $_SESSION['user_id'];
$following_id = $_POST['following_id'];
$username = $_POST['username'];

if ($follower_id == $following_id) {
    exit("You cannot follow yourself.");
}

$sql = "SELECT id FROM users WHERE id = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $following_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0) {
    exit("User not found.");
}

$sql = "SELECT id FROM follows
        WHERE follower_id = ? AND following_id = ?";

$stmt = $connection->prepare($sql);
$stmt->bind_param("ii", $follower_id, $following_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {

    $stmt->close();

    $sql = "DELETE FROM follows
            WHERE follower_id = ? AND following_id = ?";

    $stmt = $connection->prepare($sql);
    $stmt->bind_param("ii", $follower_id, $following_id);
    $stmt->execute();

    header("Location: profile.php?username=" . urlencode($username));
    exit;
}

$sql = "INSERT INTO follows (follower_id, following_id)
        VALUES (?, ?)";

$stmt = $connection->prepare($sql);
$stmt->bind_param("ii", $follower_id, $following_id);
$stmt->execute();

header("Location: profile.php?username=" . urlencode($username));
exit;

?>