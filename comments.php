<?php
// Code for submitting comments and ratings
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize user input
    $text_id = filter_var($_POST["text_id"], FILTER_SANITIZE_NUMBER_INT);
    $content = filter_var($_POST["comment_content"], FILTER_SANITIZE_STRING);
    $rating = filter_var($_POST["rating"], FILTER_VALIDATE_INT);

    // Check if any of the inputs are invalid
    if ($text_id === false || $content === false || $rating === false) {
        // Invalid input, handle the error as needed
        $error_message = "Invalid input data. Please try again.";
    } else {
        // Input is valid, proceed with database insertion
        $user_id = $_SESSION["user_id"];
        
        // Insert the comment and rating into the comments table
        $insert_query = "INSERT INTO comments (text_id, user_id, content, rating) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($insert_query);
        $stmt->execute([$text_id, $user_id, $content, $rating]);

        if ($stmt->rowCount() > 0) {
            // Comment and rating added successfully
            $success_message = "Comment and rating added successfully.";
        } else {
            // Error handling: Comment and rating insertion failed
            $error_message = "Failed to add the comment and rating. Please try again later.";
        }

        // Redirect to the text page (you can adjust the URL as needed)
        header("Location: text_page.php?text_id=$text_id");
        exit;
    }
}

// Output success and error messages
if (!empty($success_message)) {
    echo "Success: " . $success_message;
}

if (!empty($error_message)) {
    echo "Error: " . $error_message;
}

?>
