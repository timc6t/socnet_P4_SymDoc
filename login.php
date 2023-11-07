<?php
session_start();

if (isset($_SESSION["user_id"])) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $sql = "SELECT user_id, password FROM users WHERE username = ? LIMIT 1";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt,"s", $username);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) == 1) {
                mysqli_stmt_bind_result($stmt, $user_id, $hashed_password);
                
                if(mysqli_stmt_fetch($stmt)) {
                    if (password_verify($password, $hashed_password)) {
                        $_SESSION["user_id"] = $user_id;
                        header("Location: dashboard.php");
                            exit;
                    } else {
                        $login_error = "Invalid username or password.";
                    }
                }
            } else {
                $login_error = "Invalid username or password.";
            }
        } else {
            echo "Something went wrong. Please try again.";
        }
        mysqli_stmt_close($stmt);
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
    <p><a href="pwd_recovery.php">Forgot your password?</a></p>
    <!--Password recovery needs to be done-->

    <?php
    if (!empty($error_message)){
        echo "<p>$error_message</p>";
    }
    ?>

</body>
</html>