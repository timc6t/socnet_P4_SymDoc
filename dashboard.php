<?php
require_once "config.php";
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: dashboard.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$tags = [];

// Fetching user data
$sql_user = "SELECT username, profile_image FROM users WHERE user_id = ?";
if ($stmt_user = mysqli_prepare($link, $sql_user)) {
    mysqli_stmt_bind_param($stmt_user,"s", $user_id);
    
    if (mysqli_stmt_execute($stmt_user)) {
        mysqli_stmt_store_result($stmt_user);
        mysqli_stmt_bind_result($stmt_user, $username, $profile_image);
        mysqli_stmt_fetch($stmt_user);
        mysqli_stmt_close($stmt_user);
    } else {
        echo "Error fetching user data: " . mysqli_error($link);
        exit; // Add exit here to stop further execution
    }
} else {
    echo "Error preparing user query: " . mysqli_error($link);
    exit; // Add exit here to stop further execution
}

// Fetching posts from users you follow and your own
$sql_posts = "SELECT u.username AS post_user, t.title, t.content, t.created_at
              FROM texts t
              INNER JOIN users u ON t.user_id = u.user_id
              WHERE t.user_id = ? OR t.user_id IN (SELECT following_id FROM follows WHERE follower_id = ?)
              ORDER BY t.created_at DESC";

if ($stmt_posts = mysqli_prepare($link, $sql_posts)) {
    mysqli_stmt_bind_param($stmt_posts,"ss", $user_id, $user_id);

    if (mysqli_stmt_execute($stmt_posts)) {
        $result_posts = mysqli_stmt_get_result($stmt_posts);

        while ($row = mysqli_fetch_assoc($result_posts)) {
            $post_user = $row["post_user"];
            $post_title = $row["title"];
            $post_content = $row["content"];
            $post_creation = $row["created_at"];


        }

        mysqli_free_result($result_posts);
    } else {
        echo "Error fetching posts: " . mysqli_error($link);
    }

    mysqli_stmt_close($stmt_posts);
} else {
    echo "Error preparing posts query: " . mysqli_error($link);
}

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
</head>
<body>
    <h2>Welcome to Rs/Ws</h2>
    <p>Hello, <b><?php echo $username; ?></b>!</p>
    <?php if ($profile_image) : ?>
        <img src="<?php echo $profile_image; ?>" alt="Profile picture">
    <?php endif; ?><br>
    <div id="navbar">
        <p><a href="user_posts.php">My texts</a></p> <!-- TO DO: user_posts.php -->
        <p><a href="profile.php">My profile</a></p>
        <p><a href="logout.php">Logout</a></p>
    </div>
    <p>Create your own posts here!</p>

    <form method="POST" action="upload_text.php">
        <div>
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>
        </div><br>

        <div>
            <label for="content">Post:</label>
            <textarea name="content" required></textarea>
        </div><br>

        <div>
            <label for="is_public">Privacy:</label>
            <select name="is_public">
                <option value="public">Public</option>
                <option value="private">Private</option>
            </select>
        </div><br>

        <div>
            <label>Tags:</label>
            <input type="text" name="tags" value="<?php echo implode(', ', $tags); ?>"><br>
            <span>Enter tags separated by commas</span>
        </div><br>

        <div>
            <input type="submit" value="Post">
        </div>
    </form>
    <br />
    <p>
        <?php echo "<p><div class='username_post'><a href='profile.php?username=$post_user</a></div><p><br> <p><strong>$post_title</strong></p><br> ($post_creation)"; ?>
    </p>
</body>
</html>