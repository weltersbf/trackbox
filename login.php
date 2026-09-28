<?php

session_start();

require "db.php";

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT id, full_name, username, birthday, country, email, password_hash
        FROM users
        WHERE email = ?";

$stmt = $connection->prepare($sql);

$stmt->bind_param("s", $email);

$stmt->execute();

$stmt->store_result();

if ($stmt->num_rows == 1) {

    $stmt->bind_result(
        $id,
        $name,
        $username,
        $birthday,
        $country,
        $email_db,
        $password_hash
    );

    $stmt->fetch();

    if (password_verify($password, $password_hash)) {

        $_SESSION['user_id'] = $id;
        $_SESSION['name'] = $name;
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email_db;
        $_SESSION['just_logged_in'] = true;

        echo "Login successful.";

    } else {

        echo "Incorrect password.";

    }

} else {

    echo "User not found.";

}

?>