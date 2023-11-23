<?php
require_once "config.php";
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION["user_id"])) {
    $user_id = $_SESSION["user_id"];
    $text_id = $_POST["text_id"];
    $comment_content = $_POST["comment_content"];

    $sql_insert_comment = "INSERT INTO comments (user_id, text_id, content, created_at)
                           VALUES (?, ?, ?, NOW())";

    if ($stmt_insert_comment = mysqli_prepare($link, $sql_insert_comment)) {
        mysqli_stmt_bind_param($stmt_insert_comment, "iss", $user_id, $text_id, $comment_content);

        if (mysqli_stmt_execute($stmt_insert_comment)) {
            header("Location: posts.php?user_id=$user_id&text_id=$text_id");
            exit;
        } else {
            echo "Error adding comment: " . mysqli_error($link);
        }

        mysqli_stmt_close($stmt_insert_comment);
    } else {
        echo "Error preparing comment query: " . mysqli_error($link);
    }
} else {
    echo "Invalid request";
}

mysqli_close($link);
?>
