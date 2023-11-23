<?php
require_once "config.php";
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'dashboard.php'; // Set default redirect URL

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["follow_user_id"])) {
        $follow_user_id = $_POST["follow_user_id"];

        // Check if the user is trying to follow or unfollow
        if (isset($_POST["follow"])) {
            // Check if the user is not already following
            if (!isFollowing($link, $user_id, $follow_user_id)) {
                followUser($link, $user_id, $follow_user_id);
                echo "Followed successfully!<br>";
            } else {
                echo "You are already following this user.";
            }
            echo "<br><button onclick='redirectToProfile()'>Go back</button>";
        } elseif (isset($_POST["unfollow"])) {
            // Check if the user is already following
            if (isFollowing($link, $user_id, $follow_user_id)) {
                unfollowUser($link, $user_id, $follow_user_id);
                echo "Unfollowed successfully!<br>";
            } else {
                echo "You are not following this user.";
            }
            echo "<br><button onclick='redirectToProfile()'>Go back</button>";
        }
        redirectSuccessful($redirect_url);
    } else {
        echo "Invalid parameters.";
    }
} else {
    echo "Invalid request.";
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

function followUser($link, $follower_id, $following_id) {
    $sql = "INSERT INTO follows (follower_id, following_id) VALUES (?, ?)";

    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ii", $follower_id, $following_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

function unfollowUser($link, $follower_id, $following_id) {
    $sql = "DELETE FROM follows
            WHERE follower_id = ? AND following_id = ?";

    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ii", $follower_id, $following_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

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

function redirectSuccessful($url) {
    header("Location: " . $url);
    exit;
}

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

<script>
    function goBack() {
        window.history.back();
    }
</script>
