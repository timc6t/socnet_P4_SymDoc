<?php
// Include config.php and start session
require_once "config.php";
session_start();

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// Check if the form is submitted with a POST request
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve user ID and comment content from the session and form
    $user_id = $_SESSION["user_id"];
    $text_id = $_POST["text_id"];
    $comment_content = $_POST["comment_content"];

    // Insert the comment into the database
    $sql = "INSERT INTO comments (user_id, text_id, content) VALUES (?, ?, ?)";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "sss", $user_id, $text_id, $comment_content);

        if (mysqli_stmt_execute($stmt)) {
            // Redirect back to the display_texts.php page after adding the comment
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