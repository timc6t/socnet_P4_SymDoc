<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// Retrieve user's texts from the database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"];
    $text_id = $_POST["text_id"];

    if ($action == "delete") {
        // Delete the text from the database
    } elseif ($action == "change_status") {
        // Update the text's public/private status
    }
}
?>
