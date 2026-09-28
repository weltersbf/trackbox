<?php

require "db.php";

$name = $_POST['name'];
$username = $_POST['username'];
$birthday = $_POST['birthday'];
$country = $_POST['country'];
$email = $_POST['email'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

if (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username)) {
    echo "<script>alert('Username must contain only letters, numbers and underscore, and be between 3 and 30 characters.'); window.history.back();</script>";
    exit();
}

$check_sql = "SELECT id FROM users WHERE username = ?";

$check_stmt = $connection->prepare($check_sql);

$check_stmt->bind_param("s", $username);

$check_stmt->execute();

$check_stmt->store_result();

if ($check_stmt->num_rows > 0) {
    echo "<script>alert('Username already taken. Please choose another one.'); window.history.back();</script>";
    exit();
}

if ($password != $confirm_password) {

    echo "The passwords do not match.";
    exit();
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO users (full_name, username, birthday, country, email, password_hash)
        VALUES (?,?,?,?,?,?)";

$stmt = $connection->prepare($sql);

$stmt->bind_param(
    "ssssss",
    $name,
    $username,
    $birthday,
    $country,
    $email,
    $password_hash
);

$stmt->execute();

session_start();

$_SESSION['user_id'] = $connection->insert_id;
$_SESSION['name'] = $name;
$_SESSION['username'] = $username;
$_SESSION['email'] = $email;

echo "Registration successfully completed!<br>";
echo "Welcome, $name!";

echo '<meta http-equiv="refresh" content="2;url=index.php">';

?>