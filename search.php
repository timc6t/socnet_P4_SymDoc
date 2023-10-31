<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["user_search"])) {
        // Handle user search
    } elseif (isset($_POST["text_search"])) {
        // Handle text search by genre
    }
}
?>
