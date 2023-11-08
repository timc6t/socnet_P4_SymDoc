<?php
require_once "config.php";
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

$sql = "SELECT username, profile_image FROM users WHERE user_id = ?";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt,"s", $user_id);
    
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_store_result($stmt);
        mysqli_stmt_bind_result($stmt, $username, $profile_image);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
    } else {
        echo "Error fetching user data: " . mysqli_error($link);
    }
} else {
    mysqli_error($link);
}

$sql = "SELECT user_id, title, description, content, is_public, created_at FROM texts ORDER BY  created_at DESC";
$result = mysqli_query($link, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $post_user = $row["username"];
        $post_title = $row["title"];
        $post_content = $row["content"];
        $post_creation = $row["created_at"];

        echo '<p><div class="username_post">$post_user</div></p><br> <p><strong>$post_title</strong></p><br> ($post_creation)';
    }
    mysqli_free_result($result);
} else {
    echo "Error fetching posts: " . mysqli_error($link);
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
    <h2>Welcome to Your Dashboard</h2>
    <p>Hello, <?php echo $username; ?></p>
    <?php if ($profile_image) : ?>
        <img src="<?php echo $profile_image; ?>" alt="Profile picture">
    <?php endif; ?>

    <form method="POST" action="upload_text.php">
        <lable for="post_content">Create a post:</label>
        <textarea id="post_content" name="post_content" required></textarea>
        <input type="submit" value="Post">
    </form>
    
    <p><a href="logout.php">Logout</a></p>
</body>
</html>