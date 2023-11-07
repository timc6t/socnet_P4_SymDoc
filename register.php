<?php
require_once "config.php";
session_start();

$username = $password = "";
$username_err = $password_err = "";
$email = "";
$email_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } else {
        $sql = "SELECT user_id FROM users WHERE username = ?";

        if($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt,"s", $param_username);

            $param_username = trim($_POST["username"]);

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $username_err = "This username already exists.";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                echo "Something went wrong. Please try again.";
            }
            mysqli_stmt_close($stmt);
        }
    }

    if (isset($_POST["email"])) {
        $email_input = trim($_POST["email"]);
        if (empty($email_input)) {
            $email_err = "Please enter an email.";
        } else {
            $email = $email_input;
        }
    }

    if (isset($_POST["password"])) {
        $password_input = trim($_POST["password"]);
        if (empty($password_input)) {
            $password_err = "Please enter a password.";
        } elseif(strlen($password_input) < 6) {
            $password_err = "Password must have at least 6 characters.";
        } else {
            $password = $password_input;
        }
    }

    if (empty($username_err) && empty($password_err) && empty($email_err)) {
        $sql = "INSERT INTO users (username, password, email) VALUES (?, ?, ?)";

        if($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt,"sss", $param_username, $param_password, $param_email);
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            $param_email = $email;

            if (mysqli_stmt_execute($stmt)) {
                header("Location: register_success.php");
                exit;
            } else {
                echo "Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
</head>
<body>
<form method="POST">
        <lable for="username">Username:</lable>
        <input type="text" id="username" name="username" required><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br>

        <input type="submit" value="Register">
    </form>

    <?php
    if (!empty($error_message)){
        echo "<p>$error_message</p>";
    }
    ?>
</body>
</html>