<?php
require_once "config.php";

$activation_link = $_GET['code'];

if (empty($activation_link)) {
    echo "Invalid activation code.";
    exit;
}

$sql = "SELECT user_id FROM users WHERE is_activated = ?";

if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $activation_link);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) == 1) {
            $update_sql = "UPDATE users SET activated = 1 WHERE is_activated = ?";
            if ($update_stmt = mysqli_prepare($link, $update_sql)) {
                mysqli_stmt_bind_param($update_stmt, "s", $activation_link);
                mysqli_stmt_execute($update_stmt);
                echo "Your account has been successfully activated. You can now log in.";
            } else {
                echo "Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($update_stmt);
        } else {
            echo "Invalid activation code.";
        }
    } else {
        echo "Something went wrong. Please try again later.";
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($link);
?>
