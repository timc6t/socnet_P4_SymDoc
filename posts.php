<?php
require_once "config.php";

if (isset($_GET['user_id']) && isset($_GET['text_id'])) {
    $post_user_id = $_GET['user_id'];
    $text_id = $_GET['text_id'];

    // Fetch information about the post
    $sql = "SELECT u.username AS original_poster, t.title, t.content, t.created_at
            FROM texts t
            INNER JOIN users u ON t.user_id = u.user_id
            WHERE t.user_id = ? AND t.text_id = ?";
    
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ii", $post_user_id, $text_id);

        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);

            if ($row = mysqli_fetch_assoc($result)) {
                $post_user = $row["original_poster"];
                $post_title = $row["title"];
                $post_content = $row["content"];
                $post_creation = $row["created_at"];

                // Display information about the post
                echo "<h2>Post by $post_user</h2>";
                echo "<p><strong>Title:</strong> $post_title</p>";
                echo "<p><strong>Content:</strong> $post_content</p>";
                echo "<p><strong>Created at:</strong> $post_creation</p>";

                // Add the ability to comment/reply here

            } else {
                echo "Post not found";
            }

            mysqli_free_result($result);
        } else {
            echo "Error executing post query: " . mysqli_error($link);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing post query: " . mysqli_error($link);
    }

    mysqli_close($link);

} else {
    echo "Invalid parameters";
}
?>