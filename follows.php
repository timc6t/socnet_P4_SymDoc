<?php
session_start();
require_once "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : $_SESSION["user_id"];
$username = $profile_image = $profile_username = "";
$err = "";

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

if ($user_id != $_SESSION['user_id']) {
    $follow_user_id = $_GET['user_id'];

    if (!function_exists('isFollowing')) {
        require_once 'follow_func.php';
    }

    $current_page_url = htmlspecialchars($_SERVER["PHP_SELF"]) . "?user_id=$user_id";
}

function isFollowing($link, $follower_id, $following_id)
{
    $sql = "SELECT * FROM follows WHERE follower_id = ? AND following_id = ?";
    $stmt = mysqli_prepare($link, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $follower_id, $following_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        $count = mysqli_stmt_num_rows($stmt);

        mysqli_stmt_close($stmt);

        return $count > 0;
    }

    return false;
}

// Fetch users the current user is following and their follower
$following = getFollowing($link, $user_id);
$followers = getFollowers($link, $user_id);

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

function getFollowers($link, $user_id) {
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
    <title>Following</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="body">
        <div id="follows-navbar">
            <?php
                if ($profile_image) {
                    echo "<img src='$profile_image' alt='Profile picture'><br>";
                }
                echo "<h2>$profile_username</h2><br>";

                //$follow_user_id = isset($_POST['follow_user_id']) ? $_POST['follow_user_id'] : null;

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
            ?>
            <div id="navbar">
                <p><a class="no-line-navbar" href="dashboard.php">Dashboard</a></p>
                <p><a class="no-line-navbar" href="profile.php?user_id=<?php echo $user_id; ?>">Profile</a></p>
                <p><a class="no-line-navbar" href="search.php">Search</a></p>
                <p><a class="no-line-navbar" href="logout.php">Logout</a></p>
            </div>
        </div>

        <div id="follows">
            <div id="following">
                <?php
                    $following_count = count(getFollowing($link, $user_id));
                    echo "<h3>Following ($following_count)</h3>";
                    if (!empty($following)) {
                        foreach ($following as $followingUser) {
                            $following_user_id = $followingUser["user_id"];
                            $following_username = $followingUser["username"];
                            $following_profile_image = $followingUser["profile_image"];
                            echo "<div style='border: 1px solid #ddd; padding: 10px; margin-bottom: 10px;'>";
                            echo "<h4><a href='profile.php?user_id=$following_user_id'>$following_username</a></h4>";
                            if ($following_profile_image) {
                                echo "<img src='$following_profile_image' alt='Profile picture'><br>";
                            }
                            echo "</div>";
                        }
                    } else {
                        echo "<p>You are not following anyone yet.</p>";
                    }
                ?>
            </div>
            
            <div id="followers">
                <?php
                    $followers_count = count(getFollowers($link, $user_id));
                    echo "<h3>Followers ($followers_count)</h3>";
            
                    if (!empty($followers)) {
                        foreach ($followers as $followersUser) {
                            $followers_user_id = $followersUser["user_id"];
                            $followers_username = $followersUser["username"];
                            $followers_profile_image = $followersUser["profile_image"];
                            echo "<div style='border: 1px solid #ddd; padding: 10px; margin-bottom: 10px;'>";
                            echo "<h4><a href='profile.php?user_id=$followers_user_id'>$followers_username</a></h4>";
                            if ($followers_profile_image) {
                                echo "<img src='$followers_profile_image' alt='Profile picture'><br>";
                            }
                            echo "</div>";
                        }
                    } else {
                        echo "<p>You don't have any following yet.</p>";
                    }
                ?>
            </div>
        </div>
    </div>
</body>
</html>

<?php mysqli_close($link); ?>
