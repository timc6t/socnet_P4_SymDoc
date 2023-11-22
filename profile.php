<?php
require_once "config.php";
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle user settings update, if implemented
}

// Retrieve user's profile information and public texts

$sql_profile = "SELECT username, profile_image FROM users WHERE user_id = ?";
if ($stmt_profile = mysqli_prepare($link, $sql_profile)) {
    mysqli_stmt_bind_param($stmt_profile, "s", $_GET['user_id']);  // Assuming user_id is in the URL
    if (mysqli_stmt_execute($stmt_profile)) {
        mysqli_stmt_store_result($stmt_profile);
        mysqli_stmt_bind_result($stmt_profile, $profile_username, $profile_image);
        mysqli_stmt_fetch($stmt_profile);
        mysqli_stmt_close($stmt_profile);
    } else {
        echo "Error fetching user profile: " . mysqli_error($link);
        exit;
    }
} else {
    echo "Error preparing user profile query: " . mysqli_error($link);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <style>
        div {
            margin-bottom: 2px solid grey;
        }
    </style>
</head>
<body>
    <div>
        <?php
            echo "<h2>$profile_username</h2>";
            if ($profile_image) {
                echo "<img src='$profile_image' alt='Profile picture'><br>";
            }
        ?>
        <?php
            // Display follow/unfollow button
            if ($user_id != $_GET['user_id']) {  // Don't show the button for your own profile
                $follow_user_id = $_GET['user_id'];
                if (!isFollowing($link, $user_id, $follow_user_id)) {
                    echo "<form method='POST' action='follow_func.php'>";
                    echo "<input type='hidden' name='follow_user_id' value='$follow_user_id'>";
                    echo "<input type='hidden' name='follow' value='true'>";
                    echo "<input type='submit' value='Follow'>";
                    echo "</form>";
                } else {
                    echo "<form method='POST' action='follow_func.php'>";
                    echo "<input type='hidden' name='follow_user_id' value='$follow_user_id'>";
                    echo "<input type='hidden' name='unfollow' value='true'>";
                    echo "<input type='submit' value='Unfollow'>";
                    echo "</form>";
                }
            }
        ?>
    </div>
    <h3>Posts</h3>
    <?php
        $sql_user_posts =  "SELECT text_id, title, content, created_at
                            FROM texts
                            WHERE user_id = ?
                            ORDER BY created_at DESC";
        if ($stmt_user_posts = mysqli_prepare($link, $sql_user_posts)) {
            mysqli_stmt_bind_param($stmt_user_posts, "s", $_GET['user_id']);  // Assuming user_id is in the URL
            if (mysqli_stmt_execute($stmt_user_posts)) {
                $result_user_posts = mysqli_stmt_get_result($stmt_user_posts);
    
                while ($post = mysqli_fetch_assoc($result_user_posts)) {
                    $post_id = $post["text_id"];
                    $post_title = $post["title"];
                    $post_content = $post["content"];
                    $post_creation = $post["created_at"];
    
                    echo "<div style='border: 1px solid #ddd; padding: 10px; margin-bottom: 10px;'>";
                    echo "<h4><a href='posts.php?user_id=$user_id&text_id=$post_id'>$post_title</a></h4>";
                    echo "<p>$post_content</p>";
                    echo "<p><em>($post_creation)</em></p>";
                    echo "</div>";
                }
    
                mysqli_free_result($result_user_posts);

            } else {
                echo "Error fetching user's posts: " . mysqli_error($link);
            }
    
            mysqli_stmt_close($stmt_user_posts);

        } else {
            echo "Error preparing user's posts query: " . mysqli_error($link);
        }
    ?>
</body>
</html>