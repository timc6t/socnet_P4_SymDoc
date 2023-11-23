<?php
    $password = $_POST["password"];
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
?>
