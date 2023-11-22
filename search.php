<?php
session_start();
require_once "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

// Handle search queries
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["term"])) {
    $search_term = $_GET["term"];

    // Search for users
    $sql_search_user = "SELECT user_id, username, profile_image
                        FROM users
                        WHERE username LIKE ? AND user_id != ?
                        ORDER BY username";

    $stmt_search_user = mysqli_prepare($link, $sql_search_user);
    $search_term_like = "%$search_term%";
    mysqli_stmt_bind_param($stmt_search_user, "si", $search_term_like, $user_id);

    if (mysqli_stmt_execute($stmt_search_user)) {
        $result_search_user = mysqli_stmt_get_result($stmt_search_user);
        $search_results_users = mysqli_fetch_all($result_search_user, MYSQLI_ASSOC);
    } else {
        echo "Error searching for users: " . mysqli_error($link);
    }

    mysqli_stmt_close($stmt_search_user);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search</title>
    <style>
        div {
            margin-left: 10px;
        }
        .user-posts {
            border-bottom: 1px solid gainsboro;
            width: 75%;
            padding-left: 10px;
        }
    </style>
</head>
<body>
    <h2>Search</h2>
    <div id="navbar">
            <p><a href="dashboard.php">Dashboard</a></p>
            <p><a href="profile.php?user_id=<?php echo $user_id; ?>">Profile</a></p>
            <p><a href="logout.php">Logout</a></p>
    </div>
    <form method="GET" action="search.php">
        <label for="search_term"></label>
        <input type="text" id="search_term" name="term" placeholder="Search">
        <input type="submit" value="Search">
    </form>
    <div>
        <?php if (isset($search_results_users)): ?>
            <h3>Results</h3>
            <?php foreach ($search_results_users as $user): ?>
                <?php $searched_user_id = $user["user_id"]; ?>
                <div>
                    <h4><a href='profile.php?user_id=<?= $searched_user_id ?>'><?= $user["username"] ?></a></h4>
                    <?php if ($user["profile_image"]): ?>
                        <img src='<?= $user["profile_image"] ?>' alt='Profile picture'><br>
                    <?php endif; ?>
                    <?php
                        $sql_searched_user_posts = "SELECT text_id, title, content, created_at
                                                    FROM texts
                                                    WHERE user_id = ?
                                                    ORDER BY created_at DESC";
        
                        $stmt_searched_user_posts = mysqli_prepare($link, $sql_searched_user_posts);
                        mysqli_stmt_bind_param($stmt_searched_user_posts, "i", $searched_user_id);
        
                        if (mysqli_stmt_execute($stmt_searched_user_posts)) {
                            $result_searched_user_posts = mysqli_stmt_get_result($stmt_searched_user_posts);
                            $searched_user_posts = mysqli_fetch_all($result_searched_user_posts, MYSQLI_ASSOC);
        
                            foreach ($searched_user_posts as $post) {
                                echo '<div class="user-posts">';
                                echo "<h4><a href='posts.php?user_id=$searched_user_id&text_id={$post["text_id"]}'>{$post["title"]}</a></h4>";
                                echo "<p>{$post["content"]}</p>";
                                echo "<p><em>({$post["created_at"]})</em></p>";
                                echo "</div>";
                            }
                        } else {
                            echo "Error fetching user's posts: " . mysqli_error($link);
                        }
        
                        mysqli_stmt_close($stmt_searched_user_posts);
                    ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>

<?php mysqli_close($link); ?>
