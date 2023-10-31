<?php
// Code for user follows
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $follower_id = $_SESSION["user_id"];
    $following_id = $_POST["following_id"]; // You can use a form input to specify the user to follow.

    // Validate and sanitize user input

    // Check if the user is already following the target user
    $check_query = "SELECT follow_id FROM follows WHERE follower_id = ? AND following_id = ?";
    $stmt = $pdo->prepare($check_query);
    $stmt->execute([$follower_id, $following_id]);

    if ($stmt->rowCount() == 0) {
        // User is not already following, so insert a follow record
        $insert_query = "INSERT INTO follows (follower_id, following_id) VALUES (?, ?)";
        $stmt = $pdo->prepare($insert_query);
        $stmt->execute([$follower_id, $following_id]);

        if ($stmt->rowCount() > 0) {
            // Follow added successfully
            // You can display a success message or redirect to the user's profile.
        } else {
            // Error handling: Follow insertion failed
        }
    } else {
        // User is already following, you can handle this case as needed.
    }
}
?>