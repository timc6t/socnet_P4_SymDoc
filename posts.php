<?php
require_once "config.php";
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

if (isset($_GET['user_id']) && isset($_GET['text_id'])) {
    $post_user_id = $_GET['user_id'];
    $text_id = $_GET['text_id'];

    $sql_post = "SELECT u.username AS original_poster, t.title, t.content, t.created_at
                 FROM texts t
                 INNER JOIN users u ON t.user_id = u.user_id
                 WHERE t.user_id = ? AND t.text_id = ?";

    if ($stmt_post = mysqli_prepare($link, $sql_post)) {
        mysqli_stmt_bind_param($stmt_post, "ii", $post_user_id, $text_id);

        if (mysqli_stmt_execute($stmt_post)) {
            $result_post = mysqli_stmt_get_result($stmt_post);

            if ($row = mysqli_fetch_assoc($result_post)) {
                $post_user = $row["original_poster"];
                $post_title = $row["title"];
                $post_content = $row["content"];
                $post_creation = $row["created_at"];

                mysqli_free_result($result_post);
            } else {
                echo "Post not found";
                exit;
            }

            mysqli_stmt_close($stmt_post);
        } else {
            echo "Error executing post query: " . mysqli_error($link);
            exit;
        }
    } else {
        echo "Error preparing post query: " . mysqli_error($link);
        exit;
    }

    $sql_comments = "SELECT c.comment_id, c.user_id, c.content, c.created_at, u.username
                     FROM comments c
                     INNER JOIN users u ON c.user_id = u.user_id
                     WHERE c.text_id = ? AND (c.user_id = ? OR c.user_id IN (
                        SELECT following_id
                        FROM follows
                        WHERE follower_id = ?)
                     )
                     ORDER BY c.created_at DESC";

    function displayComments($text_id, $link) {
        $html = "";

        global $sql_comments;

        if ($stmt_comments = mysqli_prepare($link, $sql_comments)) {
            mysqli_stmt_bind_param($stmt_comments, "sss", $text_id, $post_user_id, $post_user_id);

            if (mysqli_stmt_execute($stmt_comments)) {
                $result_comments = mysqli_stmt_get_result($stmt_comments);

                while ($comment = mysqli_fetch_assoc($result_comments)) {
                    $comment_user = $comment["username"];
                    $comment_content = $comment["content"];
                    $comment_created_at = $comment["created_at"];

                    $html .= "<div class='comment-box' style='border: 1px solid #b0c4de; padding: 10px; margin-bottom: 10px;'>";
                    $html .= "<p><strong>$comment_user:</strong> $comment_content</p>";
                    $html .= "<p><em>($comment_created_at)</em></p>";
                    $html .= "</div>";
                }

                mysqli_free_result($result_comments);
            } else {
                echo "Error executing comment query: " . mysqli_error($link);
            }

            mysqli_stmt_close($stmt_comments);
        } else {
            echo "Error preparing comment query: " . mysqli_error($link);
        }

        return $html;
    }

    echo "<h1>Posts</h1>";
    echo "<div id='navbar'>";
    echo "<p><a href='dashboard.php'>Dashboard</a></p>";
    echo "<p><a href='profile.php'>My profile</a></p>";
    echo "<p><a href='logout.php'>Logout</a></p>";
    echo "</div>";
    echo "<div class='post-section'>";
    echo "<div class='post-box' style='border: 2px solid #7fffd4; padding: 10px; margin-bottom: 20px;'>";
    echo "<div style='display: flex; align-items: center;'>";
    echo "<h2 style='margin-right: 10px;'>$post_title</h2> <p>by $post_user</p>";
    echo "</div>";
    echo "<p><strong>Content:</strong> $post_content</p>";
    echo "<p><strong>Created At:</strong> $post_creation</p>";

    if ($user_id == $post_user_id) {
        echo "<form method='POST' action='delete_post.php'>"; // Add delete_post.php whenever possible
        echo "<input type='hidden' name='text_id' value='$text_id'>";
        echo "<input type='submit' value='Delete'>";
        echo "</form>";
    }

    echo "</div>";

    echo "<div class='comment-section' style='border: 2px solid #dda0dd; padding: 10px; margin-bottom: 20px;'>";
    echo "<form method='POST' action='add_comment.php'>";
    echo "<input type='hidden' name='text_id' value='$text_id'>";
    echo "<textarea name='comment_content' placeholder='Post your comment...' required></textarea>";
    echo "<br>";
    echo "<input type='submit' value='Add Comment'>";
    echo "</form>";
    echo "</div>";
    echo "</div>";

    echo displayComments($text_id, $link);

    mysqli_close($link);

} else {
    echo "Invalid parameters";
    exit;
}
?>
