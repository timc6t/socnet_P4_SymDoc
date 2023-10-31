<?php
// Code for submitting comments and ratings
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $text_id = $_POST["text_id"];
    $user_id = $_SESSION["user_id"];
    $content = $_POST["comment_content"];
    $rating = $_POST["rating"]; // You can use a dropdown or other input for ratings.

    // Validate and sanitize user input

    // Insert the comment and rating into the comments table
    $insert_query = "INSERT INTO comments (text_id, user_id, content, rating) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($insert_query);
    $stmt->execute([$text_id, $user_id, $content, $rating]);

    if ($stmt->rowCount() > 0) {
        // Comment and rating added successfully
        // You can display a success message or redirect to the text page.
    } else {
        // Error handling: Comment and rating insertion failed
    }
}
?>
