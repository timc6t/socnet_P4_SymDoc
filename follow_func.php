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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["follow_user_id"])) {
        $follow_user_id = $_POST["follow_user_id"];

        // Check if the user is trying to follow or unfollow
        if (isset($_POST["follow"])) {
            // Check if the user is not already following
            if (!isFollowing($link, $user_id, $follow_user_id)) {
                followUser($link, $user_id, $follow_user_id);
                echo "Followed successfully!";
            } else {
                echo "You are already following this user.";
            }
        } elseif (isset($_POST["unfollow"])) {
            // Check if the user is already following
            if (isFollowing($link, $user_id, $follow_user_id)) {
                unfollowUser($link, $user_id, $follow_user_id);
                echo "Unfollowed successfully!";
            } else {
                echo "You are not following this user.";
            }
        }
    } else {
        echo "Invalid parameters.";
    }
} else {
    echo "Invalid request.";
}

function isFollowing($link, $follower_id, $following_id) {
    $sql = "SELECT * FROM follows WHERE follower_id = ? AND following_id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ii", $follower_id, $following_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $result = mysqli_stmt_num_rows($stmt) > 0;
        mysqli_stmt_close($stmt);
        return $result;
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
    $sql = "DELETE FROM follows WHERE follower_id = ? AND following_id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ii", $follower_id, $following_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}
?>
