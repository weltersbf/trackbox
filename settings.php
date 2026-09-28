<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Settings - TrackBox</title>

    <link rel="stylesheet" href="css/home.css">

</head>

<body>

    <a href="index.php" class="back-home">Home</a>

    <main class="settings-page">

<section class="settings-card">

    <h1>Settings</h1>

    <div class="settings-section">

        <h2>Appearance</h2>

        <div class="setting-row">

            <span>Dark Mode</span>

            <button id="dark-mode-button">
                Off
            </button>

        </div>

    </div>

</section>

    </main>

</body>

</html>