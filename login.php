<?php
session_start();

//require("config.php");

if (isset($_SESSION["user_id"])) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    if ($authenticated) {
        $_SESSION["user_id"] = $user_id;
        header("Location: dashboard.php");
            exit;
    } else {
        $login_error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <form method="POST">
        <lable for="username">Username:</lable>
        <input type="text" id="username" name="username" required><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>

        <input type="submit" value="Login">
    </form>

    <p>Sign up <a href="register.php">here</a>!</p>

    <?php
    if (!empty($error_message)){
        echo "<p>$error_message</p>";
    }
    ?>

</body>
</html>