<?php
// Include config.php and start session
require_once "config.php";
session_start();

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// Get the user ID from the session
$user_id = $_SESSION["user_id"];

// Retrieve and display user's uploaded texts
$sql = "SELECT text_id, title, content, created_at FROM texts WHERE user_id = ? ORDER BY created_at DESC";
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

            // Output the text information
            echo "<div class='post-section'>";
            echo "<p><strong>Title:</strong> $title</p>";
            echo "<p><strong>Content:</strong> $content</p>";
            echo "<p><strong>Created At:</strong> $created_at</p>";
            echo "</div>";
            echo "<hr>"; // Add a horizontal line for separation
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
?>
