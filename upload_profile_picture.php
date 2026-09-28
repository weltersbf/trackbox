<?php

session_start();

require "db.php";

if (!isset($_SESSION['user_id'])) {
    exit("User not logged in");
}

if (!isset($_FILES['profile_picture'])) {
    exit("No image received.");
}

$file = $_FILES['profile_picture'];

if (getimagesize($file['tmp_name']) === false) {
    exit("Invalid image.");
}

$user_id = $_SESSION['user_id'];

$file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

$file_name = "profile_" . $user_id . "." . $file_extension;

$upload_path = "uploads/" . $file_name;

if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
    exit("Upload failed.");
}

$zoom = $_POST['profile_picture_zoom'] ?? 1.00;

$sql = "UPDATE users
        SET profile_picture = ?, profile_picture_zoom = ?
        WHERE id = ?";

$stmt = $connection->prepare($sql);

$stmt->bind_param("sdi", $upload_path, $zoom, $user_id);

$stmt->execute();

?>