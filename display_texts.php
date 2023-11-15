<?php
require_once "config.php";
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

$sql = "SELECT text_id, title, content, created_at
        FROM texts
        WHERE user_id = ?
        ORDER BY created_at DESC";

if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $user_id);

    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);

        // Display each text
        while ($row = mysqli_fetch_assoc($result)) {
            $text_id = $row["text_id"];
            $title = $row["title"];
            $content = $row["content"];
            $created_at = $row["created_at"];
        }

        mysqli_free_result($result);
    } else {
        echo "Error executing query: " . mysqli_error($link);
    }

    mysqli_stmt_close($stmt);
} else {
    echo "Error preparing query: " . mysqli_error($link);
}

mysqli_close($link);

// Function to display comments for a given text_id
function displayComments($text_id) {
    global $link;

    $sql = "SELECT c.comment_id, c.user_id, c.content, c.created_at, u.username
            FROM comments c
            INNER JOIN users u ON c.user_id = u.user_id
            WHERE c.text_id = ?
            ORDER BY c.created_at DESC";

    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $text_id);

        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);

            // Display each comment
            while ($comment = mysqli_fetch_assoc($result)) {
                $comment_id = $comment["comment_id"];
                $comment_user = $comment["username"];
                $comment_content = $comment["content"];
                $comment_created_at = $comment["created_at"];
            }

            mysqli_free_result($result);
        } else {
            echo "Error executing comment query: " . mysqli_error($link);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing comment query: " . mysqli_error($link);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Post</title>
    <style>
        .post-section {
            border: 2px solid black;
            padding: 10px;
            margin-bottom: 20px;
        }
        .comment {
            border: 1px solid gray;
            padding: 5px;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div id="navbar">
        <p><a href="user_posts.php">My texts</a></p> <!-- TO DO: user_posts.php -->
        <p><a href="profile.php">My profile</a></p>
        <p><a href="logout.php">Logout</a></p>
    </div>
    <h1>Post</h1>
    <div class='post-section'>
        <h2>$title</h2>
        <p><strong>Created At:</strong> <?php $created_at; ?></p>
        <p><strong>Content:</strong> <?php $content; ?></p>

        <h3>Comments:</h3>
        <? displayComments($text_id); ?>

        <form method='POST' action='add_comment.php'>
            <input type='hidden' name='text_id' value='$text_id'>
            <textarea name='comment_content' placeholder='Add a comment...' required></textarea>
            <br>
            <input type='submit' value='Add Comment'>
        </form>
    </div>
    <hr>
    <div class='comment'>
         <p><strong><?php $comment_user; ?>:</strong> <?php $comment_content; ?></p>
        <p><em>(<?php $comment_created_at; ?>)</em></p>
    </div>
</body>
</html>
