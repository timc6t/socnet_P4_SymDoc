<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    if ($authenticated) {
        $_SESSION["user_id"] = $user_id;
        header("Location: dashboard.php");
    } else {
        $login_error = "Invalid username or password";
    }
}
?>