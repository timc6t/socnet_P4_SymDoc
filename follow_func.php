<?php
// Code for user follows
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize user input
    $following_id = filter_var($_POST["following_id"], FILTER_SANITIZE_NUMBER_INT);
    
    // Ensure the following_id is not the same as the follower_id (you can adjust this validation as needed)
    if ($following_id == $_SESSION["user_id"]) {
        $error_message = "You can't follow yourself.";
    } else {
        // Check if the user is already following the target user
        $check_query = "SELECT follow_id FROM follows WHERE follower_id = ? AND following_id = ?";
        $stmt = $pdo->prepare($check_query);
        $stmt->execute([$_SESSION["user_id"], $following_id]);

        if ($stmt->rowCount() == 0) {
            // User is not already following, so insert a follow record
            $insert_query = "INSERT INTO follows (follower_id, following_id) VALUES (?, ?)";
            $stmt = $pdo->prepare($insert_query);
            $stmt->execute([$_SESSION["user_id"], $following_id]);

            if ($stmt->rowCount() > 0) {
                // Follow added successfully
                $success_message = "You are now following this user.";
            } else {
                // Error handling: Follow insertion failed
                $error_message = "Failed to add the follow. Please try again later.";
            }
        } else {
            // User is already following
            $error_message = "You are already following this user.";
        }
    }
    
    // Redirect to the user's profile (you can adjust the URL as needed)
    header("Location: profile.php?user_id=$following_id");
    exit;
}

?>