<?php

session_start();

require_once "db.php";

if (!isset($_SESSION['user_id'])) {
    exit;
}

$query = $_GET['q'] ?? '';

$query = trim($query);

if ($query === '') {
    echo json_encode([]);
    exit;
}

$search = "%" . $query . "%";

$sql = "SELECT id, full_name, username, profile_picture
        FROM users
        WHERE username LIKE ?
        OR full_name LIKE ?
        LIMIT 8";

$stmt = $connection->prepare($sql);

$stmt->bind_param("ss", $search, $search);

$stmt->execute();

$result = $stmt->get_result();

$users = [];

while ($user = $result->fetch_assoc()) {

    $users[] = $user;

}

header("Content-Type: application/json");

echo json_encode($users);