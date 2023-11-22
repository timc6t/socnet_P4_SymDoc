<?php
require_once "config.php";
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$tags = [];

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
        exit;
    }
} else {
    echo "Error preparing user query: " . mysqli_error($link);
    exit;
}

$sql_posts = "SELECT u.username AS post_user, t.text_id, t.user_id, t.title, t.content, t.created_at
              FROM texts t
              INNER JOIN users u ON t.user_id = u.user_id
              WHERE t.user_id = ? OR t.user_id IN (SELECT following_id FROM follows WHERE follower_id = ?)
              ORDER BY t.created_at DESC";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <style>
        .post-section {
            border: 2px solid black;
            width: 250px;
        }
        textarea { resize: vertical; }
    </style>
</head>
<body>
    <h2>Welcome to Rs/Ws</h2>
    <p>Hello, <b><?php echo $username; ?></b>!</p>
    <?php if ($profile_image) : ?>
        <img src="<?php echo $profile_image; ?>" alt="Profile picture">
    <?php endif; ?><br>
    <div id="navbar">
        <p><a href="search.php">Search</a></p>
        <p><a href="profile.php?user_id=<?php echo $user_id; ?>">Profile</a></p>
        <p><a href="logout.php">Logout</a></p>
    </div>
    <p>Create your own posts here!</p>

    <div class=post-section>
        <form method="POST" action="upload_text.php">
            <div>
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required>
            </div><br>
            <div>
                <label for="content">Post:</label>
                <textarea name="content" maxlength="512" required></textarea>
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
    </div>
    <br>
    <p>
        <?php
            if ($stmt_posts = mysqli_prepare($link, $sql_posts)) {
                mysqli_stmt_bind_param($stmt_posts,"ss", $user_id, $user_id);

                if (mysqli_stmt_execute($stmt_posts)) {
                    $result_posts = mysqli_stmt_get_result($stmt_posts);

                    while ($row = mysqli_fetch_assoc($result_posts)) {
                        $post_user_id = $row["user_id"];
                        $text_id = $row["text_id"];
                        $post_user = $row["post_user"];
                        $post_title = $row["title"];
                        $post_content = $row["content"];
                        $post_creation = $row["created_at"];

                        echo "<p><div class='username_post'><a href='profile.php?username=$post_user'>$post_user</a></div> <strong><a href='posts.php?user_id=$post_user_id&text_id=$text_id'>$post_title</a></strong> ($post_creation)</p>";
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
    </p>
</body>
</html>