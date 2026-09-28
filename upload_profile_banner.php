<?php

session_start();

require "db.php";

if (!isset($_SESSION['user_id'])) {
    exit("User not logged in.");
}

if (!isset($_FILES['profile_banner'])) {
    exit("No image received.");
}

$file = $_FILES['profile_banner'];

if (getimagesize($file['tmp_name']) === false) {
    exit("Invalid image.");
}

$user_id = $_SESSION['user_id'];

$position = $_POST['profile_banner_position'] ?? 50;

$file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

$file_name = "banner_" . $user_id . "." . $file_extension;

$upload_path = "uploads/" . $file_name;

if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
    exit("Upload failed.");
}

$sql = "UPDATE users
        SET profile_banner = ?, profile_banner_position = ?
        WHERE id = ?";

$stmt = $connection->prepare($sql);

$stmt->bind_param("sii", $upload_path, $position, $user_id);

$stmt->execute();