<?php
session_start();
require_once "config.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["user_id"])) {
    require 'follow_func.php';
    header("Location: login.php");
    exit;
}

$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : $_SESSION["user_id"];
$username = $profile_image = $profile_username = "";
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
$follow_user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;

/*if ($user_id != $_SESSION['user_id'] && $follow_user_id !== null) {
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
}*/

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
        
                    
    } else {
        echo "Error fetching user's posts: " . mysqli_error($link);
    }
    mysqli_stmt_close($stmt_user_posts);
} else {
    echo "Error preparing your posts' query: " . mysqli_error($link);
}

function isFollowing($link, $follower_id, $following_id) {
    $sql = "SELECT * 
            FROM follows
            WHERE follower_id = ? AND following_id = ?";
    $stmt = mysqli_prepare($link, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $follower_id, $following_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $result = mysqli_stmt_num_rows($stmt);
        mysqli_stmt_close($stmt);
        return $result > 0;
    } else {
        return false;
    }
}

$following = getFollowing($link, $user_id);

function getFollowing($link, $user_id) {
    $following = [];

    $sql = "SELECT u.user_id, u.username, u.profile_image
            FROM follows f
            INNER JOIN users u ON f.following_id = u.user_id
            WHERE f.follower_id = ?";
    $stmt = mysqli_prepare($link, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $following[] = $row;
        }

        mysqli_stmt_close($stmt);
    }

    return $following;
}

$followers = getFollowers($link, $user_id);

function getFollowers($link, $user_id)
{
    $followers = [];

    $sql = "SELECT u.user_id, u.username, u.profile_image
            FROM follows f
            INNER JOIN users u ON f.follower_id = u.user_id
            WHERE f.following_id = ?";
    $stmt = mysqli_prepare($link, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $followers[] = $row;
        }

        mysqli_stmt_close($stmt);
    }

    return $followers;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="body">
    <div id="profile-navbar">
        <?php
            if ($profile_image) {
                echo "<img src='$profile_image' alt='Profile picture'><br>";
            }
            echo "<h2>$profile_username</h2><br>";

            //The following block of code is doing something weird.

            if ($user_id != $_SESSION['user_id'] && $follow_user_id !== null) {
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

            $following_count = count(getFollowing($link, $user_id));
            echo "<div id='following'>";
            echo "<p><a class='no-line' href='follows.php?user_id=$user_id'>$following_count following</a></p>";
            echo "</div>";

            $follower_count = count(getFollowers($link, $user_id));
            echo "<div id='followerss'>";
            echo "<p><a class='no-line' href='follows.php?user_id=$user_id'>$follower_count followers</a></p>";
            echo "</div>";
            
        ?>
        <div id="navbar">
            <p><a class="no-line-navbar" href="dashboard.php">Dashboard</a></p>
            <p><a class="no-line-navbar" href="profile.php?user_id=<?php echo $user_id; ?>">Profile</a></p>
            <p><a class="no-line-navbar" href="search.php">Search</a></p>
            <p><a class="no-line-navbar" href="logout.php">Logout</a></p>
        </div>
    </div>

    <div id="posts-profile">
        <h3>Posts</h3> 
        <div class="indent">
            <?php
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
            ?>
        </div>
    </div>
</body>
</html>

<?php mysqli_close($link); ?>
