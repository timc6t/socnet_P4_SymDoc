<?php
require_once "config.php";
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION["user_id"];
    $text_id = $_POST["text_id"];
    $comment_content = $_POST["comment_content"];
    $sql = "INSERT INTO comments (user_id, text_id, content) VALUES (?, ?, ?)";

    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "sss", $user_id, $text_id, $comment_content);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: display_texts.php");
            exit;
        } else {
            echo "Error executing comment query: " . mysqli_error($link);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing comment query: " . mysqli_error($link);
    }
}

mysqli_close($link);
?>