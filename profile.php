<?php

session_start();
require_once "config.php";
//require_once "follow_func.php";


if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : $_SESSION["user_id"];
$username = $profile_image = "";
$profile_username = "";
$err = "";

// Retrieve user's profile information
$sql_profile = "SELECT username, profile_image FROM users WHERE user_id = ?";
if ($stmt_profile = mysqli_prepare($link, $sql_profile)) {
    mysqli_stmt_bind_param($stmt_profile, "i", $user_id);

    if (mysqli_stmt_execute($stmt_profile)) {
        mysqli_stmt_bind_result($stmt_profile, $profile_username, $profile_image);
        
        if (mysqli_stmt_fetch($stmt_profile)) {
            // User found
        } else {
            $err = "User not found";
        }
    } else {
        $err = "Something went wrong.";
    }
    mysqli_stmt_close($stmt_profile);
} else {
    $err = "Something went wrong preparing your profile." . mysqli_error($link);
}

// Display follow/unfollow button
if ($user_id != $_SESSION['user_id']) {
    $follow_user_id = $_user_id;
    if (!isFollowing($link, $_SESSION['user_id'], $follow_user_id)) {
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <style>
        div {
            margin: 20px;
        }
    </style>
</head>
<body>
    <div>
        <?php
            if ($profile_image) {
                echo "<img src='$profile_image' alt='Profile picture'><br>";
            }
            echo "<h2>$profile_username</h2>";
        ?>
        <div id="navbar">
            <p><a href="dashboard.php">Dashboard</a></p>
            <p><a href="logout.php">Logout</a></p>
        </div>
        <h3>Posts</h3>
    </div>

    <div id="posts">    
        <?php
            $profile_user_posts = [];
            $sql_user_posts = "SELECT text_id, title, content, created_at
                               FROM texts
                               WHERE user_id = ?
                               ORDER BY created_at DESC";
        
            if ($stmt_user_posts = mysqli_prepare($link, $sql_user_posts)) {
                mysqli_stmt_bind_param($stmt_user_posts, "i", $user_id);
        
                if (mysqli_stmt_execute($stmt_user_posts)) {
                    $result_user_posts = mysqli_stmt_get_result($stmt_user_posts);
                    $profile_user_posts = mysqli_fetch_all($result_user_posts, MYSQLI_ASSOC);
        
                    foreach ($profile_user_posts as $post) {
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
                } else {
                    echo "Error fetching user's posts: " . mysqli_error($link);
                }
                mysqli_stmt_close($stmt_user_posts);
            } else {
                echo "Error preparing your posts' query: " . mysqli_error($link);
            }
        ?>
    </div>
</body>
</html>

<?php mysqli_close($link); ?>
