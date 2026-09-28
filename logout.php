<?php

session_start();

require "db.php";

if (isset($_SESSION['user_id'])) {

    $user_id = $_SESSION['user_id'];

    $sql = "UPDATE now_playing
            SET is_playing = 0
            WHERE user_id = ?";

    $stmt = $connection->prepare($sql);

    $stmt->bind_param("i", $user_id);

    $stmt->execute();
}

session_destroy();

header("Location: login.html");
exit;

?>